<?php

namespace Articles\Service;

use Articles\Model\HistoryStatusModel;
use Articles\Service\CodeQRService;

class HistoryStatusService
{
	private $historyStatusModel;
	private $codeQRService;

	private function getHistoryStatusModel()
	{
		return $this->historyStatusModel = new HistoryStatusModel();
	}

	private function getCodeQRService()
	{
		return $this->codeQRService = new CodeQRService();
	}

	/**
	 * AGREGAR UN REGISTRO EN LA TABLA DE HISTORY STATUS
	 */
	public function addHistoryStatus($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// DATOS QUE SERAN GUARDADOS EN LA TABLA DE HISTORY STATUS
		$dataHistoryStatus = array(
			'id_status'     => $data['id_status_hs'],
			'id_articles'   => $data['id_articles_hs'],
			//'id_userfound'  => $data['id_users'],
			'date_change'   => date('Y-m-d H:i:s'),
		);
		//echo "<pre>"; print_r($dataHistoryStatus); exit;

		// GUARDAMOS LOS DATOS EN HISTORY STATUS
		$addHistoryStatus = $this->getHistoryStatusModel()->addHistoryStatus($dataHistoryStatus);
		//echo "<pre>"; print_r($addHistoryStatus); exit;

		return $addHistoryStatus;
	}


	/**
	 * INGRESAR LOS DATOS DE A QUIEN LE PRESTAS UN ARTICULO
	 */
	public function insertLendArticle($data)
	{

		// Arreglo con los datos de a quien se le presta un articulo
		$lend_article   = json_decode($data['dataForm'], true);
		//echo "<pre>"; print_r($lend_article); exit;
		// Añadimos los datos de aquien se le presto el articulo
		$dataLendArticle = array(
			'id_status'     => $lend_article[0]['id_status'],
			'id_articles'   => $lend_article[0]['id_articles'],
			'date_change'   => date('Y-m-d H:i:s'),
			'name_external' => $lend_article[0]['first_name_lend'] . ' ' . $lend_article[0]['last_name_lend'],
			'comment'       => $lend_article[0]['descrip_lend']
		);

		//echo "<pre>"; print_r($dataLendArticle); exit;

		// Agregamos los datos de a quien fue prestado el articulo
		$insert_lend_article = $this->getHistoryStatusModel()->insertLendArticle($dataLendArticle);
		//echo "<pre>"; print_r($insert_lend_article); exit;

		/*$dataCodeQr = array(
			'id_register_qr' => $lend_article[0]['id_register_qr'],
			'id_status'      => $lend_article[0]['id_status']
		);*/
		//print_r($dataCodeQr); exit;
		if ($insert_lend_article) {
			$updateStatusCodeQr = $this->getCodeQRService()->updateStatusCodeQR($lend_article[0]['id_register_qr'], $lend_article[0]['id_status']);
		}
		//echo "<pre>"; print_r($updateStatusCodeQr); exit;

		return $insert_lend_article;
	}

}