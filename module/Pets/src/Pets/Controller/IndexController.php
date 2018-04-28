<?php
namespace Pets\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Controller\BaseController;
use Pets\Service\PetsService;
use Articles\Service\CodeQRService;
use Pets\Form\PetsForm;
use Articles\Form\CodeQrForm;
use Articles\Service\CategoryService;
use Articles\Service\HistoryStatusService;
use Articles\Service\ReturnsService;
use Users\Service\UsersService;

class IndexController extends BaseController
{
	private $petsService;
    private $codeQRService;
    private $categoryService;
    private $historyStatusService;
    private $returnsService;
    private $usersServices;

	/**
	 * SERVICIO DE MASCOTAS
	 */
	private function getPetsService()
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
	 * LISTA DE ARTICULOS
	 */
	public function indexAction()
    {

        // ID DE USUARIO DE SESION
    	$idUser     = (int) $this->getIdUserSesion();

        // TIPO DE USUARIO
        $perfilUser = (int) $this->getPerfilUserSesion();
        
        // ESTATUS DE LA CLAVE DE INVENTARIO
        $key_status = (int) $this->getKeyStatusInventorySesion();

        // REQUEST
        $request    = $this->getRequest();

        // Validamos el tipo de peticion
        if($request->isPost()) {

            //Datos que llega por post
            $formData = $request->getPost()->toArray();

            // Validar clave de inventario
            $verifyKeyInventory = $this->getArticlesService()->verifyKeyInventoryArticles($formData['password_articles'], $idUser);
            //echo "<pre>"; print_r(($verifyKeyInventory[0]['count'])); exit;
            // VALIDAMOS LA CLAVE DE INVENTARIO
            if ($verifyKeyInventory[0]['count']) {

                // VALOR DEL ESTATUS DE LA KEY
                $valueStatusKey = 8102;
                
                // Agregamos valor a la sesion
                $addKeySession = $this->getArticlesService()->addKeyInventorySession($valueStatusKey);
            } else {
                $deleteKeySession = $this->getArticlesService()->deleteKeyInventorySession();
            }

            // RESPUESTA
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "data" => $verifyKeyInventory
                )));

            return $response;

        }

        // VALIDAMOS EL TIPO DE USURIO
        if ($perfilUser == 2 && $key_status == 0) {
            
            // Redirigimos a articulos encontrados
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles/findarticles');

        }

        // LISTA DE MASCOTAS
    	$pets = $this->getPetsService()->getAll($idUser, $perfilUser);
        //echo "<pre>"; print_r($pets); exit;

        // DATOS QUE SE ENVIAN A LA VISTA
    	$view = array('pets' => $pets, 'perfil' => $perfilUser);

        return new ViewModel($view);
    }

    /**
     * OBTENER ESTATUS DE LA KEY INVENTORY
     */
    public function statuskeyinventoryandperfilAction()
    {

        // REQUEST
        $request    = $this->getRequest();

        // ESTATUS DE LA CLAVE DE INVENTARIO
        $key_status = (int) $this->getKeyStatusInventorySesion();

        // TIPO DE USUARIO
        $perfilUser = (int) $this->getPerfilUserSesion();

        // Validamos el tipo de peticion
        if($request->isPost()) {

            // RESPUESTA
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "status_key"  => $key_status,
                "perfil_user" => $perfilUser
            )));

            return $response;
        }

        exit;

    }
    
    /**
     * AGREGAR UN ARTICULO NUEVO O ENCONTRADO
     */
    public function addarticlefoundAction()
    {
        // FORMULARIO DE ARTICULOS
        $form    = new PetsForm("articles");

        // FORMULARIO DE CODIGO QR
        $formQR  = new CodeQrForm("form_codeqr");

        // DATOS QUE SE ENVIAN A LA VISTA
        $view    = array("form" => $form, "formQR" => $formQR);

        // SOLICITUD
        $request = $this->getRequest();

        // TIPO DE SOLICITUD
        if($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            //$formData   = $request->getPost()->toArray();
            $formData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            //echo "<pre>"; print_r($formData); exit;

            // ID DEL USUARIO EN LA SESION
            $idUser                   = (int) $this->getIdUserSesion();
            
            // AGREGAR ATRBUTO AL FORMDATA CON EL ID DEL USUARIO EN LA SESION
            $formData['id_users']     = $idUser;
            
            // AGREGAR ATRBUTO AL FORMDATA CON EL ID DEL USUARIO EN LA SESION
            $formData['id_userfound'] = $idUser;
            
            //echo "<pre>"; print_r($formData); exit;
            
            // VALIDAMOS SI SE VA A REGISTRAR UN ARTICULO NUEVO O SE VA A EDITAR EL ESTATUS DE UN ARTICULO
            // SI (type_hs) VIENE EN EL POST EL ARTICULO YA ESTA LIGADO A UN CODIGO QR
            if (isset($formData['type_hs'])) {

                // 1.- AGREGAMOS UN REGISTRO EN LA TABLA DE RETURNS
                $addReturns = $this->getReturnsService()->addReturn($formData);
                //echo "<pre>"; print_r($addReturns); exit;

                // VALIDAMOS SI FUE CORRECTO EL AGREGAR UN REGISTRO EN LA TABLA RETURNS
                if ($addReturns) {

                    // 2.- AGREGAMOS UN REGISTRO EN LA TABLA DE HISTORY STATUS
                    $addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($formData);

                    // ARREGLO CON LOS DATOS DEL ARTICULO
                    //$arreArticle = array(
                    //    'id'           => $formData['id_articles_hs'],
                    //    'id_userfound' => $formData['id_userfound']
                    //);

                    // ACTUALIZAR LA TABLA DE ARTICLES
                    //$updateIdUserFoundArticle = $this->getArticlesService()->updateIdUserFound($arreArticle);

                    // ESTATUS DE CODIGO QR
                    $valueStatus = (int) 7;
                        
                    // ID DEL CODIGO QR
                    $idCodeQR    = $formData['id_code_qr'];
                        
                    // 3.- ACTUALIZAR STATUS DE CODIGO QR
                    $update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
                        
                    // GENERAMOS UNA RESPUESTA
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'ok'
                    )));

                } else {
                    // GENERAMOS UNA RESPUESTA
                     $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'fail'
                    )));
                }
                
            } else {

                // ANADIMOS UN VALOR AL FORMDATA
                $formData['id_status']    = 7;
                
                // TIPO DE ARTICULO
                $formData['own_alien']    = 2;

                //echo "<pre>"; print_r($formData); exit;

                // 1, 2.- GUARDAMOS EL ARTICULO Y ACTUALIZAR ESTATUS DEL CODIGO QR
                $addArticles = $this->getArticlesService()->addArticles($formData);
                
                // VALIDAMOS SI SE GUARDO
                if($addArticles) {

                    // AGREGAMOS UN VALOR AL FORMDATA
                    $formData['id_articles_hs'] = $addArticles;

                    // AGREGAMOS UN VALOR AL FORMDATA
                    $formData['id_status_hs']   = $formData['id_status'];

                    // 3.- AGREGAMOS UN REGISTRO EN LA TABLA DE HISTORY STATUS
                    $addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($formData);
                    
                    // VALIDAMOS SI SE AGREGO UN REGISTRO EN LA TABLA DE HISTORY STATUS
                    if($addHistoryStatus) {

                        // AGREGAMOS UN VALOR AL FORMDATA
                        $formData['id_history_status'] = $addHistoryStatus;

                        // 4.- AGREGAMOS UN REGISTRO EN LA TABLA DE RETURNS
                        $addReturns = $this->getReturnsService()->addReturn($formData);

                    }

                    // RESPUESTA
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'ok'
                    )));

                } else {

                    // RESPUESTA
                    $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                        "status" => 'fail'
                    )));

                }
            }

            return $response;
            exit;

        }

        // Envimos los datos a la vista
        return new ViewModel($view);

    }

    /**
     * AGREGAR ARTICULO
     */
    public function addAction()
    {

    	$form    = new PetsForm("form_pets");
        $formQR  = new CodeQrForm("form_codeqr");
    	$view    = array("form" => $form, "formQR" => $formQR);
    	$request = $this->getRequest();
    	
    	if($request->isPost()) {

            $formData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            //echo "<pre>"; print_r($formData); exit;

            // Id del usuario en sesion
            $idUser = (int) $this->getIdUserSesion();

            $formData['id_users'] = $idUser;

            // ANADIMOS UN VALOR AL FORMDATA
            $formData['id_status'] = 2;

            // TIPO DE ARTICULO
            //$formData['own_alien'] = 1;
            //echo "<pre>"; print_r($formData); exit;
            $addPet = $this->getPetsService()->addPet($formData);
            //print($addPet); exit;
            
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
    	
    	return new ViewModel($view);

    }

    /**
     * VALIDAR SI UN CODIGO QR EXISTE
     */
    public function validatecodeqrAction()
    {

        $request = $this->getRequest();

        if($request->isPost()) {

            $formData   = $request->getPost()->toArray();

            // CODIGO DEL QR
            $code_qr = $formData['code_article'];

            // Validamos el codigo qr
            $validateCodeQR = $this->getCodeQRService()->validateTypeCodeQr($code_qr);

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "data" => $validateCodeQR
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

            $formData   = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;

            $pin    = (int) $formData['pin'];

            // ID DE USUARIO
            $idUser = (int) $formData['id_user'];

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

            $formData   = $request->getPost()->toArray();
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
     * OBTENER LA LISTA DE NOMBRES DE LOS ARTICULOS POR ID DE CATEGORIA
     */
    public function getnamearticlesbyidcategoryAction()
    {
        $request = $this->getRequest();

        if($request->isPost()) {

            $formData   = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;

            // CODIGO DEL QR
            $code_qr = $formData['code_article'];

            // Validamos el codigo qr
            $validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExists($code_qr);
            //echo "<pre>"; print_r($validateCodeQR); exit;

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "data" => $validateCodeQR
                )));

            return $response;

        }

        exit;
    }

    /**
     * EDITAR ARTICULO
     */
    public function editAction()
    {
    	$id      = (int) $this->params()->fromRoute("id",null);
    	
    	$form    = new PetsForm("form_pets");
        $formQR  = new CodeQrForm("form_codeqr");
    	$request = $this->getRequest();

    	if($request->isPost()){
    		//$formData   = $request->getPost()->toArray();
            
            $formData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            //echo "<pre>"; print_r($formData); exit;
    		
    		$editPet = $this->getPetsService()->editPet($formData);
            //echo "<pre>"; print_r($editPet); exit;

    		if($editPet){
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));
    		}else{
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
			}

            return $response;
            
    	}

        // Obtenemos un articulo por id
        $pet = $this->getPetsService()->getPetById($id);
        //echo "<pre>"; print_r($pet); exit;

        // Pasamos los datos al formulario
        $form->setData($pet);
        //$formQR->setData($article);

        // Enviamos datos a la vista
        $view    = array(
            "form"      => $form,
            "formQR"    => $formQR,
            'imagePet'  => $pet['image_name']
        );
    	
    	return new ViewModel($view);

    }

    /**
     * OBTENEMOS LAS SUBCATEGORIAS
     */
    public function getallsubcategorysAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            
            $data = $request->getPost()->toArray();
            //echo "<pre>"; print_r($data); exit;

            $id = (int) $data['id_parent'];

            $subCategorys = $this->getCategoryService()->getAllSubCategory($id);
            //echo "<pre>"; print_r($subCategorys); exit;

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "subCategorys" => $subCategorys,
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
            
            $data = $request->getPost()->toArray();
            //print_r($data); exit;
            $id     = (int) $data['id'];

            $status = (int) $data['id_status']; 

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
     * AGREGAR UN PRESTAMO DEL ARTICULO
     */
    public function addhistorystatusAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            
            $data = $request->getPost()->toArray();
            //echo "<pre>"; print_r($data); exit;
            //$id     = (int) $data['id'];

            //$status = (int) $data['id_status']; 

            $insertLend = $this->getHistoryStatusService()->insertLendArticle($data);
            //echo "<pre>"; print_r($insertLend); exit;

            if ($insertLend) {
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
     * ELIMINAR UNA MASCOTA
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $id      = (int) $this->params()->fromRoute('id', 0);
        
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/pets');
        }
        
        if ($request->isPost()) {
            $del = $request->getPost()->toArray();
            
            $deletePet = $this->getPetsService()->deletePet($id);

            if($deletePet){

                // Redirect to list of pets
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/pets');

            }

        }
    }

    /**
     * ELIMINAR ARTICULO ENCONTRADO
     */
    public function deletefoundAction()
    {
        $request = $this->getRequest();
        $id      = (int) $this->params()->fromRoute('id', 0);
        
        // REDIRECCIONAMOS
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles/findarticles');
        }
        
        // TIPO DE SOLICITUD
        if ($request->isPost()) {

            $del = $request->getPost()->toArray();

            $deleteArticleFound = $this->getReturnsService()->deleteArticleFound($id);

            // Redirect to list of customers
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles/findarticles');
        }

    }

    /**
     * UBICACION DEL ARTICULO
     */
    public function seeMapAction()
    {
        $id      = (int) $this->params()->fromRoute("id",null);

        $location_pet = $this->getPetsService()->getLastLocationPet($id);
        //echo "<pre>"; print_r(count($location_pet)); exit;

        // Enviamos datos a la vista
        $view    = array(
            'latitude'  => (count($location_pet) != 0 && $location_pet['latitude'] != null) ? $location_pet['latitude']  : "",
            'longitude' => (count($location_pet) != 0 && $location_pet['longitude'] != null) ? $location_pet['longitude'] : "",
            'addres'    => (count($location_pet) != 0 && $location_pet['addres'] != null) ? $location_pet['addres']    : ""
        );
        
        return new ViewModel($view);
        
    }

    /**
     * UBICACION DEL ARTICULO
     */
    public function seeMapArticlefoundAction()
    {
        $id      = (int) $this->params()->fromRoute("id",null);

        $location_article = $this->getArticlesService()->getLastLocationArticle($id);
        //echo "<pre>"; print_r(count($location_article)); exit;

        // Enviamos datos a la vista
        $view    = array(
            'latitude'  => (count($location_article) != 0 && $location_article['latitude'] != null) ? $location_article['latitude']  : "",
            'longitude' => (count($location_article) != 0 && $location_article['longitude'] != null) ? $location_article['longitude'] : "",
            'addres'    => (count($location_article) != 0 && $location_article['addres'] != null) ? $location_article['addres']    : ""
        );
        
        return new ViewModel($view);
        
    }

    /**
     * DETALLE DE UNA MASCOTA
     */
    public function detailAction()
    {
        $id      = (int) $this->params()->fromRoute("id",null);

        // Obtenemos una mascota por id
        $pet = $this->getPetsService()->getPetById($id);
        //echo "<pre>"; print_r($pet); exit;

        // Enviamos datos a la vista
        $view    = array("pet" => $pet);
        
        return new ViewModel($view);
    }

    /**
     * DETALLE DE ARTICULO ENCONTRADO
     */
    public function detailarticlefoundAction()
    {
        $id      = (int) $this->params()->fromRoute("id",null);

        // Obtenemos un articulo por id
        $article = $this->getArticlesService()->getArticleById($id);
        //echo "<pre>"; print_r($article); exit;

        // Enviamos datos a la vista
        $view    = array("article" => $article);
        
        return new ViewModel($view);
    }

    /**
     * LISTA DE ARTICULOS ENCONTRADOS
     */
    public function findarticlesAction()
    {
        // Perfil de usuario
        $perfilUser = (int) $this->getPerfilUserSesion();
        // Id de usuario 
        $idUser     = (int) $this->getIdUserSesion();

        // Lista de articulos encontrados
        $articlesFound   = $this->getArticlesService()->getAllArticlesFoundByIdUser($idUser);
        //echo "<pre>"; print_r($articlesFound); exit;

        // Lista de articulos encontrados sin codigo qr
        //$articlesFoundStatusSeven   = $this->getArticlesService()->getAllArticlesFoundStatusSeven($idUser);

        //echo "<pre>"; print_r($articlesFoundStatusSeven); exit;

        $view = array('articles' => $articlesFound,'perfil' => $perfilUser);

        return new ViewModel($view);

    }

    /**
     * CUANDO UN CODIGO QR DE MASCOTA ES ESCANEADO
     */
    public function codeqrAction()
    {   
        $view    = new ViewModel();
        $view->setTerminal(true);
        $request = $this->getRequest();
        $codeQR  = $this->params()->fromRoute("id",null);

        // VALIDAMOS EL TIPO DE PETICION
        if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            $formData = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;
            $idCodeQR    = $formData['id_code_qr'];
            
            $valueStatus = 7; 

            // CAMBIAMOS EL ESTATUS DEL ARTICLE
            $updateStatusCodeQR = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);

            // VALIDAMOS SI SE ACTUALIZO EL STATUS
            if ($updateStatusCodeQR) {
                 
                // AGREGAMOS UN REGISTRO EN LA TABLA DE HISTORY STATUS
                //$addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($formData);
                //echo "<pre>"; print($addHistoryStatus); exit;

                // ENVIO DE CORREO
                $sendEmail = $this->getPetsService()->sendEMailPetFound($formData);

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

            }

            return $response;
            
        }
        
        $petByCodeQr  = $this->getPetsService()->getPetByCodeQr($codeQR);
        //echo "<pre>"; print_r($petByCodeQr); exit;

        // Validamos el codigo qr
        $validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExistsPet($codeQR);
        //echo "<pre>"; print_r($validateCodeQR); exit;

        // VALIDAMOS SI EL CODIGO EXISTE
        if ($validateCodeQR[0]['count'] == 1) {

            // VALIDAMOS EL TIPO DEL CODIGO QR
            if ($validateCodeQR[0]['type_qr'] == 2) {

                // VALIDAMOS EL ESTATUS Y EL ID DE LA MASCOTA
                if ($validateCodeQR[0]['id_status'] == 1 || $validateCodeQR[0]['id_pet'] == -1) {
                    // Redirigimos al inicio
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
                }

            } else if ($validateCodeQR[0]['type_qr'] == 1) {
                $urlArticle = $this->getRequest()->getBaseUrl() . "/articles/codeqr/" . $codeQR;
                //print_r($urlArticle); exit;
                // Redirigimos al inicio
                return $this->redirect()->toUrl($urlArticle);
            }

        } else if ($validateCodeQR[0]['count'] == 0) {
            // Redirigimos al inicio
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
        }
        
        $view->setVariables(array(
            'validateCodeQR'    => json_encode($validateCodeQR[0]),
            'petByCodeQr'       => json_encode($petByCodeQr[0])
        ));

        //$petByCodeQr  = $this->getPetsService()->getPetByCodeQr($codeQR);

      // $view->setVariables(array('petByCodeQr' => json_encode($petByCodeQr[0])));
        //echo "<pre>"; print_r($petByCodeQr); exit;

        return $view;
    }


/**
     * CUANDO UN CODIGO QR DE MASCOTA ES ESCANEADO ingles
     */
    public function codeqrenAction()
    {   
        $view    = new ViewModel();
        $view->setTerminal(true);
        $request = $this->getRequest();
        $codeQR  = $this->params()->fromRoute("id",null);

        // VALIDAMOS EL TIPO DE PETICION
        if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            $formData = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;
            $idCodeQR    = $formData['id_code_qr'];
            
            $valueStatus = 7; 

            // CAMBIAMOS EL ESTATUS DEL ARTICLE
            $updateStatusCodeQR = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);

            // VALIDAMOS SI SE ACTUALIZO EL STATUS
            if ($updateStatusCodeQR) {
                 
                // AGREGAMOS UN REGISTRO EN LA TABLA DE HISTORY STATUS
                //$addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($formData);
                //echo "<pre>"; print($addHistoryStatus); exit;

                // ENVIO DE CORREO
                $sendEmail = $this->getPetsService()->sendEMailPetFound($formData);

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

            }

            return $response;
            
        }
        
        $petByCodeQr  = $this->getPetsService()->getPetByCodeQr($codeQR);
        //echo "<pre>"; print_r($petByCodeQr); exit;

        // Validamos el codigo qr
        $validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExistsPet($codeQR);
        //echo "<pre>"; print_r($validateCodeQR); exit;

        // VALIDAMOS SI EL CODIGO EXISTE
        if ($validateCodeQR[0]['count'] == 1) {

            // VALIDAMOS EL TIPO DEL CODIGO QR
            if ($validateCodeQR[0]['type_qr'] == 2) {

                // VALIDAMOS EL ESTATUS Y EL ID DE LA MASCOTA
                if ($validateCodeQR[0]['id_status'] == 1 || $validateCodeQR[0]['id_pet'] == -1) {
                    // Redirigimos al inicio
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
                }

            } else if ($validateCodeQR[0]['type_qr'] == 1) {
                $urlArticle = $this->getRequest()->getBaseUrl() . "/articles/codeqr/" . $codeQR;
                //print_r($urlArticle); exit;
                // Redirigimos al inicio
                return $this->redirect()->toUrl($urlArticle);
            }

        } else if ($validateCodeQR[0]['count'] == 0) {
            // Redirigimos al inicio
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
        }
        
        $view->setVariables(array(
            'validateCodeQR'    => json_encode($validateCodeQR[0]),
            'petByCodeQr'       => json_encode($petByCodeQr[0])
        ));

        //$petByCodeQr  = $this->getPetsService()->getPetByCodeQr($codeQR);

      // $view->setVariables(array('petByCodeQr' => json_encode($petByCodeQr[0])));
        //echo "<pre>"; print_r($petByCodeQr); exit;

        return $view;
    }

    /**
     * GUARDAR UBICACION DE LA MASCOTA
     */
    public function savelastlocationAction()
    {
        // SOLICITUD
        $request = $this->getRequest();

        // TIPO DE PETICION
        if ($request->isPost()) {

            // DATOS RECIBIDOS POR POST
            $postData       = $this->getRequest()->getContent();
            
            // PARSEAMOS JSON A ARRAY PHP
            $decodePostData = json_decode($postData, true);

            // GUARDAR UBICACION
            $saveLastLocation = $this->getPetsService()->saveLastLocation($decodePostData);

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
     * DETALLE DE DEVOLUCION
     */
    public function detailreturnarticleAction()
    {

        $request = $this->getRequest();

        if($request->isPost()) {

            $formData   = $request->getPost()->toArray();
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

}