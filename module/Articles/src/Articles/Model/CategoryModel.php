<?php

namespace Articles\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class CategoryModel extends TableGateway
{
	private $dbAdapter;

	public function __construct()
	{
		$this->dbAdapter  = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
    	$this->table      = 'category';
       	$this->featureSet = new Feature\FeatureSet();
     	$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
    	$this->initialize();
	}

	/**
	 * OBTEMOS TODAS LAS CATEGORIAS PADRE
	 */
	public function getAllCategoryParent()
	{
		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			->columns(array('id', 'id_parent', 'namecategory'))
			->from(array('c' => $this->table))
			//->join(array('r_q' => 'register_qr'), 'a.id_register_qr = r_q.id', array('id_register_qr' => 'id', 'code_article'=>'foliocodeqr', 'id_status'), 'Left')
			//->join(array('s' => 'status'), 'r_q.id_status = s.id', array('name_status' => 'name'), 'Left')
			->where(array('c.id_parent' => NULL))
			//->where('r_q.id_status != 5')
			//->where->notequalTo('r_q.id_status', 5)
			//->where('r_q.id_status' != 5)
			//->where->notEqualTo('dr_q.id_status',5)
			//->where('r_q.id_status != ?', 5)
			->order('c.namecategory ASC');

		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();


		return $result;

	}

	/**
	 * OBTEMOS TODAS LAS CATEGORIAS POR ID DE PADRE
	 */
	public function getAllSubCategory($id_parent)
	{
		$sql = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select
			->columns(array('id', 'id_parent', 'namecategory'))
			->from(array('c' => $this->table))
			->where(array('c.id_parent' => $id_parent))
			->order('c.namecategory ASC');

		$selectString = $sql->getSqlStringForSqlObject($select);
		//print_r($selectString); exit;
		$execute      = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$result       = $execute->toArray();


		return $result;
	}

}