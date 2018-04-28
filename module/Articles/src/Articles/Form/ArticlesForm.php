<?php 

namespace Articles\Form;

use Zend\Form\Form;

use Articles\Service\CategoryService;
use Articles\Service\ColorService;
use Articles\Service\ArticlesService;

class ArticlesForm extends Form
{

	public function __construct($name =null){

		parent::__construct($name);

		$this->setAttributes(array(
			'action'  => "",
			'enctype' => 'multipart/form-data',
			'method'  => 'post'
			));
		//$this->setAttribute('enctype','multipart/form-data');

		$this->add(array(
			'name'=>'id',
			'type'=>'Hidden',
		));

		$this->add(array(
			'name'=>'id_register_qr',
			'type'=>'Hidden',
			'attributes' => array(
					'id' => 'id_register_qr'
			)
		));

		$this->add(array(
			'name'=>'imageone',
			'type'=>'File',
			'attributes' => array(
					'id' => 'imageone'
			)
		));

		$this->add(array(
			'name'=>'imagetwo',
			'type'=>'File',
			'attributes' => array(
					'id' => 'imagetwo'
			)
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

		$this->add(array(
				'name' => 'asignated_to',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'asignated_to',
						'class'       => 'validate',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Indica a quien esta asignado"
				)
		));

		// CAMPO DE EVENTO
		$this->add(array(
			'name' => 'event_articles',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'event_articles',
				//'class' => 'validate',
				//'data-validetta' => 'required',
				//'data-vd-message-required' => 'Indica un evento'
			)
		));

		$this->add(array(
				'name' => 'name_article',
				'type' => 'Select',
				'options' => array (
      				'label' => 'Categoria:',
      				'empty_option' => 'Selecciona un artículo',
      				'value_options' => array()//$this->getAllNamearticle()
      		),
				'attributes' => array(
						'id' => 'name_article',
						'class'       => 'validate',
						//'placeholder' => 'Nombre',
						//'required'    => 'required',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Ingresa el artículo"
				)
		));

		$this->add(array(
				'name' => 'brand',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'brand',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Ingresa la marca"
				)
		));

		$this->add(array(
				'name' => 'color2',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'color2',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Ingresa el color"
				)
		));

		$this->add(array(
      		'name' => 'color',
      		'type' => 'Select',
      		'options' => array (
      				'label' => 'Categoria:',
      				'empty_option' => 'Selecciona un color',
      				'value_options' => $this->getAllColor()
      		),
      		'attributes' => array(
      				'id'    => 'color',
      				//'class' => 'form-control input-lg'
      				'data-validetta'           => "required",
					'data-vd-message-required' => "Indica el color"
      		)
		));

		$this->add(array(
				'name' => 'category_2',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'category',
						'class'       => 'validate',
						//'required'    => 'required',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Ingresa la categoría"
				)
		));

		$this->add(array(
      		'name' => 'category',
      		'type' => 'Select',
      		'options' => array (
      				'label' => 'Categoria:',
      				'empty_option' => 'Selecciona una categoría',
      				'value_options' => $this->getAllCategoryParent()
      		),
      		'attributes' => array(
      				'id'    => 'category',
      				'data-validetta'           => "required",
					'data-vd-message-required' => "Indica la categoría"
      		)
		));

		$this->add(array(
      		'name' => 'clothes_size',
      		'type' => 'Select',
      		'options' => array (
      				'label' => 'Talla:',
      				'empty_option' => 'Selecciona una talla',
      				'value_options' => array(1 => 'Chica', 2 => 'Mediana', 3 => 'Grande', 4 => 'Extra Grande')
      		),
      		'attributes' => array(
      				'id'    => 'clothes_size',
      				//'class' => 'browser-default'
      		)
		));


		$this->add(array(
				'name' => 'reward',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'reward',
						'class'       => 'validate',
						//'required'    => 'required',
				)
		));


		$this->add(array(
				'name' => 'model_serie',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'model_serie',
						'class'       => 'validate',
						//'required'    => 'required',
				)
		));

		$this->add(array(
				'name' => 'description',
				'type' => 'Textarea',
				/*'options' => array(
						'label' => 'Nombre o Razon social:',
				),*/
				'attributes' => array(
						'id' => 'description',
						'class'       => 'materialize-textarea',
						//'placeholder' => 'Dirección',
						//'required'    => 'required',
						'data-validetta'           => "required",
						'data-vd-message-required' => "Ingresa una descripción"
				)
		));

		/* estatus */
		$this->add(array(
      		'name' => 'id_status',
      		'type' => 'Select',
      		'options' => array(
      				'label'         => 'Estatus',
      				'empty_option'  => 'Seleccione una opción',
      				'value_options' => array("2" => "Asignado", "3" => "Prestado", "4" => "Extraviado")
      		),
      		'attributes' => array(
      				'id'    => 'id_status',
      				//'class' => 'form-control input-lg'
      		)
		));

		/*
		* CAMPOS PARA MASCOTAS
		*/

		// IMAGEN DE MASCOTAS
		$this->add(array(
			'name'=>'img_pet',
			'type'=>'File',
			'attributes' => array(
					'id' => 'img_pet'
			)
		));

		// TIPO DE MASCOTA
		$this->add(array(
      		'name' => 'type_pet',
      		'type' => 'Select',
      		'options' => array(
      				'label'         => 'Tipo de mascota',
      				'empty_option'  => 'Seleccione una opción',
      				'value_options' => array("1" => "Perro", "2" => "Gato")
      		),
      		'attributes' => array(
      				'id'    => 'type_pet'
      		)
		));

		// NOMBRE DE MASCOTA
		$this->add(array(
			'name' => 'name_pet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'name_pet',
				'class' => 'validate',
				'data-validetta' => 'required',
				'data-vd-message-required' => 'Indica un nombre'
			)
		));

		// RAZA DE MASCOTA
		$this->add(array(
			'name' => 'breed_pet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'breed_pet',
				'class' => 'validate',
				'data-validetta' => 'required',
				'data-vd-message-required' => 'Indica una raza'
			)
		));

		// COLOR DE MASCOTA
		$this->add(array(
			'name' => 'color_pet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'color_pet',
				'class' => 'validate',
				'data-validetta' => 'required',
				'data-vd-message-required' => 'Indica un color'
			)
		));

		// TAMANO DE MASCOTA
		$this->add(array(
			'name' => 'size_pet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'size_pet',
				'class' => 'validate',
				'data-validetta' => 'required',
				'data-vd-message-required' => 'Indica un Tamaño'
			)
		));

		// EDAD DE MASCOTA
		$this->add(array(
			'name' => 'age_pet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'age_pet',
				'class' => 'validate',
				'data-validetta' => 'required',
				'data-vd-message-required' => 'Indica una edad'
			)
		));

		// DESCRIPCION DE MASCOTA
		$this->add(array(
			'name' => 'description_pet',
			'type' => 'Textarea',
			'attributes' => array(
					'id' => 'description_pet',
					'class'       => 'materialize-textarea',
					//'placeholder' => 'Dirección',
					//'required'    => 'required',
					'data-validetta'           => "required",
					'data-vd-message-required' => "Ingresa una descripción"
			)
		));

		// CUMPLEANOS DE MASCOTA
		$this->add(array(
			'name' => 'birthday_pet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'birthday_pet',
				//'class' => 'validate',
				//'data-validetta' => 'required',
				//'data-vd-message-required' => 'Indica una fecha de cumpleaños'
			)
		));

		// N°. Microchip
		$this->add(array(
			'name' => 'microchip_pet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'microchip_pet',
				//'class' => 'validate',
				//'data-validetta' => 'required',
				//'data-vd-message-required' => 'Indica una n° de microchip'
			)
		));


		// NOMBRE DE VETERINARIO
		$this->add(array(
			'name' => 'name_vet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'name_vet',
				//'class' => 'validate',
				//'data-validetta' => 'required',
				//'data-vd-message-required' => 'Indica un nombre de veterinario'
			)
		));

		// TELEFONO DE VETERINARIO
		$this->add(array(
			'name' => 'phone_vet',
			'type' => 'Text',
			'attributes' => array(
				'id' => 'phone_vet',
				//'class' => 'validate',
				//'data-validetta' => 'required',
				//'data-vd-message-required' => 'Indica un telefono de veterinario'
			)
		));

	}

	// Funcion que obtiene todas las categorias padres
	public function getAllCategoryParent()
	{
		$categoryService = new CategoryService();
     	$categorys       = $categoryService->getAllCategoryParent();
     	$result          = array();
     	//echo "<pre>"; print_r($categorys); exit;
     	foreach ($categorys as $cate){
     		$result[$cate['id']] = $cate['namecategory'];
     	}
     	
     	return $result;
	}

	// Funcion que obtiene todos los colores
	public function getAllColor()
	{
		$colorService = new ColorService();
     	$colors       = $colorService->getAllColor();
     	$result          = array();
     	//echo "<pre>"; print_r($categorys); exit;
     	foreach ($colors as $col){
     		$result[$col['id']] = $col['name'];
     	}
     	
     	return $result;
	}

	// Funcion que obtiene todos los artículos
	public function getAllNamearticle()
	{
		$namearticleService = new ArticlesService();
     	$namearticle       = $namearticleService->getAllNamearticle();
     	$result          = array();
     	//echo "<pre>"; print_r($categorys); exit;
     	foreach ($namearticle as $col){
     		$result[$col['id']] = $col['name_article'];
     	}
     	
     	return $result;
	}

}
