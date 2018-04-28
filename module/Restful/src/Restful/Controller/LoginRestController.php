<?php

namespace Restful\Controller;
     
use Zend\Mvc\Controller\AbstractRestfulController;
use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Auth\Service\LoginService;
     
class LoginRestController extends BaseController
{

    private $loginService;

    /**
     * Instanciamos el servicio de login
     */
    public function getLoginService()
    {
        return $this->loginService = new LoginService();
    }

    public function authAction()
    {
        $request = $this->getRequest();
        //echo "<pre>"; print_r($request->getHeaders()->get('Authorization')->getFieldValue()); exit;
        
        // Validamos el tipo de peticion
        if ($request->isPost()) {

            // Obtenemos los datos que vienen por post
            //$data = $request->getPost()->toArray();
            //echo "<pre>"; print_r($data); exit;
            $postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            //$values = \Zend\Json\Json::decode ($postData);

            //echo "<pre>"; print_r($postData); exit;
            // Parseamos en array el objeto recibido
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData['_bodyInit']); exit;
            // Lamamos al metodo de autenticacion
            $authentication = $this->getLoginService()->authenticationUser($decodePostData);
            //echo "<pre>"; print_r($authentication); exit;
            // Validamos autenticacion
            /*if ($authentication['code'] == 1) {
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/users/index');
            }*/

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "response" => $authentication,
            )));
            
            return $response;

        }

        exit;
    }

}