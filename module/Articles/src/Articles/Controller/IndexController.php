<?php
namespace Articles\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Controller\BaseController;
use Articles\Service\ArticlesService;
use Articles\Service\CodeQRService;
use Articles\Form\ArticlesForm;
use Articles\Form\CodeQrForm;
use Articles\Service\CategoryService;
use Articles\Service\HistoryStatusService;
use Articles\Service\ReturnsService;
use Users\Service\UsersService;

class IndexController extends BaseController
{
	private $articlesService;
    private $codeQRService;
    private $categoryService;
    private $historyStatusService;
    private $returnsService;
    private $usersServices;

	/**
	 * SERVICIO DE ARTICULOS
	 */
	private function getArticlesService()
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

        // LISTA DE ARTICULOS
    	$articles = $this->getArticlesService()->getAll($idUser, $perfilUser);
        //echo "<pre>"; print_r($articles); exit;

        // DATOS QUE SE ENVIAN A LA VISTA
    	$view = array('articles' => $articles, 'perfil' => $perfilUser);

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
        $form    = new ArticlesForm("articles");

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

    	$form    = new ArticlesForm("articles");
        $formQR  = new CodeQrForm("form_codeqr");
    	$view    = array("form" => $form, "formQR" => $formQR);
    	$request = $this->getRequest();
    	
    	if($request->isPost()) {

            //sleep(3);

    		//$formData   = $request->getPost()->toArray();
            //$file    = $this->params()->fromFiles('imageone');
            //$file =  $this->getRequest()->getFiles()->toArray();
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
            $formData['own_alien'] = 1;

            $addArticles = $this->getArticlesService()->addArticles($formData);
            //print($addArticles); exit;
            
            if($addArticles) {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));
            } else {
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
            }

            return $response;

            // CODIGO DEL QR

            //$code_article = $formData['code_article'];

            // Validamos el codigo qr
            //$validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExists($code_article);
            //echo "<pre>"; print_r($validateCodeQR); exit;

            // Validamos si existe o no el codigo qr
            /*if ($validateCodeQR[0]['count'] == 0) {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
                
            } else if($validateCodeQR[0]['count'] == 1) {

                // Id del usuario en sesion
                $idUser = (int) $this->getIdUserSesion();

                $formData['id_users'] = $idUser;

                // Variable con el id del codigo qr

                $addArticles = $this->getArticlesService()->addArticles($formData);
                //print($addArticles); exit;
                if($addArticles) {
                    //return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles');
                } else {
                    //return $this->redirect()->refresh();
                }

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));

            }*/

            //return $response;

    	}
    	
    	return new ViewModel($view);

    }

    /**
     * VALIDAR SI UN CODIGO QR EXISTE
     */
    public function validatecodeqrAction()
    {
        //sleep(3);
        $request = $this->getRequest();

        if($request->isPost()) {

            $formData   = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;

            // CODIGO DEL QR
            $code_article = $formData['code_article'];

            // Validamos el codigo qr
            $validateCodeQR = $this->getCodeQRService()->validateTypeCodeQr($code_article);
            //echo "<pre>"; print_r($validateCodeQR); exit;

            // OBTENEMOS LOS DATOS DEL DUEÑO DEL ARTICULO

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
            $code_article = $formData['code_article'];

            // Validamos el codigo qr
            $validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExists($code_article);
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
    	
    	$form    = new ArticlesForm("articles");
        $formQR  = new CodeQrForm("form_codeqr");
    	$request = $this->getRequest();
    	//echo "<pre>"; print_r($request->isPost()); exit;
    	if($request->isPost()){
    		//$formData   = $request->getPost()->toArray();
            
            $formData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            //echo "<pre>"; print_r($formData); exit;
    		
    		$editArticles = $this->getArticlesService()->editArticles($formData);
            //echo "<pre>"; print_r($editArticles); exit;

    		if($editArticles){
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));
    			//return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles');
    		}else{
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));
				//return $this->redirect()->refresh();
			}

            return $response;
            
    	}

        // Obtenemos un articulo por id
        $article = $this->getArticlesService()->getArticleById($id);
        $article['category'] = $article['id_category'];
        $article['color']    = $article['id_color'];
        //echo "<pre>"; print_r($article); exit;
        // Pasamos los datos al formulario
        $form->setData($article);
        $formQR->setData($article);

        // Enviamos datos a la vista
        $view    = array(
            "form"      => $form,
            "formQR"    => $formQR,
            "imageOne"  => $article['image_name'],
            "imageTwo"  => $article['image_name_two'],
            'id_status' => $article['id_status'],
            "value_name_article"     => $article['name_article'],
            "value_name_article_two" => $article['name_article_two'],
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
     * ELIMINAR ARTICULO
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $id      = (int) $this->params()->fromRoute('id', 0);
        
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles');
        }
        
        if ($request->isPost()) {
            $del = $request->getPost()->toArray();
            //echo "<pre>"; print_r($id); exit;
            //if ($del['del'] == 'Aceptar'){
            //$id = (int) $del['id'];
            $deleteArticle = $this->getArticlesService()->deleteArticle($id);
            //}

            // Redirect to list of customers
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles');
        }
    
        //$data = $this->getArticlesServices()->getArticlesById($id);
        //$article = $this->getArticlesService()->getArticleById($id);
        //echo "<pre>"; print_r($article); exit();
        return array(
            //'id'    => $id,
            //'data'  => $article
        );
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
     * DETALLE DE ARTICULO
     */
    public function detailAction()
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
     * ELIMINAR ARTICULOS ENCONTRADOS
     */
    public function deletearticlesAction()
    {
        $request    = $this->getRequest();
        // Perfil de usuario
        $perfilUser = (int) $this->getPerfilUserSesion();
        // Id de usuario 
        $idUser     = (int) $this->getIdUserSesion();

        // Lista de articulos encontrados
        $articlesFound   = $this->getArticlesService()->getAllArticlesFoundByIdUser($idUser);
        //echo "<pre>"; print_r($articlesFound); exit;

        // TIPO DE PETICION
        if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            $formData = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;

            $deleteArticlesFound = $this->getArticlesService()->deleteArticleFound($formData);

            // VALIDAR SI SE ELIMINO CORRECTAMENTE
            if($deleteArticlesFound) {

                // GENERAMOS UNA RESPUESTA
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                    "data"   => $deleteArticlesFound
                )));

            } else {

                // GENERAMOS UNA RESPUESTA
                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                )));

            }

            return $response;

        }

        $view = array('articles' => $articlesFound,'perfil' => $perfilUser);

        return new ViewModel($view);

    }

    /**
     * CUANDO UN ARTICULO ES ESCANEADO
     */
    public function codeqrAction()
    {
        $view    = new ViewModel();
        $view->setTerminal(true);
        //$view->setTerminal(true);
        $request = $this->getRequest();
        $codeQR  = $this->params()->fromRoute("id",null);
       
        // VALIDAMOS EL TIPO DE PETICION
        if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            $formData = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;

            //$data_article_decode = json_decode($formData['data_article'], true);
            //echo "<pre>"; print_r($data_article_decode); exit;

            //$idCodeQR    = $data_article_decode['id'];

            $idCodeQR    = $formData['id_code_qr'];
            
            $valueStatus = 7; 

            // CAMBIAMOS EL ESTATUS DEL ARTICLE
            $updateStatusCodeQR = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
            //echo "<pre>"; print_r($updateStatusCodeQR); exit;

            // VALIDAMOS SI SE ACTUALIZO EL STATUS
            if ($updateStatusCodeQR) {
                 
                // AGREGAMOS UN REGISTRO EN LA TABLA DE HISTORY STATUS
                //$addHistoryStatus = $this->getHistoryStatusService()->addHistoryStatus($formData);
                //echo "<pre>"; print($addHistoryStatus); exit;

                // ENVIO DE CORREO
                $sendEmail = $this->getArticlesService()->sendEMailArticleFound($formData);
                //echo "<pre>"; print_r($sendEmail); exit;

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

            }

            return $response;
            
        }
        
        $articleByCodeQr  = $this->getArticlesService()->getArticleByCodeQr($codeQR);
        //echo "<pre>"; print_r($articleByCodeQr); exit;

        // Validamos el codigo qr
        $validateCodeQR = $this->getCodeQRService()->verifyCodeQrUniqueExists($codeQR);
        //echo "<pre>"; print_r($validateCodeQR); exit;

        // VALIDAMOS SI EL CODIGO EXISTE
        if ($validateCodeQR[0]['count'] == 1) {

            // VALIDAMOS EL TIPO DEL CODIGO QR
            if ($validateCodeQR[0]['type_qr'] == 1) {

                // VALIDAMOS EL ESTATUS Y EL ID DEL ARTICULO
                if ($validateCodeQR[0]['id_status'] == 1 || $validateCodeQR[0]['id_article'] == -1) {
                    // Redirigimos al inicio
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
                }

            } else if ($validateCodeQR[0]['type_qr'] == 2) {

                $urlPet = $this->getRequest()->getBaseUrl() . "/pets/codeqr/" . $codeQR;
                //print_r($urlPet); exit;
                // Redirigimos al inicio
                return $this->redirect()->toUrl($urlPet);
            }

        } else if ($validateCodeQR[0]['count'] == 0) {
            // Redirigimos al inicio
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
        }

        $view->setVariables(array('validateCodeQR' => json_encode($validateCodeQR[0])));

        return $view;
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

    /**
     * RECORDATORIO ARTICULO DE ARTICULO ENCONTRADO
     */
    public function rememberpushnotificationAction()
    {
        // PETICION
        $request = $this->getRequest();

        // VALIDAR PETICION
        if($request->isPost()) {

            $formData             = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;

            // ID NOTIFICATION
            $id_push_notification = $formData['id_push_notification'];

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

            // 1.- CAMBIAMOS EL ESTATUS DEL ARTICULO
            $updateStatusCodeQR = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);
            //echo "<pre>"; print_r($updateStatusCodeQR); exit;

            // VALIDAMOS SI SE ACTUALIZO ESTATUS DE CODIGO
            if($updateStatusCodeQR) {

                // 2.- NECESITAMOS OBTENER LOS DATOS DE LA CUENTA QUE TIENE SESION EN EL PORTAL WEB
                $getProfileSession     = $this->getProfileUser();
                //echo "<pre>"; print_r($getProfileSession); exit;

                // 3.- OBTENEMOS LOS DATOS DEL PERFIL POR ID DE USUARIO
                $getPerfilById        = $this->getUsersService()->getPerfilById($getProfileSession['id']);
                //echo "<pre>"; print_r($getPerfilById); exit;

                // AGREGAMOS LOS DATOS DE LA SESION AL ARREGLO DE LA NOTIFICACION
                $decodePostData['name_p']   = $getPerfilById[0]['name'];
                $decodePostData['email_p']  = $getProfileSession['email'];
                $decodePostData['phone_p']  = $getPerfilById[0]['phone'];
                //echo "<pre>"; print_r($decodePostData); exit;

                // 4.- ENVIO DE CORREO
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

}