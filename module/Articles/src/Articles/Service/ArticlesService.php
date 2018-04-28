<?php

namespace Articles\Service;

use Articles\Model\ArticlesModel;
use Articles\Model\DeviceUserModel;
use Articles\Service\CodeQRService;
use Articles\Service\HistoryStatusService;
use Articles\Service\ReturnsService;

use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;

class ArticlesService
{
	private $articlesModel;
	private $codeQRService;
	private $historyStatusService;
	private $deviceModel;
	private $returnsService;

	private function getArticlesModel()
	{
		return $this->articlesModel = new ArticlesModel();
	}

	private function getDeviceUserModel(){
		return $this->deviceModel = new DeviceUserModel();
	}

	private function getCodeQRService()
	{
		return $this->codeQRService = new CodeQRService();
	}

	private function getHistoryStatusService()
	{
		return $this->historyStatusService = new HistoryStatusService();
	}

	/**
	 * SERVICIO DE DEVOLUCIONES
	 */
	private function getReturnsService()
	{
		return $this->returnsService = new ReturnsService();
	}

	/**
	 * BASE64 A IMAGEN
	 */
	public function base64ToImagePath()
	{
		// OBTENEMOS LOS DATOS DE LA BASE
		$stringBase64 = $this->getArticlesModel()->base64ToImagePath();
		//echo "<pre>"; print_r($stringBase64); exit;

		// RESULTADO
		$dataResult = array();

		// RECORREMOS LOS DATOS OBTENIDOS
		foreach ($stringBase64 as $key => $value) {

			if($value['image_name'] == "") {

				// VALIDAMOS SI EXISTE EL BAE64
				if($value['imageone'] != "" || $value['imageone'] != null) {
				
				// CONVERTIMOS EL IMAGE BASE64 A IMAGEN
				$base64ToImg = $this->createImgArticleBase64($value['imageone'], $value['id_register_qr']);

				// almacenar nombres de imagenes
				array_push($dataResult, array("img_name" => $base64ToImg));

				// ARRAY CON LOS DATOS A ACTUALIZAR
				$dataUpdate = array(
					"id" 			=> $value['id'],
					"image_name"	=> $base64ToImg
				);

				// ACTUALIZAMOS EL NOMBRE DE LA IMAGEN EN LA BASE DE DATOS
				$updateImageName = $this->getArticlesModel()->editArticles($dataUpdate);

				}

			} else {
				// almacenar nombres de imagenes
				array_push($dataResult, array("img_name" => "No hay imagenes en base64"));
			}

		}
		//echo "<pre>"; print_r($dataResult); exit;

		return $dataResult;
	}

	/**
	 * BASE64 A IMAGEN
	 */
	public function base64ToImagePathTwo()
	{
		// OBTENEMOS LOS DATOS DE LA BASE
		$stringBase64 = $this->getArticlesModel()->base64ToImagePathTwo();
		//echo "<pre>"; print_r($stringBase64); exit;
		// RESULTADO
		$dataResult = array();

		// RECORREMOS LOS DATOS OBTENIDOS
		foreach ($stringBase64 as $key => $value) {

			if($value['image_name_two'] == "") {

				// VALIDAMOS SI EXISTE EL BAE64
				if($value['imagetwo'] != "" || $value['imagetwo'] != null) {
				
				// CONVERTIMOS EL IMAGE BASE64 A IMAGEN
				$base64ToImg = $this->createImgArticleBase64Two($value['imagetwo'], $value['id_register_qr']);

				// almacenar nombres de imagenes
				array_push($dataResult, array("img_name_two" => $base64ToImg));

				// ARRAY CON LOS DATOS A ACTUALIZAR
				$dataUpdate = array(
					"id" 				=> $value['id'],
					"image_name_two"	=> $base64ToImg
				);

				// ACTUALIZAMOS EL NOMBRE DE LA IMAGEN EN LA BASE DE DATOS
				$updateImageName = $this->getArticlesModel()->editArticles($dataUpdate);

				}

			} else {
				// almacenar nombres de imagenes
				array_push($dataResult, array("img_name_two" => "No hay imagenes en base64"));
			}

		}
		//echo "<pre>"; print_r($dataResult); exit;

		return $dataResult;
	}

	/**
	 * OBTEMOS TODOS LOS NOMBRES DEL ARTÍCULO
	 */
	public function getAllNamearticle()
	{
		$namearticle = $this->getArticlesModel()->getAllNamearticle();

		return $namearticle;
	}

	/**
	 * OBTEMOS TODOS LOS ARTICULOS POR ID DE USUARIO
	 */
	public function getAll($idUser, $typePerfil = null)
	{
		$articles = $this->getArticlesModel()->getAll($idUser, $typePerfil);

		return $articles;
	}

	/**
	 * OBTEMOS TODOS LOS ARTICULOS ENCONTRADOS POR ID DE USUARIO
	 */
	public function getAllArticlesFoundByIdUser($idUser)
	{
		$articlesFound = $this->getArticlesModel()->getAllArticlesFoundByIdUser($idUser);

		return $articlesFound;
	}

	/**
	 * OBTEMOS TODOS LOS ARTICULOS ENCONTRADOS CON UN STATUS DE 7
	 */
	public function getAllArticlesFoundStatusSeven($idUser)
	{
		$articlesFoundStatusSeven = $this->getArticlesModel()->getAllArticlesFoundStatusSeven($idUser);

		return $articlesFoundStatusSeven;
	}

	/**
	 * OBTENER ARTICULO POR ID
	 */
	public function getArticleById($id)
	{

		$articles = $this->getArticlesModel()->getArticleById($id);
		$result   = array();

		// VALIDAMOS SI EXISTE LA POSICION 0 EN EL ARRAY
		if(isset($articles[0])){

			$articles[0]['repeat_code_article'] = $articles[0]['code_article'];

			// Resultado
			$result = $articles[0];

		}
		
		return $result;
	}

	/**
	 * OBTENER ARTICULO POR CODIGO QR
	 */
	public function getArticleByCodeQr($codeQR)
	{

		$articleByCodeQr = $this->getArticlesModel()->getArticleByCodeQr($codeQR);
		//echo "<pre>"; print_r($articleByCodeQr); exit;
		
		/*if(isset($articles[0])){
			$articles[0]['repeat_code_article'] = $articles[0]['code_article'];
		}*/

		return $articleByCodeQr;
	}

	/**
	 * METODO PARA VALIDAR LA CLAVE DEL INVENTARIO
	 */
	public function verifyKeyInventoryArticles($key, $idUser)
	{
		//print_r($key); exit;
		// Encriptamos la contraseña
		$securityPass = $this->bcryptPassSecurity($key);
		//echo "<pre>"; print_r($securityPass); exit;

		$verifyKeyInventory = $this->getArticlesModel()->verifyKeyInventoryArticles($securityPass, $idUser);
		//echo "<pre>"; print_r($verifyKeyInventory[0]['count']); exit;

		return $verifyKeyInventory;
	}

	/**
	 * AGREGAR UN VALOR A LA SESION
	 */
	public function addKeyInventorySession($statusKey)
	{
		// SESION DE PEGALINAS
		$session = new Container('PegalinasUser');

		$session->offsetSet('key_status', $statusKey);

		return $session->offsetGet('key_status');
	}

	/**
	 * DELETE UN VALOR A LA SESION
	 */
	public function deleteKeyInventorySession()
	{
		// SESION DE PEGALINAS
		$session = new Container('PegalinasUser');

		$session->offsetUnset('key_status');

		return 0;//$session->offsetGet('key_status');
	}

	/**
	 * OBTENER NOMBRE DE UNA IMAGEN
	 */
	private function getNameImageArticle($idQr)
	{
		$nameImage = $this->getArticlesModel()->getNameImageArticle($idQr);

		return $nameImage;
	}

	/**
	 * GENERAR IMAGEN DE ARTICULO
	 */
	private function redimensionarImagenArticulo($img_article, $id_code_qr = null, $no_image = null)
	{
		// Ruta donde se almacenaran las imagenes
		$path_images	= "./public/images/articles/";

		// OBTENEMOS EL NOMBRE ACTUAL DE LA IMAGEN
		$nameImgCurrent	= $this->getNameImageArticle($id_code_qr);
		//echo "<pre>"; print_r($nameImgCurrent); exit;

		// VALIDAR SI EXISTE UN NOMBRE DE IMAGEN
		if (count($nameImgCurrent) > 0) {
			// VALIDAR EL NUMERO DE IMAGEN
			if ($no_image == 1) {
				// COMPROBAR SI EL NOMBRE DE LA IMG 1 NO ESTA VACIO
				if($nameImgCurrent[0]['image_name'] != "") {
					$nImg = $nameImgCurrent[0]['image_name'];

					if (file_exists($path_images.$nImg)) {
					    //eliminando del servidor
						unlink($path_images . $nImg);
					}
				}
			} else if ($no_image == 2) {
				// COMPROBAR SI EL NOMBRE DE LA IMG 2 NO ESTA VACIO
				if($nameImgCurrent[0]['image_name_two'] != "") {
					
					$nImg2 = $nameImgCurrent[0]['image_name_two'];

					if (file_exists($path_images.$nImg2)) {
					    //eliminando del servidor
						unlink($path_images . $nImg2);
					}
				}
			}
		}

		// MARCA DE TIEMPO
		$timeStampImg = strtotime("now");
		//echo "<pre>"; print_r($timeStampImg); exit;

		// Nombre final de la imagen
		$full_image_name	= "art-" . sha1($id_code_qr) . "-" . $timeStampImg . "-" . $no_image;
		//echo "<pre>"; print_r($full_image_name); exit;

		// DATOS DE LA IMAGEN
		$name_image  = $img_article['name'];
		$tmp_image   = $img_article['tmp_name'];
		$type_image  = $img_article['type'];
		$size_image  = $img_article['size'];

		$final_image = 0;

		// VALIDAR SI EXISTE LA IMAGEN
		if ( isset( $img_article ) && !empty( $name_image ) && !empty( $tmp_image ) ){

			//indicamos los formatos que permitimos subir a nuestro servidor
		   if (($type_image == "image/gif") || ($type_image == "image/jpeg") || ($type_image == "image/jpg") || ($type_image == "image/png")) {

			   	//Definir tamaño máximo y mínimo
				$max_ancho 			= 300;
				$max_alto			= 300;

		   		// Validar el tipo de extesion de la imagen
		   		switch ($type_image) {
		   			case 'image/gif':

		   				$img_original	= imagecreatefromgif($tmp_image);

		   				break;
		   			case 'image/jpeg':
		   				
		   				$img_original	= imagecreatefromjpeg($tmp_image);

		   				break;

		   			case 'image/jpg':

		   				$img_original	= imagecreatefromjpeg($tmp_image);
		   				
		   				break;

		   			case 'image/png':
		   				
		   				$img_original	= imagecreatefrompng($tmp_image);

		   				break;
		   			
		   			default:
		   				
		   				break;
		   		}

		   		//Recoger ancho y alto de la original
				list($ancho,$alto) = getimagesize($tmp_image);

				//Calcular proporción ancho y alto
				$x_ratio = $max_ancho / $ancho;
				$y_ratio = $max_alto / $alto;

				if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
					//Si es más pequeña que el máximo no redimensionamos
		    		$ancho_final = $ancho;
		    		$alto_final  = $alto;
				}
				//si no calculamos si es más alta o más ancha y redimensionamos
				elseif ( ($x_ratio * $alto) < $max_alto ){
		    		$alto_final  = ceil($x_ratio * $alto);
		   			$ancho_final = $max_ancho;
				}
				else{
		    		$ancho_final = ceil($y_ratio * $ancho);
		    		$alto_final  = $max_alto;
				}

				//Crear lienzo en blanco con proporciones
				$lienzo = imagecreatetruecolor($ancho_final,$alto_final);

				//Copiar $original sobre la imagen que acabamos de crear en blanco ($tmp)
				imagecopyresampled($lienzo,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);

				//Limpiar memoria
				imagedestroy($img_original);

				//Definimos la calidad de la imagen final
				$calidad = 90;

				imagejpeg($lienzo, $path_images . $full_image_name . ".jpg", $calidad);
				
				// Retornamos nombre de la imagen
				$final_image = $full_image_name . ".jpg";

   			}

		} 

		return $final_image;
		//echo "<pre>"; print_r(is_uploaded_file($img_article)); exit;
		/*$file_tempname = null;
		if (is_uploaded_file($img_article)) {
		    $file_tempname = $img_article;
		}
		else{
		    exit('Wrong file type');
		}

		$file_dimensions = getimagesize($file_tempname);
		$file_type = strtolower($file_dimensions['mime']);
		echo "<pre>"; print_r($file_type);exit;
		if ($file_type=='image/jpeg' || $file_type=='image/pjpeg'){
		    if(imagecreatefromjpeg($file_tempname)){
		        $im = imagecreatefromjpeg($file_tempname);
		        return $im;	
		    } 
		}
		echo "<pre>"; print_r($file_type);exit;*/
		/*
		try {

			//Ruta de la original
			$rtOriginal = $img_article;

			//Crear variable de imagen a partir de la original
			$original   = imagecreatefromjpeg($rtOriginal);
			
			//echo "<pre>"; print_r($original); exit;
			//Definir tamaño máximo y mínimo
			$max_ancho = 300;
			$max_alto  = 300;

			//Recoger ancho y alto de la original
			list($ancho,$alto) = getimagesize($rtOriginal);

			//Calcular proporción ancho y alto
			$x_ratio = $max_ancho / $ancho;
			$y_ratio = $max_alto / $alto;

			if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
				//Si es más pequeña que el máximo no redimensionamos
	    		$ancho_final = $ancho;
	    		$alto_final = $alto;
			}
			//si no calculamos si es más alta o más ancha y redimensionamos
			elseif (($x_ratio * $alto) < $max_alto){
	    		$alto_final  = ceil($x_ratio * $alto);
	   			$ancho_final = $max_ancho;
			}
			else{
	    		$ancho_final = ceil($y_ratio * $ancho);
	    		$alto_final = $max_alto;
			}

			//Crear lienzo en blanco con proporciones
			$lienzo = imagecreatetruecolor($ancho_final,$alto_final);

			//Copiar $original sobre la imagen que acabamos de crear en blanco ($tmp)
			imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);

			//Limpiar memoria
			imagedestroy($original);

			//Definimos la calidad de la imagen final
			$cal = 90;

			//ob_start();

			// NOMBRE DE LA IMAGEN
			$name_image = "imagen";

			// Se crea la imagen final en el directorio indicado
			// "./data/imagenesresize/thumb.jpg"
			//imagejpeg($lienzo,NULL,$cal);
			imagejpeg($lienzo, "./public/images/articles/" . $name_image . ".jpg", $cal);

			//$final_image = ob_get_contents();
			//echo "<pre>"; print_r($final_image); exit;
			$final_image = $name_image . ".jpg";

			//ob_end_clean();

		} catch (Exception $e) {
			$final_image = 0;
    		//echo 'Excepción capturada: ',  $e->getMessage(), "\n"; exit;
		}
 
		//echo "<pre>"; print_r($final_image); exit;
		return $final_image;
		*/
	}

	/**
	 * AGREGAR UN ARTICULO
	 */
	public function addArticles($data)
	{
		//echo "<pre>"; print_r($data); exit;
		//$imagen_escalada = $this->rescalarimagen($data['imageone']['tmp_name']);
		//$imagen_escalada = $this->resize($data['imageone']['tmp_name']);

		$redimensionarImgArt = 0;

		// Validamos si viene una imagen
		if (isset($data['imageone']['tmp_name']) && $data['imageone']['tmp_name'] != "") {
			// REDIMENSIONAR IMAGEN 1
			$redimensionarImgArt    = $this->redimensionarImagenArticulo($data['imageone'], $data['id_register_qr'], 1);
		}

		$redimensionarImgArtTwo = 0;

		// Validamos si viene una imagen
		if (isset($data['imagetwo']['tmp_name']) && $data['imagetwo']['tmp_name'] != "") {
			// REDIMENSIONAR IMAGEN 2
			$redimensionarImgArtTwo = $this->redimensionarImagenArticulo($data['imagetwo'], $data['id_register_qr'], 2);
		}
		//echo "<pre>"; print_r($redimensionarImgArt); exit;
		//echo "<pre>"; print_r(base64_encode($redimensionarImgArt)); exit;
		//$im = file_get_contents($data['imageone']['tmp_name']);
		//$im = file_get_contents($imagen_escalada);
		//$imdata = base64_encode($imagen_escalada);
		//echo "<pre>"; print_r($imdata); exit;

		// ID del codigo qr
		$idCodeQR = (int) $data['id_register_qr'];
		//echo "<pre>"; print_r($idCodeQR); exit;

		// Estatus codigo qr
		$status_code_qr = $this->getCodeQRService()->getStatusCodeQR($idCodeQR);
		//echo "<pre>"; print_r($status_code_qr[0]['id_status']); exit;

		if ($status_code_qr[0]['id_status'] == 1) {
			
			// Formamos un array con los datos del articulo
			$dataArticle = array(
				'asignated_to'   => $data['asignated_to'],
				'event_articles' => $data['event_articles'],
				'id_user'        => $data['id_users'],
				'id_register_qr' => $data['id_register_qr'], // Revisar para agregar el id del qr
				'name_article'   => $data['name_article'],
				'description'    => $data['description'],
				'id_color'       => $data['color'],
				'brand'          => $data['brand'],
				'id_category'    => $data['category'],
				'reward'         => $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
				'model_serie'    => $data['model_serie'],
				'clothes_size'   => $data['clothes_size'],
				//'imageone'       => ($redimensionarImgArt) ? base64_encode($redimensionarImgArt) : '',
				'imageone'       => '',
				'image_name'     => ($redimensionarImgArt) ? $redimensionarImgArt : '',
				'imagetwo'       => '',//($redimensionarImgArtTwo) ? base64_encode($redimensionarImgArtTwo) : '',
				'image_name_two' => ($redimensionarImgArtTwo) ? $redimensionarImgArtTwo : '',
				'registration_date' => date('Y-m-d H:i:s'),
				
				'own_alien'    => $data['own_alien'],
				'id_userfound' => (isset($data['id_userfound'])) ? $data['id_userfound'] : 0
			);

			//echo "<pre>"; print_r($dataArticle); exit;
			
			// Agregamos el articulo
			$saveArticles = $this->getArticlesModel()->addArticles($dataArticle);
			//echo "<pre>"; print_r($saveArticles); exit;
			// Actualizamos el status del codigo qr
			if($saveArticles)
			{

				// ESTATUS DE CODIGO QR
				$valueStatus = (int) $data['id_status'];

				// Actualizamos el status del codigo qr
				$update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);

			}

		} else {
			$saveArticles = 0;
		}
		
		return $saveArticles;
	}

	/**
	 * MODIFICAR UN ARTICULO
	 */
	public function editArticles($data)
	{
		//echo "<pre>"; print_r($data); exit;

		$redimensionarImgArt = 0;

		// Validamos si viene una imagen
		// ($data['imageone']['tmp_name'] != "")
		if (isset($data['imageone']['tmp_name']) && $data['imageone']['tmp_name'] != "") {
			// REDIMENSIONAR IMAGEN 1
			//$redimensionarImgArt    = $this->redimensionarImagenArticulo($data['imageone']['tmp_name']);
			$redimensionarImgArt    = $this->redimensionarImagenArticulo($data['imageone'], $data['id_register_qr'], 1);
		}
		//echo "<pre>"; print_r($redimensionarImgArt); exit;

		$redimensionarImgArtTwo = 0;

		// Validamos si viene una imagen
		if (isset($data['imagetwo']['tmp_name']) && $data['imagetwo']['tmp_name'] != "") {
			// REDIMENSIONAR IMAGEN 2
			//$redimensionarImgArtTwo = $this->redimensionarImagenArticulo($data['imagetwo']['tmp_name']);
			$redimensionarImgArtTwo = $this->redimensionarImagenArticulo($data['imagetwo'], $data['id_register_qr'], 2);
		}

		//echo "<pre>"; print_r($redimensionarImgArt);
		//echo "<pre>"; print_r($redimensionarImgArtTwo); exit;
		// ID del codigo qr
		//$idCodeQR       = (int) $data['id_register_qr'];

		// Estatus codigo qr
		//$status_code_qr = (int) $data['id_status'];

		// Arreglo con los datos de a quien se le presta un articulo
		//$lend_article   = json_decode($data['input_lend_article'], true);
		
		// Arreglo con los datos del articulo
		$dataArticle = array(
			'id'           => $data['id'],
			'asignated_to' => $data['asignated_to'],
			'event_articles' => $data['event_articles'],
			'name_article' => $data['name_article'],
			'description'  => $data['description'],
			'id_color'     => $data['color'],
			'brand'        => $data['brand'],
			'id_category'  => $data['category'],
			'reward'       => $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
			'model_serie'  => $data['model_serie'],
			'clothes_size' => $data['clothes_size'],
			//'imageone'     => ($redimensionarImgArt) ? base64_encode($redimensionarImgArt) : '',
			//'imagetwo'     => ($redimensionarImgArtTwo) ? base64_encode($redimensionarImgArtTwo) : '',
		);

		// Validamos si existe la imagen
		if ($redimensionarImgArt) {
			//echo "<pre>"; print_r($redimensionarImgArt); exit;
			//$dataArticle['imageone'] = base64_encode($redimensionarImgArt);
			$dataArticle['image_name'] = ($redimensionarImgArt) ? $redimensionarImgArt : '';
		}

		// Validamos si existe la imagen
		if ($redimensionarImgArtTwo) {
			//$dataArticle['imagetwo'] = base64_encode($redimensionarImgArtTwo);
			$dataArticle['image_name_two'] = ($redimensionarImgArtTwo) ? $redimensionarImgArtTwo : '';
		}
		 
		//echo "<pre>"; print_r($dataArticle); exit;

		// Editamos un articulo
		$editArticles = $this->getArticlesModel()->editArticles($dataArticle);
		//echo "<pre>"; print_r($editArticles); exit;

		// Actualizamos el status del codigo qr
		/*if($editArticles)
		{

			// VALIDAMOS SI EL ARTICULO VA A SER PRESTADO
			if ($status_code_qr == 3) {

				// VALIDAMOS SI ESTA EN ESTATUS PRESTADO, NO HACEMOS NADA
				$validateStatusCodeQR = $this->getCodeQRService()->getStatusCodeQR($idCodeQR);
				//echo "<pre>";print_r($validateStatusCodeQR[0]['id_status']); exit;

				if($validateStatusCodeQR[0]['id_status'] != 3) {
					
					// Añadimos los datos de aquien se le presto el articulo
					$dataLendArticle = array(
						'id_status'     => $data['id_status'],
						'id_articles'   => $data['id'],
						'date_change'   => date('Y-m-d H:i:s'),
						'name_external' => $lend_article[0]['first_name_lend'] . ' ' . $lend_article[0]['last_name_lend'],
						'comment'       => $lend_article[0]['descrip_lend']
					);

					//echo "<pre>"; print_r($dataLendArticle); exit;

					// Agregamos los datos de a quien fue prestado el articulo
					$insert_lend_article = $this->getHistoryStatusService()->insertLendArticle($dataLendArticle);

				}

			}

			// Actualizamos el status del codigo qr
			$update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $status_code_qr);

		}*/

		return $editArticles;
	}

	/**
	 * CREAR IMAGEN DE ARTIULO CON BASE64
	 */
	private function createImgArticleBase64($img_base64, $id_code_qr = null)
	{
		//echo "<pre>"; print_r($img_base64); exit;
		//echo "<pre>"; print_r($id_code_qr); exit;

		// Ruta donde se almacenaran las imagenes
		$path_images		= "./public/images/articles/";

		// OBTENEMOS EL NOMBRE ACTUAL DE LA IMAGEN
		$nameImgCurrent	= $this->getNameImageArticle($id_code_qr);
		//echo "<pre>"; print_r($nameImgCurrent); exit;

		// VALIDAR SI EXISTE UN NOMBRE DE IMAGEN
		if (count($nameImgCurrent) > 0) {
			// COMPROBAR SI EL NOMBRE DE LA IMG 1 NO ESTA VACIO
			if($nameImgCurrent[0]['image_name'] != "") {
				$nImg = $nameImgCurrent[0]['image_name'];

				if (file_exists($path_images.$nImg)) {
					   //eliminando del servidor
					unlink($path_images . $nImg);
				}
			}
		}

		// MARCA DE TIEMPO
		$timeStampImg = strtotime("now");
		//echo "<pre>"; print_r($timeStampImg); exit;

		// Numero de imagen
		$no_image = 1;

		// Nombre final de la imagen
		$full_image_name	= "art-" . sha1($id_code_qr) . "-" . $timeStampImg . "-" . $no_image;
		//echo "<pre>"; print_r($full_image_name); exit;

		// DECODIFICAR LA IMAGEN EN BASE64
		$dataDecodeImage	= base64_decode($img_base64);
		//echo "<pre>"; print_r($dataDecodeImage); exit;

		// INFORMACION DEL TAMANO DE LA IMAGEN
		$size_info 			= getimagesizefromstring($dataDecodeImage);
		//echo "<pre>"; print_r($size_info); exit;

		//Definir tamaño máximo y mínimo
		$max_ancho 			= 300;
		$max_alto			= 300;

		// Ancho de la imagen
		$widthImg  			= $size_info[0];
		//echo "<pre>"; print_r($widthImg); exit;
		// Alto de la imagen
		$heightImg 			= $size_info[1];
		//echo "<pre>"; print_r($heightImg); exit;

		//Calcular proporción ancho y alto
		$x_ratio 			= $max_ancho / $widthImg;
		$y_ratio 			= $max_alto / $heightImg;

		if( ($widthImg <= $max_ancho) && ($heightImg <= $max_alto) ){
			//Si es más pequeña que el máximo no redimensionamos
			$ancho_final = $widthImg;
			$alto_final  = $heightImg;
		}
		//si no calculamos si es más alta o más ancha y redimensionamos
		elseif ( ($x_ratio * $heightImg) < $max_alto ){
			$alto_final  = ceil($x_ratio * $heightImg);
			$ancho_final = $max_ancho;
		}
		else{
		    $ancho_final = ceil($y_ratio * $widthImg);
		    $alto_final  = $max_alto;
		}

		// CREAR IMAGEN
		$imageResult 		= imagecreatefromstring($dataDecodeImage);
		//echo "<pre>"; print_r($imageResult); exit;

		// Calidad de la imagen
		$calidad 			= 90;

		$lienzo 			= imagecreatetruecolor($ancho_final, $alto_final);
    	//imagecopyresampled($lienzo, $imageResult, 0,0,0,0, $ancho_final, $alto_final, imagesx($imageResult), imagesy($imageResult));
    	imagecopyresampled($lienzo, $imageResult, 0,0,0,0, $ancho_final, $alto_final, $widthImg, $heightImg);
    	
    	// CREAR IMAGEN JEPG
    	imagejpeg($lienzo, $path_images . $full_image_name . ".jpg", $calidad);

		// Destruir imagen de memoria
		imagedestroy($imageResult);
		imagedestroy($lienzo);

		// Retornamos nombre de la imagen
		$final_image 		= $full_image_name . ".jpg";

    	return $final_image;

	}

	/**
	 * CREAR IMAGEN DE ARTIULO CON BASE64 2
	 */
	private function createImgArticleBase64Two($img_base64, $id_code_qr = null)
	{
		// Ruta donde se almacenaran las imagenes
		$path_images	= "./public/images/articles/";

		// OBTENEMOS EL NOMBRE ACTUAL DE LA IMAGEN
		$nameImgCurrent	= $this->getNameImageArticle($id_code_qr);

		// VALIDAR SI EXISTE UN NOMBRE DE IMAGEN
		if (count($nameImgCurrent) > 0) {
			// COMPROBAR SI EL NOMBRE DE LA IMG 1 NO ESTA VACIO
			if($nameImgCurrent[0]['image_name_two'] != "") {
				$nImg = $nameImgCurrent[0]['image_name_two'];

				if (file_exists($path_images.$nImg)) {
					   //eliminando del servidor
					unlink($path_images . $nImg);
				}
			}
		}

		// MARCA DE TIEMPO
		$timeStampImg = strtotime("now");

		// Numero de imagen
		$no_image = 2;

		// Nombre final de la imagen
		$full_image_name	= "art-" . sha1($id_code_qr) . "-" . $timeStampImg . "-" . $no_image;

		// DECODIFICAR LA IMAGEN EN BASE64
		$dataDecodeImage	= base64_decode($img_base64);

		// INFORMACION DEL TAMANO DE LA IMAGEN
		$size_info 			= getimagesizefromstring($dataDecodeImage);

		//Definir tamaño máximo y mínimo
		$max_ancho 			= 300;
		$max_alto			= 300;

		// Ancho de la imagen
		$widthImg  			= $size_info[0];
		
		// Alto de la imagen
		$heightImg 			= $size_info[1];

		//Calcular proporción ancho y alto
		$x_ratio 			= $max_ancho / $widthImg;
		$y_ratio 			= $max_alto / $heightImg;

		if( ($widthImg <= $max_ancho) && ($heightImg <= $max_alto) ){
			//Si es más pequeña que el máximo no redimensionamos
			$ancho_final = $widthImg;
			$alto_final  = $heightImg;
		}
		//si no calculamos si es más alta o más ancha y redimensionamos
		elseif ( ($x_ratio * $heightImg) < $max_alto ){
			$alto_final  = ceil($x_ratio * $heightImg);
			$ancho_final = $max_ancho;
		}
		else{
		    $ancho_final = ceil($y_ratio * $widthImg);
		    $alto_final  = $max_alto;
		}

		// CREAR IMAGEN
		$imageResult 		= imagecreatefromstring($dataDecodeImage);

		// Calidad de la imagen
		$calidad 			= 90;

		$lienzo 			= imagecreatetruecolor($ancho_final, $alto_final);
    	imagecopyresampled($lienzo, $imageResult, 0,0,0,0, $ancho_final, $alto_final, $widthImg, $heightImg);
    	
    	// CREAR IMAGEN JEPG
    	imagejpeg($lienzo, $path_images . $full_image_name . ".jpg", $calidad);

		// Destruir imagen de memoria
		imagedestroy($imageResult);
		imagedestroy($lienzo);

		// Retornamos nombre de la imagen
		$final_image 		= $full_image_name . ".jpg";

    	return $final_image;
	}

	/**
	 * AGREGAR UN ARTICULO EN EL APP MOVIL
	 */
	public function addArticlesAppMovil($data)
	{

		// ID del codigo qr
		$idCodeQR = (int) $data['id_register_qr'];

		// Estatus codigo qr
		$status_code_qr = $this->getCodeQRService()->getStatusCodeQR($idCodeQR);
		//echo "<pre>"; print_r($status_code_qr); exit;

		if ($status_code_qr[0]['id_status'] == 1) {
			
			// Formamos un array con los datos del articulo
			$dataArticle = array(
				'asignated_to'   => $data['asignated_to'],
				'id_user'        => $data['id_users'],
				'id_register_qr' => $data['id_register_qr'], // Revisar para agregar el id del qr
				'event_articles' => $data['event_articles'],
				'name_article'   => $data['name_article'],
				'description'    => $data['description'],
				'id_color'       => ($data['color'] != "") ? $data['color'] : null,
				'brand'          => $data['brand'],
				'id_category'    => ($data['category'] != "") ? $data['category'] : null,
				'reward'         => $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
				'model_serie'    => $data['model_serie'],
				'clothes_size'   => ($data['clothes_size'] != "") ? $data['clothes_size'] : null,
				'imagetwo'       => "",
				'registration_date' => date('Y-m-d H:i:s'),
				'own_alien'    => $data['own_alien'],
				'id_userfound' => (isset($data['id_userfound'])) ? $data['id_userfound'] : 0
			);

			// VALIDAMOS SI VIENE IMAGEN
			if($data['imageone'] != "") {

				//Llamamos a la funcion para crear imagen
				$fullImgArticle = $this->createImgArticleBase64($data['imageone'], $data['id_register_qr']);
				// AGREGAMOS UN DATO AL ARREGLO
				$dataArticle['imageone']	= $data['imageone'];
				$dataArticle['image_name']	= ($fullImgArticle) ? $fullImgArticle : '';
			}

			//echo "<pre>"; print_r($dataArticle); exit;
			
			// Agregamos el articulo
			$saveArticles = $this->getArticlesModel()->addArticles($dataArticle);
			//echo "<pre>"; print_r($saveArticles); exit;
			
			// Actualizamos el status del codigo qr
			if($saveArticles)
			{

				// ESTATUS DE CODIGO QR
				$valueStatus = (int) $data['id_status'];

				// Actualizamos el status del codigo qr
				$update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);

			}

		} else {
			$saveArticles = 0;
		}
		
		return $saveArticles;
	}

	/**
	 * MODIFICAR UN ARTICULO EN EL APP MOVIL
	 */
	public function editArticlesAppMovil($data)
	{
		
		// Arreglo con los datos del articulo
		$dataArticle = array(
			'id'             	=> $data['id'],
			'asignated_to'   	=> $data['asignated_to'],
			'event_articles'	=> $data['event_articles'],
			'name_article' 		=> $data['name_article'],
			'description'  		=> $data['description'],
			'id_color'     		=> ($data['color'] != "") ? $data['color'] : null,
			'brand'        		=> $data['brand'],
			'id_category'  		=> ($data['category'] != "") ? $data['category'] : null,
			'reward'       		=> $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
			'model_serie'  		=> $data['model_serie'],
			'clothes_size' 		=> ($data['clothes_size'] != "") ? $data['clothes_size'] : null
		);

		// VALIDAMOS SI VIENE IMAGEN
		if($data['imageone'] != "") {

			//Llamamos a la funcion para crear imagen
			$fullImgArticle = $this->createImgArticleBase64($data['imageone'], $data['id_register_qr']);
			// AGREGAMOS UN DATO AL ARREGLO
			$dataArticle['imageone'] 	= $data['imageone'];
			$dataArticle['image_name']	= ($fullImgArticle) ? $fullImgArticle : '';
		}

		//echo "<pre>"; print_r($dataArticle); exit;

		// Editamos un articulo
		$editArticles = $this->getArticlesModel()->editArticles($dataArticle);

		return $editArticles;
	}

	/**
	 * MODIFICAR EL ID USER FOUND DE UN ARTICULO
	 */
	public function updateIdUserFound($dataArticle)
	{
		$editArticles = $this->getArticlesModel()->editArticles($dataArticle);

		return $editArticles;
	}

	/**
	 * Metodo para validar si un codigo de qr existe
	 */
	public function verifyCodeQrUniqueExists($code)
	{
		//print_r($email); exit;
		$code_qr_unique = array('foliocodeqr' => $code);

		$verifyCodeQrUnique = $this->getArticlesModel()->verifyCodeQrUniqueExists($code_qr_unique);

		return $verifyCodeQrUnique;
	}

	/**
	 * ELIMINAR ARTICULO
	 */
	public function deleteArticle($id)
	{
		$idCodeQR = (int) $id;
		//echo "<pre>"; print_r($idCodeQR); exit;
		//$deleteArticle = $this->getArticlesModel()->deleteArticle($idArticles);
		// Actualizamos el status del codigo qr
		$update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, 5);
		//print_r($update_status_code_qr); exit;

		return $update_status_code_qr;
	}

	/**
	 * ELIMINAR ARTICULOS ENCONTRADOS
	 */
	public function deleteArticleFound($formData)
	{
		//echo "<pre>"; print_r($formData); exit;
		// ARRAY PARA ALMACENAR LOS IDS
		$dataIdReturn = array();

		// RECORREMOS LOS ELEMENTOS QUE LLEGAN
		foreach ($formData['articles'] as $key => $value) {

			// id de devolucion
			$idReturn = (int) $value['id_return'];

			// Eliminar articulo encontrado
			$deteArticlesFound = $this->getReturnsService()->deleteArticleFound($idReturn);
			//echo "<pre>"; print_r($deteArticlesFound); exit;
			
			// Almacenamos los datos en el arreglo
			array_push($dataIdReturn, $value['id_return']);

		}
		//echo "<pre>"; print_r($dataIdReturn); exit;
		return $dataIdReturn;
	}

	/**
	 * OBTENER LA UBICACION DE UN ARTICULO
	 */
	public function getLastLocationArticle($idArt)
	{
		$location_art = $this->getArticlesModel()->getLastLocationArticle((int) $idArt);
		$result       = array();
		
		if(isset($location_art[0])){
			$result = $location_art[0];
		}

		return $result;
	}

	/**
	 * REESCALAR IMAGEN
	 */
	private function rescalarimagen($img_art)
	{
		//BASADO EN JPEG, PARA USAR EN PNG, GIF ETC CAMBIAR EL NOMBRE DE LAS FUNCIONES

		//if (isset($_FILES['imagen1']) && $_FILES['imagen1']['tmp_name']!=''){

		//Imagen original
		//$rtOriginal=$_FILES['imagen1']['tmp_name'];
		$rtOriginal = $img_art;

		//Crear variable
		$original = imagecreatefromjpeg($rtOriginal);

		//Ancho y alto máximo
		$max_ancho = 600; $max_alto = 400;
 
		//Medir la imagen
		list($ancho,$alto)=getimagesize($rtOriginal);

		//Ratio
		$x_ratio = $max_ancho / $ancho;
		$y_ratio = $max_alto / $alto;

		//Proporciones
		if(($ancho <= $max_ancho) && ($alto <= $max_alto) ){
		    $ancho_final = $ancho;
		    $alto_final = $alto;
		}
		else if(($x_ratio * $alto) < $max_alto){
		    $alto_final = ceil($x_ratio * $alto);
		    $ancho_final = $max_ancho;
		}
		else {
		    $ancho_final = ceil($y_ratio * $ancho);
		    $alto_final = $max_alto;
		}

		//Crear un lienzo
		$lienzo=imagecreatetruecolor($ancho_final,$alto_final); 

		//Copiar original en lienzo
 		imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
 
		//Destruir la original
		imagedestroy($original);

		ob_start();

		//Crear la imagen y guardar en directorio upload/
		//imagejpeg($lienzo,"upload/".$_FILES['imagen1']['name']);
		imagejpeg($lienzo);

		 $i = ob_get_clean();

		//}
		$full_img = base64_encode( $i );
		return  $full_img;
	}

	private function resize($image) {
		// $image is the uploaded image
  		list($width, $height) = getimagesize($image);
  		
  		//setup the new size of the image
  		$ratio      = $width/$height;
  		$new_height = 500;
  		$new_width  = $new_height * $ratio;

  		//move the file in the new location
  		//move_uploaded_file($image['tmp_name'], $target_file);
 
  		// resample the image       
  		$new_image = imagecreatetruecolor($new_width, $new_height);

  		$old_image = imagecreatefromjpeg($image);

  		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width, $new_height, $width, $height);       

  		ob_start();
  		//output
  		imagejpeg($new_image, null, 100);
  		$i = ob_get_clean();

		//}
		$full_img = base64_encode( $i );
		return  $full_img;
	}

	/**
	 * Metodo para encriptar la contraseña
	 */
	private function bcryptPassSecurity($pass)
	{
		$bcrypt = new Bcrypt(array(
			'salt' => '$2y$05$KkFmCjGPJiC1jdt.SFcJ5uDXkF1yYCQFgiQIjjT6p.z7QIHyU1elW',
			'cost' => 5
		));
		
		// Contrasena segura
		$securePass = $bcrypt->create(strip_tags($pass));

		return $securePass;
	}

	/**
	 * OBTENER LISTA DE NOTIFICACIONES
	 */
	public function getAllPushNotifications($idUser)
	{
		$pushNotifications = $this->getArticlesModel()->getAllPushNotifications($idUser);

		return $pushNotifications;
	}

	/**
	 * recordatorio de artículo encontrado
	 */
	public function articleReminderFound($idPushNotification)
	{
		// TRY CATCH
		try {
			
			// 1.- OBTENER NOTIFICACION POR ID
			$pushNotification = $this->getPushNotificationById($idPushNotification);
			//echo "<pre>"; print_r($pushNotification); exit;

			// DATOS PARA MODIFICAR EL ESTATUS DE LA NOTIFICACION
			$dataStatusNotification = array(
				"id_notification" 			=> $pushNotification[0]['id'],
				"status_notification"		=> 1,
				'modification_notification' => date('Y-m-d H:i:s')
			);
			//echo "<pre>"; print_r($dataStatusNotification); exit;

			// 2.- ACTUALIZAMOS NOTIFICACION
			$updateStatusNotification = $this->updateStatusNotification($dataStatusNotification);
			//echo "<pre>"; print_r($updateStatusNotification); exit;

			// DATOS PARA ENVIAR NOTIFICACION
			$dataSendNotification = array(
				"reminder" 			=> true,
				"id_user" 			=> $pushNotification[0]['id_user'],
				"id_article" 		=> $pushNotification[0]['id_article'],
				"name_p" 			=> $pushNotification[0]['contact_name'],
				"email_p" 			=> $pushNotification[0]['contact_email'],
				"phone_p" 			=> $pushNotification[0]['contact_phone'],
				"comment_p" 		=> $pushNotification[0]['contact_comment'],
				"isFound" 			=> $pushNotification[0]['isFound'],
				"warehouse" 		=> $pushNotification[0]['warehouse'],
				"phone_warehouse" 	=> $pushNotification[0]['phone_warehouse'],
				"comment_warehouse" => $pushNotification[0]['comment_warehouse']
			);
			//echo "<pre>"; print_r($dataSendNotification); exit;

			// 3.- ENVIAR CORREO Y NOTIFICACION AL USUARIO
			//$sendPushNotification = $this->notificationFoundArticle($dataSendNotification);
			//$sendEmailAndPushNotification = $this->sendEMailArticleFound($dataSendNotification);
			//echo "<pre>"; print_r($sendEmailAndPushNotification); exit;
			// ENVIAMOS NOTIFICACION
			$notificationFoundArticle = $this->notificationFoundArticle($dataSendNotification);
			//echo "<pre>"; print_r($notificationFoundArticle); exit;

			// RESULTADO
			$resultReminder = array("status" => "ok", "msg" => "email and push notification success");

		} catch (Exception $e) {
			// RESULTADO
			$resultReminder = array("status" => "fail", "msg" => "email and push notification error");
		}

		// DEVOLVEMOS LA RESPUESTA
		return $resultReminder;
	}


	/**
	 * OBTENER NOTIFICACION POR ID
	 */
	public function getPushNotificationById($idPushNotification)
	{
		$pushNotification = $this->getArticlesModel()->getPushNotificationById($idPushNotification);

		return $pushNotification;
	}

	/**
	 * GUARDAR NOTIFICACION
	 */
	private function saveNotification($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// ID
		$idArticle 		 = (int) $data['id_article'];

		// OBTENER ULTIMA UBICACION DE LA MASCOTA
		$getLastLocation = $this->getLastLocationArticle($idArticle);
		//echo "<pre>"; print_r($getLastLocation); exit;

		// ARREGLO CON LOS DATOS DE LA NOTIFICACION
		$dataSaveNotification = array(
			"id_return"				=> $data['id_return'],
			"id_user" 			 	=> $data['id_user'],
			"id_article" 			=> $data['id_article'],
			"id_location_art"		=> (count($getLastLocation) > 0) ? $getLastLocation['id_location'] : "",
			"title_notification"	=> 'Enhorabuena!',
			"message_notificacion"	=> $data['message_notificacion'],
			"contact_name" 			=> $data['name_p'],
			"contact_email" 		=> $data['email_p'],
			"contact_phone" 		=> $data['phone_p'],
			"contact_comment" 		=> $data['comment_p'],
			"type_notification"		=> (int) 1 // Articulo = 1 o Mascota = 2
		);

		// VALIDAMOS SI ES UNA INSTITUCION
		//var_dump($data['isFound']); exit;
		if(isset($data['isFound']) && $data['isFound'] == true) {
			//echo "eres verdadero";

			// OBTENEMOS EL DETALLE DE LA DEVOLUCION

			// AGREGAMOS CAMPOS AL ARREGLO
			$dataSaveNotification['isFound']			= (isset($data['isFound'])) ? $data['isFound'] : "";
			$dataSaveNotification['warehouse']			= (isset($data['warehouse'])) ? $data['warehouse'] : "";
			$dataSaveNotification['phone_warehouse']	= (isset($data['phone_warehouse'])) ? $data['phone_warehouse'] : "";
			$dataSaveNotification['comment_warehouse']	= (isset($data['comment_warehouse'])) ? $data['comment_warehouse'] : "";

		} else {
			//echo "eres falso";
			// AGREGAMOS CAMPOS AL ARREGLO
			$dataSaveNotification['isFound']			= 0;
			$dataSaveNotification['warehouse']			= "";
			$dataSaveNotification['phone_warehouse']	= "";
			$dataSaveNotification['comment_warehouse']	= "";
		}
		//exit;

		//echo "<pre>"; print_r($dataSaveNotification); exit;


		// GUARDAMOS LA NOTIFICACION
		$saveNotification = $this->getArticlesModel()->saveNotificacion($dataSaveNotification);
		//echo "<pre>"; print_r($saveNotification); exit;

		return $saveNotification;

	}

	/**
	 * ACTUALIZAR ESTATUS DE NOTIFICACION
	 */
	public function updateStatusNotification($data)
	{

		// ARREGLO CON LOS DATOS DE LA NOTIFICACION
		$dataStatusNotification = array(
			"id" 			 		=> $data['id_notification'],
			"status_notification"	=> $data['status_notification']
		);

		// VALIDAMOS SI VIENE LA FECHA DE MODIFICACION DE LA NOTIFICACION
		if (isset($data['modification_notification'])) {
			// Agregamos la fecha de modificacion al arreglo
			$dataStatusNotification['modification_notification'] = date('Y-m-d H:i:s');
		}
		//echo "<pre>"; print_r($dataStatusNotification); exit;

		// GUARDAMOS LA NOTIFICACION
		$updateStatusNotification = $this->getArticlesModel()->updateStatusNotification($dataStatusNotification);
		//echo "<pre>"; print_r($updateStatusNotification); exit;

		return $updateStatusNotification;

	}

	public function notificationFoundArticle($data)
	{
		//echo "<pre>"; print_r($data); exit;
		//id_article y el id_user
		$userId      = trim($data['id_user']);
		$articleId   = trim($data['id_article']);

		// OBTENER ARTICULO
		$infoArticle = $this->getArticleById($articleId);
		//echo "<pre>"; print_r($infoArticle); exit;

		// OBTENER DISPOSITIVOS REGISTRADOS
		$arrDevice   = $this->getDeviceUserModel()->getAllDeviceByIdUser($userId);
		//echo "<pre>"; print_r($arrDevice); exit;
		
		// VALIDAMOS SI EXISTE ALGUN DISPOSITIVO
		if (count($arrDevice) > 0) {

			// NOMBRE DEL ARTICULO
			if($infoArticle['name_article_two']){
				$artitleName = $infoArticle['name_article_two'];
			}else{
				$artitleName = $infoArticle['name_article'];
			}

			// MENSAJE DE LA NOTIFICACION
			$messageNotificacion = "Hola, encontré tú artículo: $artitleName. Contactame!";

			// AGREGAMOS UN VALOR AL ARRAY DATA
			$data['message_notificacion'] = $messageNotificacion;

			//print_r(isset($data['reminder'])); exit;
			// VALIDAMOS SI ES UNA NOTIFICACION NUEVA O UN RECORDATORIO
			if (!isset($data['reminder'])) {
				//echo "No es recordatorio";
				// ********************************
				// GUARDAR LA NOTIFICACION
				// ********************************
				$saveNotification = $this->saveNotification($data);
				//echo "<pre>"; print_r($saveNotification); exit;
			} /*else {
				echo "Es recordatorio";
			}*/
			//exit;
		
			// RECORREMOS CADA DISPOSITIVO ENCONTRADO
			foreach ($arrDevice as $device) {

	    		//IDENTIFICADOR DEL DISPOSITIVO LO REGRESA LA APP AL HACER EL LOGIN 
				$idDevice 			= $device['Key_device'];
				$title             	= "Enhorabuena!";
				$message           	= $messageNotificacion;
				$contact_name      	= $data['name_p'];
				$contact_email     	= $data['email_p'];
				$contact_phone     	= $data['phone_p'];
				$contact_comment   	= $data['comment_p'];
				
				// DATOS CUANDO LO ENCUENTRA UNA INSTITUCIÓN
				$isFound           = (isset($data['isFound'])) ? $data['isFound'] : "";
				$warehouse         = (isset($data['warehouse'])) ? $data['warehouse'] : "";
				$phone_warehouse   = (isset($data['phone_warehouse'])) ? $data['phone_warehouse'] : "";
				$comment_warehouse = (isset($data['comment_warehouse'])) ? $data['comment_warehouse'] : "";
				
				$fields = array (
					'to' =>  $idDevice,
					'priority' => 'high',
					'notification' => array (
							"sound" => "default",
							"title" => $title,
							"body"  => $message
					),
					'data' => array (
						"idArticle"         => $articleId,
						"contact_name"      => $contact_name,
						"contact_email"     => $contact_email,
						"contact_phone"     => $contact_phone,
						"contact_comment"   => $contact_comment,
						"isFound"           => $isFound,
						"warehouse"         => $warehouse,
						"phone_warehouse"   => $phone_warehouse,
						"comment_warehouse" => $comment_warehouse
					)
				);
						
				//echo "<pre>"; print_r($fields); exit;

		    	$adapter = new Curl();

		    	$adapter->setOptions([
			        'curloptions' => [
			            //CURLOPT_POST => 1,
			            //CURLOPT_POSTFIELDS => $postString,
			            CURLOPT_HTTPHEADER => array(
			    			'Content-Type'   => 'application/json',
				    		'Authorization' => 'key=AAAAwNskNn8:APA91bF3tyadL8cIU_5PbpN6R3w8jTB0UaxIcnhOhQKHYTh3qCGSfOSmaA1hw_0C1wYCVBAzi5C8BL3MLq8DciKpNGmmueWfJ4hVu8A8nH4YvgUOhnCtl0L4uhIMJnvTlLfYubPyfgx9' //KEY DE FIREBASE NO CAMBIAR
						),
			            CURLOPT_RETURNTRANSFER => true,
			            CURLOPT_SSL_VERIFYHOST => 0,
			            CURLOPT_SSL_VERIFYPEER=> 0
			        ]
			    ]);

	    	    $request = new \Zend\Http\Request();
			    $request->getHeaders()->addHeaders([
			    	'Content-Type'   => 'application/json',
			    	'Authorization' => 'key=AAAAwNskNn8:APA91bF3tyadL8cIU_5PbpN6R3w8jTB0UaxIcnhOhQKHYTh3qCGSfOSmaA1hw_0C1wYCVBAzi5C8BL3MLq8DciKpNGmmueWfJ4hVu8A8nH4YvgUOhnCtl0L4uhIMJnvTlLfYubPyfgx9'
			        
			    ]);

			    $url = 'https://fcm.googleapis.com/fcm/send';
			    
			    $request->setUri($url);
			    $request->setMethod('POST'); //uncomment this if the POST is used
			    $request->setContent(\Zend\Json\Json::encode($fields));
			    //echo "<pre>"; print_r($request); exit;

			    $client = new Client();
			    $client->setAdapter($adapter);
			    //echo "<pre>"; print_r($client); exit;

			    $response = $client->dispatch($request);
			    //echo "<pre>"; print_r($response->getBody()); exit;
			    //var_dump($response->getContent()); exit;

			}

		}
		
		//return $response->getBody();
		return "Notificacion enviada";
	}

	/**
	 * ENVIAR CORREO ELECTRONICO
	 */
	public function sendEMailArticleFound($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// ENVIAMOS NOTIFICACION
		$notificationFoundArticle = $this->notificationFoundArticle($data);
		//echo "<pre>"; print_r($notificationFoundArticle); exit;
		
		// ID DEL ARTICULO
		$articleId                = trim($data['id_article']);	
		
		// OBTENER DATOS DE UN ARTICULO
		$infoArticle              = $this->getArticleById($articleId);

		// VALIDAR NOMBRE DEL ARTICULO
		if($infoArticle['name_article_two']){
			$artitleName = $infoArticle['name_article_two'];
		}else{
			$artitleName = $infoArticle['name_article'];
		}

		$message = new Message();
		$message->addTo(trim($data['email_owner']))
		    ->addFrom('noreply@pegalinas.com')
		    ->setSubject('!Artículo encontrado¡');
		    
		// Setup SMTP transport using LOGIN authentication
		$transport = new SmtpTransport();
		$options   = new SmtpOptions(array(
			'name'              => 'smtp.gmail.com',
			'host'              => 'smtp.gmail.com',
			'port'              => 587, // Notice port change for TLS is 587
			'connection_class'  => 'plain',
			'connection_config' => array(
				'username' => 'noreply@pegalinas.com',
				'password' => 'swuetDefBek6',
				'ssl'      => 'tls',
		    ),
		));

		//VALIDAMOS SI EL MENSAJE DE PARTE DE UNA INSTITUCIÓN		
		if(isset($data['isFound']) &&  $data['isFound'] != false) {

			$messageEmailInstitution = '<div class="row"><div style="background-color: #fff;padding:40px;"><div 
				style="border-radius: 18px 18px 18px 18px; -moz-border-radius: 18px 18px 18px 18px; -webkit-border-radius: 18px 18px 18px 18px; border: 1px solid #57585A; -webkit-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); -moz-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75);"><div><center><a href="#"><img style="pointer-events: none; max-width:100%; " alt="pegalinas recupera"
									src="https://2.bp.blogspot.com/-xJpntXf0Ey8/WPkTQAjTekI/AAAAAAAABFY/e-pXWWKejFoZHEn9JcX5lcIgbRWfgF7GwCLcB/s320/Recupera%2Bby%2Bpegalinas.png" /></a></center><div
								style="padding-left: 40px; padding-right: 40px; color: black;"><center><h2> ¡Enhorabuena! </h2></center><br/><center><h3>Encontraron tu artículo: ' .$artitleName. '</h3></center><br/><center><h3>Datos de Contacto de la Institución</h3></center><h4>Nombre: ' . $data['name_p'] . ' </h4><h4>Teléfono: ' . $data['phone_p'] .'</h4><h4>Correo Electrónico de contacto: '. $data['email_p'] . '</h4><br/><center><h3>Datos del almacén</h3></center><h4>Almacén: ' . $data['warehouse'] . ' </h4><h4>Teléfono del almacén: ' . $data['phone_warehouse'] . ' </h4><h4>Comentario: ' . $data['comment_warehouse'] . ' </h4></div></div><br/><center><a href="https://www.youtube.com/channel/UCnzEiknp5FrTbw3zAUaNpjg" target="_blank"><img alt="youtube"
									src="http://icon-icons.com/icons2/70/PNG/64/youtube_14198.png" /></a><a href="https://twitter.com/pegalinas" target="_blank"><img alt="twitter"
									src="http://icon-icons.com/icons2/642/PNG/64/twitter_2_icon-icons.com_59206.png" /></a><a href="https://www.facebook.com/RecuperaMx/" target="_blank"><img alt="facebook"
									src="http://icon-icons.com/icons2/91/PNG/64/facebook_16423.png" /></a></center><br/></div></div>';
									
			$html = new MimePart($messageEmailInstitution);

		} else {
			
			// CONTENIDO DEL CORREO ELECTRONICO
			$messageEmail =  '<div class="row"><div style="background-color: #fff;padding:40px;"><div 
				style="border-radius: 18px 18px 18px 18px; -moz-border-radius: 18px 18px 18px 18px; -webkit-border-radius: 18px 18px 18px 18px; border: 1px solid #57585A; -webkit-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); -moz-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75);"><div><center><div style="padding: 20px;"><a href="#"><img style="pointer-events: none; max-width:100%; " alt="pegalinas recupera"
								src="https://2.bp.blogspot.com/-xJpntXf0Ey8/WPkTQAjTekI/AAAAAAAABFY/e-pXWWKejFoZHEn9JcX5lcIgbRWfgF7GwCLcB/s320/Recupera%2Bby%2Bpegalinas.png" /></a></div><h1 style="color: black;"></h1></center><div
							style="padding-left: 40px; padding-right: 40px; color: black;"><center><h2> ¡Enhorabuena! </h2></center><h3>Encontraron tu artículo: ' .$artitleName. '</h3><h3>Datos del contacto:</h3><h3>Nombre: ' . $data['name_p'] . ' </h3><h3>Teléfono:  '.$data['phone_p'] .'</h4><h3>Correo Electrónico de contacto: '. $data['email_p'] . '</h3><h3>Comentario:</h3><h3> '. $data['comment_p'] . '</h3><h3>Saludos.</h3></div><br><br><center><a href="https://www.youtube.com/channel/UCnzEiknp5FrTbw3zAUaNpjg" target="_blank"><img alt="youtube"
								src="http://icon-icons.com/icons2/70/PNG/64/youtube_14198.png" /></a><a href="https://twitter.com/pegalinas" target="_blank"><img alt="twitter"
								src="http://icon-icons.com/icons2/642/PNG/64/twitter_2_icon-icons.com_59206.png" /></a><a href="https://www.facebook.com/RecuperaMx/" target="_blank"><img alt="facebook"
								src="http://icon-icons.com/icons2/91/PNG/64/facebook_16423.png" /></a></center></div></div></div></div>';
			//echo "<pre>"; print_r($messageEmail); exit;
		
			$html = new MimePart($messageEmail);
			
		}
		
		$html->type = "text/html";

		$body = new MimeMessage();
		$body->addPart($html);

		$message->setBody($body);

		$transport->setOptions($options);
		$transport->send($message);


		return "Mensaje Enviado";
	}

	/**
	 * GUARDAR UBICACION DEL ARTICULO
	 */
	public function saveLastLocation($data)
	{

		$dataLastLocation = array(
			'id_articles' => $data['id_articles'],
			'longitude'   => $data['longitude'],
			'latitude'    => $data['latitude'],
			'addres'      => $data['addres'],
		);
		//echo "<pre>"; print_r($dataLastLocation); exit;

		$saveLastLocation = $this->getArticlesModel()->saveLastLocation($dataLastLocation);

		return $saveLastLocation;

	}


	/**
	 * ENVIAR CORREO ELECTRONICO AL CONTACTO
	 */
	public function sendEMailLost($data)
{
//echo "<pre>"; print_r(trim($data['email_owner'])); exit;
//OBTENER CORREO DEL DUENO DEL ARTICULO

$message = new Message();
$message->addTo('segurac.carlos@gmail.com')
  ->addFrom(trim($data['email_p']))
  ->setSubject('Contacto');
  
// Setup SMTP transport using LOGIN authentication
$transport = new SmtpTransport();
$options   = new SmtpOptions(array(
  'name' => 'smtp.gmail.com',
  'host' => 'smtp.gmail.com',
  'port' => 587, // Notice port change for TLS is 587
  'connection_class' => 'plain',
  'connection_config' => array(
      'username' => 'noreply@pegalinas.com',
      'password' => 'swuetDefBek6',
      'ssl' => 'tls',
  ),
));

$messageEmail = 
//echo "<pre>"; print_r($messageEmail); exit;

'<div class="row">
		<div style="background-color: #E6E6E6;padding-left: 40px;padding-right: 40px;padding-top: 2px;padding-bottom: 2px;">
			<div class="col s8"
				style="border-radius: 28px 28px 28px 28px; -moz-border-radius: 28px 28px 28px 28px; -webkit-border-radius: 28px 28px 28px 28px; border: 2px solid #57585A; -webkit-box-shadow: 0px 0px 23px 8px rgba(0, 0, 0, 0.75); -moz-box-shadow: 0px 0px 23px 8px rgba(0, 0, 0, 0.75); box-shadow: 0px 0px 23px 8px rgba(0, 0, 0, 0.75);">
				<div class="row">

					<div class="col s4"
						style="border-radius: 28px 28px 0px 0px; -moz-border-radius: 28px 28px 0px 0px; -webkit-border-radius: 28px 28px 0px 0px; background-color: #ffffff;">
						<center>
						<div style="padding: 20px;">
							<a href="#"><img style="pointer-events: none; max-width:100%; " alt="pegalinas recupera"
								src="https://2.bp.blogspot.com/-xJpntXf0Ey8/WPkTQAjTekI/AAAAAAAABFY/e-pXWWKejFoZHEn9JcX5lcIgbRWfgF7GwCLcB/s320/Recupera%2Bby%2Bpegalinas.png" /></a>
						</div>
							
							<h1 style="color: black;">  </h1>
						</center>

						<div
							style="padding-left: 40px; padding-right: 40px; color: black;">
							
						<center><h2> ¡¡ Hola !! </h2></center>
				           <h3>' . $data['name_p'] . ' quiere contactarte para que le ayudes a encontrar su artículo extraviado</h3>
		                  <h3>Datos del artículo:</h3>
		                  <h4>Artículo: '.$data['name_article'] .'</h4>
		                  <h4>Marca:  '.$data['brand'] .'</h4>
		                  <h4>Descripcion: '. $data['description'] . '</h4>
		                  <h3>Fotografia:</h3><br><h4> '. $data['imageone'] . '</h4>


						</div>
						<br>
					</div>
					<div class="col s4"
						style="border-radius: 0px 0px 28px 28px; -moz-border-radius: 0px 0px 28px 28px; -webkit-border-radius: 0px 0px 28px 28px; background-color: white;">
						<center>
							<a href="https://www.youtube.com/channel/UCnzEiknp5FrTbw3zAUaNpjg" target="_blank"><img alt="youtube"
								src="http://icon-icons.com/icons2/70/PNG/64/youtube_14198.png" /></a>
							<a href="https://twitter.com/pegalinas" target="_blank"><img alt="twitter"
								src="http://icon-icons.com/icons2/642/PNG/64/twitter_2_icon-icons.com_59206.png" /></a>
							<a href="https://www.facebook.com/Pegalinas/" target="_blank"><img alt="facebook"
								src="http://icon-icons.com/icons2/91/PNG/64/facebook_16423.png" /></a>
							
							
						</center>
					</div>
				</div>
			</div>
		</div>
	</div>
';
//$html = new MimePart('<b>heii, <i>sorry</i>, i\'m going late</b>');
$html = new MimePart($messageEmail);
$html->type = "text/html";

$body = new MimeMessage();
$body->addPart($html);

$message->setBody($body);

$transport->setOptions($options);
$transport->send($message);

return "Mensaje Enviado";
}



}