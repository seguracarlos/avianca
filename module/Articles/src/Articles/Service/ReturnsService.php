<?php

namespace Articles\Service;

use Articles\Model\ReturnsModel;

class ReturnsService
{
	private $returnsModel;

	/**
	  * MODEL DE DEVOLUCIONES
	  */ 
	private function getReturnsModel()
	{
		return $this->returnsModel = new ReturnsModel();
	}

	/**
	 * AGREGAR UN REGISTRO EN LA TABLA DE DEVOLUCIONES
	 */
	public function addReturn($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// DATOS QUE SERAN GUARDADOS EN LA TABLA RETURNS
		$dataReturns = array(
			//'id_history_status' => $data['id_history_status'],
			'id_articles'       => $data['id_articles_hs'],
			'warehouse'         => $data['warehouse'],
			'phone_warehouse'   => $data['phone_warehouse'],
			'comment'           => $data['comment'],
			'date_found'        => date('Y-m-d H:i:s'),
			'id_userfound'      => $data['id_userfound'],
			'id_status'         => $data['id_status_hs']
		);
		//echo "<pre>"; print_r($dataReturns); exit;

		// GUARDAMOS LOS DATOS EN RETURNS
		$addReturns = $this->getReturnsModel()->addReturn($dataReturns);
		//echo "<pre>"; print_r($addReturns); exit;
		return $addReturns;
	}

	/**
	 * MODIFICAR UN REGISTRO EN LA TABLA DE DEVOLUCIONES
	 */
	public function editReturn($data)
	{
		//echo "<pre>"; print_r($data); exit;
		$dataReturns = array(
			'id'          => $data['id_return'], 
			'name'        => $data['first_name_return'],
			'surname'     => $data['last_name_return'],
			'phone'       => $data['phone_return'],
			'email'       => $data['email_return'],
			'return_date' => date('Y-m-d H:i:s'),

			'id_status'   => (int) 6
		);
		//echo "<pre>"; print_r($dataReturns); exit;

		$editReturns = $this->getReturnsModel()->editReturn($dataReturns);
		//echo "<pre>"; print_r($editReturns); exit;

		return $editReturns;
	}

	/**
	 * DETALLE DE DEVOLUCION
	 */
	public function detailReturnArticle($idReturn)
	{
		$detailReturnArticle = $this->getReturnsModel()->detailReturnArticle($idReturn);

		return $detailReturnArticle;
	}

	/**
	 * ELIMINAR ARTICULO ENCONTRADO
	 */
	public function deleteArticleFound($id)
	{
		//echo "<pre>"; print_r($id); exit;
		$idReturnArticle    = (int) $id;
		
		$deleteArticleFound = $this->getReturnsModel()->deleteArticleFound($idReturnArticle);

		return $deleteArticleFound;
	}

}