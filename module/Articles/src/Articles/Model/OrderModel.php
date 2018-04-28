<?php

namespace Articles\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class OrderModel extends TableGateway
{
	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
    	$this->table      = 'orders';
       	$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

	/**
	 * AGREGAR PEDIDO DE CODIGOS QR
	 */
	public function addOrderCodeQr($data)
	{
		$connection = null;
	    
		try {
			$connection = $this->dbAdapter->getDriver()->getConnection();
			$connection->beginTransaction();
	    
			$order = $this->insert($data);
			$saveOrder = $this->getLastInsertValue();
			$connection->commit();
		}
		catch (\Exception $e) {
			if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
	    		$connection->rollback();
			}
	      	// Tratamiento de errores
			$saveOrder = $e->getCode();
		}
		//echo "<pre>"; print_r($saveOrder); exit;
	    return $saveOrder;
	}

}