<?php

namespace Articles\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class ReturnsModel extends TableGateway
{
	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
    	$this->table      = 'returns';
       	$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

  /**
   * AGREGAR UN REGISTRO EN LA TABLA DE DEVOLUCIONES
   */
  public function addReturn($data)
  {
    $connection = null;
        
    try {
      $connection = $this->dbAdapter->getDriver()->getConnection();
      $connection->beginTransaction();
        
      $returns     = $this->insert($data);
      $saveReturns = $this->getLastInsertValue();
      $connection->commit();
    }
    catch (\Exception $e) {
      if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
          $connection->rollback();
      }
      
      // Tratamiento de errores
      $saveReturns = $e->getCode();
    }
        
    return $saveReturns;
  }

  /**
   * MODIFICAR UN REGISTRO EN LA TABLA DE DEVOLUCIONES
   */
  public function editReturn($data)
  {
      $connection = null;
  
      try {
          $connection = $this->dbAdapter->getDriver()->getConnection();
          $connection->beginTransaction();
  
          /* Ejecutar una o m�s consultas aqu� */
          $returns     = $this->update($data, array("id" => $data['id']));
          $editReturns = $data['id'];
          $connection->commit();
      }
      catch (\Exception $e) {
          if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
            $connection->rollback();
          }
          /* Tratamiento de errores */
          $editReturns = $e->getCode();
      }
      //echo "<pre>"; print_r($editCompany); exit;
      return $editReturns;
  }

  /**
   * DETALLE DE DEVOLUCION
   */
  public function detailReturnArticle($idReturn)
  {
    $sql = new Sql($this->dbAdapter);
    $select = $sql->select();
    $select
      //->columns(array('id', 'name_article'))
      ->from(array('r' => $this->table))
      ->where(array('r.id' => $idReturn));

    $selectString = $sql->getSqlStringForSqlObject($select);
    //print_r($selectString); exit;
    $execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $result       = $execute->toArray();


    return $result;
  }

  /**
   * ELIMINAR ARTICULO ENCONTRADO
   */
  public function deleteArticleFound($id)
  {

    $delete = $this->delete(array('id' => (int) $id));
    
    return $delete;
  }

}