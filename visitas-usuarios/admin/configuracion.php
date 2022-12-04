<?php

defined('ABSPATH') or die( "Bye bye" );

//Comprueba que tienes permisos para acceder a esta pagina
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));

//Aqui se definen las constantes
define('VISITAS_RUTA',plugin_dir_path(__FILE__));
define('VISITAS_NOMBRE','Visitas Usuario');

//Archivos externos
include(VISITAS_RUTA.'/../includes/opciones.php');
include(VISITAS_RUTA.'/../includes/crud.php');

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar</title>
	
	<link rel="stylesheet" href="css/configuracion.css">
	
</head>

<body>

	<div class="wrap">
		<h2><?php _e( 'Configuración Visitas Usuarios', 'Configuración Visitas Usuarios' ) ?></h2>
		
		Bienvenido a la configuración del pluguin de Visitas de Usuarios.
		
		<br/>
		<br/>
		
		<h3>Integración con el notificador de visitas:</h3>
		<p>Para integrase con el notificador de visitas desde una web se tiene que realizar una llamada GET con los siguientes parametros y token de authorizacion:</p>
		
		<h4>Parametros posibles:</h4>
		
		<table class="default" id="tablaParametros">
			<tr class="title">
				<td>Nome Parametro</td>
				<td>Obligatorio</td>
				<td>Descripción</td>
			</tr>
			<tr>
				<td>ip</td>
				<td>NO</td>
				<td>En caso de non enviarse IP calculase no lado do servidor</td>
			</tr>
			<tr>
				<td>web</td>
				<td>SI</td>
				<td>Url da WEB a que se accedeu</td>
			</tr>
			<tr>
				<td>pais</td>
				<td>NO</td>
				<td>Pais dende donde se visita</td>
			</tr>
			<tr>
				<td>region</td>
				<td>NO</td>
				<td></td>
			</tr>
			<tr>
				<td>ciudad</td>
				<td>NO</td>
				<td></td>
			</tr>
			<tr>
				<td>codigoPostal</td>
				<td>NO</td>
				<td></td>
			</tr>
			<tr>
				<td>cordenadas</td>
				<td>NO</td>
				<td></td>
			</tr>
			<tr>
				<td>os</td>
				<td>NO</td>
				<td></td>
			</tr>
			<tr>
				<td>dispositivo</td>
				<td>NO</td>
				<td></td>
			</tr>
			<tr>
				<td>navegador</td>
				<td>NO</td>
				<td></td>
			</tr>
		</table>
		
		<h4>Token Authorization: <?=getTokenAuthorization();?></h4>
		
		<h3>Curl Exemplo:</h3>
		
<pre>
curl "<?=get_option('siteurl', false);?>/wp-json/visitas/v2/insertarEvento
?ip=77.27.210.17&web=www.pruebas.gal/probas&pais=ES&region=Galicia&ciudad=Santiago
&codigoPostal=36613&cordenadas=42.5963,-8.7643&os=Windows&dispositivo=Lenovo&navegador=Firefox" 
-H "authorization: <?=get_option('visitas_token_authorization', false);?>" 
-H "content-type: application/json" 
-H "Connection: keep-alive" 
</pre>
		
	</div>

</body>
