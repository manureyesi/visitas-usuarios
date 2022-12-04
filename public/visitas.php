<?php

defined('ABSPATH') or die( "Bye bye" );

//Aqui se definen las constantes
define('VISITAS_RUTA',plugin_dir_path(__FILE__));
define('VISITAS_NOMBRE','Visitas Usuario');

//Archivos externos
include(VISITAS_RUTA.'/../includes/opciones.php');
include(VISITAS_RUTA.'/../includes/crud.php');

function insertarVisita() {
	insertarVisitasUsuarios('wordpress', $_SERVER['REMOTE_ADDR'], 'ES', '', '', '', '', '', '', '');
}



?>