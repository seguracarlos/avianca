<?php

namespace Articles\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class ColorModel extends TableGateway
{
	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
    	$this->table      = 'color';
       	$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

	/**
	 * OBTEMOS TODOS los colores
	 */
	public function getAllColor()
	{
		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			->columns(array('id', 'name'))
			->from(array('c' => $this->table))
			//->join(array('r_q' => 'register_qr'), 'a.id_register_qr = r_q.id', array('id_register_qr' => 'id', 'code_article'=>'foliocodeqr', 'id_status'), 'Left')
			//->join(array('s' => 'status'), 'r_q.id_status = s.id', array('name_status' => 'name'), 'Left')
			//->where(array('c.id_parent' => NULL))
			//->where('r_q.id_status != 5')
			//->where->notequalTo('r_q.id_status', 5)
			//->where('r_q.id_status' != 5)
			//->where->notEqualTo('dr_q.id_status',5)
			//->where('r_q.id_status != ?', 5)
			->order('c.name ASC');

		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();


		return $result;

	}

}