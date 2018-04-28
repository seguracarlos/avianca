<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class BaseController extends AbstractActionController
{

	private $session;

    /**
     * OBTENEMOS EL CONTENEDOR DE LA SESION
     */
    private function getContainerSession()
    {
        return $this->session = new Container('PegalinasUser');
    }

    /**
     * INDEX ACTION
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * OBTENER DATOS DE LA SESION DEL USUARIO
     */
    public function getProfileUser()
    {
        $data    = array(
            "id"     => $this->getContainerSession()->offsetGet('id'),
            "email"  => $this->getContainerSession()->offsetGet('email'),
        );
        
        return $data;
    }

    /**
     * OBTENER ID DEL USUARIO DE LA SESION
     */
    public function getIdUserSesion()
    {
    	$id = $this->getContainerSession()->offsetGet('id');

    	return $id;
    }

    /**
     * OBTENER PERFIL DEL USUARIO DE LA SESION
     */
    public function getPerfilUserSesion()
    {
        $perfil = $this->getContainerSession()->offsetGet('perfil');

        return $perfil;
    }

    /**
     * OBTENER ESTATUS DE LA CLAVE DE INVENTARIO DE LA SESION
     */
    public function getKeyStatusInventorySesion()
    {
        $perfil = $this->getContainerSession()->offsetGet('key_status');

        return $perfil;
    }

}
