// *****************************************************
// VALIDAR SI SE PUEDE OBTENER LA UBICACION 
// *****************************************************
if ("geolocation" in navigator) {
	console.log('Geolocalización está disponible');
} else {
	console('La geolocalización no está disponible');
}

// Formulario Articulos
var $form_articles                 = $("#articles");

// Formulario Codigo QR
var $form_QR                       = $("#form_codeqr");

// Formulario Articulo encontrado
var $form_articles_found           = $("#form_articles_found");

// Codigo del QR
var $code_qr_article               = $("#code_article");

// Codigo del QR
var $repeat_code_qr_article        = $("#repeat_code_article");

// container_form_articles
var $container_form_articles       = $("#container_form_articles");

// container_form_articles_found
var $container_form_articles_found = $("#container_form_articles_found");

// btn_validate_codeQR
var $btn_validate_codeQR           = $("#btn_validate_codeQR");

// btn_cancel_validate_codeQR
var $btn_cancel_validate_codeQR    = $("#btn_cancel_validate_codeQR");

// id_register_qr
var $id_register_qr                = $("#id_register_qr");

// CONTENEDOR DEL FORMULARIO QR
var $container_form_code_qr        = $("#container_form_code_qr");

// NOMBRE ARTICULO
var $name_article                  = $("#name_article");

// CATEGORIA
var $category                      = $("#category");

// clothes_size
var $clothes_size                  = $("#clothes_size");

// container_clothes_size
var $container_clothes_size        = $("#container_clothes_size");

// ES UN ARTICULO NUEVO;
var IS_NEW_ARTICLE 				   = false;

// VARIABLES GLOBALES
var ID_ARTICLE_VAL;
var ID_USER_VAL;
var EMAIL_OWNER_VAL;

// ************************************************************************
// EVENTOS QUE SE EJECUTAN CUANDO INICIA UN AJAX Y CUANDO TERMINA
// ************************************************************************
$(document)
	.ajaxStart(function () {
		//console.log("Inicia Ajax");
		// Iniciamos panel de carga
		$.LoadingOverlay("show");
	})
	.ajaxStop(function () {
		//console.log("Termina Ajax");
		// ocultamos panel de carga
		$.LoadingOverlay("hide");
	});

// ************************************************************************
// CUANDO EL DOCUMETO ESTA LISTO
// ************************************************************************
$(document).ready(function() {

	// ************************************************************************
	// INICIALIZAMOS LOS COMBOS
	// ************************************************************************
	$('#category, #clothes_size, #color, #name_article').material_select(); //inicializar el select de materialize

	// ************************************************************************
	// PERMITIR SOLO NUMEROS EN EL CAMPO RECOMPENSA
	// ************************************************************************
	$("#reward").keyup(function (e){
 		this.value = (this.value).replace(/[^0-9-.]/g, '');
	});

	// ************************************************************************
	// FORMATO MONEDA EL CAMPO RECOMPENSA
	// ************************************************************************
	$('#reward').blur(function(){
		$('#reward').formatCurrency({ colorize:true });
	});

	// ************************************************************************
	// CAMBIO EN EL CAMPO DE CATEGORIA
	// ************************************************************************
	$category.on('change', function(e) {

		// Valor del elemento sseleccionado
		var $valCat  = $(this).val();
		
		// Texto del elemento seleccionado
		var $txtCat  = $( "#category option:selected" ).text();

		// Si la categoria es diferente de vacio
		if ($valCat != "") {

			// Validamos el tipo de categoria
			if ($valCat == 1 && $txtCat == "Ropa") {

				// FUNCION QUE GENERA LAS OPCIONES DEL SELECT NOMBRE
				getNameArticlesByIdCategory($valCat);

				// Mostramos la talla
				$container_clothes_size.show();

				// Agregamos atributo de requerido
				$clothes_size.attr('data-validetta', 'required');

				// Agregamos un mensaje al atributo
				$clothes_size.attr('data-vd-message-required', 'Por favor ingresa la talla!');

				$('#clothes_size').val("");
	        	$('#clothes_size').material_select();

	        	// Limpiamos el campo de nombre
	        	//$name_article.val("");

			} else if($valCat == 65 || $txtCat == "Varios" || $txtCat == "varios") {

				/***********************************************/
				// MODIFICAMOS EL TIPO DE CAMPO DE SELECT INPUT
				/***********************************************/

				// Eliminamos el combo y creamos una caja de texto
				$('#container_name_article').html('<input name="name_article" id="name_article" class="validate" type="text" value="" /><label for="name_article">Artículo</label>');

				// Ocultamos la talla
				$container_clothes_size.hide();

				// Quitamos atributo
				$clothes_size.removeAttr('data-validetta');

				// Quitamos atributo
				$clothes_size.removeAttr('data-vd-message-required');

				$('#clothes_size').val("");
	        	$('#clothes_size').material_select();

			} else {

				// FUNCION QUE GENERA LAS OPCIONES DEL SELECT NOMBRE
				getNameArticlesByIdCategory($valCat);

				// Ocultamos la talla
				$container_clothes_size.hide();

				// Quitamos atributo
				$clothes_size.removeAttr('data-validetta');

				// Quitamos atributo
				$clothes_size.removeAttr('data-vd-message-required');

				$('#clothes_size').val("");
	        	$('#clothes_size').material_select();

			}

		} else {

			// LIMPIAMOS EL SELECT DE NOMBRE DE ARTICULO
			$('#name_article')
				.empty()
				.append('<option selected="selected" value="">Selecciona un artículo</option>');

			// Seteamos por default una opcion
			$('#name_article').val("");

			// Inicializamos nuevamente el select de nombre de articulo
			$('#name_article').material_select();

		}

	});

    // ************************************************************************
    // CAMBIO EN IMAGEN 1
    // ************************************************************************
	$("#imageone").on('change', function() {
		// LLamamos a la funcion
        previewImgArticle(this,"preview_image_one");
    });

    // ************************************************************************
	// CAMBIO EN IMAGEN 2
	// ************************************************************************
    $("#imagetwo").change(function() {
    	// LLamamos a la funcion
        previewImgArticle(this,"preview_image_two");
    });

   	// ************************************************************************
	// FORMULARIO PARA VALIDAR CODIGO QR
	// ************************************************************************
	$form_QR.validetta({
    		bubblePosition: 'bottom',
    		bubbleGapTop: 10,
        	bubbleGapLeft: 0,
			realTime : true,
			onValid : function( event ) {

				event.preventDefault();

				// Validamos si los codigos qr son correctos
				if ($code_qr_article.val() != $repeat_code_qr_article.val()) {
					// Mostramos alerta
					swal(
		  				'Atención',
		  				'¡Los codigos no coinciden!',
		  				'error'
					).catch(swal.noop);
						
				} else {

	        		$.ajax({
	        			url: $basePath + '/articles/validatecodeqr',
	        			type: 'POST',
	        			dataType: 'json',
	        			data: $(this.form).serialize(),
	        		})
	        		.done(function(response) {
	        			
	        			// VALIDAMOS SI EL CODIGO INGRESADO EXISTE
	        			if (response.data[0].count == 1) {

	        				// VALIDAMOS QR (ARTICULOS - MASCOTAS)
	        				if(response.data[0].type_qr == 1) {

		     					// VALIDAMOS EL ESTATUS DEL CODIGO
		     					if (response.data[0]['id_status'] == 1 && response.data[0]['id_article'] == -1) {

		     						//Cambiamos bandera que indica que el artículo es nuevo
									IS_NEW_ARTICLE = true;
		     						
			        				swal(
					  					'Correcto..',
					  					'¡El código ingresado es valido!',
					  					'success'
									).catch(swal.noop);

									// OCULTAMOS EL CONTENEDOR DEL FORM CODIGO QR
									$container_form_code_qr.slideUp();

									$container_form_articles.show("slow");

									$code_qr_article.prop("disabled", true);

									$repeat_code_qr_article.prop("disabled", true);

									$btn_validate_codeQR.prop("disabled", true);

									$btn_cancel_validate_codeQR.attr("disabled", true);

									// Añadimos un valor al campo $id_register_qr
									$id_register_qr.val(response.data[0]['id']);

		     					} else if(response.data[0]['id_status'] != 1 && response.data[0]['id_article'] != -1) {

		     						// AGREGAR ARTICULO CON CODIGO QR ASIGNADO

		     						// Cambiamos bandera que indica que el artículo esta registrado por otro usuario
									IS_NEW_ARTICLE = false;

									// OBTENEMOS UBICACION
									navigator.geolocation.getCurrentPosition(success, error);

									// MSG DE ALERTA
		     						swal(
					  					'Correcto..',
					  					'¡El código ingresado es valido. Pero ya esta asignado a un artículo!',
					  					'success'
									).catch(swal.noop);

									// OCULTAMOS EL CONTENEDOR DEL FORM CODIGO QR
									$container_form_code_qr.slideUp();

		     						// MOSTRAMOS EL CONTENEDOR DEL FORM DE ARTICULOS ENCONTRADOS
									$container_form_articles_found.show('slow');

									$code_qr_article.prop("disabled", true); // Deshabilitamos campo de texto

									$repeat_code_qr_article.prop("disabled", true); // Deshabilitamos campo de texto

									$btn_validate_codeQR.prop("disabled", true); // Deshabilitamos boton

									$btn_cancel_validate_codeQR.attr("disabled", true); // Deshabilitamos boton

									// ASIGNAMOS EL ID DEL ARTICULO AL CAMPO id_articles_hs
									$('#id_articles_hs').val(response.data[0].id_article);

									// ASIGNAMOS EL ID DEL QR AL CAMPO id_code_qr
									$('#id_code_qr').val(response.data[0].id);

									// DATOS PARA PODER ENVIAR UNA NOTIFICACION AL USUARIO
									ID_ARTICLE_VAL 	= response.data[0].id_article;
									ID_USER_VAL 	= response.data[0].id_user;
									EMAIL_OWNER_VAL = response.data[0].email_owner;
		     					}

	     					} else if(response.data[0].type_qr == 2) {
								swal(
									'Opsss..',
									'¡El código ingresado es para agregar una mascota!. Intenta con otro.',
									'warning'
								).catch(swal.noop);
							} else {
								swal(
									'Opsss..',
									'¡El código ingresado no es valido!',
									'warning'
								).catch(swal.noop);
							};

	        			} else if(response.data[0].count == 0) {
	        				swal(
			  					'Opsss..',
			  					'¡El código ingresado no es valido!',
			  					'error'
							).catch(swal.noop);
	        			}

	        		})
	        		.fail(function() {
	        			//console.log("error");
	        			swal(
			  				'Opsss..',
			  				'¡Ocurrio un error intentalo de nuevo!',
			  				'error'
						).catch(swal.noop);
	        		})
	        		.always(function() {
	        			//console.log("complete");
	        		});

        		}
        	}
	});

	// ************************************************************************
	// FORMULARIO PARA AGREGAR ARTICULOS ENCONTRADOS
	// ************************************************************************
	$form_articles_found.validetta({
    		bubblePosition: 'bottom',
    		bubbleGapTop: 10,
        	bubbleGapLeft: 0,
			realTime : true,
			onValid : function( event ) {

				event.preventDefault();

				// Validamos si los codigos qr son correctos
				if ($code_qr_article.val() != $repeat_code_qr_article.val()) {
					swal(
		  				'Atención',
		  				'¡Los codigos no coinciden!',
		  				'error'
					).catch(swal.noop);
				} else { // AGREGAMOS EL ARTICULO ENCONTRADO
					
					// OBTENER DATOS DEL FORMULARIO ARTICULO ENCONTRADO
					var dataArticleForm = new FormData($(this.form)[0]);
					//var dataArticleForm = $(this.form).serializeArray();

	        		$.ajax({
	        			url 		: $basePath + '/articles/addarticlefound',
	        			type 		: 'POST',
	        			dataType 	: 'json',
						contentType : false,
						processData : false,
						//data : parametros
	        			//data 		: {dataForm : $(this.form).serializeArray(), dataNotification : dataArticleFoundNotification}//$(this.form).serialize(),
	        			data: dataArticleForm
	        		})
	        		.done(function(response) {
	        			console.log("ADD ARTICLE FOUND: " + JSON.stringify(response));
	        			// {"status":"ok","id_return":"5"}

	        			// validamos si el articulo se agrego correctamente
	        			if (response.status == 'ok') {

	        				// ID DE LA DEVOLUCION DEL ARTICULO ENCONTRADO
	        				var id_return 	= response.id_return;

	        				// TEXTO A MOSTRAR DESPUES DE AGREGAR EL ARTICULO
	        				var textAddArt 	= "El artículo encontrado se agregó exitósamente!";
	        				
							// ALMACENAR DATOS PARA NOTIFICACION
							var dataArticleFoundNotification = [];

							// VALIDAR SI SE AGREGO UN ART NUEVO O YA TIENE ASIGNADO UN CODIGO QR
							// SI ESTA ASIGNADO ENVIAMOS UNA NOTIFICACION
							if(!IS_NEW_ARTICLE) {

								//console.log("ARTICULO CON CODIGO QR ASIGNADO");

								textAddArt = "El artículo encontrado se agregó exitósamente, Y se notificó al dueño!";

								// DATOS PARA GENERAR UNA NOTIFICACION
								//var comentario         		= "Encontramos tu artículo.  ";
								var warehouse_val         	= $("#container_form_articles_found #warehouse").val();
								var phone_warehouse_val  	= $("#container_form_articles_found #phone_warehouse").val();
								var comment_warehouse_val 	= $("#container_form_articles_found #comment").val();

								// ARREGLO CON LOS DATOS DE LA NOTIFICACION
								dataArticleFoundNotification = {
									id_return 			: id_return,
									id_article			: ID_ARTICLE_VAL,
									id_user				: ID_USER_VAL,
									email_owner			: EMAIL_OWNER_VAL,
									id_code_qr			: $('#id_code_qr').val(),
									name_p				: "",
									email_p				: "",
									phone_p				: "",
									comment_p			: "Una institución encontro tu artículo.",
									isFound				: true,
									warehouse			: warehouse_val,
									phone_warehouse		: phone_warehouse_val,
									comment_warehouse	: comment_warehouse_val
								};

								// ENVIAMOS NOTIFICACION AL USUARIO
								sendEmailNotification(dataArticleFoundNotification, textAddArt);								

							} else {
								swal({
									title : 'Artículo Encontrado',
									text  : textAddArt,
									type  : 'info',
									showCancelButton   : false,
									confirmButtonColor : '#3085d6',
									//cancelButtonColor: '#d33',
									confirmButtonText  : 'Aceptar'
								}).catch(swal.noop).then(function () {
									// Redirigimos a la lista de articulos encontrados
									window.location = $basePath + '/articles/findarticles';
								});
							}

	        			} else if(response.status == 'fail') {
	        				swal(
			  					'Opsss..',
			  					'¡Ocurrio un error intentalo de nuevo!',
			  					'error'
							).catch(swal.noop);
	        			}

	        		})
	        		.fail(function() {
	        			swal(
			  				'Opsss..',
			  				'¡Ocurrio un error intentalo de nuevo!',
			  				'error'
						).catch(swal.noop);
	        		})
	        		.always(function() {
	        			//console.log("complete");
	        		});

        		}

        	}
	});
	
});

// ************************************************************************
// CORRECTO AL OBTENER UBICACION
// ************************************************************************
function success(position)
{
    var latitude  = position.coords.latitude;
    var longitude = position.coords.longitude;

    // OBJETO CON LOS DATOS DE LA UBICACION
    var $objDataLocationArticle = {
    	id_articles : $("#id_articles_hs").val(),
    	latitude    : latitude,
    	longitude   : longitude,
    	addres      : ""
    };

    // LLAMAMOS A LA FUNCION QUE GUARDAR LA UBICACION DEL ARTICULO
    saveLastLocationArticle($objDataLocationArticle);
};

// ************************************************************************
// ERROR AL OBTENER UBICACION
// ************************************************************************
function error()
{
    // OBJETO CON LOS DATOS DE LA UBICACION
    var $objDataLocationArticleNot = {
    	id_articles : $("#id_articles_hs").val(),
    	latitude    : 0,
    	longitude   : 0,
    	addres      : ""
    };

    // LLAMAMOS A LA FUNCION QUE GUARDAR LA UBICACION DEL ARTICULO
    saveLastLocationArticle($objDataLocationArticleNot);
};

// ************************************************************************
// GUARDAR LA UBICACION DE LA PERSONA QUE ESCANEA EL CODIGO
// ************************************************************************
function saveLastLocationArticle($dataLocation)
{		
	$.ajax({
		url      : $basePath + '/articles/savelastlocation',
		type     : 'POST',
		dataType : 'json',
		data     : JSON.stringify($dataLocation),
	})
	.done(function(response) {
		//console.log("Guardar ubicacion: " + JSON.stringify(response));
	})
	.fail(function() {
		console.log("error!!!");
	})
	.always(function() {
		//console.log("complete");
	});
};

// ************************************************************************
// funcion que obtiene los nombres de los articulos dependiendo la catgoria
// ************************************************************************
function getNameArticlesByIdCategory($id_category)
{
	// Creamos un select dinamico
	$name_article_dynamic = $('<select class="validate" name="name_article" id="name_article"><option name="name_article" selected="selected" value="">Selecciona un artículo</option></select><label for="name_article">Art&iacute;culo:</label>');

	// Agregamos al contenedor el select
	$('#container_name_article').html($name_article_dynamic).promise().done(function() {

		// Inicializamos el combo
		$('#name_article').material_select();

		$.ajax({
			url: $basePath + '/articles/getallsubcategorys',
			type: 'POST',
			dataType: 'json',
			data: {id_parent: $id_category},
		})
		.done(function(response) {

			// LLENAMOS DINAMICAMENTE EL COMBO DE ARTICULO
			$.each(response.subCategorys, function(index, el) {
				$('#name_article').append('<option value="'+el.id+'">'+el.namecategory+'</option>');
			});

			// Seteamos por default una opcion
			$('#name_article').val("");

			// Inicializamos nuevamente el select de nombre de articulo
			$('#name_article').material_select();

		})
		.fail(function() {
			//console.log("error");
		})
		.always(function() {
			//console.log("complete");
		});

     });
		
}

// ************************************************************************
// FUNCION QUE GENERA UNA PREVIEW DE LA IMAGEN
// ************************************************************************
function previewImgArticle(input, img_art)
{

	if (typeof (FileReader) != "undefined") {

		var image_preview = $("#"+img_art);

		// Limpiar img preview
	 	image_preview.empty();

	 	var reader = new FileReader();

        reader.onload = function (e) {
            image_preview.attr('src', e.target.result);
        }
        
        //image_holder.show();
        reader.readAsDataURL(input.files[0]);

 	} else {
        alert("Este navegador no admite FileReader.");
    }
}

// ************************************************************************
// ENVIAR NOTIFICACION
// ************************************************************************
function sendEmailNotification(dataArticleFoundNotification, textAddArt)
{
	$.ajax({
		url 		: $basePath + '/articles/sendemailarticleowner',
		type 		: 'POST',
		dataType 	: 'json',
		data 		: JSON.stringify(dataArticleFoundNotification),
	})
	.done(function(response) {
		
		console.log("SEND NOTIFICACION: " + JSON.stringify(response));

		// VALIDAMOS EL RESPONSE
		if (response.status == 'ok') {

			swal({
				title: 'Artículo Encontrado',
				text: textAddArt,
				type: 'info',
				showCancelButton: false,
				confirmButtonColor: '#3085d6',
				//cancelButtonColor: '#d33',
				confirmButtonText: 'Aceptar'
			}).catch(swal.noop).then(function () {
				// Redirigimos a la lista de articulos encontrados
				window.location = $basePath + '/articles/findarticles';
			});

		} else {
			// REDIRECCIONAMOS
			window.location = $basePath + '/articles/findarticles';
		}
				        			
	})
	.fail(function() {
		// REDIRECCIONAMOS
		window.location = $basePath + '/articles/findarticles';
	})
	.always(function() {
		//console.log("complete");
	});
}