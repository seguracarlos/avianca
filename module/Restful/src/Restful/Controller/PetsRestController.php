<?php

namespace Restful\Controller;
     
use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Pets\Service\PetsService;
use Articles\Service\CodeQRService;
use Articles\Service\CategoryService;
use Articles\Service\ColorService;
use Articles\Service\HistoryStatusService;
use Articles\Service\ReturnsService;
use Users\Service\UsersService;
     
class PetsRestController extends BaseController
{

    private $petsService;
    private $codeQRService;
    private $categoryService;
    private $colorService;
    private $historyStatusService;
    private $returnsService;
    private $usersServices;

    /**
     * Instanciamos el servicio de mascotas
     */
    public function getPetsService()
    {
        return $this->petsService = new PetsService();
    }

    /**
     * SERVICIO DE CODIGOS QR
     */
    private function getCodeQRService()
    {
        return $this->codeQRService = new CodeQRService();
    }

    /**
     * SERVICIO DE CATEGORIAS
     */
    private function getCategoryService()
    {
        return $this->categoryService = new CategoryService();
    }

    /**
     * SERVICIO DE COLORES
     */
    private function getColorService()
    {
        return $this->colorService = new ColorService();
    }

    /**
     * SERVICIO DE HISTORY STATUS
     */
    private function getHistoryStatusService()
    {
        return $this->historyStatusService = new HistoryStatusService();
    }

    /**
     * SERVICIO DE RETURNS
     */
    private function getReturnsService()
    {
        return $this->returnsService = new ReturnsService();
    }

    /**
     * SERVICIO DE USUARIOS
     */
    private function getUsersService()
    {
        return $this->usersServices = new UsersService();
    }

    /**
     * BIENVENIDA AL WS MASCOTAS
     */
    public function indexAction()
    {
        echo "WS Rest Mascotas.";
        exit;
    }

    /**
     * OBTENER MI LISTA DE MASCOTAS
     */
    public function getpetsAction()
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
            
            // ID DE USUARIO
            $idUser         = $decodePostData['id_user'];
            
            // TIPO DE PERFIL
            $perfilUser     = $decodePostData['type_user'];

            // LISTA DE MASCOTAS
    	    $pets = $this->getPetsService()->getAll($idUser, $perfilUser);

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "pets" => $pets
            )));

            return $response;
   
        }

        exit;
    }

    /**
     * AGREGAR UNA MASCOTA
     */
    public function addpetAction()
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

            // AGREGAR MASCOTA
            $addPet = $this->getPetsService()->addPetAppMovil($decodePostData);
            //echo "<pre>"; print_r($addPet); exit;

            // VALIDAMOS SI SE AGREGO EL REGISTRO
            if($addPet) {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));
            } else {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
            }

            return $response;

        }

        exit;

    }

    /**
     * EDITAR UNA MASCOTA
     */
    public function editpetAction()
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

            // EDITAR ARTICULO
            $editPet = $this->getPetsService()->editPetAppMovil($decodePostData);
            //echo "<pre>"; print_r($editPet); exit;

            // VALIDAMOS SI SE MODIFICO EL ARTICULO
            if($editPet) {

                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                    "pet" => $editPet
                )));

            }else {

                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            }

            return $response;

        }

        exit;

    }

    /**
     * DETALLE DE UNA MASCOTA
     */

    public function detailAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

         // TIPO DE PETICION
        if ($request->isPost()) {

            //sleep(3);

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            
            // ID MASCOTA
            $idPet      = (int) $decodePostData['id_pet'];
            
            // Obtenemos una mascota por id
            $pet = $this->getPetsService()->getPetById($idPet);

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "pet" => $pet
            )));

            return $response;

        }

        exit;
    }

    /**
     * ELIMINAR MASCOTA
     */
    public function deleteAction()
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

            // ID CODIGO QR
            $idCodeQr = (int) $decodePostData['id'];
           //echo "<pre>"; print_r($idCodeQr); exit;

           // ELIMINAR MASCOTA
            $deletePet = $this->getPetsService()->deletePet($idCodeQr);
            //echo "<pre>"; print_r($deletePet); exit;

            if($deletePet) {

                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));

            } else {

                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            }

            return $response;

        }

        exit;
    }

    /**
     * ULTIMA LOCALIZACION DE MASCOTA
     */
    public function lastlocationAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

         // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            
            // ID MASCOTA
            $idPet      = (int) $decodePostData['id_pet'];

            // ULTIMA UBICACION DE UNA MASCOTA
            $location_pet = $this->getPetsService()->getLastLocationPet($idPet);
            
            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                'latitude'  => (count($location_pet) != 0 && $location_pet['latitude'] != null) ? $location_pet['latitude']  : "",
                'longitude' => (count($location_pet) != 0 && $location_pet['longitude'] != null) ? $location_pet['longitude'] : "",
                'addres'    => (count($location_pet) != 0 && $location_pet['addres'] != null) ? $location_pet['addres']    : ""
            )));

            return $response;

        }

        exit;
    }

    /**
     * GUARDAR UBICACION DE UNA MASCOTA
     */
    public function savelastlocationAction()
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

            // GUARDAR UBICACION DE MASCOTA
            $saveLastLocation = $this->getPetsService()->saveLastLocation($decodePostData);
            //echo "<pre>"; print_r($saveLastLocation); exit;

            // VALIDAMOS SI SE AGREGO EL ARTICULO
            if($saveLastLocation) {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));
            } else {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
            }

            return $response;

        }

        exit;
    }

    /**
     * VALIDAR CODIGO QR MASCOTAS
     */
    public function validatecodeqrAction()
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
            
            // CODIGO DEL QR
            $code_pet   = $decodePostData['code_qr'];
            
            // Validamos el codigo qr
            $validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExistsPet($code_pet);

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "data" => $validateCodeQR
            )));

            return $response;

        }

        exit;
    }

    /**
     * ACTUALIZAR STATUS DE CODIGO QR
     */
    public function updatestatusAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            $id     = (int) $decodePostData['id'];

            $status = (int) $decodePostData['id_status']; 

            $updateStatus = $this->getCodeQRService()->updateStatusCodeQR($id, $status);
            //echo "<pre>"; print_r($updateStatus); exit;

            if ($updateStatus) {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));
            } else {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail',
                )));
            }
            
            return $response;

        }

        exit;
    }

    /**
     * ENVIAR CORREO A DUENO DE UNA MASCOTA ENCONTRADA
     */
    public function sendemailpetownerAction()
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

            $idCodeQR    = $decodePostData['id_code_qr'];
            
            $valueStatus = 7;

            // CAMBIAMOS EL ESTATUS DEL CODIGO QR
            $updateStatusCodeQR = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
            //echo "<pre>"; print_r($updateStatusCodeQR); exit;

            // VALIDAMOS SI SE ACTUALIZO ESTATUS DE CODIGO
            if($updateStatusCodeQR) {

                // ENVIO DE CORREO
                $sendEmail = $this->getPetsService()->sendEMailPetFound($decodePostData);
                //echo "<pre>"; print_r($sendEmail); exit;

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));

            } else {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            }

            return $response;

        }

        exit;
    }

    /**
     * ENVIAR CORREO DE CONTACTO
     */
    public function sendemailcontactAction()
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

            //$idCodeQR    = $decodePostData['id_code_qr'];
            
            //$valueStatus = 7;

            // CAMBIAMOS EL ESTATUS DEL CODIGO QR
            //$updateStatusCodeQR = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
            //echo "<pre>"; print_r($updateStatusCodeQR); exit;


            // ENVIO DE CORREO
            $sendEmailContact = $this->getPetsService()->sendEMailContact($decodePostData);
            //echo "<pre>"; print_r($sendEmailContact); exit;

            // VALIDAMOS SI SE ACTUALIZO ESTATUS DE CODIGO
            if($sendEmailContact) {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));

            } else {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            }

            return $response;

        }

        exit;
    }

    /**
     * GENERAR CARTEL DE SE BUSCA MASCOTA EXTRAVIADA
     */
    public function generateimglostpetsearchAction()
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

            // ID MASCOTA
            $idPet          = $decodePostData['code_pet'];
            
            // Obtenemos una mascota por id
            $petSearch = $this->getPetsService()->generateImgLostPetSearch($idPet);
            //echo "<pre>"; print_r($petSearch); exit;

            // VALIDAMOS SI SE ACTUALIZO ESTATUS DE CODIGO
            if($petSearch) {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status"    => 'ok',
                    "petSearch" => $petSearch
                )));

            } else {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            }

            return $response;

        }

        exit;

    }

    /**
     * OBTENER LISTA DE NOTIFICACIONES
     */
    public function getallpushnotificationAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData           = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData     = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;
            
            // ID DE USUARIO
            $idUser             = $decodePostData['id_user'];

            // LISTA DE NOTIFICACIONES
            $pushNotifications  = $this->getPetsService()->getAllPushNotifications($idUser);
            //echo "<pre>"; print_r($pushNotifications); exit;

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "notifications" => $pushNotifications
            )));

            return $response;
   
        }

        exit;
    }

    /**
     * ACTUALIZAR STATUS DE LA NOTIFICACION
     */
    public function updatestatusnotificationAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            $updateStatusNotification = $this->getPetsService()->updateStatusNotification($decodePostData);
            //echo "<pre>"; print_r($updateStatusNotification); exit;

            if ($updateStatusNotification) {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));
            } else {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail',
                )));
            }
            
            return $response;

        }

        exit;
    }

    /**
     * BASE64 A IMAGEN EN EL SERVIDOR
     */
    public function base64toimagepathAction()
    {
        $request = $this->getRequest();

        //if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            //$postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            //$decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            $stringBase64 = $this->getPetsService()->base64ToImagePath();
            //echo "<pre>"; print_r($stringBase64); exit;

            if ($stringBase64) {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                    "data"   => $stringBase64
                )));
            } else {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail',
                )));
            }
            
            return $response;

        //}

        exit;

    }

}