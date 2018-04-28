<?php

namespace Auth\Service;

use Zend\Session\Container;
use Zend\Crypt\Password\Bcrypt;

// Login Model
use Auth\Model\LoginModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

class LoginService
{
	// Variable para almacenar un obj
	private $loginModel;

	/**
	 * Instancia del login model
	 */
	private function getLoginModel()
	{
		return $this->loginModel = new LoginModel();
	}


	/**
	 * ENVIAR CORREO ELECTRONICO AL CONTACTO
	 */
	public function sendEMailContact($data)
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
							
						<center><h2> ¡Enhorabuena! </h2></center>
				           <h3>' . $data['name_p'] . ' quiere contactar a Recupera</h3>
		                  <h3>Datos del contacto:</h3>
		                  <h4>Nombre de la empresa o institución: '.$data['name_empresa'] .'</h4>
		                  <h4>Puesto:  '.$data['puesto_p'] .'</h4>
		                  <h4>Teléfono:  '.$data['phone_p'] .'</h4>
		                  <h4>Correo Electrónico de contacto: '. $data['email_p'] . '</h4>
		                  <h3>Comentario:</h3><br><h4> '. $data['comment_p'] . '</h4>

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
							<a href="https://www.facebook.com/RecuperaMx/" target="_blank"><img alt="facebook"
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




	/**
	 * Metodo para autenticar al usuario
	 */
	public function authenticationUser($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// Metodo para encriptar el password
		$bcrypt = new Bcrypt(array(
			'salt' => '$2y$05$KkFmCjGPJiC1jdt.SFcJ5uDXkF1yYCQFgiQIjjT6p.z7QIHyU1elW',
			'cost' => 5
		));
		
		// Contrasena segura
		$securePass = $bcrypt->create(strip_tags($data['password_user']));
		//echo "<pre>"; print_r($securePass); exit;

		// Arreglo con los datos a enviar al model
		$dataDB = array(
			'username' => strip_tags($data['email_user']),
			'password' => $securePass
		);
		//echo "<pre>"; print_r($dataDB); exit;

		// Llamamos al metodo autenticar del model
		$auth = $this->getLoginModel()->authenticationUser($dataDB);

		// Valinamos el resultado de la autenticacion
		if ($auth['code'] == 1) {
			
			// Generamos sesion al usuario autenticado
			$userSession = $this->_getUserSession($auth);
			//echo "<pre>"; print_r($userSession); exit;

		}

		//echo "<pre>"; print_r($auth); exit;

		// Regresamos un valor
		return $auth;
	}

	/**
	 * Metodo para generar una sesion al usuario
	 */
	private function _getUserSession($userDetails)
	{
		$session = new Container('PegalinasUser');
    	$session->offsetSet('id', $userDetails['id']);
    	$session->offsetSet('email', $userDetails['email']);
    	$session->offsetSet('perfil', $userDetails['perfil']);

    	return $session;
	}

	/**
	 * Metodo para cerrar sesion del usuario
	 */
	public function logoutUser()
	{
		// Llamamos al metodo cerrar sesion del model
		$logout = $this->getLoginModel()->logoutUser();
		
		// Destruimos la sesion
		$session = new Container('PegalinasUser');
        $session->getManager()->destroy();

		return $logout;
	}

	/**
	 * Metodo para validar si un email ya existe
	 */
	public function verifyEmailExists($email)
	{
		//print_r($email); exit;
		$emailFull = array('email' => $email);

		$verifyEmail = $this->getLoginModel()->verifyEmailExists($emailFull);

		return $verifyEmail;
	}

	/**
	 * Metodo para validar si un token ya existe
	 */
	public function verifyToken($tokenExisting)
	{
		//print_r($tokenExisting); exit;
		$tokenExistingFull = array('token' => $tokenExisting);

		$verifyToken = $this->getLoginModel()->verifyToken($tokenExistingFull);

		return $verifyToken;
	}


		/**
	 * ENVIAR CORREO ELECTRONICO PARA RECUPERAR CONTRASEÑA
	 */
	public function sendEMailRestorePassword($data,$token)
	{

		//echo '<pre>'; print_r($data);exit;
		//$url = $token;
		//OBTENER CORREO DEL DUENO DEL ARTICULO

		$message = new Message();
		$message->addTo($data['email_user'])
		    ->addFrom('segurac.carlos@gmail.com')
		    ->setSubject('!Recuperación de contraseña¡');
		    
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
							
							<h2> Hola </h2>
				           <br><h3>Detectamos que olvidaste tu contraseña.<br>
		                  <h3>Para recuperarla da clic en el botón Recuperar contraseña.<br> <br>
		                  <a href="http://localhost:8081/recuperalo/public/auth/passwordrecovery/'.$token.'" target="_self"><button style="width: 200px; height: 50px; background-color: #57585A; color: white; font-style: inherit;">RECUPERAR CONTRASEÑA</button></a>
						  <h3><font color="red">Este token estará activo por 1 hora</font></h3>

						</div>
						
						<center>
						
						</center>
						<br> <br>
						
					</div>
					<div class="col s4"
						style="border-radius: 0px 0px 28px 28px; -moz-border-radius: 0px 0px 28px 28px; -webkit-border-radius: 0px 0px 28px 28px; background-color: white;">
						<center>
							<a href="https://www.youtube.com/channel/UCnzEiknp5FrTbw3zAUaNpjg" target="_blank"><img alt="youtube"
								src="http://icon-icons.com/icons2/70/PNG/64/youtube_14198.png" /></a>
							<a href="https://twitter.com/pegalinas" target="_blank"><img alt="twitter"
								src="http://icon-icons.com/icons2/642/PNG/64/twitter_2_icon-icons.com_59206.png" /></a>
							<a href="https://business.facebook.com/Pegalinas/" target="_blank"><img alt="facebook"
								src="http://icon-icons.com/icons2/91/PNG/64/facebook_16423.png" /></a>		
							
						</center>
					</div>
				</div>
			</div>
		</div>
	</div>
';
		//$url =  "https://www.horusrobot.mx/cetecreloaded/recoverypass/recovery/recoverypass/" . $token['token'];
        //$url =  "localhost:8081/recuperalo/public/auth/forgot-password/" . $token['token'];
		//echo "<pre>"; print_r($token); exit;
		//echo "<pre>"; print_r($messageEmail); exit;

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



	/**
	 * Metodo para agregar el token
	 */
	public function addToken($data)
	{
		//echo "<pre>"; print_r($data); exit;

		// Arreglo de datos
		$dataToken = array(
			'id_users'    => $data['id'],
			'token' => $data['token'],			
			'token_created'   => date('Y-m-d H:i:s')
		);

		//echo "<pre>"; print_r($dataToken); exit;

		// Agregamos un usuario
		$addToken = $savetoken = $this->getLoginModel()->addToken($dataToken);
		//echo "<pre>"; print_r($addUser); exit;	

		return $addToken;
	}



	/**
	 * ELIMINAR TOKEN
	 */
	public function deleteToken($tokenExisting)
	{
		//print_r($tokenExisting); exit;
		//$tokenExistingFull = array('token' => $tokenExisting);

		$deleteToken = $this->getLoginModel()->deleteToken($tokenExisting);

		return $deleteToken;
	}


	


}