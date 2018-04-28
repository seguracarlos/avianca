<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;

class LoginForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'email_user',
            'type' => 'text',
            'attributes' => array(
                'id' => 'email_user',
                'class' => 'validate',
                'data-validetta' => 'required,email',
                'data-vd-message-required' => 'Por favor ingrese su correo'
                //'placeholder' => 'iofractal@iofractal.com'
            ),
            /*'options' => array(
                'label' => 'Email',
            )*/
        ));
        
        $this->add(array(
            'name' => 'password_user',
            'type' => 'password',
            'attributes' => array(
                'id' => 'password_user',
                'class' => 'validate',
                'data-validetta' => 'required'
                //'placeholder' => '**********'
            ),
            /*'options' => array(
                'label' => 'Contraseña',                
            )*/
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'loginCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
        ));
        
        /*$this->add(array(
            'name' => 'btn_login',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Ingresar',
                'class' => 'btn btn-lg btn-success btn-block',
            	'id' => 'btn_login',
            )
        ));*/
    }
}