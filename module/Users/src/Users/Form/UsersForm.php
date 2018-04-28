<?php 

namespace Users\Form;

use Zend\Form\Form;

class UsersForm extends Form
{
	
	public function __construct($name = null){

		parent::__construct($name);
		
		$this->setAttributes(array(
				'action'=>"",
				'method' => 'post'
		));

		$this->add(array(
				'name' => 'id',
				'type' => 'Hidden',
		));

		$this->add(array(
				'name' => 'name_user',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'name_user',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese su nombre!"
				)
		));

		/*$this->add(array(
				'name' => 'name_company',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
		/*		'attributes' => array(
						'id' => 'name_company',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese el nombre la empresa o institución!"
				)
		));*/


		$this->add(array(
				'name' => 'surname',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'surname',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese su apellido!"
				)
		));

		$this->add(array(
				'name' => 'campus',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'campus',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese su campus!"
				)
		));

		// campo brand para proveedor

		$this->add(array(
				'name' => 'brand',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'brand',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese su campus!"
				)
		));

		$this->add(array(
				'name' => 'rfc',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'rfc',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese su campus!"
				)
		));

		$this->add(array(
				'name' => 'website',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'website',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese su campus!"
				)
		));

		$this->add(array(
				'name' => 'name_bank',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'name_bank',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese su campus!"
				)
		));

		$this->add(array(
				'name' => 'number_acount',
				'type' => 'number',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'number_acount',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese su campus!"
				)
		));

		$this->add(array(
				'name' => 'interestingin',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'interestingin',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese su campus!"
				)
		));

		$this->add(array(
				'name' => 'email_user',
				'type' => 'text',
				'attributes' => array(
						'id' => 'email_user',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta' => 'required,email',
						'data-vd-message-required'=>"Por favor ingrese su correo!"
				)
		));

		$this->add(array(
				'name' => 'repeat_email_user',
				'type' => 'text',
				'attributes' => array(
						'id' => 'repeat_email_user',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta' => 'required,email',
						'data-vd-message-required'=>"Por favor ingrese nuevamente el correo!"
				)
		));

		$this->add(array(
				'name' => 'password_user',
				'type' => 'password',
				'attributes' => array(
						'id' => 'password_user',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese una contraseña"
				)
		));

		$this->add(array(
				'name' => 'repeat_password_user',
				'type' => 'password',
				'attributes' => array(
						'id' => 'repeat_password_user',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese nuevamente la contraseña"
				)
		));

		$this->add(array(
				'name' => 'key_inventory',
				'type' => 'password',
				'attributes' => array(
						'id' => 'key_inventory',
						'class'       => 'validate',
						//'required'    => 'required',
						//'data-validetta' => 'required',
						//'data-vd-message-required'=>"Por favor ingrese una contraseña"
				)
		));

		$this->add(array(
				'name' => 'pin',
				'type' => 'password',
				'attributes' => array(
						'id' => 'pin',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese su pin"
				)
		));

		$this->add(array(
				'name' => 'phone',
				'type' => 'number',
				'attributes' => array(
						'id' => 'phone',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese su teléfono"
				)
		));

		$this->add(array(
				'name' => 'btn_register',
				'type' => 'Submit',
				'attributes' => array(
						'value' => 'Regístrate ahora',
						'id'    => 'btn_register',
						'class' => 'waves-effect waves-light btn blue col s12 m12 l12',
				),
		));

	}

}