<?php

namespace Restful\Controller;
     
use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Articles\Service\ArticlesService;
use Articles\Service\CodeQRService;
use Articles\Service\CategoryService;
use Articles\Service\ColorService;
use Articles\Service\HistoryStatusService;
use Articles\Service\ReturnsService;
use Users\Service\UsersService;
     
class ArticlesRestController extends BaseController
{

    private $articlesService;
    private $codeQRService;
    private $categoryService;
    private $colorService;
    private $historyStatusService;
    private $returnsService;
    private $usersServices;

    /**
     * Instanciamos el servicio de articulos
     */
    public function getArticlesService()
    {
        return $this->articlesService = new ArticlesService();
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
     * BIENVENIDA AL WS ARTICULOS
     */
    public function indexAction()
    {
        echo "WS Rest Articulos.";
        exit;
    }

    /**
     * OBTENER MI LISTA DE ARTICULOS
     */
    public function getarticlesAction()
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
            
            // ID DE USUARIO
            $idUser         = $decodePostData['id_user'];
            
            // TIPO DE PERFIL
            $perfilUser     = $decodePostData['type_user'];

            // LISTA DE ARTICULOS
            $articles = $this->getArticlesService()->getAll($idUser, $perfilUser);

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "articles" => $articles
            )));

            return $response;
   
        }

        exit;
    }

    /**
     * LISTA DE ARTICULOS ENCONTRADOS
     */
    public function findarticlesAction()
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
            
            // ID DE USUARIO
            $idUser         = $decodePostData['id_user'];
            
            // TIPO DE PERFIL
            $perfilUser     = $decodePostData['type_user'];

            // Lista de articulos encontrados
            $articlesFound   = $this->getArticlesService()->getAllArticlesFoundByIdUser($idUser);

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "articlesFound" => $articlesFound
            )));

            return $response;
   
        }

        exit;

    }

    /**
     * ONTENER LISTA DE ARTICULOS ENCONTRADOS
     */
    public function getfoundarticlesAction()
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
            
            // ID DE USUARIO
            $idUser         = $decodePostData['id_user'];
            
            // TIPO DE PERFIL
            $perfilUser     = $decodePostData['type_user'];

            // LISTA DE ARTICULOS ENCONTRADOS
            $articlesFound   = $this->getArticlesService()->getAllArticlesFoundByIdUser($idUser);

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "articlesfound" => $articlesFound
            )));

            return $response;

        }

        exit;
    }

    /**
     * DETALLE DE ARTICULO
     */

    public function detailAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

         // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            
            // ID ARTICULO
            $idArticle      = (int) $decodePostData['id_article'];
            
            // Obtenemos un articulo por id
            $article        = $this->getArticlesService()->getArticleById($idArticle);

            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "article" => $article
            )));

            return $response;

        }

        exit;
    }

    /**
     * ULTIMA LOCALIZACION DEL ARTICULO
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
            
            // ID ARTICULO
            $idArticle      = (int) $decodePostData['id_article'];

            // ULTIMA UBICACION DEL ARTICULO
            $location_article = $this->getArticlesService()->getLastLocationArticle($idArticle);
            
            // RESPUESTA DEL WS OK
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                'latitude'  => (count($location_article) != 0 && $location_article['latitude'] != null) ? $location_article['latitude']  : "",
                'longitude' => (count($location_article) != 0 && $location_article['longitude'] != null) ? $location_article['longitude'] : "",
                'addres'    => (count($location_article) != 0 && $location_article['addres'] != null) ? $location_article['addres']    : ""
            )));

            return $response;

        }

        exit;
    }

    /**
     * GUARDAR UBICACION DEL ARTICULO
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

            // AGREGAR ARTICULO
            $saveLastLocation = $this->getArticlesService()->saveLastLocation($decodePostData);
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
     * NOTIFICAR ARTICULO ENCONTRADO
     */
    public function notificationfoundarticleAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);

            // NOTIFICAR ARTICULO ENCONTRADO
            $notificationFoundArticle = $this->getArticlesService()->notificationFoundArticle($decodePostData); // CREAR ESTE METODO EL EL SERVICIO DE ARTICULOS O DONDE SEA NECESARIO

            // VALIDAMOS SI FUE CORRECTO
            if($notificationFoundArticle) {
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
     * ELIMINAR ARTICULO
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

            $deleteArticle = $this->getArticlesService()->deleteArticle($idCodeQr);
            //echo "<pre>"; print_r($deleteArticle); exit;

            if($deleteArticle) {

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
     * VALIDAR CODIGO QR
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
            
            // CODIGO DEL QR
            $code_article   = $decodePostData['code_qr'];
            
            // Validamos el codigo qr
            $validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExists($code_article);

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "data" => $validateCodeQR
            )));

            return $response;

        }

        exit;
    }

    /**
     * OBTENER LISTA DE CATEGORIAS
     */
    public function getallcategoryparentAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

         // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            //$postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            //$decodePostData = json_decode($postData, true);
            
            // LISTA DE CATEGORIAS
            $categorys = $this->getCategoryService()->getAllCategoryParent();

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "categories" => $categorys
            )));

            return $response;

        }

        exit;
    }

    /**
     * OBTENER LISTA DE ARTICULOS
     */
    public function getallsubcategorysAction()
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

            $id = (int) $decodePostData['id_parent'];
            
            // LISTA DE SUBCATEGORIAS
            $subCategorys = $this->getCategoryService()->getAllSubCategory($id);
            //echo "<pre>"; print_r($subCategorys); exit;

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "subCategorys" => $subCategorys
            )));

            return $response;

        }

        exit;
    }

    /**
     * OBTENER LISTA DE COLORES
     */
    public function getallcolorsAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

         // TIPO DE PETICION
        if ($request->isPost()) {
            
            // LISTA DE COLORES
            $colors = $this->getColorService()->getAllColor();

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "colors" => $colors
            )));

            return $response;

        }

        exit;
    }

    /**
     * EDITAR UN ARTICULO
     */
    public function editarticleAction()
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
            $editArticle = $this->getArticlesService()->editArticlesAppMovil($decodePostData);
            //echo "<pre>"; print_r($editArticle); exit;

            // VALIDAMOS SI SE MODIFICO EL ARTICULO
            if($editArticle) {

                // RESPUESTA DEL WS OK
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                    "article" => $editArticle
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
     * AGREGAR UN ARTICULO
     */
    public function addarticleAction()
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

            // AGREGAR ARTICULO
            $addArticle = $this->getArticlesService()->addArticlesAppMovil($decodePostData);
            //echo "<pre>"; print_r($addArticle); exit;

            // VALIDAMOS SI SE AGREGO EL ARTICULO
            if($addArticle) {
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
     * AGREGAR UN ARTICULO NUEVO O ENCONTRADO
     */
    public function addarticlefoundAction()
    {
        // Solicitud
        $request = $this->getRequest();

        if($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            // Id del usuario en sesion
            //$idUser = (int) $this->getIdUserSesion();

            // ID del usuario de la sesion
            //$decodePostData['id_users']     = $idUser;
            
            // ID DEL USUARIO
            //$decodePostData['id_userfound'] = $idUser;
            
            //echo "<pre>"; print_r($decodePostData); exit;
            
            // Validamos si se va a agregar un articulo o se va a editar el estatus history
            if (isset($decodePostData['type_hs'])) {

                //echo "Llegue aqui"; exit;

                // AGREGAMOS UN REGISTRO EN LA TABLA DE RETURNS
                $addReturns = $this->getReturnsService()->addReturn($decodePostData);
                //echo "<pre>"; print_r($addReturns); exit;

                // VALIDAMOS SI ES CORRECOTO
                if ($addReturns) {

                    // AGREGAMOS UN REGISTRO EN LA TABLA DE HISTORY STATUS
                    $addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($decodePostData);
                    //echo "<pre>"; print_r($addHistoryStatus); exit;

                    // AGREGAMOS UN VALOR AL FORMDATA
                    //$formData['id_history_status'] = $addHistoryStatus;

                    // ARREGLO CON LOS DATOS DEL ARTICULO
                    //$arreArticle = array(
                    //    'id'           => $decodePostData['id_articles_hs'],
                    //    'id_userfound' => $decodePostData['id_userfound']
                    //);
                    //echo "<pre>"; print_r($arreArticle); exit;

                    // ACTUALIZAR LA TABLA DE ARTICLES
                    //$updateIdUserFoundArticle = $this->getArticlesService()->updateIdUserFound($arreArticle);
                    //echo "<pre>"; print_r($updateIdUserFoundArticle); exit;

                    // ESTATUS DE CODIGO QR
                    $valueStatus = (int) 7;
                    
                    // ID DEL CODIGO QR
                    $idCodeQR    = $decodePostData['id_code_qr'];
                    
                    // ACTUALIZAR STATUS DE CODIGO QR
                    $update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
                    //echo "<pre>"; print_r($update_status_code_qr); exit;
                    
                    // GENERAMOS UNA RESPUESTA
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'ok',
                        "id_return" => $addReturns
                    )));

                } else {
                    // GENERAMOS UNA RESPUESTA
                     $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'fail'
                    )));
                }
                
            } else {

                //echo "Ando por acá"; exit;

                // ANADIMOS UN VALOR AL FORMDATA
                //$decodePostData['id_status']    = 7;
                
                // TIPO DE ARTICULO
                //$decodePostData['own_alien']    = 2;

                // 1 y 2.-  GUARDAMOS EL ARTICULO Y ACTUALIZAR ESTATUS DEL CODIGO QR
                $addArticles = $this->getArticlesService()->addArticlesAppMovil($decodePostData);
                //$addArticles = 322423;
                
                // VALIDAMOS SI SE GUARDO
                if($addArticles) {

                    // AGREGAMOS UN VALOR AL FORMDATA
                    $decodePostData['id_articles_hs'] = $addArticles;

                    // AGREGAMOS UN VALOR AL FORMDATA
                    $decodePostData['id_status_hs']   = $decodePostData['id_status'];

                    // 3.- AGREGAMOS UN REGISTRO EN LA TABLA DE HISTORY STATUS
                    $addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($decodePostData);
                    //$addHistoryStatus = 9994;

                    // VALIDAMOS SI SE AGREGO UN REGISTRO EN LA TABLA DE HISTORY STATUS
                    if($addHistoryStatus) {

                        // AGREGAMOS UN VALOR AL FORMDATA
                        //$decodePostData['id_history_status'] = $addHistoryStatus;

                        // 4.- AGREGAMOS UN REGISTRO EN LA TABLA DE RETURNS
                        $addReturns = $this->getReturnsService()->addReturn($decodePostData);

                    }

                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'ok'
                    )));
                } else {
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'fail'
                    )));
                }
            }

            return $response;

        }

        exit;

    }

    /**
     * ENVIAR CORREO A DUENO DE ARTICULO ENCONTRADO
     */
    public function sendemailarticleownerAction()
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

            // CAMBIAMOS EL ESTATUS DEL ARTICULO
            $updateStatusCodeQR = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
            //echo "<pre>"; print_r($updateStatusCodeQR); exit;

            // VALIDAMOS SI SE ACTUALIZO ESTATUS DE CODIGO
            if($updateStatusCodeQR) {

                // ENVIO DE CORREO
                $sendEmail = $this->getArticlesService()->sendEMailArticleFound($decodePostData);
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
     * VALIDAR CLAVE DE INVENTARIO
     */
    public function validatekeyinventoryAction()
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
            $verifyKeyInventory = $this->getArticlesService()->verifyKeyInventoryArticles($decodePostData['password_articles'], $idUser);
            //echo "<pre>"; print_r($verifyKeyInventory); exit;

            // RESPUESTA DEL WS
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "data" => $verifyKeyInventory
            )));

            return $response;

        }

        exit;
    }

    /**
     * VALIDAR PIN DE USUARIOS
     */
    public function verifypinuserAction()
    {
        $request    = $this->getRequest();

        if($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;
            
            $pin      = (int) $decodePostData['pin'];
            
            // ID DE USUARIO
            $idUser   = (int) $decodePostData['id_user'];

            // Validamos el codigo qr
            $verifyPinUser = $this->getUsersService()->verifyPinUser($pin, $idUser);
            //echo "<pre>"; print_r($verifyPinUser); exit;

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "data" => $verifyPinUser
                )));

            return $response;

        }

        exit;

    }

    /**
     * ACTUALIZAR REGISTRO DE DEVOLUCION
     */
    public function updatereturnarticleAction()
    {
        $request    = $this->getRequest();

        // VALIDAMOS EL TIPO DE PETICIÓN
        if($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $formData = json_decode($postData, true);
            //echo "<pre>"; print_r($formData); exit;

            // 1.- MODIFICAMOS UN REGISTRO EN LA TABLA DE RETURNS
            $editReturns = $this->getReturnsService()->editReturn($formData);
            //echo "<pre>"; print_r($editReturns); exit;

            if ($editReturns) {

                // ESTATUS DE CODIGO QR
                $valueStatus                = (int) 6; // DEVUELTO
                    
                // ID DEL CODIGO QR
                $idCodeQR                   = $formData['id_code_qr'];
                    
                // 2.- ACTUALIZAR STATUS DE CODIGO QR
                $update_status_code_qr      = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
                //echo "<pre>"; print_r($update_status_code_qr); exit;
                    
                // AGREGAMOS UN VALOR AL FORM DATA
                $formData['id_articles_hs'] = $formData['id_articles'];
                    
                // ESTATUS PARA EL REGISTRO DE HISTORY ESTATUS
                $formData['id_status_hs']   = 6;

                // 3.- AGREGAMOS UN REGISTRO A LA TABLA HISTORY STATUS
                $addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($formData);
                //echo "<pre>"; print_r($addHistoryStatus); exit;
                
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                    "data"   => $editReturns
                )));

            } else {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail',
                    "data"   => 0
                )));
            }

            

            return $response;

        }

        exit;
    }

    /**
     * DETALLE DE DEVOLUCION
     */
    public function detailreturnarticleAction()
    {

        $request = $this->getRequest();

        if($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData  = $this->getRequest()->getContent();
            //echo "<pre>"; print_r($postData); exit;
            
            // PARSEAMOS JSON A ARRAY PHP
            $formData  = json_decode($postData, true);
            //echo "<pre>"; print_r($formData); exit;
            
            // ID RETURN
            $id_return = $formData['id_return'];

            // DETALLE DE LA DEVOLUCION
            $detailReturnArticle = $this->getReturnsService()->detailReturnArticle($id_return);
            //echo "<pre>"; print_r($detailReturnArticle); exit;

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "data" => $detailReturnArticle
            )));

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
            $pushNotifications  = $this->getArticlesService()->getAllPushNotifications($idUser);
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

            $updateStatusNotification = $this->getArticlesService()->updateStatusNotification($decodePostData);
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

        $stringBase64 = $this->getArticlesService()->base64ToImagePath();
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

        exit;

    }

    /**
     * BASE64 A IMAGEN EN EL SERVIDOR
     */
    public function base64toimagepathtwoAction()
    {
        $request = $this->getRequest();

        $stringBase64 = $this->getArticlesService()->base64ToImagePathTwo();
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

        exit;

    }

    /**
     * RECORDATORIO ARTICULO DE ARTICULO ENCONTRADO
     */
    public function rememberpushnotificationAction()
    {
        // PETICION
        $request = $this->getRequest();

        // VALIDAR PETICION
        if($request->isPost()) {

            $postData             = $request->getContent();
            //echo "<pre>"; print_r($postData); exit;

            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);
            //echo "<pre>"; print_r($decodePostData); exit;

            // ID NOTIFICATION
            $id_push_notification = $decodePostData['id_push_notification'];

            // recordatorio de artículo encontrado
            $articleReminderFound = $this->getArticlesService()->articleReminderFound($id_push_notification);
            //echo "<pre>"; print_r($articleReminderFound); exit;

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "articleReminderFound" => $articleReminderFound
            )));

            return $response;

        }

        exit;

    }

}