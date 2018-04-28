<?php 

namespace Users\Form;

use Zend\Form\Form;

class PerfilForm extends Form
{
	
	public function __construct($name = null){

		parent::__construct($name);
		
		$this->setAttributes(array(
				'action'=>"",
				'enctype' => 'multipart/form-data',
				'method' => 'post'
		));

		$this->add(array(
				'name' => 'id',
				'type' => 'Hidden',
		));

	

		$this->add(array(
				'name' => 'name',
				'type' => 'Text',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'name',
						'class'       => 'validate',
						'maxlength' =>'50',	
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese su nombre!"
				)
		));

			$this->add(array(
				'name' => 'campus',
				'type' => 'text',
				'attributes' => array(
						'id' => 'campus',
						'class'       => 'validate',
						'maxlength' =>'200',	
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese un campus o sucursal!"
				)
		));

		$this->add(array(
				'name' => 'surname',
				'type' => 'text',
				'attributes' => array(
						'id' => 'surname',
						'class'       => 'validate',
						'maxlength' =>'200',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese sus apellidos!"
				)
		));

		$this->add(array(
				'name' => 'phone',
				'type' => 'text',
				'attributes' => array(
						'id' => 'phone',
						'class'       => 'validate',
						'maxlength' =>'13',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese su numero telefonico"
				)
		));

		$this->add(array(
				'name' => 'addres',
				'type' => 'text',
				'attributes' => array(
						'id' => 'addres',
						'class'       => 'validate',
						'maxlength' =>'500',
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese su dirección"
				)
		));

		

		$this->add(array(
				'name' => 'pin',
				'type' => 'text',
				'attributes' => array(
						'id' => 'pin',
						'class'       => 'validate',
						//'maxlength' =>'4',						
						
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese tu pin"
				)
		));

		$this->add(array(
				'name' => 'key_inventory',
				'type' => 'text',
				'attributes' => array(
						'id' => 'pin',
						'class'       => 'validate',
												
						
						//'required'    => 'required',
						'data-validetta' => 'required',
						'data-vd-message-required'=>"Por favor ingrese tu contraseña para el inventario"
				)
		));


		$this->add(array(
			'name'=>'image',
			'type'=>'File',
			'attributes' => array(
					'id' => 'image'
			)
		));

		$this->add(array(
				'name' => 'Submit',
				'type' => 'Submit',
				'attributes' => array(
						'value' => 'Guardar',
						'id'    => 'Submit',
						'class' => 'waves-effect waves-light btn blue col s12 m12 l12',
				),
		));

	}

}