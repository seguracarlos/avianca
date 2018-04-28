<?php

namespace Articles\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class CodeQRModel extends TableGateway
{
	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
    	$this->table      = 'register_qr';
       	$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

  /**
  * METODO PARA VALIDAR EL TIPO DE CODIGO QR
  */
  public function validateTypeCodeQr($code)
  {
    
    $codeQrUnique   = $code['foliocodeqr'];
    
    $consult = $this->dbAdapter->query("SELECT count(foliocodeqr) as count, id, id_status, type_qr FROM register_qr WHERE foliocodeqr = '$codeQrUnique'",Adapter::QUERY_MODE_EXECUTE);
    
    $result  = $consult->toArray();

    return $result;

  }

	/**
	 * Metodo para validar si un codigo de qr existe
	 */
	public function verifyCodeQrUniqueExists($code)
	{
		$codeQrUnique   = $code['foliocodeqr'];
		
		$consult = $this->dbAdapter->query("SELECT count(r_q.foliocodeqr) as count, type_qr, r_q.id, r_q.id_status, IFNULL(art.id,-1) AS id_article, art.name_article, art.reward, art.asignated_to, u.id AS id_user, u.email AS email_owner, u_d.phone as phone_users FROM register_qr AS r_q LEFT jOIN articles AS art ON r_q.id = art.id_register_qr LEFT JOIN users AS u ON art.id_user = u.id LEFT JOIN users_details as u_d on art.id_user = u_d.id_user WHERE r_q.foliocodeqr='$codeQrUnique' ",Adapter::QUERY_MODE_EXECUTE);
    //echo "<pre>"; print_r($consult); exit;
    /*
  $consult = $this->dbAdapter->query("SELECT count(r_q.foliocodeqr) as r_q.count, r_q.id, r_q.id_status FROM register_qr AS r_q LEFT JOIN articles AS art ON r_q.id = art.id_register_qr  WHERE foliocodeqr='$codeQrUnique'",Adapter::QUERY_MODE_EXECUTE);
    */
		
		$result  = $consult->toArray();

		return $result;
	}

		/**
	 	* Metodo para validar si un codigo de qr de mascota existe
	 	*/
		public function verifyCodeQrUniqueExistsPet($code)
		{
			$codeQrUnique   = $code['foliocodeqr'];
		
			$consult = $this->dbAdapter->query("SELECT count(r_q.foliocodeqr) as count, type_qr, r_q.id, r_q.id_status, IFNULL(pet.id,-1) AS id_pet, pet.name_pet, pet.reward, u.id AS id_user, u.email AS email_owner, u_d.phone as phone_users FROM register_qr AS r_q LEFT jOIN pets AS pet ON r_q.id = pet.id_register_qr LEFT JOIN users AS u ON pet.id_users = u.id LEFT JOIN users_details as u_d on pet.id_users = u_d.id_user WHERE r_q.foliocodeqr='$codeQrUnique' ",Adapter::QUERY_MODE_EXECUTE);
		
			$result  = $consult->toArray();

			return $result;
		}

	public function getStatusCodeQR($id)
	{
    	$sql = new Sql($this->dbAdapter);
    	$select = $sql->select();
    	$select
      		->columns(array('id', 'id_status'))
      		->from(array('c'   => $this->table))
      		->where(array('c.id' => $id));
  
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    	$result       = $execute->toArray();

    	return $result;
	}

	/**
	 * MODIFICAR STATUS DE CODIGO QR
	 */
	public function updateStatusCodeQR($data)
	{
    	$connection = null;
  
    	try {
      		$connection = $this->dbAdapter->getDriver()->getConnection();
      		$connection->beginTransaction();
  
      		/* Ejecutar una o m�s consultas aqu� */
      		$articles  = $this->update($data, array("id" => $data['id']));
      		$editStatusCodeQR = $data['id'];
      		$connection->commit();
    	}
    	catch (\Exception $e) {
      		if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
        		$connection->rollback();
      		}
      		/* Tratamiento de errores */
      		$editStatusCodeQR = $e->getCode();
    	}
    	//echo "<pre>"; print_r($editStatusCodeQR); exit;
    	return $editStatusCodeQR;
	}

  /**
   * GENERAR CODIGOS QR
   */
  public function generateCodeQrByOrder($data)
  {
    $connection = null;
      
    try {
      $connection = $this->dbAdapter->getDriver()->getConnection();
      $connection->beginTransaction();
      
      $codeQr = $this->insert($data);
      $saveCodeQr = $this->getLastInsertValue();
      $connection->commit();
    }
    catch (\Exception $e) {
      if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
          $connection->rollback();
      }
          // Tratamiento de errores
      $saveCodeQr = $e->getCode();
    }
      
      return $saveCodeQr;
  }

}