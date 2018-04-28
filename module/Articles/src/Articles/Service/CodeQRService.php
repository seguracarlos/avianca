<?php

namespace Articles\Service;

use Articles\Model\CodeQRModel;

class CodeQRService
{
	private $codeQRModel;

	private function getCodeQRModel()
	{
		return $this->codeQRModel = new CodeQRModel();
	}

	/**
	* METODO PARA VALIDAR UN CODIGO QR
	*/
	public function validateTypeCodeQr($code)
	{
		$code_qr_unique = array('foliocodeqr' => $code);
		$resultTypeQr   = array();

		$validateTypeCodeQr = $this->getCodeQRModel()->validateTypeCodeQr($code_qr_unique);

		// VALIDAMOS SI EL CODIGO QR EXISTE
		if($validateTypeCodeQr[0]['count'] == 1) {

			// VALIDAMOS EL TIPO DE CODIGO QR
			if($validateTypeCodeQr[0]['type_qr'] == 1) {

				$resultTypeQr = $this->verifyCodeQrUniqueExists($code);

			} else if($validateTypeCodeQr[0]['type_qr'] == 2) {

				$resultTypeQr = $this->verifyCodeQrUniqueExistsPet($code);

			}

		} else {
			$resultTypeQr = $validateTypeCodeQr;
		}

		return $resultTypeQr;
	}


	/**
	 * Metodo para validar si un codigo de qr existe
	 */
	public function verifyCodeQrUniqueExists($code)
	{
		//print_r($email); exit;
		$code_qr_unique = array('foliocodeqr' => $code);

		$verifyCodeQrUnique = $this->getCodeQRModel()->verifyCodeQrUniqueExists($code_qr_unique);

		return $verifyCodeQrUnique;
	}

	/**
	 * Metodo para validar si un codigo de qr de mascotas existe
	 */
	public function verifyCodeQrUniqueExistsPet($code)
	{
		//print_r($email); exit;
		$code_qr_unique = array('foliocodeqr' => $code);

		$verifyCodeQrUnique = $this->getCodeQRModel()->verifyCodeQrUniqueExistsPet($code_qr_unique);

		return $verifyCodeQrUnique;
	}

	/**
	 * OBTENER STATUS DE CODIGO QR
	 */
	public function getStatusCodeQR($id)
	{
		$code_qr_id = (int) $id;

		$verifyStatusCodeQr = $this->getCodeQRModel()->getStatusCodeQR($code_qr_id);

		return $verifyStatusCodeQr;
	}

	/**
	 * ACTUALIZAR STATUS DE CODIGO QR
	 */
	public function updateStatusCodeQR($id, $status)
	{
		$code_qr_id = (int) $id;

		// DATOS QUE SERAN GUARDADOS EN LA TABLA REGISTER QR
		$dataCodeQr = array(
			'id'        => $id,
			'id_status' => $status
		);
		//print_r($dataCodeQr); exit;

		// GUARDAMOS LOS DATOS EN REGISTER QR
		$updateStatusCodeQr = $this->getCodeQRModel()->updateStatusCodeQR($dataCodeQr);

		return $updateStatusCodeQr;
	}

	/**
	 * bytes seudoaleatorios criptográficamente seguros
	 */
	private function generateCryptographicallySecurePseudoRandomBytes()
	{
		$bytes = bin2hex(random_bytes(4));

		return $bytes;
	}

	/**
	 * Openssl_random_pseudo_bytes - Generos de una cadena de bytes pseudo-aleatoria
	 */
	private function generatePseudoRandomStringBytes()
	{
		$bytes = bin2hex(openssl_random_pseudo_bytes(4));

		return $bytes;
	}

	/**
   	* GENERAR CODIGOS QR - LOCAL
   	*/
  	/*public function generateCodeQrByOrder($data)
  	{
  		//echo "<pre>"; print_r($data); exit;
  		//$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		//$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
		$protocol = (isset($_SERVER["HTTPS"])) ? 'https://' : 'http://';
  		//echo "<pre>"; print_r($protocol); exit;

  		// NUMEROS DE CODIGOS QR
  		$numberCodeQr    = $data['number_labels'];

  		// BASE PATH
  		$getBasePathFull = $data['getBasePathFull'];

  		// lista de qr
  		$listCodesQR = array();

  		// RECORREMOS DEPENDIENDO LA CANTIDAD DE CODIGOS SOLICITADOS
  		for ($i=0; $i < $numberCodeQr ; $i++) {

  			$number_qr = ( $i + 1 );

  			// GENERAMOS UN VALOR UNICO
  			//$keyUnique = //uniqid('rmx_');
  			//$securePseudoRandomBytes = $this->generateCryptographicallySecurePseudoRandomBytes();
  			$securePseudoRandomBytes = strtoupper($this->generatePseudoRandomStringBytes());

  			$moduleCodeQR = "articles";

  			// tipo de codigo
  			if( isset($data['type_qr']) && $data['type_qr'] != "" && $data['type_qr'] == 1 ) {

  				$moduleCodeQR = "articles";

  			} else if( isset($data['type_qr']) && $data['type_qr'] != "" && $data['type_qr'] == 2 ) {

  				$moduleCodeQR = "pets";

  			}


  			// Contenido del codigo qr

        	//$content_code_qr = $protocol . $_SERVER['SERVER_NAME'] . '/' . substr($getBasePathFull, 1) . '/articles/codeqr/' . $securePseudoRandomBytes;

  			// Current
        	//$content_code_qr = $protocol . $_SERVER['SERVER_NAME'] . '/' . $moduleCodeQR . '/codeqr/' . $securePseudoRandomBytes;

        	// Update
        	$content_code_qr = $protocol . $_SERVER['SERVER_NAME'] . '/' . substr($getBasePathFull, 1)  . '/' . $moduleCodeQR . '/codeqr/' . $securePseudoRandomBytes;

	  		// ARREGLO CON LOS DATOS DEL CODIGO QR
	    	$dataCodeQr = array(
				'id_orders'        => $data['id_order'],
				'id_status'        => 1,
				'foliocodeqr'      => $securePseudoRandomBytes,
				'info_qr'          => $content_code_qr,
				'number_qr'        => $number_qr,
				'type_qr'          => (isset($data['type_qr']) && $data['type_qr'] != "") ? $data['type_qr'] : 1
			);
			//echo "<pre>"; print_r($dataCodeQr); exit;
			// GUARDAMOS EL CODIGO QR
			$addOrder = $this->getCodeQRModel()->generateCodeQrByOrder($dataCodeQr);

			// ***************************************************************************
			// GENERAMOS UN CODIGO QR
			//****************************************************************************

			// Ruta y nombre para la imagen qr que se va a generar
        	$image_qr = "./public/codigos_qr/images_qr/" . $securePseudoRandomBytes . '.svg';

        	// Generamos la imagen del codigo qr
        	$svgCode = \QRcode::svg($content_code_qr, $image_qr);
        	
			// ***************************************************************************
			// GENERAR PDF'S CON LOS CODIGOS QR
			// ***************************************************************************

        	// Ruta del codigo qr generado
			//$path_img_qr =  $getBasePathFull . '/codigos_qr/images_qr/' . $securePseudoRandomBytes . '.svg';
			$path_img_qr =  $protocol . $_SERVER['SERVER_NAME'] . $getBasePathFull . '/codigos_qr/images_qr/' . $securePseudoRandomBytes . '.svg';
			//echo "<pre>"; print_r($path_img_qr); exit;

			$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array('34','34'), true, 'UTF-8', false);

			// remove default header/footer
        	$pdf->setPrintHeader(false);
        	$pdf->setPrintFooter(false);

        	// set margins
        	$pdf->SetMargins(0, 0, 0);

        	// set auto page breaks
        	$pdf->SetAutoPageBreak(false, 0);

        	// set image scale factor
        	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        	// add a page
        	$pdf->AddPage();

        	$pdf->ImageSVG($file=$path_img_qr, $x=-0.5, $y=-0.5, $w='', $h='', $link='', $align='', $palign='', $border=0, $fitonpage=false);

        	//Close and output PDF document
        	//$pdf->Output('example_058.pdf', 'D');
        	//print_r($_SERVER['DOCUMENT_ROOT'] . substr($getBasePathFull, 1)); exit;
        	$pdf->Output($_SERVER['DOCUMENT_ROOT'] . substr($getBasePathFull, 1) .'/codigos_qr/pdf_qr/' . $securePseudoRandomBytes . '.pdf', 'F');

        	//echo "Termine!"; exit;

        	$listCodesQR[] = array(
        		'folio_order' => $data['folio_order'],
        		'date create' => date('Y-m-d H:i:s'),
        		'code_qr'     => $securePseudoRandomBytes,
        		'info_qr'     => $content_code_qr,
        		'name_pdf'    => $securePseudoRandomBytes . '.pdf',
        		'root_pdf'    => $_SERVER['DOCUMENT_ROOT'] . substr($getBasePathFull, 1) .'/codigos_qr/pdf_qr/' . $securePseudoRandomBytes . '.pdf',
        		'path_pdf'    => $protocol . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/' . substr($getBasePathFull, 1) .'/codigos_qr/pdf_qr/' . $securePseudoRandomBytes . '.pdf',
        		'number_qr'   => $number_qr

        	);

		}

		return $listCodesQR;

  	}*/

  	/**
   	* GENERAR CODIGOS QR
   	*/
  	public function generateCodeQrByOrder($data)
  	{
  		//echo "<pre>"; print_r($data); exit;
  		//$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		//$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
		$protocol = (isset($_SERVER["HTTPS"])) ? 'https://' : 'http://';
  		//echo "<pre>"; print_r($protocol); exit;

  		// NUMEROS DE CODIGOS QR
  		$numberCodeQr    = $data['number_labels'];

  		// BASE PATH
  		$getBasePathFull = $data['getBasePathFull'];

  		// lista de qr
  		$listCodesQR = array();

  		// RECORREMOS DEPENDIENDO LA CANTIDAD DE CODIGOS SOLICITADOS
  		for ($i=0; $i < $numberCodeQr ; $i++) {

  			$number_qr = ( $i + 1 );

  			// GENERAMOS UN VALOR UNICO
  			//$keyUnique = //uniqid('rmx_');
  			//$securePseudoRandomBytes = $this->generateCryptographicallySecurePseudoRandomBytes();
  			$securePseudoRandomBytes = strtoupper($this->generatePseudoRandomStringBytes());

  			$moduleCodeQR = "articles";

  			// tipo de codigo
  			if( isset($data['type_qr']) && $data['type_qr'] != "" && $data['type_qr'] == 1 ) {

  				$moduleCodeQR = "articles";

  			} else if( isset($data['type_qr']) && $data['type_qr'] != "" && $data['type_qr'] == 2 ) {

  				$moduleCodeQR = "pets";

  			}


  			// Contenido del codigo qr

        	//$content_code_qr = $protocol . $_SERVER['SERVER_NAME'] . '/' . substr($getBasePathFull, 1) . '/articles/codeqr/' . $securePseudoRandomBytes;

  			// Current
        	//$content_code_qr = $protocol . $_SERVER['SERVER_NAME'] . '/' . $moduleCodeQR . '/codeqr/' . $securePseudoRandomBytes;

        	// Update
        	$content_code_qr = $protocol . $_SERVER['SERVER_NAME'] . $getBasePathFull  . '/' . $moduleCodeQR . '/codeqr/' . $securePseudoRandomBytes;
        	//print_r($content_code_qr); exit;

	  		// ARREGLO CON LOS DATOS DEL CODIGO QR
	    	$dataCodeQr = array(
				'id_orders'        => $data['id_order'],
				'id_status'        => 1,
				'foliocodeqr'      => $securePseudoRandomBytes,
				'info_qr'          => $content_code_qr,
				'number_qr'        => $number_qr,
				'type_qr'          => (isset($data['type_qr']) && $data['type_qr'] != "") ? $data['type_qr'] : 1
			);
			//echo "<pre>"; print_r($dataCodeQr); exit;
			// GUARDAMOS EL CODIGO QR
			$addOrder = $this->getCodeQRModel()->generateCodeQrByOrder($dataCodeQr);
			//echo "<pre>"; print_r($addOrder); exit;

			// ***************************************************************************
			// GENERAMOS UN CODIGO QR
			//****************************************************************************

			// Ruta y nombre para la imagen qr que se va a generar
			// LOCAL
        	$image_qr = "./public/codigos_qr/images_qr/" . $securePseudoRandomBytes . '.svg';
        	//echo "<pre>"; print_r($image_qr); exit;
        	
        	// PRODUCCION
        	//$image_qr = $_SERVER['DOCUMENT_ROOT']. substr($getBasePathFull, 1) . '/codigos_qr/images_qr/' . $securePseudoRandomBytes . '.svg';
        	//echo "<pre>"; print_r($image_qr); exit;

        	// FALTA VALIDAR EL TIPO DE PROTOCOLO PARA ASIGNAR UN IMAGE PATH

        	// Generamos la imagen del codigo qr
        	$svgCode = \QRcode::svg($content_code_qr, $image_qr);
        	//echo "<pre>"; print_r($svgCode); exit;
        	
			// ***************************************************************************
			// GENERAR PDF'S CON LOS CODIGOS QR
			// ***************************************************************************

        	// Ruta del codigo qr generado
			//$path_img_qr =  $getBasePathFull . '/codigos_qr/images_qr/' . $securePseudoRandomBytes . '.svg';
			$path_img_qr =  $protocol . $_SERVER['SERVER_NAME'] . $getBasePathFull . '/codigos_qr/images_qr/' . $securePseudoRandomBytes . '.svg';
			//echo "<pre>"; print_r($path_img_qr); exit;

			$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array('34','34'), true, 'UTF-8', false);

			// remove default header/footer
        	$pdf->setPrintHeader(false);
        	$pdf->setPrintFooter(false);

        	// set margins
        	$pdf->SetMargins(0, 0, 0);

        	// set auto page breaks
        	$pdf->SetAutoPageBreak(false, 0);

        	// set image scale factor
        	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        	// add a page
        	$pdf->AddPage();

        	$pdf->ImageSVG($file=$path_img_qr, $x=-0.5, $y=-0.5, $w='', $h='', $link='', $align='', $palign='', $border=0, $fitonpage=false);

        	//Close and output PDF document
        	//$pdf->Output('example_058.pdf', 'D');
        	//print_r($_SERVER['DOCUMENT_ROOT'] . substr($getBasePathFull, 1)); exit;
        	$pdf->Output($_SERVER['DOCUMENT_ROOT'] . substr($getBasePathFull, 1) .'/codigos_qr/pdf_qr/' . $securePseudoRandomBytes . '.pdf', 'F');

        	//echo "Termine!"; exit;

        	$listCodesQR[] = array(
        		'folio_order' => $data['folio_order'],
        		'date create' => date('Y-m-d H:i:s'),
        		'code_qr'     => $securePseudoRandomBytes,
        		'info_qr'     => $content_code_qr,
        		'name_pdf'    => $securePseudoRandomBytes . '.pdf',
        		'root_pdf'    => $_SERVER['DOCUMENT_ROOT'] . substr($getBasePathFull, 1) .'/codigos_qr/pdf_qr/' . $securePseudoRandomBytes . '.pdf',
        		'path_pdf'    => $protocol . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/' . substr($getBasePathFull, 1) .'/codigos_qr/pdf_qr/' . $securePseudoRandomBytes . '.pdf',
        		'number_qr'   => $number_qr

        	);

		}

		return $listCodesQR;

  	}

  	/**
  	 * OBTENER LISTA DE LOS 
  	 */

}