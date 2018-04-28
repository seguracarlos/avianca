<?php
namespace Restful\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Controller\BaseController;
use Articles\Service\CodeQRService;
use Articles\Service\OrderService;


class CodeQrRestController extends BaseController
{

	private $codeQRService;
	private $orderService;

	/**
     * SERVICIO DE CODIGOS QR
     */
    private function getCodeQRService()
    {
        return $this->codeQRService = new CodeQRService();
    }

    /**
     * SERVICIO DE PEDIDOS DE CODIGOS QR
     */
    private function getOrderService()
    {
        return $this->orderService = new OrderService();
    }

    public function homecodeqrAction()
    {
        echo "Web service to generate qr codes";
        exit;
    }

	public function generateAction()
    {
    	$request = $this->getRequest();

    	// Validar tipo de peticion
    	if ($request->isPost()) {

            // BASE PATH
            $getBasePathFull = $this->getRequest()->getBasePath();

    		// Datos recibidos en la paticion
    		//$postData   = $request->getPost();
    		$postData = $this->getRequest()->getContent();

    		// Parseamos en array el objeto recibido
    		$decodePostData = json_decode($postData, true);

            // AGREGAMOS UN CAMPO AL ARREGLO
            $decodePostData['getBasePathFull'] = $getBasePathFull;

    		//print_r($decodePostData); exit;

    		// GUARDAMOS EL PEDIDO
    		$saveOrder = $this->getOrderService()->addOrderCodeQr($decodePostData);
            //echo "<pre>"; print_r($saveOrder); exit;
    		// VALIDAMOS SI SE GUARDO CORRECTAMENTE EL PEDIDO
    		if($saveOrder) {

    			// Agregamos un campo con el id del pedido
    			$decodePostData['id_order'] = (int) $saveOrder;

    			// GENERAMOS LOS CODIGOS QR
    			$generateCodeQr = $this->getCodeQRService()->generateCodeQrByOrder($decodePostData);
                //print_r($generateCodeQr); exit;

                // RESPUESTA DEL WS
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "codes_qr" => $generateCodeQr
                )));

                return $response;

    		}

    	};
    	//echo "<pre>"; 
    	//print_r($request); exit;
    	exit();
    }

    /**
     * VALIDAR  TIPO DE CODIGO QR
     */
    public function validatetypecodeqrAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

         // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            
            // CODIGO DEL QR
            $code_article   = $decodePostData['code_qr'];
            
            // Validamos el codigo qr
            $validateCodeQR = $this->getCodeQRService()->validateTypeCodeQr($code_article);

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "data" => $validateCodeQR
            )));

            return $response;

        }

        exit;
    }
	
}