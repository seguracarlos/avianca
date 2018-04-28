<?php

namespace Pets\Service;

use Pets\Model\PetsModel;
use Articles\Service\CodeQRService;
use Articles\Service\HistoryStatusService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;

use Articles\Model\DeviceUserModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;

class PetsService
{
	private $petsModel;
	private $codeQRService;
	private $historyStatusService;
	private $deviceModel;

	/*
	* MODELO DE MASCOTAS
	*/
	private function getPetsModel()
	{
		return $this->petsModel = new PetsModel();
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
	 * BASE64 A IMAGEN
	 */
	public function base64ToImagePath()
	{
		// OBTENEMOS LOS DATOS DE LA BASE
		$stringBase64 = $this->getPetsModel()->base64ToImagePath();
		//echo "<pre>"; print_r($stringBase64); exit;

		// RESULTADO
		$dataResult = array();

		// RECORREMOS LOS DATOS OBTENIDOS
		foreach ($stringBase64 as $key => $value) {

			if($value['image_name'] == "") {

				// VALIDAMOS SI EXISTE EL BAE64
				if($value['img_pet'] != "" || $value['img_pet'] != null) {
				
				// CONVERTIMOS EL IMAGE BASE64 A IMAGEN
				$base64ToImg = $this->createImgPetBase64($value['img_pet'], $value['id_register_qr']);

				// almacenar nombres de imagenes
				array_push($dataResult, array("img_name" => $base64ToImg));

				// ARRAY CON LOS DATOS A ACTUALIZAR
				$dataUpdate = array(
					"id" 			=> $value['id'],
					"image_name"	=> $base64ToImg
				);

				// ACTUALIZAMOS EL NOMBRE DE LA IMAGEN EN LA BASE DE DATOS
				$updateImageName = $this->getPetsModel()->editPet($dataUpdate);

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
	 * OBTEMOS TODAS LAS MASCOTAS POR ID DE USUARIO
	 */
	public function getAll($idUser, $typePerfil = null)
	{
		$articles = $this->getPetsModel()->getAll($idUser, $typePerfil);

		return $articles;
	}

	/**
	 * OBTENER MASCOTA POR ID
	 */
	public function getPetById($id)
	{

		$pet = $this->getPetsModel()->getPetById($id);
		$result   = array();

		// VALIDAMOS SI EXISTE LA POSICION 0 EN EL ARRAY
		if(isset($pet[0])){

			$pet[0]['repeat_code_article'] = $pet[0]['code_article'];

			// Resultado
			$result = $pet[0];

		}
		
		return $result;
	}

	/**
	 * OBTENER MASCOTA POR CODIGO QR
	 */
	public function getPetByCodeQr($codeQR)
	{

		$petByCodeQr = $this->getPetsModel()->getPetByCodeQr($codeQR);
		//echo "<pre>"; print_r($petByCodeQr); exit;

		return $petByCodeQr;
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
	private function getNameImagePet($idQr)
	{
		$nameImage = $this->getPetsModel()->getNameImagePet($idQr);

		return $nameImage;
	}

	/**
	 * GENERAR IMAGEN DE MASCOTA
	 */
	private function redimensionarImagenMascota($img_pet, $id_code_qr = null)
	{
		// Ruta donde se almacenaran las imagenes
		$path_images	= "./public/images/pets/";

		// OBTENEMOS EL NOMBRE ACTUAL DE LA IMAGEN
		$nameImgCurrent	= $this->getNameImagePet($id_code_qr);
		//echo "<pre>"; print_r($nameImgCurrent); exit;

		// VALIDAR SI EXISTE UN NOMBRE DE IMAGEN
		if (count($nameImgCurrent) > 0) {
			// COMPROBAR SI EL NOMBRE DE LA IMG NO ESTA VACIO
			if($nameImgCurrent[0]['image_name'] != "") {
				$nImg = $nameImgCurrent[0]['image_name'];

				if (file_exists($path_images.$nImg)) {
					//eliminando del servidor
					unlink($path_images . $nImg);
				}
			}
		}

		// MARCA DE TIEMPO
		$timeStampImg		= strtotime("now");

		// Nombre final de la imagen
		$full_image_name	= "pet-" . sha1($id_code_qr) . "-" . $timeStampImg;

		// DATOS DE LA IMAGEN
		$name_image  = $img_pet['name'];
		$tmp_image   = $img_pet['tmp_name'];
		$type_image  = $img_pet['type'];
		$size_image  = $img_pet['size'];

		$final_image = 0;

		// VALIDAR SI EXISTE LA IMAGEN
		if ( isset( $img_pet ) && !empty( $name_image ) && !empty( $tmp_image ) ){

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

				//Definimos la calidad de la imagen final
				$calidad = 90;

				// CREAR IMAGEN JEPG
				imagejpeg($lienzo, $path_images . $full_image_name . ".jpg", $calidad);

				//Limpiar memoria
				imagedestroy($img_original);
				//Limpiar memoria
				imagedestroy($lienzo);
				
				// Retornamos nombre de la imagen
				$final_image = $full_image_name . ".jpg";

   			}

		} 

		return $final_image;
		/*
		try {

			//Ruta de la original
			$rtOriginal = $img_pet;

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

			ob_start();

			// Se crea la imagen final en el directorio indicado
			// "./data/imagenesresize/thumb.jpg"
			imagejpeg($lienzo,NULL,$cal);

			$final_image = ob_get_contents();

			ob_end_clean();

		} catch (Exception $e) {
			$final_image = 0;
    		//echo 'Excepción capturada: ',  $e->getMessage(), "\n"; exit;
		}
 
		//echo "<pre>"; print_r($final_image); exit;
		return $final_image;
		*/
	}

	/**
	 * AGREGAR UNA MASCOTA
	 */
	public function addPet($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// FECHA DE CUMPLEANOS DE MASCOTA
		$fechaformatoingles = ($data['birthday_pet'] != '') ? date('Y-m-d', strtotime($data['birthday_pet'])) : "0000-00-00";
		//echo "<pre>"; print_r($fechaformatoingles); exit;

		$redimensionarImgPet = 0;

		// Validamos si viene una imagen
		if (isset($data['img_pet']['tmp_name']) && $data['img_pet']['tmp_name'] != "") {
			// REDIMENSIONAR IMAGEN
			$redimensionarImgPet    = $this->redimensionarImagenMascota($data['img_pet'], $data['id_register_qr']);
		}
		//echo "<pre>"; print_r($redimensionarImgPet); exit;

		// ID del codigo qr
		$idCodeQR = (int) $data['id_register_qr'];

		// Estatus codigo qr
		$status_code_qr = $this->getCodeQRService()->getStatusCodeQR($idCodeQR);
		//echo "<pre>"; print_r($status_code_qr[0]['id_status']); exit;

		// VALIDAMOS ESTATUS DEL CODIGO QR
		if ($status_code_qr[0]['id_status'] == 1) {
			
			// Formamos un array con los datos de la mascota
			$dataPet = array(
				'registration_date' => date('Y-m-d H:i:s'),
				'id_register_qr' => $data['id_register_qr'],
				'type_pet'       => $data['type_pet'],
				'name_pet'       => $data['name_pet'],
				'breed_pet'      => $data['breed_pet'],
				'color_pet'      => $data['color_pet'],
				'size_pet'       => $data['size_pet'],
				'age_pet'        => $data['age_pet'],
				'name_vet'       => $data['name_vet'],
				'phone_vet'      => $data['phone_vet'],
				'microchip_pet'  => $data['microchip_pet'],
				'birthday_pet'   => $fechaformatoingles,
				'img_pet'        => '',//($redimensionarImgPet) ? base64_encode($redimensionarImgPet) : '',
				'image_name'	 => ($redimensionarImgPet) ? $redimensionarImgPet : '',
				'reward'         => $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
				'description_pet'   => $data['description_pet'],
				'id_users'          => (isset($data['id_users'])) ? $data['id_users'] : 0
			);

			//echo "<pre>"; print_r($dataPet); exit;
			
			// Agregar mascota
			$savePet = $this->getPetsModel()->addPet($dataPet);
			//echo "<pre>"; print_r($savePet); exit;
			// Actualizamos el status del codigo qr
			if($savePet)
			{

				// ESTATUS DE CODIGO QR
				$valueStatus = (int) $data['id_status'];

				// Actualizamos el status del codigo qr
				$update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);

			}

		} else {
			$savePet = 0;
		}
		
		return $savePet;
	}

	/**
	 * MODIFICAR UNA MASCOTA
	 */
	public function editPet($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// FECHA DE CUMPLEANOS DE MASCOTA
		$fechaformatoingles = ($data['birthday_pet'] != '') ? date('Y-m-d', strtotime($data['birthday_pet'])) : "0000-00-00";
		//echo "<pre>"; print_r($fechaformatoingles); exit;

		$redimensionarImgPet = 0;

		// Validamos si viene una imagen
		if (isset($data['img_pet']['tmp_name']) && $data['img_pet']['tmp_name'] != "") {
			// REDIMENSIONAR IMAGEN 1
			$redimensionarImgPet    = $this->redimensionarImagenMascota($data['img_pet'], $data['id_register_qr']);
		}
		//echo "<pre>"; print_r($redimensionarImgPet); exit;
		
		// Arreglo con los datos de la mascota
		$dataPet = array(
			'id'              => $data['id'],
			'type_pet'        => $data['type_pet'],
			'name_pet'        => $data['name_pet'],
			'breed_pet'       => $data['breed_pet'],
			'color_pet'       => $data['color_pet'],
			'size_pet'        => $data['size_pet'],
			'age_pet'         => $data['age_pet'],
			'name_vet'        => $data['name_vet'],
			'phone_vet'       => $data['phone_vet'],
			'microchip_pet'   => $data['microchip_pet'],
			'birthday_pet'    => $fechaformatoingles,
			'reward'          => $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
			'description_pet' => $data['description_pet']
		);

		// Validamos si existe la imagen
		if ($redimensionarImgPet) {
			//$dataPet['img_pet'] = base64_encode($redimensionarImgPet);
			$dataPet['image_name'] = ($redimensionarImgPet) ? $redimensionarImgPet : '';
		}
		 
		//echo "<pre>"; print_r($dataPet); exit;

		// Editamos una mascota
		$editPet = $this->getPetsModel()->editPet($dataPet);
		//echo "<pre>"; print_r($editPet); exit;

		return $editPet;
	}

	/**
	 * CREAR IMAGEN DE MASCOTA CON BASE64
	 */
	private function createImgPetBase64($img_base64, $id_code_qr = null)
	{
		//echo "<pre>"; print_r($img_base64); exit;
		//echo "<pre>"; print_r($id_code_qr); exit;

		// Ruta donde se almacenaran las imagenes
		$path_images	= "./public/images/pets/";

		// OBTENEMOS EL NOMBRE ACTUAL DE LA IMAGEN
		$nameImgCurrent	= $this->getNameImagePet($id_code_qr);
		//echo "<pre>"; print_r($nameImgCurrent); exit;

		// VALIDAR SI EXISTE UN NOMBRE DE IMAGEN
		if (count($nameImgCurrent) > 0) {
			// COMPROBAR SI EL NOMBRE DE LA IMG NO ESTA VACIO
			if($nameImgCurrent[0]['image_name'] != "") {
				$nImg = $nameImgCurrent[0]['image_name'];

				if (file_exists($path_images.$nImg)) {
					//eliminando del servidor
					unlink($path_images . $nImg);
				}
			}
		}

		// MARCA DE TIEMPO
		$timeStampImg		= strtotime("now");
		//echo "<pre>"; print_r($timeStampImg); exit;

		// Nombre final de la imagen
		$full_image_name	= "pet-" . sha1($id_code_qr) . "-" . $timeStampImg;
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
	 * AGREGAR UNA MASCOTA EN EL APP MOVIL
	 */
	public function addPetAppMovil($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// FECHA DE CUMPLEANOS DE MASCOTA
		$fechaformatoingles = ($data['birthday_pet'] != '') ? date('Y-m-d', strtotime($data['birthday_pet'])) : "0000-00-00";

		// ID del codigo qr
		$idCodeQR = (int) $data['id_register_qr'];

		// Estatus codigo qr
		$status_code_qr = $this->getCodeQRService()->getStatusCodeQR($idCodeQR);
		//echo "<pre>"; print_r($status_code_qr); exit;

		if ($status_code_qr[0]['id_status'] == 1) {
			
			// Formamos un array con los datos de la mascota
			$dataPet = array(
				'registration_date' => date('Y-m-d H:i:s'),
				'id_register_qr' 	=> $data['id_register_qr'],
				'type_pet'       	=> $data['type_pet'],
				'name_pet'       	=> $data['name_pet'],
				'breed_pet'      	=> $data['breed_pet'],
				'color_pet'      	=> $data['color_pet'],
				'size_pet'       	=> $data['size_pet'],
				'age_pet'        	=> $data['age_pet'],
				'name_vet'       	=> $data['name_vet'],
				'phone_vet'      	=> $data['phone_vet'],
				'microchip_pet'  	=> $data['microchip_pet'],
				'birthday_pet'   	=> $fechaformatoingles,
				//'img_pet'        	=> ($redimensionarImgPet) ? base64_encode($redimensionarImgPet) : '',
				'reward'         	=> $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
				'description_pet'   => $data['description_pet'],
				'id_users'          => (isset($data['id_users'])) ? $data['id_users'] : 0
			);
			//echo "<pre>"; print_r($dataPet); exit;
			// VALIDAMOS SI VIENE IMAGEN
			if($data['img_pet'] != "") {

				//Llamamos a la funcion para crear imagen
				$fullImgPet = $this->createImgPetBase64($data['img_pet'], $data['id_register_qr']);
				//echo "<pre>"; print_r($fullImgPet); exit;

				// AGREGAMOS UN DATO AL ARREGLO
				$dataPet['img_pet'] 	= $data['img_pet'];
				$dataPet['image_name'] 	= ($fullImgPet) ? $fullImgPet : '';
			}
			//echo "<pre>"; print_r($dataPet); exit;
			
			// Agregar mascota
			$savePet = $this->getPetsModel()->addPet($dataPet);
			//echo "<pre>"; print_r($saveArticles); exit;
			
			// VALIDAMOS SI SE AGREGO EL REGISTRO
			if($savePet)
			{

				// ESTATUS DE CODIGO QR
				$valueStatus = (int) $data['id_status'];

				// Actualizamos el status del codigo qr
				$update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, $valueStatus);

			}

		} else {
			$savePet = 0;
		}
		
		return $savePet;
	}

	/**
	 * MODIFICAR UNA MASCOTA EN EL APP MOVIL
	 */
	public function editPetAppMovil($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// FECHA DE CUMPLEANOS DE MASCOTA
		$fechaformatoingles = ($data['birthday_pet'] != '') ? date('Y-m-d', strtotime($data['birthday_pet'])) : "0000-00-00";

		// Arreglo con los datos de la mascota
		$dataPet = array(
			'id'              => $data['id'],
			'type_pet'        => $data['type_pet'],
			'name_pet'        => $data['name_pet'],
			'breed_pet'       => $data['breed_pet'],
			'color_pet'       => $data['color_pet'],
			'size_pet'        => $data['size_pet'],
			'age_pet'         => $data['age_pet'],
			'name_vet'        => $data['name_vet'],
			'phone_vet'       => $data['phone_vet'],
			'microchip_pet'   => $data['microchip_pet'],
			'birthday_pet'    => $fechaformatoingles,
			'reward'          => $data['reward'],//preg_replace('/[^0-9-.]+/', '', $data['reward']),
			'description_pet' => $data['description_pet']
		);

		// VALIDAMOS SI VIENE IMAGEN
		if($data['img_pet'] != "") {

			//Llamamos a la funcion para crear imagen
			$fullImgPet = $this->createImgPetBase64($data['img_pet'], $data['id_register_qr']);
			//echo "<pre>"; print_r($fullImgPet); exit;

			// AGREGAMOS UN DATO AL ARREGLO
			$dataPet['image_name'] 	= ($fullImgPet) ? $fullImgPet : '';

			// AGREGAMOS UN DATO AL ARREGLO
			$dataPet['img_pet'] 	= $data['img_pet'];
		}

		//echo "<pre>"; print_r($dataPet); exit;

		// Editamos un articulo
		$editPet = $this->getPetsModel()->editPet($dataPet);

		return $editPet;
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
	 * ELIMINAR UNA MASCOTA
	 */
	public function deletePet($id)
	{
		$idCodeQR = (int) $id;

		// Actualizamos el status del codigo qr
		$update_status_code_qr = $this->getCodeQRService()->updateStatusCodeQR($idCodeQR, 5);
		//print_r($update_status_code_qr); exit;

		return $update_status_code_qr;
	}

	/**
	 * OBTENER LA UBICACION DE UNA MASCOTA
	 */
	public function getLastLocationPet($idPet)
	{
		$location_pet = $this->getPetsModel()->getLastLocationPet((int) $idPet);
		$result       = array();
		
		if(isset($location_pet[0])){
			$result = $location_pet[0];
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

	private function resize($image)
	{
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
		$pushNotifications = $this->getPetsModel()->getAllPushNotifications($idUser);

		return $pushNotifications;
	}

	/**
	 * GUARDAR NOTIFICACION
	 */
	private function saveNotification($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// ID
		$idPet 		  	 = (int) $data['id_pet'];

		// OBTENER ULTIMA UBICACION DE LA MASCOTA
		$getLastLocation = $this->getLastLocationPet($idPet);
		//echo "<pre>"; print_r(count($getLastLocation)); exit;

		// ARREGLO CON LOS DATOS DE LA NOTIFICACION
		$dataSaveNotification = array(
			"id_user" 			 	=> $data['id_user'],
			"id_pet" 			 	=> $data['id_pet'],
			"id_location_pet"		=> (count($getLastLocation) > 0) ? $getLastLocation['id_location'] : "",
			"title_notification"	=> 'Enhorabuena!',
			"message_notificacion"	=> $data['message_notificacion'],
			"contact_name" 			=> $data['name_p'],
			"contact_email" 		=> $data['email_p'],
			"contact_phone" 		=> $data['phone_p'],
			"contact_comment" 		=> $data['comment_p'],
			"type_notification"		=> (int) 2
		);

		// VALIDAMOS SI ES UNA INSTITUCION
		//var_dump($data['isFound']); exit;
		if(isset($data['isFound']) && $data['isFound'] == true) {

			// OBTENEMOS EL DETALLE DE LA DEVOLUCION

			// AGREGAMOS CAMPOS AL ARREGLO
			$dataSaveNotification['isFound']			= (isset($data['isFound'])) ? $data['isFound'] : "";
			$dataSaveNotification['warehouse']			= (isset($data['warehouse'])) ? $data['warehouse'] : "";
			$dataSaveNotification['phone_warehouse']	= (isset($data['phone_warehouse'])) ? $data['phone_warehouse'] : "";
			$dataSaveNotification['comment_warehouse']	= (isset($data['comment_warehouse'])) ? $data['comment_warehouse'] : "";

		} else {
			// AGREGAMOS CAMPOS AL ARREGLO
			$dataSaveNotification['isFound']			= 0;
			$dataSaveNotification['warehouse']			= "";
			$dataSaveNotification['phone_warehouse']	= "";
			$dataSaveNotification['comment_warehouse']	= "";
		}

		//echo "<pre>"; print_r($dataSaveNotification); exit;

		// GUARDAMOS LA NOTIFICACION
		$saveNotification = $this->getPetsModel()->saveNotificacion($dataSaveNotification);
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
		//echo "<pre>"; print_r($dataStatusNotification); exit;

		// GUARDAMOS LA NOTIFICACION
		$updateStatusNotification = $this->getPetsModel()->updateStatusNotification($dataStatusNotification);
		//echo "<pre>"; print_r($updateStatusNotification); exit;

		return $updateStatusNotification;

	}

	public function notificationFoundPet($data)
	{
		//echo "<pre>"; print_r($data); exit;
		//id_article y el id_user
		$userId  = trim($data['id_user']);
		$petId   = trim($data['id_pet']);

		// ********************************
		// OBTENER ARTICULO
		// ********************************
		$infoPet = $this->getPetById($petId);
		//echo "<pre>"; print_r($infoPet); exit;

		// ********************************
		// OBTENER DISPOSITIVOS REGISTRADOS
		// ********************************
		$arrDevice   = $this->getDeviceUserModel()->getAllDeviceByIdUser($userId);
		//echo "<pre>"; print_r($arrDevice); exit;
		
		// VALIDAMOS SI EXISTE ALGUN DISPOSITIVO
		if (count($arrDevice) > 0) {

			// NOMBRE DE LA MASCOTA
			$petName = ($infoPet['name_pet'] != "") ? $infoPet['name_pet'] : "Nombre no asignado";

			// MENSAJE DE LA NOTIFICACION
			$messageNotificacion = "Hola, encontré a tú mascota: $petName. Contactame!";

			// AGREGAMOS UN VALOR AL ARRAY DATA
			$data['message_notificacion'] = $messageNotificacion;

			// ********************************
			// GUARDAR LA NOTIFICACION
			// ********************************
			$saveNotification = $this->saveNotification($data);
			//echo "<pre>"; print_r($saveNotification); exit;
		
			// RECORREMOS CADA DISPOSITIVO ENCONTRADO
			foreach ($arrDevice as $device) {

	    		//IDENTIFICADOR DEL DISPOSITIVO LO REGRESA LA APP AL HACER EL LOGIN 
				$idDevice			= $device['Key_device'];
				$title             	= "Enhorabuena!";
				$message           	= $messageNotificacion;
				$contact_name      	= $data['name_p'];
				$contact_email     	= $data['email_p'];
				$contact_phone     	= $data['phone_p'];
				$contact_comment   	= $data['comment_p'];
				
				// Arreglo con los datos de la notificacion
				$fields = array (
					'to' =>  $idDevice,
					'priority' => 'high',
					'notification' => array (
							"sound" => "default",
							"title" => $title,
							"body"  => $message
					),
					'data' => array (
						"idPet"         	=> $petId,
						"contact_name"      => $contact_name,
						"contact_email"     => $contact_email,
						"contact_phone"     => $contact_phone,
						"contact_comment"   => $contact_comment
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
	}

	/**
	 * ENVIAR CORREO ELECTRONICO
	 */
	public function sendEMailPetFound($data)
	{
		//echo "<pre>"; print_r($data); exit;
		// ENVIAMOS NOTIFICACION
		$notificationFoundPet = $this->notificationFoundPet($data);
		//echo "<pre>"; print_r($notificationFoundPet); exit;

		// ID DE LA MASCOTA
		$petId                = trim($data['id_pet']);	
		
		// OBTENER DATOS DE UNA MASCOTA
		$infoPet              = $this->getPetById($petId);
		//echo "<pre>"; print_r($infoPet); exit;

		$message = new Message();
		$message->addTo(trim($data['email_owner']))
		    ->addFrom('noreply@pegalinas.com')
		    ->setSubject('!Mascota encontrada¡');
		    
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
			
		// CONTENIDO DEL CORREO ELECTRONICO
		$messageEmail =  '<div class="row"><div style="background-color: #fff;padding:40px;"><div 
			style="border-radius: 18px 18px 18px 18px; -moz-border-radius: 18px 18px 18px 18px; -webkit-border-radius: 18px 18px 18px 18px; border: 1px solid #57585A; -webkit-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); -moz-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75);"><div><center><div style="padding: 20px;"><a href="#"><img style="pointer-events: none; max-width:100%; " alt="pegalinas recupera"
			src="https://lh3.googleusercontent.com/--X82ko57j5c/WWPRvr9cMpI/AAAAAAAABUY/KfXmWyvkAzkk4Ee1IhCVCwIJX6XrKyPUQCJoC/w530-h188-p-rw/logorecuperamascotaoutline.png" /></a></div><h1 style="color: black;"></h1></center><div
			style="padding-left: 40px; padding-right: 40px; color: black;"><center><h2> ¡Enhorabuena! </h2></center><h3>Encontraron a tu mascota: ' .$infoPet['name_pet']. '</h3><h3>Datos del contacto:</h3><h3>Nombre: ' . $data['name_p'] . ' </h3><h3>Teléfono:  '.$data['phone_p'] .'</h4><h3>Correo Electrónico de contacto: '. $data['email_p'] . '</h3><h3>Comentario:</h3><h3> '. $data['comment_p'] . '</h3><h3>Saludos.</h3></div><br><br><center><a href="https://www.youtube.com/user/pegalinas" target="_blank"><img alt="youtube"
			src="http://icon-icons.com/icons2/70/PNG/64/youtube_14198.png" /></a><a href="https://twitter.com/pegalinas" target="_blank"><img alt="twitter"
			src="http://icon-icons.com/icons2/642/PNG/64/twitter_2_icon-icons.com_59206.png" /></a><a href="https://www.facebook.com/recuperapetMx/" target="_blank"><img alt="facebook"
			src="http://icon-icons.com/icons2/91/PNG/64/facebook_16423.png" /></a></center></div></div></div></div>';
		
		$html       = new MimePart($messageEmail);
		$html->type = "text/html";

		$body = new MimeMessage();
		$body->addPart($html);

		$message->setBody($body);

		$transport->setOptions($options);
		$transport->send($message);

		return "Mensaje Enviado";
	}

	/**
	 * ENVIAR CORREO ELECTRONICO DE CONTACTO
	 */
	public function sendEMailContact($data)
	{
		//echo "<pre>"; print_r($data); exit;
		
		// ID DE LA MASCOTA
		//$petId                = trim($data['id_pet']);	
		
		// OBTENER DATOS DE UNA MASCOTA
		//$infoPet              = $this->getPetById($petId);
		//echo "<pre>"; print_r($infoPet); exit;

		$message = new Message();
		$message->addTo('noreply@pegalinas.com')
		    ->addFrom('noreply@pegalinas.com')
		    ->setSubject('!Contacto¡');
		    
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
			
		// CONTENIDO DEL CORREO ELECTRONICO
		$messageEmail =  '<div class="row"><div style="background-color: #fff;padding:40px;"><div 
			style="border-radius: 18px 18px 18px 18px; -moz-border-radius: 18px 18px 18px 18px; -webkit-border-radius: 18px 18px 18px 18px; border: 1px solid #57585A; -webkit-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); -moz-box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75); box-shadow: 0px 0px 3px 0px rgba(0, 0, 0, 0.75);"><div><center><div style="padding: 20px;"><a href="#"><img style="pointer-events: none; max-width:100%; " alt="pegalinas recupera"
			src="https://lh3.googleusercontent.com/--X82ko57j5c/WWPRvr9cMpI/AAAAAAAABUY/KfXmWyvkAzkk4Ee1IhCVCwIJX6XrKyPUQCJoC/w530-h188-p-rw/logorecuperamascotaoutline.png" /></a></div><h1 style="color: black;"></h1></center><div
			style="padding-left: 40px; padding-right: 40px; color: black;"><h3>Datos del contacto:</h3><h3>Nombre:' . $data['name_p'] . ' </h3><h3>Correo Electrónico de contacto: '. $data['email_p'] . '</h3><h3>Comentario:</h3><h3> '. $data['comment_p'] . '</h3><h3>Saludos.</h3></div><br><br><center><a href="https://www.youtube.com/user/pegalinas" target="_blank"><img alt="youtube"
			src="http://icon-icons.com/icons2/70/PNG/64/youtube_14198.png" /></a><a href="https://twitter.com/pegalinas" target="_blank"><img alt="twitter"
			src="http://icon-icons.com/icons2/642/PNG/64/twitter_2_icon-icons.com_59206.png" /></a><a href="https://www.facebook.com/Pegalinas/" target="_blank"><img alt="facebook"
			src="http://icon-icons.com/icons2/91/PNG/64/facebook_16423.png" /></a></center></div></div></div></div>';
		//echo "<pre>"; print_r($messageEmail); exit;
		
		$html       = new MimePart($messageEmail);
		$html->type = "text/html";

		$body = new MimeMessage();
		$body->addPart($html);

		$message->setBody($body);

		$transport->setOptions($options);
		$transport->send($message);

		return "Mensaje Enviado";
	}

	/**
	 * GUARDAR UBICACION DE UNA MASCOTA
	 */
	public function saveLastLocation($data)
	{

		$dataLastLocation = array(
			'id_pets'      => $data['id_pet'],
			'longitude'   => $data['longitude'],
			'latitude'    => $data['latitude'],
			'addres'      => $data['addres'],
		);
		//echo "<pre>"; print_r($dataLastLocation); exit;

		$saveLastLocation = $this->getPetsModel()->saveLastLocation($dataLastLocation);

		return $saveLastLocation;

	}

	/**
	 * GENERAR CARTEL DE SE BUSCA MASCOTA EXTRAVIADA
	 */
	public function generateImgLostPetSearch($idCode)
	{
		// NOMBRE DE LA IMAGEN GENERADA
		$fileName 	= "";

		// ENLACE AL ARCHIVO DE FUENTES NO AL SERVIDOR
		$fontname	= './public/font/Capriola-Regular.ttf';

		// CONTROLA EL ESPACIO ENTRE EL TEXTO
		$i 			= 30;

		// CALIDAD DE IMAGEN JPG 0-100
		$quality	= 90;

		// DATOS DEL CODIGO QR Y DE LA MASCOTA
		$pet 		= $this->getPetsModel()->getPetByCodeQr($idCode);
		//echo "<pre>"; print_r($pet); exit;

		// VALIDAMOS SI EXISTEN DATOS
		if (count($pet) > 0) {

			// VALIDAMOS SI EXISTE IMAGEN O NO
			if($pet[0]['image_name'] != "") {

				// IMAGEN EN BASE64
				//$imgSringBase64		= $pet[0]['img_pet'];

				// DECODIFICAR LA IMAGEN EN BASE64
				//$dataDecodeImage	= base64_decode($imgSringBase64);

				$pathImgPet		= "./public/images/pets/" . $pet[0]['image_name'];
				//echo "<pre>"; print_r($pathImgPet); exit;

				// CREAR IMAGEN
				//$imageResult 		= imagecreatefromstring($dataDecodeImage);
				$imageResult	= imagecreatefromjpeg($pathImgPet);

			} else {
				// CREAR IMAGEN
				$imageResult 	= imagecreatefrompng("./public/img/icon_pet_real.png");
			}

			//echo "<pre>"; print_r($imageResult); exit;

			// ARREGLO CON DATOS QUE VA A LLEVAR EL CARTEL
			$dataPoster = array(
				array(
					'name'		=> 	$pet[0]['name_pet'],
					'font-size'	=>	'82',
					'color'		=>	'white'
				),
				array(
					'name'		=>	'Tel: ' . $pet[0]['phone'],
					'font-size'	=>	'52',
					'color'		=>	'white'
				),
				array(
					'name'		=>	'RECOMPENSA',
					'font-size'	=>	'52',
					'color'		=>	'white'
				),
				array(
					'name'		=>	$pet[0]['reward'],
					'font-size'	=>	'52',
					'color'		=>	'white'
				)
			);

			// NOMBRE DEL ARCHIVO
			$fileName 		= md5($pet[0]['registration_date'] . $pet[0]['foliocodeqr']) . ".jpg";

			// ARCHIVO
			$filePath 		= "./public/wantedPoster/" . $fileName;

			// IMAGEN DE FONDO
			$imgBackground	= imagecreatefrompng("./public/img/se_busca.png");
			//$imgBackgroundName	= imagecreatefrompng("./public/img/se_busca.png");
			
			// CAMBIAR EL TAMAÑO DE UNA IMAGEN

			//$imgBackground = imagecreatetruecolor(200,500);
			//$srcimage = imagecreatefrompng($src);
			//imagecopyresampled($imgBackground,$imgBackgroundName,0,0,0,0, 200,500,imagesx($imgBackgroundName), imagesy($imgBackgroundName));

			// CAMBIAR EL TAMAÑO DE UNA IMAGEN

			// AGREGAMOS UNA IMAGEN SOBRE OTRA
			// Copia y cambia el tamaño de parte de una imagen
			imagecopyresized(
				$imgBackground,
				$imageResult,
				135, 380, 0, 0,
				800,
				880,
				imagesx($imageResult),
				imagesy($imageResult)
			);

			// CONFIGURAR LOS COLORES DEL TEXTO
			$color['grey']	= imagecolorallocate($imgBackground, 54, 56, 60);
			$color['green']	= imagecolorallocate($imgBackground, 55, 189, 102);
			$color['white']	= imagecolorallocate($imgBackground, 255, 255, 255);

			// DEFINE LA ALTURA INICIAL DEL BLOQUE DE TEXTO
			$y = imagesy($imgBackground) - 450;

			// RECORRE LA MATRIZ Y ESCRIBE EL TEXTO
			foreach($dataPoster as $value) {

				// CENTRAR EL TEXTO EN NUESTRA IMAGEN - DEVUELVE EL VALOR x
				$x = $this->center_text($value['name'], $value['font-size']);

				imagettftext($imgBackground, $value['font-size'], 0, $x, $y + $i, $color[$value['color']], $fontname, $value['name']);

				// AGREGUE 32 PX A LA ALTURA DE LA LINEA PARA EL SIGUIENTE BLOQUE DE TEXTO
				$i = $i + 100;

			}
			

			// CREAR LA IMAGEN
			imagejpeg($imgBackground, $filePath, $quality);

			// DESTRUIR LAS IMAGENES GENERADAS
			imagedestroy($imgBackground);
			imagedestroy($imageResult);

		}

		return $fileName;

	}

	// CENTRAR TEXTO
	private function center_text($string, $font_size) {
		//global $fontname;
		// ENLACE AL ARCHIVO DE FUENTES NO AL SERVIDOR
		$fontname		= './public/font/Capriola-Regular.ttf';
		$image_width	= 1080;
		$dimensions		= imagettfbbox($font_size, 0, $fontname, $string);
		return ceil(($image_width - $dimensions[4]) / 2);
	}


}