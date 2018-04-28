<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Users\Form\UsersForm;
use Users\Form\PerfilForm;
use Users\Service\UsersService;
use Application\Controller\BaseController;

class IndexController extends BaseController
{
    private $usersServices;
    
    // Instanciamos servicio de companias
    public function getUsersServices()
    {
        return $this->usersServices = new UsersService();
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function registerAction()
    {
        $layout    = $this->layout(); // Indicamos layout
        $layout->setTemplate('layout/layoutAuth');

        $form    = new UsersForm("users");
        $view    = array("form" => $form);
        $request = $this->getRequest();

        if($request->isPost()){

            $formData   = $request->getPost()->toArray();
            //echo "<pre>"; print_r($formData); exit;
            // Verrificamos si el correo existe
            $verifyEmail = $this->getUsersServices()->verifyEmailExists($formData['email_user']);
            //echo "<pre>"; print_r($verifyEmail); exit;
            // Validamos si existe o no el correo
            if ($verifyEmail[0]['count'] == 1) {

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'fail'
                )));

            } else if ($verifyEmail[0]['count'] == 0) {

                $addUser = $this->getUsersServices()->addUsers($formData);
                //$addCompany = $this->getUsersServices()->addCompany($formData);

               //echo "<pre>"; print_r($addUser); exit;

                $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                    "name"  => $formData['name_user']
                )));
            };

            return $response;
            //echo "<pre>"; print_r($verifyEmail); exit;

            //if($addUser){
            //    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
            //}

        }

        return new ViewModel($view);
    }

    public function perfilAction()
    {
        // ID DE USUARIO DE SESION
        $idUser      = (int) $this->getIdUserSesion();

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
            //print_r($formData); exit;
            // Validar clave de usuario
            $verifyKeyPass = $this->getUsersServices()->verifyKeyPass($formData['password'], $idUser);

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "password" => $verifyKeyPass,
                "perfil_user" => $perfilUser
            )));
            return $response;
        }

        // VALIDAMOS EL TIPO DE USURIO
        if ($perfilUser == 2 && $key_status == 0) {
            
            // Redirigimos a articulos encontrados
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/articles/findarticles');

        }

        // PERFIL
        $perfil      = $this->getUsersServices()->getAll($idUser);

        $view = array(
            'perfil'        => $perfil[0],
            'type_perfil'   => $perfilUser
        );

        return new ViewModel($view);
    }

/*
** METODO PARA ACTUALIZAR CONTRASEÑA DE SESION
*/
     public function updatepassAction()
    {
        $request    = $this->getRequest();
        $idUser = (int) $this->getIdUserSesion();
        // Validamos el tipo de peticion
        if($request->isPost()) {
            //Datos que llega por post
            $formData = $request->getPost()->toArray();
           // print_r($formData); exit;
            // Validar clave de usuario
            $updateKeyPass = $this->getUsersServices()->updateKeyPass($formData['change_pass'], $idUser);

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
               "status" => 'ok'
               // "change_pass" => $updateKeyPass,
                //"perfil_user" => $perfilUser
            )));
            return $response;
        }
        return new ViewModel();
    }


    /*
** METODO PARA ACTUALIZAR EMAIL
*/
     public function updateemailAction()
    {
        $request    = $this->getRequest();
        $idUser = (int) $this->getIdUserSesion();
        // Validamos el tipo de peticion
        if($request->isPost()) {
            //Datos que llega por post
            $formData = $request->getPost()->toArray();
           // print_r($formData); exit;
            // Validar clave de usuario
            $updateEmail = $this->getUsersServices()->updateKeyEmail($formData['change_email'], $idUser);

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
               "status" => 'ok'
               // "change_pass" => $updateKeyPass,
                //"perfil_user" => $perfilUser
            )));
            return $response;
        }
        return new ViewModel();
    }


/*
** METODO PARA VALIDAR CONTRASEÑA DE INVENTARIO
*/
      public function perfilinventoryAction()
    {
        // ID DE USUARIO DE SESION
       $idUser = (int) $this->getIdUserSesion();
       // TIPO DE USUARIO
       $perfilUser = (int) $this->getPerfilUserSesion();
        $perfil = $this->getUsersServices()->getAll($idUser);
//print_r($perfil); exit;
        // REQUEST
        $request    = $this->getRequest();
        // Validamos el tipo de peticion
        if($request->isPost()) {
            //Datos que llega por post
            $formData = $request->getPost()->toArray();
            //print_r($formData); exit;
            // Validar clave de usuario
            $verifyKeyPassinventory = $this->getUsersServices()->verifyKeyPassinventory($formData['password_inventory'], $idUser);

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "password_inventory" => $verifyKeyPassinventory,
                "perfil_user" => $perfilUser
            )));
            return $response;
        }

 //$view = array('perfil' => $perfil[0],'type_perfil'=>$perfilUser);
        return new ViewModel($view);
    }


/*
** METODO PARA VALIDAR PIN
*/
      public function pinAction()
    {
        // ID DE USUARIO DE SESION
       $idUser = (int) $this->getIdUserSesion();
       // TIPO DE USUARIO
       $perfilUser = (int) $this->getPerfilUserSesion();
        $perfil = $this->getUsersServices()->getAll($idUser);
//print_r($perfil); exit;
        // REQUEST
        $request    = $this->getRequest();
        // Validamos el tipo de peticion
        if($request->isPost()) {
            //Datos que llega por post
            $formData = $request->getPost()->toArray();
            //print_r($formData); exit;
            // Validar clave de usuario
            $verifyKeyPin = $this->getUsersServices()->verifyKeyPin($formData['pin'], $idUser);

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "pin" => $verifyKeyPin,
                "perfil_user" => $perfilUser
            )));
            return $response;
        }

 //$view = array('perfil' => $perfil[0],'type_perfil'=>$perfilUser);
        return new ViewModel($view);
    }



/*
** METODO PARA ACTUALIZAR PIN
*/
     public function updatepinAction()
    {
        $request    = $this->getRequest();
        $idUser = (int) $this->getIdUserSesion();
        // Validamos el tipo de peticion
        if($request->isPost()) {
            //Datos que llega por post
            $formData = $request->getPost()->toArray();
           // print_r($formData); exit;
            // Validar pin de usuario
            $updateKeyPin = $this->getUsersServices()->updateKeyPin($formData['change_pin'], $idUser);
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
               "status" => 'ok'
            )));
            return $response;
        }
        return new ViewModel();
    }



    /*
** METODO PARA ACTUALIZAR CONTRASEÑA DE INVENTARIO
*/
     public function updatepassinventoryAction()
    {
        $request    = $this->getRequest();
        $idUser = (int) $this->getIdUserSesion();
        // Validamos el tipo de peticion
        if($request->isPost()) {
            //Datos que llega por post
            $formData = $request->getPost()->toArray();
           // print_r($formData); exit;
            // Validar clave de usuario
            $updateKeyPassinventory = $this->getUsersServices()->updateKeyPassinventory($formData['change_passinventory'], $idUser);

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
               "status" => 'ok'
            )));
            return $response;
        }
        return new ViewModel();
    }


    public function editperfilAction()
    {
         $perfilUser = (int) $this->getPerfilUserSesion();
        $id      = (int) $this->params()->fromRoute("id",null);
        
        $form    = new PerfilForm("formperfil");

        $perfil = $this->getUsersServices()->getPerfilById($id);
       //echo "<pre>"; print_r($perfil); exit;
        //$perfil['surname'] = $perfil['name']
       $form->setData($perfil[0]);


        $view    = array("form" => $form, "image" => $perfil[0]['image'],'perfil' => $perfil[0],'type_perfil'=>$perfilUser);
        //echo "<pre>"; print_r($view); exit;
        $request = $this->getRequest();
       // echo "<pre>"; print_r($request); exit;
        if($request->isPost()){

             $formData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
             
           // $formData   = $request->getPost()->toArray();
            
            $editPerfil = $this->getUsersServices()->editPerfil($formData);
            
            if($editPerfil){
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/users/perfil');
            }else{
                return $this->redirect()->refresh();
            }
        }
        
        return new ViewModel($view);

    }


      /**
     * OBTENER ESTATUS DE LA KEY INVENTORY
     */
     public function statuskeypassAction()
    {

        // REQUEST
        $request    = $this->getRequest();

        // ESTATUS DE LA CLAVE DE INVENTARIO
        $key_pass = (int) $this->verifyKeyPass();

        // TIPO DE USUARIO
        $perfilUser = (int) $this->getPerfilUserSesion();

        // Validamos el tipo de peticion
        if($request->isPost()) {

            // RESPUESTA
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                "pass_key"  => $key_pass,
                "perfil_user" => $perfilUser
            )));

            return $response;
        }

        exit;

    }



       /*
** METODO PARA VALIDAR EMAIL
*/
      public function restorkeyinventoAction(){

        $request    = $this->getRequest();
        $idUser = (int) $this->getIdUserSesion();
        $perfilUser = (int) $this->getPerfilUserSesion();
        $perfil = $this->getUsersServices()->getAll($idUser);
        //print_r($perfil); exit;   
       
       // $user_email = $perfil['email'];
//print_r($user_email); exit;
        $key_inventory=substr(md5(uniqid()), 0, 8);
         $email_user = array('email_p'=>$perfil[0]['email'],'key_inventory'=>$key_inventory);
        
       // print_r($key_inventory); exit;

        // ENVIO DE CORREO
        $sendEmail = $this->getUsersServices()->sendEMailkeyinvento($email_user);
        $response = $this->getResponse()->setContent(\Zend\Json\Json::encode(array(
                    "status" => 'ok',
                )));
        
        //ACTUALIZAMOS LA CLAVE
        $updatekey_inventory = $this->getUsersServices()->updatekey_inventory($email_user['key_inventory'], $idUser);

//print_r($email_user); exit;
        return $response;
       
    }


}