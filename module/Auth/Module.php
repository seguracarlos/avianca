<?php

namespace Auth;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();

        $moduleRouteListener->attach($eventManager);

        /*$eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'boforeDispatch'
        ), 100);*/
        
    }

    function boforeDispatch(MvcEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();
        $target   = $event->getTarget();

        // LISTA DE VISTAS DISPONIBLES SIN SESION
        $whiteList = array(
            'Auth\Controller\Index-index',
            'Auth\Controller\Index-logout',
            'Auth\Controller\Index-login',
            'Auth\Controller\Index-forgot-password',
            'Articles\Controller\Index-codeqr',
            'Articles\Controller\Index-savelastlocation',
            'Users\Controller\Index-register',
            // SERVICIOS ARTICULOS REST
            'Restful\Controller\ArticlesRest-index',
            'Restful\Controller\ArticlesRest-getarticles',
            'Restful\Controller\ArticlesRest-findarticles',
            'Restful\Controller\ArticlesRest-getfoundarticles',
            'Restful\Controller\ArticlesRest-detail',
            'Restful\Controller\ArticlesRest-lastlocation',
            'Restful\Controller\ArticlesRest-savelastlocation',
            'Restful\Controller\ArticlesRest-notificationfoundarticle',
            'Restful\Controller\ArticlesRest-delete',
            'Restful\Controller\ArticlesRest-validatecodeqr',
            'Restful\Controller\ArticlesRest-getallcategoryparent',
            'Restful\Controller\ArticlesRest-getallsubcategorys',
            'Restful\Controller\ArticlesRest-getallcolors',
            'Restful\Controller\ArticlesRest-editarticle',
            'Restful\Controller\ArticlesRest-addarticle',
            'Restful\Controller\ArticlesRest-addarticlefound',
            'Restful\Controller\ArticlesRest-sendemailarticleowner',
            'Restful\Controller\ArticlesRest-updatestatus',
            'Restful\Controller\ArticlesRest-validatekeyinventory',
            'Restful\Controller\ArticlesRest-verifypinuser',
            'Restful\Controller\ArticlesRest-updatereturnarticle',
            'Restful\Controller\ArticlesRest-detailreturnarticle',
            // SERVICIOS CODIGOS QR REST
            'Restful\Controller\CodeQrRest-homecodeqr',
            'Restful\Controller\CodeQrRest-generate',
            'Restful\Controller\CodeQrRest-validatetypecodeqr',
            // SERVICIOS INICIO SESION REST
            'Restful\Controller\LoginRest-auth',
            // SERVICIOS USUARIOS REST
            'Restful\Controller\UsersRest-index',
            'Restful\Controller\UsersRest-addDevice',
            'Restful\Controller\UsersRest-add',
            'Restful\Controller\UsersRest-getperfilbyid',
            'Restful\Controller\UsersRest-edituser',
            'Restful\Controller\UsersRest-changepin',
            'Restful\Controller\UsersRest-changepassword',
            'Restful\Controller\UsersRest-validatepassword',
            'Restful\Controller\UsersRest-changeemail',
            'Restful\Controller\UsersRest-validatesecurepassword',
            'Restful\Controller\UsersRest-changesecurepassword',
            // SERVICIOS MASCOTAS REST
            'Restful\Controller\PetsRest-index',
            'Restful\Controller\PetsRest-getpets',
            'Restful\Controller\PetsRest-addpet',
            'Restful\Controller\PetsRest-editpet',
            'Restful\Controller\PetsRest-detail',
            'Restful\Controller\PetsRest-delete',
            'Restful\Controller\PetsRest-lastlocation',
            'Restful\Controller\PetsRest-savelastlocation',
            'Restful\Controller\PetsRest-validatecodeqr',
            'Restful\Controller\PetsRest-updatestatus',
            'Restful\Controller\PetsRest-sendemailpetowner',
            'Restful\Controller\PetsRest-sendemailcontact',
            'Restful\Controller\PetsRest-generateimglostpetsearch',
            'Restful\Controller\PetsRest-getallpushnotification',
            'Restful\Controller\PetsRest-updatestatusnotification'
        );

        $requestUri        = $request->getRequestUri();
        $controller        = $event->getRouteMatch()->getParam('controller');
        $action            = $event->getRouteMatch()->getParam('action');
        $requestedResourse = $controller . "-" . $action;
        $session           = new Container('PegalinasUser');
        
        $router            = $event->getRouter();
        $uri               = $router->getRequestUri();
        $baseUrl = sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $request->getBaseUrl());

        //print_r($baseUrl); exit;

        //Comprueba que existe la clave en la sesion:
        if ($session->offsetExists('email')) {
            if ($requestedResourse == 'Auth\Controller\Index-index' || in_array($requestedResourse, $whiteList)) {
                
                //$url = '/Osiris/osiris/system/profile/index';
                $url = $request->getBaseUrl() . '/articles';

                $response->setHeaders($response->getHeaders()
                         ->addHeaderLine('Location', $url));
                $response->setStatusCode(302);

            } /*else {
                $serviceManager = $event->getApplication()->getServiceManager();
                $userRole       = $session->offsetGet('roleName');
                $acl            = $serviceManager->get('Acl');
                $acl->initAcl();

                $status = $acl->isAccessAllowed($userRole, $controller, $action);

                if (!$status) {
                    die('Permiso denegado');
                }
            }*/
            
        } else {
            if ($requestedResourse != 'Auth\Controller\Index-index' && ! in_array($requestedResourse, $whiteList)) {
                //$url = '/Osiris/osiris/login';
                $url = $request->getBaseUrl() . '/auth';
                $response->setHeaders($response->getHeaders()
                    ->addHeaderLine('Location', $url));
                $response->setStatusCode(302);
            }
            $response->sendHeaders();
        }
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                //__DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}