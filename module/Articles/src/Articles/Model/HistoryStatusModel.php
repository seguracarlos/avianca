<?php

namespace Articles\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class HistoryStatusModel extends TableGateway
{
	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
    	$this->table      = 'history_status';
       	$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

  /**
   * AGREGAR UN HISTORY STATUS
   */
  public function addHistoryStatus($data)
  {
    $connection = null;
        
    try {
      $connection = $this->dbAdapter->getDriver()->getConnection();
      $connection->beginTransaction();
        
      $historyStatus     = $this->insert($data);
      $savehistoryStatus = $this->getLastInsertValue();
      $connection->commit();
    }
    catch (\Exception $e) {
      if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
          $connection->rollback();
      }
      
      // Tratamiento de errores
      $savehistoryStatus = $e->getCode();
    }
        
    return $savehistoryStatus;
  }

	/**
	 * INGRESAR LOS DATOS DE A QUIEN LE PRESTAS UN ARTICULO
	 */
  public function insertLendArticle($data)
  {
    $connection = null;
      
    try {
      $connection = $this->dbAdapter->getDriver()->getConnection();
      $connection->beginTransaction();
      
      $article         = $this->insert($data);
      $saveLendArticle = $this->getLastInsertValue();
      $connection->commit();
    }
    catch (\Exception $e) {
      if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
          $connection->rollback();
      }
          // Tratamiento de errores
      $saveLendArticle = $e->getCode();
    }
      
      return $saveLendArticle;
  }

}