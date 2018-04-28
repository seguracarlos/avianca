<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// Formulario de login
use Auth\Form\LoginForm;
use Auth\Service\LoginService;
use Users\Service\UsersService;
use Application\Controller\BaseController;

class IndexController extends AbstractActionController
{

	private $loginService;
	private $usersServices;

	/**
	 * Instanciamos el servicio de login
	 */
	public function getLoginService()
	{
		return $this->loginService = new LoginService();
	}

	 // Instanciamos servicio de usuarios
    public function getUsersServices()
    {
        return $this->usersServices = new UsersService();
    }


     public function faqAction()
	{
		// Indicamos layout
		$layout    = $this->layout();
		$layout->setTemplate('layout/layoutAuth');
	}

	public function faqpetAction()
	{
		// Indicamos layout
		$layout    = $this->layout();
		$layout->setTemplate('layout/layoutPets');
	}


    public function loginAction()
	{
		// Indicamos layout
		$layout    = $this->layout();
		$layout->setTemplate('layout/layoutAuth');

		// Peticion
		$request   = $this->getRequest();
		// Vista
		$view      = new ViewModel();
		// Formulario
		$loginForm = new LoginForm('loginForm');

		// Validamos el tipo de peticion
		if ($request->isPost()) {

			// Obtenemos los datos que vienen por post
			$data = $request->getPost()->toArray();
			//echo "<pre>"; print_r($data); exit;

			// Lamamos al metodo de autenticacion
			$authentication = $this->getLoginService()->authenticationUser($data);

			// Validamos autenticacion
			/*if ($authentication['code'] == 1) {
				return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/users/index');
			}*/

			$response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
				//"response" => "ok", 
				//"tries" => $try, 
				"data" => $authentication, 
				//"status" => "Correcto", 
				//"data" => $dataView
			)));
			
			return $response;

		}

		// Variables que se pasan a la vista
		$view->setVariable('loginForm', $loginForm);
		
		return $view;
	}


	//inicio de sesion para mascotas
	public function loginpetsAction()
	{
		// Indicamos layout
		$layout    = $this->layout();
		$layout->setTemplate('layout/layoutPets');

		// Peticion
		$request   = $this->getRequest();
		// Vista
		$view      = new ViewModel();
		// Formulario
		$loginForm = new LoginForm('loginForm');

		// Validamos el tipo de peticion
		if ($request->isPost()) {

			// Obtenemos los datos que vienen por post
			$data = $request->getPost()->toArray();
			//echo "<pre>"; print_r($data); exit;

			// Lamamos al metodo de autenticacion
			$authentication = $this->getLoginService()->authenticationUser($data);

			// Validamos autenticacion
			/*if ($authentication['code'] == 1) {
				return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/users/index');
			}*/

			$response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
				//"response" => "ok", 
				//"tries" => $try, 
				"data" => $authentication, 
				//"status" => "Correcto", 
				//"data" => $dataView
			)));
			
			return $response;

		}

		// Variables que se pasan a la vista
		$view->setVariable('loginForm', $loginForm);
		
		return $view;
	}

	
	public function indexAction()
	{
		// Indicamos layout
		$layout    = $this->layout();
		$layout->setTemplate('layout/layoutAuth');

		// Peticion
		$request   = $this->getRequest();
		// Vista
		$view      = new ViewModel();
		// Formulario
		$loginForm = new LoginForm('loginForm');

		// Validamos el tipo de peticion
		if ($request->isPost()) {

			// Obtenemos los datos que vienen por post
			$data = $request->getPost()->toArray();
			//echo "<pre>"; print_r($data); exit;

			// Lamamos al metodo de autenticacion
			$authentication = $this->getLoginService()->authenticationUser($data);

			// Validamos autenticacion
			/*if ($authentication['code'] == 1) {
				return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/users/index');
			}*/

			$response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
				//"response" => "ok", 
				//"tries" => $try, 
				"data" => $authentication, 
				//"status" => "Correcto", 
				//"data" => $dataView
			)));
			
			return $response;

		}

		// Variables que se pasan a la vista
		$view->setVariable('loginForm', $loginForm);
		
		return $view;
	}

	/**
	 * CERRAR SESION
	 */
	public function logoutAction()
	{
		// Llamamos al metodo cerrar sesion
		$logoutUser = $this->getLoginService()->logoutUser();
        
        // Redireccionamos al inicio de sesion
        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/auth');
    }

	public function registerAction()
	{
		$layout    = $this->layout(); // Indicamos layout
		$layout->setTemplate('layout/layoutAuth');
	}

	public function forgotPasswordAction()
	{
		$layout    = $this->layout(); // Indicamos layout
		$layout->setTemplate('layout/layoutAuth');
		
	}



	  /*
** METODO PARA ENVIAR MAIL DE CONTACTO
*/
      public function contactAction(){
        $view    = new ViewModel();
        $view->setTerminal(true);

        // REQUEST
        $request = $this->getRequest();

        // VALIDAMOS EL TIPO DE PETICION
        if ($request->isPost()) {
            
            // DATOS RECIBIDOS POR POST
            $formData = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;
            $sendEmail = $this->getLoginService()->sendEMailContact($formData);
                //echo "<pre>"; print_r($sendEmail); exit;

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));
            }

            return $response;

        }




    /*
** METODO PARA VALIDAR EMAIL
*/
      public function restorepasswordAction(){
         $layout    = $this->layout(); // Indicamos layout
        $layout->setTemplate('layout/layoutAuth');

        // REQUEST
        $request    = $this->getRequest();
        if($request->isPost()){

            $formData   = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;
            // Verrificamos si el correo existe
            $verifyEmail = $this->getLoginService()->verifyEmailExists($formData['email_user']);
            //echo "<pre>"; print_r($verifyEmail); exit;
            // Validamos si existe o no el correo
            if ($verifyEmail[0]['count'] == 0) {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            } else if ($verifyEmail[0]['count'] == 1 && $verifyEmail[0]['id'] != 0) {

            	 // ENVIO DE CORREO

            	$verifyEmail[0]['id'];
            	//echo "<pre>"; print_r($verifyEmail[0]['id']); exit;

            	 $cadena = rand(1,9999999).date('Y-m-d H:i:s');
                 $token = sha1($cadena);
                 
               
                 $formData['token']=$token;
                 $formData['id']=$verifyEmail[0]['id'];
                 
                // $formData['token_created']=$date('Y-m-d H:i:s');
               //  echo "<pre>"; print_r($url); exit;
                 $addToken = $this->getLoginService()->addToken($formData);
                  //echo "<pre>"; print_r($addToken); exit;
                $sendEmail = $this->getLoginService()->sendEMailRestorePassword($formData,$token);
                //echo "<pre>"; print_r($sendEmail); exit;

                //echo "<pre>"; print_r($addUser); exit;

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok'
                   // "name"  => $formData['name_user']
                )));
            };

            return $response;

        }

        return new ViewModel($view);
    }



public function passwordrecoveryAction()
	{
		$layout    = $this->layout(); // Indicamos layout
		$layout->setTemplate('layout/layoutAuth');
		$request    = $this->getRequest();


		$tokenExisting = $this->params()->fromRoute("id",null);

		if($request->isPost()) {
        	//echo "hola"; exit;
        	$formData = $request->getPost()->toArray();
           //echo "<pre>"; print_r($formData); exit;
            // Validar clave de usuario
            $updateKeyPass = $this->getUsersServices()->updateKeyPass($formData['change_pass'], $formData['value_id_user']);

             $deleteToken = $this->getLoginService()->deleteToken($formData['value_id_user']);
             //echo "<pre>"; print_r($deleteToken); exit;

             $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));

             return $response;

        }

		// Validar clave de usuario
        $verifyToken = $this->getLoginService()->verifyToken($tokenExisting);

           // print_r($verifyToken); exit;
        if ($verifyToken[0]['count'] == 0) {
        	//print_r("hola no existo"); exit;
        	return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/auth');
        };



         return new ViewModel(array('id_user' => $verifyToken[0]['id'],'token'=> $tokenExisting));

	}


	/**
     * index de mascotas
     */
    public function indexpetsAction()
    {
        $layout    = $this->layout(); // Indicamos layout
		$layout->setTemplate('layout/layoutPets');
		$request    = $this->getRequest();
	
    }

     public function communityAction()
	{
		// Indicamos layout
		$layout    = $this->layout();
		$layout->setTemplate('layout/layoutAuth');
	}

	 public function communitypetsAction()
	{
		// Indicamos layout
		$layout    = $this->layout();
		$layout->setTemplate('layout/layoutPets');
	}


}