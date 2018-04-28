<?php

namespace Restful\Controller;
     
use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Users\Service\UsersService;

class UsersRestController extends BaseController
{

	private $usersService;

    /**
     * Instanciamos el servicio de usuarios
     */
    public function getUsersService()
    {
        return $this->usersService = new UsersService();
    }

    /**
     * METODO DE BIENVENIDA
     */
    public function indexAction()
    {
    	echo "WS DE USUARIOS RECUPERALO.";
    	exit;
    }

    /**
     * AGREGAR UN DISPOSITICO A UN USUARIO PARA LAS NOTIFICACIONES
     */
    public function addDeviceAction()
    {
        $request = $this->getRequest();
        // TIPO DE PETICION
        if ($request->isPost()) {
            // DATOS RECIBIDOS POR POST
    		$postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
    		$decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;


            // REGISTRAMOS EL TOKEN DEL DISPOSITIVO
            $tokenRegister = $this->getUsersService()->insertTokenPush($decodePostData['key_device'],$decodePostData['id_user'],$decodePostData['os_Type']);

            if ($tokenRegister) {
                    
                    // RESPUESTA DEL WS OK
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'ok',
                        "id_device"   => $decodePostData
                    )));

                } else {

                    // RESPUESTA DEL WS FAIL
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'error'
                    )));
                    
                }

            return $response;
        }
        exit;
    }

    /**
     * AGREGAR UN USUARIO
     */
    public function addAction()
    {
    	$request = $this->getRequest();

    	// TIPO DE PETICION
    	if ($request->isPost()) {

    		// DATOS RECIBIDOS POR POST
    		$postData = $this->getRequest()->getContent();
    		//echo "<pre>"; print_r($postData); exit;

    		// PARSEAMOS JSON A ARRAY PHP
    		$decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            // VALIDAMOS EL CORREO ELECTRONICO
            $verifyEmail = $this->getUsersService()->verifyEmailExists($decodePostData['email_user']);
            //echo "<pre>"; print_r($verifyEmail); exit;

            // Validamos si existe o no el correo
            if ($verifyEmail[0]['count'] == 1) {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            } else if ($verifyEmail[0]['count'] == 0) {

                // AGREGAMOS UN USUARIO
                $addUsers = $this->getUsersService()->addUsers($decodePostData);
                //echo "<pre>"; print_r($addUsers); exit;

                // VALIDAMOS SI SE AGREGO EL REGISTRO
                if ($addUsers) {
                    
                    // RESPUESTA DEL WS OK
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'ok',
                        "name"   => $decodePostData['name_user']
                    )));

                } else {

                    // RESPUESTA DEL WS FAIL
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'error'
                    )));
                    
                }


            };

    		return $response;
    		
    	}

    	exit;

    }

    /**
     * OBTENER PERFIL DE USUARIO
     */
    public function getperfilbyidAction()
    {
        
        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData['id_user']); exit;

            // ID DE USUARIO
            $idUser = (int )$decodePostData['id_user'];
            //echo "<pre>"; print_r($idUser); exit;

            // EDITAMOS PERFIL
            $getperfilbyid = $this->getUsersService()->getAll($idUser);
            //echo "<pre>"; print_r($getperfilbyid); exit;

            $dataUser = array();

            // RESULT
            if(isset($getperfilbyid[0])) {
                $dataUser = $getperfilbyid[0];
            }

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "detail_user" => $dataUser
            )));


            return $response;
            
        }

        exit;

    }

    /**
     * OBTENER UN USUARIO POR ID
     */
    public function edituserAction()
    {

        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            // EDITAMOS PERFIL
            $editPerfil = $this->getUsersService()->editProfileAppMovil($decodePostData);
            //echo "<pre>"; print_r($editPerfil); exit;

            // VALIDAMOS SI SE MODIFICO EL USUARIO
            if ($editPerfil) {
                    
                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

            } else {

                // RESPUESTA DEL WS FAIL
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
                    
            }


            return $response;
            
        }

        exit;
        
    }

    /**
     * CAMBIAR PIN DE USUARIO
     */
    public function changepinAction()
    {

        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            // MODIFICAMOS PIN
            $changePin = $this->getUsersService()->updateKeyPin($decodePostData['pin'], $decodePostData['id']);
            //echo "<pre>"; print_r($changePin); exit;

            // VALIDAMOS SI SE MODIFICO EL USUARIO
            if ($changePin) {
                    
                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

            } else {

                // RESPUESTA DEL WS FAIL
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
                    
            }


            return $response;
            
        }

        exit;
        
    }

    /**
     * CAMBIAR CONTRASEÑA DE CUENTA
     */
    public function changepasswordAction()
    {

        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            // MODIFICAMOS CONTRASENA
            $changePassword = $this->getUsersService()->updateKeyPass($decodePostData['password'], $decodePostData['id_user']);
            //echo "<pre>"; print_r($changePassword); exit;

            // VALIDAMOS SI SE MODIFICO
            if ($changePassword) {
                    
                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

            } else {

                // RESPUESTA DEL WS FAIL
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
                    
            }


            return $response;
            
        }

        exit;
        
    }

    /**
     * VALIDAR CONTRASENA
     */
    public function validatepasswordAction()
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
            //echo "<pre>"; print_r($decodePostData); exit;
            
            // ID DE USUARIO DE SESION
            $idUser         = (int) $decodePostData['id_user'];
            
            // Validar clave de inventario
            $verifyPasswordAccount = $this->getUsersService()->verifyKeyPass($decodePostData['password'], $idUser);
            //echo "<pre>"; print_r($verifyPasswordAccount); exit;

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "data" => $verifyPasswordAccount
            )));

            return $response;

        }

        exit;
    }

    /**
     * CAMBIAR CORREO ELECTRONIO
     */
    public function changeemailAction()
    {
        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            $verifyEmail = $this->getUsersService()->verifyEmailExists($decodePostData['email']);
            //echo "<pre>"; print_r($verifyEmail); exit;
            // Validamos si existe o no el correo
            if ($verifyEmail[0]['count'] == 1) {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            } else if ($verifyEmail[0]['count'] == 0) {

                // ID DE USUARIO DE SESION
                $idUser         = (int) $decodePostData['id_user'];

                $editEmail = $this->getUsersService()->updateKeyEmail($decodePostData['email'], $idUser);

                //echo "<pre>"; print_r($addUser); exit;

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));
            };

            return $response;
            
        }

        exit;
    }

    /**
     * VALIDAR CONTRASEÑA DE SEGURIDAD
     */
    public function validatesecurepasswordAction()
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
            //echo "<pre>"; print_r($decodePostData); exit;
            
            // ID DE USUARIO DE SESION
            $idUser         = (int) $decodePostData['id_user'];
            
            // Validar clave de inventario
            $verifyPasswordSecure = $this->getUsersService()->verifyKeyPassinventory($decodePostData['password'], $idUser);
            //echo "<pre>"; print_r($verifyPasswordSecure); exit;

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "data" => $verifyPasswordSecure
            )));

            return $response;

        }

        exit;
    }

    /**
     * CAMBIAR CONTRASEÑA DE SEGURIDAD
     */
    public function changesecurepasswordAction()
    {
       $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            // MODIFICAMOS CONTRASENA
            $changePassword = $this->getUsersService()->updateKeyPassinventory($decodePostData['password'], $decodePostData['id_user']);
            //echo "<pre>"; print_r($changePassword); exit;

            // VALIDAMOS SI SE MODIFICO
            if ($changePassword) {
                    
                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

            } else {

                // RESPUESTA DEL WS FAIL
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
                    
            }


            return $response;
            
        }

        exit; 
    }

}