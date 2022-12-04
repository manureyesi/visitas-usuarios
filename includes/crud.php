<?php

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );

	//Aqui se definen las constantes
	define('VISITAS_RUTA',plugin_dir_path(__FILE__));
	define('VISITAS_NOMBRE','Visitas Usuario');
		
	function consultaVisitasUsuarios($dias) {
		// Calcular desde dias
		$fecha_actual = date("Y-m-d");
		$fecha_calcular=date("Y-m-d",strtotime($fecha_actual."- {$dias} days")); 
		
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'visitas_usuarios';
		$query="SELECT * FROM {$nombre_tabla} WHERE fecha > '{$fecha_calcular}' ORDER BY fecha DESC, hora DESC";
		$resultados=$wpdb->get_results($query);
		
		return $resultados;
	}
	
	function consultaDiasVisitasUsuarios($dias) {
		// Calcular desde dias
		$fecha_actual = date("Y-m-d");
		$fecha_calcular=date("Y-m-d",strtotime($fecha_actual."- {$dias} days")); 
		
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'visitas_usuarios';
		$query="SELECT DISTINCT fecha FROM {$nombre_tabla} WHERE fecha > '{$fecha_calcular}' ORDER BY fecha";
		$resultados=$wpdb->get_results($query);
		
		return $resultados;
	}
	
	function consultaNumeroVisitasUsuariosPorDia($diaConsulta) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'visitas_usuarios';
		$query="SELECT COUNT(ip) as contador_visitas_count FROM {$nombre_tabla} WHERE fecha = '{$diaConsulta}'";
		$resultados=$wpdb->get_results($query);
		
		$contador;
		foreach ($resultados as $resultado) {
			$contador=$resultado->contador_visitas_count;
		}
		
		return $contador;
	}

	function crearTablaVisitas() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$collate = $wpdb->collate;
		$nombre_tabla = $prefix.'visitas_usuarios';
		$sql = "CREATE TABLE {$nombre_tabla} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			fecha date DEFAULT CURRENT_TIMESTAMP NOT NULL,
			hora time DEFAULT CURRENT_TIMESTAMP NOT NULL,
			web varchar(255),
			ip varchar(15),
			pais varchar(255),
			region varchar(255),
			ciudad varchar(255),
			codigoPostal varchar(255),
			coordenadas varchar(255),
			os varchar(255),
			dispositivo varchar(255),
			navegador varchar(255),
			PRIMARY KEY  (id)
			)
		COLLATE {$collate}";
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
	}
	
	function eliminarTablaVisitas() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'visitas_usuarios';
		$sql="DROP TABLE IF EXISTS {$nombre_tabla}";
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$wpdb->query($sql);
  
	}
	
	function insertarVisitasUsuarios($web, $ip, $pais, $region, $ciudad, $codigoPostal, $coordenadas, $os, $dispositivo, $navegador) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'visitas_usuarios';
		$fila=array(
			'web'=>$web,
			'ip'=>$ip,
			'pais'=>$pais,
			'region'=>$region,
			'ciudad'=>$ciudad,
			'codigoPostal'=>$codigoPostal,
			'coordenadas'=>$coordenadas,
			'os'=>$os,
			'dispositivo'=>$dispositivo,
			'navegador'=>$navegador
		);
		$resultado=$wpdb->insert($nombre_tabla,$fila); 
		
	}
	
	function contadorVisitasPorNavegador($navegador, $dias) {
		// Calcular desde dias
		$fecha_actual = date("Y-m-d");
		$fecha_calcular=date("Y-m-d",strtotime($fecha_actual."- {$dias} days")); 
		
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'visitas_usuarios';
		$query="SELECT COUNT(id) as contador_visitas_count FROM {$nombre_tabla} WHERE navegador = '{$navegador}' AND fecha > '{$fecha_calcular}'";
		$resultados=$wpdb->get_results($query);
		
		$contador;
		foreach ($resultados as $resultado) {
			$contador=$resultado->contador_visitas_count;
		}
		
		return $contador;
	}

	function contadorVisitasPorSO($SO, $dias) {
		// Calcular desde dias
		$fecha_actual = date("Y-m-d");
		$fecha_calcular=date("Y-m-d",strtotime($fecha_actual."- {$dias} days")); 
		
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'visitas_usuarios';
		$query="SELECT COUNT(id) as contador_visitas_count FROM {$nombre_tabla} WHERE os = '{$SO}' AND fecha > '{$fecha_calcular}'";
		$resultados=$wpdb->get_results($query);
		
		$contador;
		foreach ($resultados as $resultado) {
			$contador=$resultado->contador_visitas_count;
		}
		
		return $contador;
	}

?>