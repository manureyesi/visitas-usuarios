<?php
/*
Plugin Name: Visitas Usuario
Plugin URI: https://github.com/manureyesi/visitas-usuarios
Description: Plugin que registra visitas en una web de la corporación a traves de un API Rest
Version: 1.0
Author: Manuel Reyes
Author URI: https://github.com/manureyesi
License: CC BY-NC-SA 3.0 ES
*/

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );

	//Aqui se definen las constantes
	define('VISITAS_RUTA',plugin_dir_path(__FILE__));
	define('VISITAS_NOMBRE','Visitas Usuario');

	//Archivos externos
	include(VISITAS_RUTA.'/includes/opciones.php');
	include(VISITAS_RUTA.'/includes/crud.php');

	function activacionPluguinVisitasUsuario() {
		//A partir de aquí escribe todas las tareas que quieres realizar en la activación
		//Vas a añadir una función nueva. La sintaxis de add_option es la siguiente:add_option($nombre,$valor,'',$cargaautomatica)
		//add_option('',255,'','yes');
		crearTablaVisitas();
		generarTokenAuthorization();
		add_option('visitas_dias_visualizacion','30','','yes');
	}

	function desactivacionPluguinVisitasUsuario() {
		eliminarTablaVisitas();
	}

	register_activation_hook(__FILE__, 'activacionPluguinVisitasUsuario');

	register_deactivation_hook(__FILE__, 'desactivacionPluguinVisitasUsuario')

?>
