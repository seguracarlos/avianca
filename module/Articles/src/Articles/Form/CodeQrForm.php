<?php 

namespace Articles\Form;

use Zend\Form\Form;

class CodeQrForm extends Form
{

	public function __construct($name =null){

		parent::__construct($name);

		$this->setAttributes(array(
			'action'=>"",
			'method'=> 'post'
			));
		
		$this->add(array(
				'name' => 'code_article',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'code_article',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Ingresa el código"
				)
		));

		$this->add(array(
				'name' => 'repeat_code_article',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'repeat_code_article',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Vuelve a ingresa el código"
				)
		));

	}
}
