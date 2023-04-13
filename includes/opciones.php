<?php

	// Iniciar sesion
	//session_start();

	defined('ABSPATH') or die( "Bye bye" );

	// Mostrar errores
	//ini_set('display_errors', 1); 
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);

	/*
 	* Nuevo menu de administrador
 	*/

	// El hook admin_menu ejecuta la funcion rai_menu_administrador
	add_action( 'admin_menu', 'visitas_menu_administrador' );

	add_action( 'rest_api_init', function () {
			register_rest_route( 'visitas/v2', '/insertarEvento/',
			array(
				'methods' => 'GET', 
				'callback' => 'insertarEventoVisita'
			)
			);
	});

	// Top level menu del plugin
	function visitas_menu_administrador() {
		add_menu_page(VISITAS_NOMBRE, VISITAS_NOMBRE, 'edit_pages', VISITAS_RUTA.'/admin/visitas.php', '', 'dashicons-chart-pie', 10);
		add_submenu_page(VISITAS_RUTA.'/admin/visitas.php', 'Configuración', 'Configuracion', 'manage_options', VISITAS_RUTA.'/admin/configuracion.php');
	}
	
	// Shorcode insertar visitas
	add_shortcode('insertar_visitas_web_reservas', 'insertarVisitaswebReservas');
		
	function insertarEventoVisita() {
		
		// Comprobar headers
		if (comprobarHeaders()) {
			
			$ip=$_GET["ip"];
			$web=$_GET["web"];
			$pais=$_GET["pais"];
			$region=$_GET["region"];
			$ciudad=$_GET["ciudad"];
			$codigoPostal=$_GET["codigoPostal"];
			$coordenadas=$_GET["cordenadas"];
			$os=$_GET["os"];
			$dispositivo=$_GET["dispositivo"];
			$navegador=$_GET["navegador"];

			if (empty($ip)) {
				$ip=$_SERVER['REMOTE_ADDR'];			
			}

			insertarVisitasUsuarios($web, $ip, $pais, $region, $ciudad, $codigoPostal, $coordenadas, $os, $dispositivo, $navegador);
			
			$data = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$data = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $data;
	}
	
	/**
	* Comprobar Headers
	*/
	function comprobarHeaders() {
		
		// ver headers
		$apache_headers= apache_request_headers();

		foreach ($apache_headers as $key => $value) {
			if ($key=="authorization" || $key=="Authorization") {
				$authorization=$value;
			}
		}
		
		$permitido=false;
		// Comprobar Header
		if ($authorization==getTokenAuthorization()) {
			$permitido=true;
		}
		
		return $permitido;
	}
	
	function generarTokenAuthorization() {
		
		// Generar token authorization
		$tokenAuthorization=md5(date('l jS \of F Y h:i:s A'));
		
		add_option('visitas_token_authorization',$tokenAuthorization,'','yes');
		
	}
	
	function getTokenAuthorization() {
		return get_option('visitas_token_authorization', false);
	}
	
	function getDiasFiltrados() {
		return get_option('visitas_dias_visualizacion', false);
	}
	
	function setDiasFiltrados($dia) {
		return update_option('visitas_dias_visualizacion', $dia);
	}
	
	function getBrowser() { 
	  $u_agent = $_SERVER['HTTP_USER_AGENT'];
	  $bname = 'Unknown';
	  $platform = 'Unknown';
	  $version= "";

	  //First get the platform?
	  if (preg_match('/linux/i', $u_agent)) {
		$platform = 'Linux';
	  }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'Mac';
	  }elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'Windows';
	  }

	  // Next get the name of the useragent yes seperately and for good reason
	  if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	  }elseif(preg_match('/Firefox/i',$u_agent)){
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	  }elseif(preg_match('/OPR/i',$u_agent)){
		$bname = 'Opera';
		$ub = "Opera";
	  }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
		$bname = 'Google Chrome';
		$ub = "Chrome";
	  }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
		$bname = 'Apple Safari';
		$ub = "Safari";
	  }elseif(preg_match('/Netscape/i',$u_agent)){
		$bname = 'Netscape';
		$ub = "Netscape";
	  }elseif(preg_match('/Edge/i',$u_agent)){
		$bname = 'Edge';
		$ub = "Edge";
	  }elseif(preg_match('/Trident/i',$u_agent)){
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	  }

	  // finally get the correct version number
	  $known = array('Version', $ub, 'other');
	  $pattern = '#(?<browser>' . join('|', $known) .
	')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	  if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
	  }
	  // see how many we have
	  $i = count($matches['browser']);
	  if ($i != 1) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
			$version= $matches['version'][0];
		}else {
			$version= $matches['version'][1];
		}
	  }else {
		$version= $matches['version'][0];
	  }

	  // check if we have a number
	  if ($version==null || $version=="") {$version="?";}

	  return array(
		'userAgent' => $u_agent,
		'name'      => $bname,
		'version'   => $version,
		'platform'  => $platform,
		'pattern'    => $pattern,
		'ub'		=> $ub
	  );
	}

	
	function insertarVisitaswebReservas() {
		
		if (!isset($_SESSION["registroIp"])) {
			$actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			
			// Comprobar paguina
			if (str_contains($actual_link, '/wp-json/wp/v2/pages/6')) {
				$actual_link = "$_SERVER[HTTP_HOST]/accommodation/casa-manola/";
			} else if (str_contains($actual_link, 'post=6')) {
				$actual_link = "$_SERVER[HTTP_HOST]/accommodation/casa-manola/";
			}
			
			$ua=getBrowser();
			insertarVisitasUsuarios($actual_link, $_SERVER['REMOTE_ADDR'], '', '', '', '', '', $ua['platform'], 'Unknown', $ua['ub']);
			$_SESSION["registroIp"] = 'true';
		}
		
	}
	
?>
