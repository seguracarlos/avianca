<?php

namespace Pets\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class PetsModel extends TableGateway
{
	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
    	$this->table      = 'pets';
       	$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

	/**
	 * BASE64 A IMAGEN
	 */
	public function base64ToImagePath()
	{
		$sql 	= new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			->columns(array('id', 'id_register_qr', 'img_pet', 'image_name'))
			->from(array('pet' => $this->table));

		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();
		//echo "<pre>"; print_r($result); exit;

		return $result;
	}

	/**
	 * OBTEMOS TODOS los nombres del articulos
	 */
	public function getAllNamearticle()
	{
		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			->columns(array('id', 'name_article'))
			->from(array('c' => $this->table))
		
			->order('c.name_article ASC');

		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();


		return $result;

	}

	/**
	 * OBTEMOS TODOS LAS MASCOTAS POR ID DE USUARIO
	 */
	public function getAll($idUser, $typePerfil = null)
	{
		//echo "<pre>"; print_r($idUser); exit;
		// Arreglo de status
		//$arrayStatusArticles = array(1, 5, 7);
		$arrayStatusArticles = array(1, 5);

		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			->columns(array('id', 'type_pet', 'name_pet', 'breed_pet', 'color_pet', 'size_pet', 'description_pet', 'age_pet', 'img_pet', 'image_name'))
			->from(array('pet' => $this->table))
			//->join(array('c' => 'color'), 'a.id_color = c.id', array('name_color' => 'name'), 'Inner')
			//->join(array('cat' => 'category'), 'a.id_category = cat.id', array('name_category' => 'namecategory'), 'Inner')
			//->join(array('cat2' => 'category'), 'a.name_article = cat2.id', array('name_article_two' => 'namecategory'), 'Left')
			->join(array('r_q' => 'register_qr'), 'pet.id_register_qr = r_q.id', array('id_register_qr' => 'id', 'code_article'=>'foliocodeqr', 'id_status'), 'Inner')
			->join(array('s' => 'status'), 'r_q.id_status = s.id', array('name_status' => 'name'), 'Inner')
			->where(array('pet.id_users' => $idUser))
			//->where(array('own_alien' => (int) 1))
			->order('pet.id ASC')
			->where->notIn("r_q.id_status", $arrayStatusArticles);

		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();
		//echo "<pre>"; print_r($result); exit;

		return $result;
	}

	/**
	 *  OBTEMOS TODOS LOS ARTICULOS ENCONTRADOS POR ID DE USUARIO
	 */
	public function getAllArticlesFoundByIdUser($idUser)
	{
		//echo "<pre>"; print_r($idUser); exit;
		// Arreglo de status
		$arrayStatusArticles = array(1, 5);

		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			// COLUMNAS DE LA TABLA ARTICLES 
			->columns(array('id', 'id_user', 'id_category', 'name_article', 'description', 'brand', 'imageone', 'own_alien', 'asignated_to'))

			// FROM TABLA DE ARTICLES
			->from(array('a' => $this->table))

			// JOIN TABLA DE RETURNS
			->join(array('ret' => 'returns'), 'a.id = ret.id_articles', array('id_return' => 'id', 'warehouse', 'phone_warehouse', 'date_found', 'return_date', 'id_userfound', 'id_status'), 'Inner')
			
			// JOIN TABLA DE COLOR
			->join(array('c' => 'color'), 'a.id_color = c.id', array('name_color' => 'name'), 'Inner')
			
			// JOIN TABLA DE CATEGORY
			->join(array('cat' => 'category'), 'a.id_category = cat.id', array('name_category' => 'namecategory'), 'Inner')
			->join(array('cat2' => 'category'), 'a.name_article = cat2.id', array('name_article_two' => 'namecategory'), 'Left')
			
			// JOIN TABLA DE REGISTER QR
			->join(array('r_q' => 'register_qr'), 'a.id_register_qr = r_q.id', array('id_register_qr' => 'id', 'code_article'=>'foliocodeqr'), 'Inner')
			
			// JOIN TABLA DE STATUS
			->join(array('s' => 'status'), 'ret.id_status = s.id', array('name_status' => 'name'), 'Inner')

			// JOIN A LA TABLA DE HISTORY STATUS
			//->join(array('h_s' => 'history_status'), 'a.id = h_s.id_articles', array('date_change' => new \Zend\Db\Sql\Expression('MAX(date_change)')), 'Inner')

			//->join(array('h_s' => 'history_status'), 'a.id = h_s.id_articles', array('date_change'), 'Inner')

			// JOIN A LA TABLA RETURNS
			//->join(array('ret' => 'returns'), 'h_s.id = ret.id_history_status', array('id_return' => 'id', 'warehouse', 'phone_warehouse', 'date_found', 'return_date'), 'Inner')

			// CONDICION
			->where(array('ret.id_userfound' => $idUser))

			// ORDER BY
			->order('a.id ASC')
			//->group ( array ("h_s.id_articles") )
			->where->notIn("ret.id_status", $arrayStatusArticles);


		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();
		//echo "<pre>"; print_r($result); exit;

		return $result;
	}

	/**
	 *  OBTEMOS TODOS LOS ARTICULOS ENCONTRADOS CON UN STATUS DE 7
	 */
	public function getAllArticlesFoundStatusSeven($idUser)
	{
		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			->columns(array('id', 'id_category', 'name_article', 'description', 'brand', 'imageone'))
			->from(array('a' => $this->table))
			->join(array('c' => 'color'), 'a.id_color = c.id', array('name_color' => 'name'), 'Left')
			->join(array('cat' => 'category'), 'a.id_category = cat.id', array('name_category' => 'namecategory'), 'Left')
			->join(array('cat2' => 'category'), 'a.name_article = cat2.id', array('name_article_two' => 'namecategory'), 'Left')
			->join(array('r_q' => 'register_qr'), 'a.id_register_qr = r_q.id', array('id_register_qr' => 'id', 'code_article'=>'foliocodeqr', 'id_status'), 'Left')
			->join(array('s' => 'status'), 'r_q.id_status = s.id', array('name_status' => 'name'), 'Left')
			->where(array('a.id_user' => $idUser))
			->where('r_q.id_status = 7')
			->order('a.id ASC');
			//->where->notIn("r_q.id_status", array(1, 5, 7));

		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();


		return $result;
	}

	/**
	 * OBTENER ARTICULO POR ID
	 */
	public function getPetById($id)
	{
    	$sql = new Sql($this->dbAdapter);
    	$select = $sql->select();
    	$select
      		//->columns(array('id', 'type_pet', 'name_pet', 'breed_pet', 'color_pet', 'size_pet', 'age_pet', 'description_pet', 'birthday_pet', 'name_vet', 'phone_vet', 'microchip_pet', 'img_pet', 'reward'))
      		->from(array('pet'   => $this->table))
      		//->join(array('c' => 'color'), 'a.id_color = c.id', array('name_color' => 'name'), 'Left')
			//->join(array('cat' => 'category'), 'a.id_category = cat.id', array('name_category' => 'namecategory'), 'Left')
			//->join(array('cat2' => 'category'), 'a.name_article = cat2.id', array('name_article_two' => 'namecategory'), 'Left')
      		->join(array('r_q' => 'register_qr'), 'pet.id_register_qr = r_q.id', array('code_article'=>'foliocodeqr', 'id_status'), 'Left')

      		->where(array('pet.id' => $id));
  
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    	$result       = $execute->toArray();

    	return $result;
	}

	/**
	 * OBTENER UNA MASCOTA POR CODIGO QR
	 */
	public function getPetByCodeQr($codeQR)
	{
    	$sql = new Sql($this->dbAdapter);
    	$select = $sql->select();
    	$select
      		->columns(array('id', 'id_orders', 'id_status', 'foliocodeqr', 'info_qr'))
      		->from(array('r_q'   => 'register_qr'))

      		->join(array('pet' => 'pets'), 'r_q.id = pet.id_register_qr', array('name_pet', 'description_pet', 'reward', 'image_name', 'registration_date'), 'Inner')

      		->join(array('u' => 'users'), 'pet.id_users = u.id', array('email'), 'Inner')

      		->join(array('u_d' => 'users_details'), 'u.id = u_d.id_user', array('name', 'surname', 'phone'), 'Inner')

      		->where('r_q.id_status != 1')
      		->where(array('r_q.foliocodeqr' => $codeQR));
  
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    	$result       = $execute->toArray();

    	return $result;
	}

	/**
	 * OBTENER NOMBRE DE UNA IMAGEN
	 */
	public function getNameImagePet($idQr)
	{
		$sql = new Sql($this->dbAdapter);
    	$select = $sql->select();
    	$select
      		->columns(array('id', 'image_name'))
      		->from(array('pet'   => 'pets'))
      		->where(array('pet.id_register_qr' => $idQr));
  
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    	$result       = $execute->toArray();

    	return $result;
	}

	/**
	 * OBTENER LISTA DE NOTIFICACIONES
	 */
	public function getAllPushNotifications($idUser)
	{
		$sql 	= new Sql($this->dbAdapter);
    	$select = $sql->select();
    	$select
      		//->columns(array('id', 'id_orders', 'id_status', 'foliocodeqr', 'info_qr'))
      		->from(array('p_n'   => 'push_notifications'))

      		// MASCOTA
      		->join(array('pet' => 'pets'), 'p_n.id_pet = pet.id', array('name_pet'), 'Left')

      		// ARTICULO
      		->join(array('article' => 'articles'), 'p_n.id_article = article.id', array('name_article'), 'Left')
      		
			// CATEGORIA
			->join(array('categ2' => 'category'), 'article.name_article = categ2.id', array('name_article_two' => 'namecategory'), 'Left')

			// UBICACION MASCOTA
      		->join(array('l_p' => 'location_pets'), 'p_n.id_location_pet = l_p.id', array('latitude_pet' => 'latitude', 'longitude_pet' =>  'longitude'), 'Left')

      		// UBICACION ARTICULO
      		->join(array('l_a' => 'location'), 'p_n.id_location_art = l_a.id', array('latitude_article' => 'latitude', 'longitude_article' =>  'longitude'), 'Left')

      		->order('p_n.id DESC')
			//->group ( array ('a.id') )
			//->limit(1)
      		->where(array('p_n.id_user' => $idUser));
  
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    	$result       = $execute->toArray();

    	return $result;

	}

	/**
	 * GUARDAR NOTIFICACION
	 */
	public function saveNotificacion($data)
	{
		$connection = null;
	    
		try {
			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();
	    
			//$notification		= $this->insert($data);
			//$saveNotification 	= $this->getLastInsertValue();
			$sql    = new Sql($this->dbAdapter);
    		$insert = $sql->insert('push_notifications');
    	
    		$insert->values($data);
    		$selectString 		= $sql->getSqlStringForSqlObject($insert);
    		$results 			= $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$saveNotification 	= $results->getGeneratedValue();

			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
	    		$connection->rollback();
			}
	      	// Tratamiento de errores
			$saveNotification = $e->getCode();
		}
	    //echo "<pre>"; print_r($saveArticles); exit;
	    return $saveNotification;
	}

	/**
	 * ACTUALIZAR ESTATUS DE NOTIFICACION
	 */
	public function updateStatusNotification($data)
	{

		$sql    = new Sql( $this->adapter );
		$update = $sql->update();
		$update->table( 'push_notifications' );
		$update->set( $data );
		$update->where( array( 'id' => $data['id'] ) );

		$statement  = $sql->prepareStatementForSqlObject( $update );
		//$results    = $statement->execute()->getAffectedRows();

		try {
		    $affectedRows = $statement->execute()->getAffectedRows();
		    //throw new \Exception("Un mensaje de error", 30);
		} catch (\Exception $e) {
		    //die('Error: ' . $e->getMessage());
		    $affectedRows = $e->getCode();
		}

		if (empty($affectedRows)) {
		    //die('Zero rows affected');
		    $affectedRows = 0;
		}

		return $affectedRows;

	}

	/**
	 * AGREGAR UNA MASCOTA
	 */
	public function addPet($data)
	{
		$connection = null;
	    
		try {
			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();
	    
			$articles = $this->insert($data);
			$saveArticles = $this->getLastInsertValue();
			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
	    		$connection->rollback();
			}
	      	// Tratamiento de errores
			$saveArticles = $e->getCode();
		}
	    //echo "<pre>"; print_r($saveArticles); exit;
	    return $saveArticles;
	}

	/**
	 * MODIFICAR UNA MASCOTA
	 */
	public function editPet($data)
	{
    	$connection = null;
  
    	try {
      		$connection = $this->dbAdapter->getDriver()->getConnection();
      		$connection->beginTransaction();
  
      		$articles  = $this->update($data, array("id" => $data['id']));
      		$editPet = $data['id'];
      		$connection->commit();
    	}
    	catch (\Exception $e) {
      		if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
        		$connection->rollback();
      		}
      		/* Tratamiento de errores */
      		$editPet = $e->getCode();
    	}

    	return $editPet;
	}

	/**
	 * Metodo para validar si un codigo de qr existe
	 */
	public function verifyCodeQrUniqueExists($code)
	{
		$codeQrUnique   = $code['foliocodeqr'];
		
		$consult = $this->dbAdapter->query("SELECT count(foliocodeqr) as count FROM register_qr WHERE foliocodeqr='$codeQrUnique'",Adapter::QUERY_MODE_EXECUTE);
		
		$result  = $consult->toArray();

		return $result;
	}

	/**
	 * ELIMINAR ARTICULO
	 */
	public function deleteArticle($id)
	{
		//echo "hola"; print_r($id); exit;
		$delete = $this->delete(array('id' => (int) $id));
    	//$delete = $this->update(array('company_isactive' => 0), array('id_company' => $id));
    	return $delete;
	}

	/**
	 * OBTENER LA UBICACION DE UNA MASCOTA
	 */
	public function getLastLocationPet($id)
	{
    	$sql = new Sql($this->dbAdapter);
    	$select = $sql->select();
    	$select
      		->columns(array('id', 'name_pet', 'description_pet'))
      		->from(array('pet'   => $this->table))

      		->join(array('r_q' => 'register_qr'), 'pet.id_register_qr = r_q.id', array('code_article'=>'foliocodeqr', 'status' => 'id_status'), 'Inner')
      		->join(array('lo' => 'location_pets'), 'pet.id = lo.id_pets', array('id_location' => 'id', 'longitude', 'latitude', 'addres'), 'Inner')

      		->where(array('pet.id' => $id))

      		->order('lo.id DESC')
			//->group ( array ('a.id') )
			->limit(1);
  
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	//echo "<pre>"; print_r($selectString); exit;
    	$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    	$result       = $execute->toArray();

    	return $result;
	}

	/**
	 * METODO PARA VALIDAR LA CLAVE DEL INVENTARIO
	 */
	public function verifyKeyInventoryArticles($key, $idUser)
	{
		
		$consult = $this->dbAdapter->query("SELECT count(key_inventory) as count FROM users_details WHERE key_inventory='$key' AND id_user = '$idUser'",Adapter::QUERY_MODE_EXECUTE);
		//print_r($consult); exit;
		$result  = $consult->toArray();

		return $result;
	}

	/**
	 * GUARDAR UBICACION DE UNA MASCOTA
	 */
	public function saveLastLocation($data)
	{

		$sql    = new Sql($this->dbAdapter);
    	$insert = $sql->insert('location_pets');
    	
    	$insert->values($data);
    	$selectString = $sql->getSqlStringForSqlObject($insert);
    	$results = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);

		return $results->getGeneratedValue();

	}

}