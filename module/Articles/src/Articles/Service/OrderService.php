<?php
namespace Articles\Service;

use Articles\Model\OrderModel;

class OrderService
{
	private $orderModel;

	private function getOrderModel()
	{
		return $this->orderModel = new OrderModel();
	}

	/**
	 * AGREGAR PEDIDO DE CODIGOS QR
	 */
	public function addOrderCodeQr($data)
	{
		//echo "<pre>"; print_r($data); exit;
		// Array con los datos del pedido
		$dataOrder = array(
			'id_client'       => $data['id_client'],
			'folio_order'     => $data['folio_order'],
			'number_labels'   => $data['number_labels'],
			'date'            => date('Y-m-d H:i:s')

		);
		//echo "<pre>"; print_r($dataOrder); exit;
		$addOrder = $this->getOrderModel()->addOrderCodeQr($dataOrder);

		return $addOrder;

	}

}