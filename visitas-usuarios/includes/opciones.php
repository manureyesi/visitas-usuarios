<?php

	defined('ABSPATH') or die( "Bye bye" );

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
		add_menu_page(VISITAS_NOMBRE, VISITAS_NOMBRE, 'manage_options', VISITAS_RUTA.'/admin/visitas.php'); //Crea el menu
		add_submenu_page(VISITAS_RUTA.'/admin/visitas.php', 'Configuración', 'Configuracion', 'manage_options', VISITAS_RUTA.'/admin/configuracion.php');
	}
		
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
	
	function insertarVisita() {
		echo 'Insertar visita';
		insertarVisitasUsuarios('wordpress', $_SERVER['REMOTE_ADDR'], 'ES', '', '', '', '', '', '', '');
	}
	
?>
