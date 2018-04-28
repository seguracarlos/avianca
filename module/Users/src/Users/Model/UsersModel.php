<?php
namespace Users\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class UsersModel extends TableGateway
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
	 * OBTEMOS TODOS LOS DATOS DE USUARIO
	 */


  public function getAll($id_user)
	{

		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select->from('users');
		$select->columns(array('id','email'));
	
		$select->join(array('i_u'=>'users_details'),'users.id = i_u.id_user',array('id_user','name','surname', 'campus', 'phone', 'addres','image','pin','key_inventory'), 'Left');
		$select->where(array('users.id' =>$id_user ));

		$selectString = $sql->getSqlStringForSqlObject($select);
        $execute = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        $result=$execute->toArray();   

		//echo "<pre>";print_r($result);exit;
		return $result;

	}

   	/**
   	 * Metodo para agregar un usuario 
   	 */
	public function addUsers($data)
	{
		$connection = null;
		
		try {
			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();
		
			$users = $this->insert($data);
			$saveUsers = $this->getLastInsertValue();
			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
				$connection->rollback();
			}
			// Tratamiento de errores
			$saveUsers = $e->getCode();
		}
		
		return $saveUsers;
	}

	   /**
   	 * Metodo para agregar el detalle de un usuario 
   	 */
	public function addUsersDetails($data)
	{
		$connection = null;
		
		try {
			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();

			$sql    = new Sql($this->dbAdapter);

   			$insert = $sql->insert("users_details");
   		
   			$insert->values($data);

   			$saveUsersDetails   = $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();



			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
				$connection->rollback();
			}
			// Tratamiento de errores
			$saveUsersDetails = $e->getCode();
		}
		
		return $saveUsersDetails;
	}

	   /**
   	 * Metodo para agregar el un proveedor 
   	 */
	public function addCompany($data)
	{
		$connection = null;
		
		try {
			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();

			$sql    = new Sql($this->dbAdapter);

   			$insert = $sql->insert("company");
   		
   			$insert->values($data);

   			$saveCompany   = $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();



			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
				$connection->rollback();
			}
			// Tratamiento de errores
			$saveCompany = $e->getCode();
		}
		
		return $saveCompany;
	}

	/**
	 * Metodo para validar si un email ya existe
	 */
	public function verifyEmailExists($email)
	{
		$emailFull   = $email['email'];
		
		$consult = $this->dbAdapter->query("SELECT count(email) as count FROM users WHERE email='$emailFull'",Adapter::QUERY_MODE_EXECUTE);
		
		$result  = $consult->toArray();

		return $result;
	}


	/**
	 * OBTENER PERFIL POR ID
	 */
	public function getPerfilById($id)
	{
    	$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select->from('users');
		$select->columns(array('id','email','password'));
	
		$select->join(array('i_u'=>'users_details'),'users.id = i_u.id_user',array('id_user','name','surname', 'campus', 'phone', 'addres','image','pin','key_inventory'), 'Left');
		$select->where(array('users.id' =>$id ));

		$selectString = $sql->getSqlStringForSqlObject($select);
        $execute = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        $result=$execute->toArray();   

		//echo "<pre>";print_r($result);exit;
		return $result;

	}


	/**
	 * MODIFICAR PERFIL
	 */
	public function editPerfil($data)
	{
		//print_r($data); exit;
		$perfil  = $this->update($data, array("id" => $data['id']));
      		$editPerfil = $data['id'];
    	/*$connection = null;
  
    	try {
      		$connection = $this->dbAdapter->getDriver()->getConnection();
      		$connection->beginTransaction();
  
      		
      		$perfil  = $this->update($data, array("id" => $data['id']));
      		$editPerfil = $data['id'];
      		$connection->commit();
    	}
    	catch (\Exception $e) {
      		if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
        		$connection->rollback();
      		}
      		
      		$editPerfil = $e->getCode();
    	}
    	//echo "<pre>"; print_r($editPerfil); exit;
    	return $editPerfil;*/
	}

	public function editusersDetails($data)
	{

		$sql    = new Sql( $this->adapter );
$update = $sql->update();
$update->table( 'users_details' );
$update->set( $data );
$update->where( array( 'id_user' => $data['id_user'] ) );

$statement  = $sql->prepareStatementForSqlObject( $update );
$results    = $statement->execute();
return true;

	}


	/**
	 * METODO PARA VALIDAR LA CLAVE DE SESION
	 */
	public function verifyKeyPass($key, $idUser)
	{
		
		$consult = $this->dbAdapter->query("SELECT count(password) as count FROM users WHERE password='$key' AND id = '$idUser'",Adapter::QUERY_MODE_EXECUTE);
		//print_r($consult); exit;
		$result  = $consult->toArray();

		return $result;
	}
	

	/**
	 * METODO PARA ACTUALIZAR LA CONTRASEÑA DE SESION
	 */
	public function updateKeyPass($key, $idUser)
	{
		/*$sql = new Sql ( $this->dbAdapter );
		$consult = $this->dbAdapter->query ( "UPDATE users SET password='$key' WHERE id='$idUser'", Adapter::QUERY_MODE_EXECUTE );
		
		 //print_r($consult); exit;

		return $consult;*/
		$dataPass = array("password" => $key);

		$sql    = new Sql( $this->adapter );
		$update = $sql->update();
		$update->table( 'users' );
		$update->set( $dataPass );
		$update->where( array( 'id' => $idUser ) );

		$statement  = $sql->prepareStatementForSqlObject( $update );
		$results    = $statement->execute();
		//echo "<pre>"; print_r($results); exit;
		return true;

	}

	/**
	 * METODO PARA ACTUALIZAR EMAIL
	 */
	public function updateEmail($email, $idUser)
	{
		/*$sql = new Sql ( $this->dbAdapter );
		$consult = $this->dbAdapter->query ( "UPDATE users SET email='$email' WHERE id='$idUser'", Adapter::QUERY_MODE_EXECUTE );
		
		 //print_r($consult); exit;

		return $consult;*/
		$dataEmail = array("email" => $email);

		$sql    = new Sql( $this->adapter );
		$update = $sql->update();
		$update->table( 'users' );
		$update->set( $dataEmail );
		$update->where( array( 'id' => $idUser ) );

		$statement  = $sql->prepareStatementForSqlObject( $update );
		$results    = $statement->execute();
		//echo "<pre>"; print_r($results); exit;
		return true;

	}

	/**
	 * METODO PARA VALIDAR EL PIN DEL USUARIO
	 */
	public function verifyPinUser($pin, $idUser)
	{
		
		$consult = $this->dbAdapter->query("SELECT count(pin) as count FROM users_details WHERE pin='$pin' AND id_user = '$idUser'",Adapter::QUERY_MODE_EXECUTE);
		//print_r($consult); exit;
		$result  = $consult->toArray();

		return $result;
	}

	/**
	 * METODO PARA ACTUALIZAR LA CONTRASEÑA DE INVENTARIO
	 */
	public function updateKeyPassinventory($key, $idUser)
	{
		/*$sql = new Sql ( $this->dbAdapter );
		$consult = $this->dbAdapter->query ( "UPDATE users_details SET key_inventory='$key' WHERE id='$idUser'", Adapter::QUERY_MODE_EXECUTE );
		
		 //print_r($consult); exit;

		return $consult;*/
		$dataEmail = array("key_inventory" => $key);

		$sql    = new Sql( $this->adapter );
		$update = $sql->update();
		$update->table( 'users_details' );
		$update->set( $dataEmail );
		$update->where( array( 'id_user' => $idUser ) );

		$statement  = $sql->prepareStatementForSqlObject( $update );
		$results    = $statement->execute();
		//echo "<pre>"; print_r($results); exit;
		return true;

	}


	/**
	 * METODO PARA VALIDAR LA CLAVE DE INVENTARIO
	 */
	public function verifyKeyPassinventory($key, $idUser)
	{
		
		$consult = $this->dbAdapter->query("SELECT count(key_inventory) as count FROM users_details WHERE key_inventory='$key' AND id_user = '$idUser'",Adapter::QUERY_MODE_EXECUTE);
		//print_r($consult); exit;
		$result  = $consult->toArray();

		return $result;
	}


	/**
	 * METODO PARA VALIDAR EL PIN
	 */
	public function verifyKeyPin($key, $idUser)
	{
		
		$consult = $this->dbAdapter->query("SELECT count(pin) as count FROM users_details WHERE pin='$key' AND id = '$idUser'",Adapter::QUERY_MODE_EXECUTE);
		//print_r($consult); exit;
		$result  = $consult->toArray();

		return $result;
	}



	/**
	 * METODO PARA ACTUALIZAR EL PIN
	 */
	public function updateKeyPin($key, $idUser)
	{
		 /*$sql = new Sql ( $this->dbAdapter );
		$consult = $this->dbAdapter->query ( "UPDATE users_details SET pin='$key' WHERE id='$idUser'", Adapter::QUERY_MODE_EXECUTE );
		
		 //print_r($consult); exit;

		return $consult;*/
		$dataPin = array("pin" => $key);

		$sql    = new Sql( $this->adapter );
		$update = $sql->update();
		$update->table( 'users_details' );
		$update->set( $dataPin );
		$update->where( array( 'id_user' => $idUser ) );

		$statement  = $sql->prepareStatementForSqlObject( $update );
		$results    = $statement->execute();
		//echo "<pre>"; print_r($results); exit;
		return true;

	}

	/**
	 * METODO PARA INSERTAR
	 */
	public function insertTokenPush($token, $idUser, $osType){

		//echo "<pre>"; print_r('TOKEN: '.$token.'IDUSER: '.$idUser.' OSTYPE: '.$osType); exit;

		$data = array( 'id_users' => $idUser,
			'Key_device' => $token,
			'types' => $osType,
			'date_registration' => date('Y-m-d H:i:s'));

		//echo "<pre>"; print_r($data); exit;

		$connection = null;
		try {

			

			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();

			$sql    = new Sql($this->dbAdapter);								
			$delete = $this->dbAdapter->query("DELETE FROM user_device WHERE Key_device = '$token'",Adapter::QUERY_MODE_EXECUTE);

   			$insert = $sql->insert("user_device");
   			$insert->values($data);
   			$saveUsersDevice   = $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();
			//echo "<pre>"; print_r($saveUsersDevice); exit;
			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
				$connection->rollback();
			}
			// Tratamiento de errores
			$saveUsersDevice = $e->getCode();
		}
		
		return $saveUsersDevice;
	}

	/**
	 * METODO PARA ACTUALIZAR CONTRASEÑA DE INVENTARIO
	 */
	public function updatekey_inventory($key, $idUser)
	{
		 $sql = new Sql ( $this->dbAdapter );
		$consult = $this->dbAdapter->query ( "UPDATE users_details SET key_inventory='$key' WHERE id='$idUser'", Adapter::QUERY_MODE_EXECUTE );
		
		 //print_r($consult); exit;

		return $consult;

	}


}