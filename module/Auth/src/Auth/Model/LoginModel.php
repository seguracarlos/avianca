<?php

namespace Auth\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

// Componentes de autenticacion
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class LoginModel extends TableGateway
{

	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
		$this->table      = 'users';
		$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

	/**
	 * Metodo para autenticar al usuario
	 */
	public function authenticationUser($data)
	{
		//echo "<pre>"; print_r($data); exit;
		// Instanciar el servicio de autenticación
		$auth = new AuthenticationService();

		// Configurar la instancia con parámetros de constructor ...
		/*$authAdapter = new AuthAdapter($this->dbAdapter,
			'users',
			'username',
			'password'
		);*/

		// ... o configurar la instancia con métodos setter
		$authAdapter = new AuthAdapter($this->dbAdapter);

		$authAdapter
    		->setTableName('users')
    		->setIdentityColumn('email')
    		->setCredentialColumn('password');

    	// Establece los valores de credenciales de entrada (por ejemplo, desde un formulario de inicio de sesión)
    	$authAdapter
    		->setIdentity($data['username'])
    		->setCredential($data['password']);
    	
    	// Pasamos el adaptador al servixio de autenticacion
    	$auth->setAdapter($authAdapter);

    	// Llevamos a acabo la autenticacion
    	$result = $auth->authenticate();

    	$dataResult = array();

    	// Validamos el resultado
    	if ($result->isValid()) {

    		// Columnas que deseamos
			$columnsToReturn = array(
    			'id', 'email', 'perfil'
			);

			// Datos de autenticacion
    		$resultRowObject = (array) $authAdapter->getResultRowObject($columnsToReturn);
			//echo "<pre>"; print_r($resultRowObject); exit;
			// Arreglo con los datos a devolver
			$dataResult = array(
				'code'   => $result->getCode(),
				'id'     => $resultRowObject['id'],
				'email'  => $resultRowObject['email'],
				'perfil' => $resultRowObject['perfil']
			);
    		
    	} else {
    		// Arreglo con los datos a devolver
			$dataResult = array(
				'code' => $result->getCode()
			);
    	}
    	
    	// obtenemos la identidad
    	// $auth->getIdentity()
    	// Obtenemos el codigo de autenticacion
    	// $result->getCode()
    	// Obtenemos los datos de sesion
    	// $authAdapter->getResultRowObject()
    	//echo "<pre>"; print_r($dataResult); exit;

    	// Devolvemos un resultado
    	return $dataResult;
		
	}

	/**
	 * Metodo para cerrar sesion del usuario
	 */
	public function logoutUser()
	{
		$authService = new AuthenticationService();
        
        $authService->clearIdentity();

        return true;
	}


	/**
	 * Metodo para validar si un email ya existe
	 */
	public function verifyEmailExists($email)
	{
		$emailFull   = $email['email'];
		
		$consult = $this->dbAdapter->query("SELECT count(email) as count ,id  FROM users WHERE email='$emailFull'",Adapter::QUERY_MODE_EXECUTE);
		
		$result  = $consult->toArray();

		return $result;
	}

	/**
	 * Metodo para validar si un token ya existe
	 */
	public function verifyToken($tokenExisting)
	{
		$tokenExistingFull   = $tokenExisting['token'];
		
		$consult = $this->dbAdapter->query("SELECT count(token) as count, users.id FROM token INNER JOIN users ON token.id_users=users.id WHERE token.token='$tokenExistingFull' ",Adapter::QUERY_MODE_EXECUTE);
		
		$result  = $consult->toArray();
		//print_r($result); exit;

		return $result;
	}


	  	/**
   	 * Metodo para agregar un token
   	 */
	public function addToken($data)
	{
		$connection = null;
		
		try {
			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();

			$sql    = new Sql($this->dbAdapter);

   			$insert = $sql->insert("token");
   		
   			$insert->values($data);

   			$saveToken   = $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();



			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
				$connection->rollback();
			}
			// Tratamiento de errores
			$saveToken = $e->getCode();
		}
		
		return $saveToken;
	}


	/**
	 * ELIMINAR TOKEN
	 */
	public function deleteToken($tokenExisting)
	{
		//$tokenExistingFull   = $tokenExisting['token'];
		
		$consult = $this->dbAdapter->query("DELETE FROM token WHERE id_users='$tokenExisting'",Adapter::QUERY_MODE_EXECUTE);
		//print_r($consult); exit;
		//$result  = $consult->toArray();

		return true;
	}


}