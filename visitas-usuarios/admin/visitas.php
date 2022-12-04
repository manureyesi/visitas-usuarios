<?php

defined('ABSPATH') or die( "Bye bye" );

//Aqui se definen las constantes
define('VISITAS_RUTA',plugin_dir_path(__FILE__));
define('VISITAS_NOMBRE','Visitas Usuario');

//Archivos externos
include(VISITAS_RUTA.'/../includes/opciones.php');
include(VISITAS_RUTA.'/../includes/crud.php');

//Comprueba que tienes permisos para acceder a esta pagina
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver visitas</title>
	
	<link rel="stylesheet" href="css/visitas.css">
	
	<style>
		.graficas {
			width: 500px;
		}

		.container {
			display : flex;
			flex-direction : row;
		}

		.flex-container {
		  display: flex;
		  flex-direction: row;
		}

		/* Responsive layout - makes a one column layout instead of a two-column layout */
		@media (max-width: 1000px) {
		  .flex-container {
			flex-direction: column;
		  }
		}

	</style>
	
    <script src="https://cdn.jsdelivr.net/npm/chart.js@latest/dist/Chart.min.js"></script>
</head>

<body>

	<div class="wrap">
		<h2><?php _e( 'Visitas Usuarios', 'Visitas Usuarios' ) ?></h2>
		Ver estadisticas WEB
		
		<?php
					
			$uri = $_SERVER['REQUEST_URI'];
		
			// Comprobar parametro
			if (isset($_POST['filtrado'])) {
				setDiasFiltrados($_POST['filtrado']);
			}
			
			$diasFiltrados=getDiasFiltrados();
		
		?>
		
		<br/>
		<form action="<?=$uri;?>" method="post">
			<label for="filtrado">Días filtrar:</label>
			<select name="filtrado" id="filtrado">
				<option value="10"<?php
				if($diasFiltrados=="10") {
					echo " selected ";
				}
				?>>10</option>
				<option value="20"<?php
				if($diasFiltrados=="20") {
					echo " selected ";
				}
				?>>20</option>
				<option value="30"<?php
				if($diasFiltrados=="30") {
					echo " selected ";
				}
				?>>30</option>
				<option value="40"<?php
				if($diasFiltrados=="40") {
					echo " selected ";
				}
				?>>40</option>
				<option value="50"<?php
				if($diasFiltrados=="50") {
					echo " selected ";
				}
				?>>50</option>
				<option value="60"<?php
				if($diasFiltrados=="60") {
					echo " selected ";
				}
				?>>60</option>
			</select>
			<input type="submit" value="Refrescar" />
		</form>

		
		<br/>
		
			<div class="flex-container">
				<div class="flex-item-left">
					<h2>Grafica de visitas por día</h1>
					<div class="graficas">
						<canvas id="canvasGraficaVisitas"></canvas>
					</div>
				</div>
				
				<br/>
				<div class="flex-item-center">
					<h2>Grafica de visitas por navegadores</h1>
					<div class="graficas">
						<canvas id="canvasGraficaNavegadores"></canvas>
					</div>
				</div>
				
				<br/>
				<div class="flex-item-right">
					<h2>Grafica de visitas por Sistema Operativo</h1>
					<div class="graficas">
						<canvas id="canvasGraficaOS"></canvas>
					</div>
				</div>
			</div>
			
			
		<br/>
		<br/>
		<br/>
		<br/>
		
		<h2>Lista de Visitas WEB:</h1>
		
		<table class="wp-list-table widefat fixed striped table-view-list posts">
	
		<thead>
		
			<tr class="title">
				<td scope="col" id="idFechaVisita" class="manage-column column-status">Fecha Visita</td>
				<td scope="col" id="idWeb" class="manage-column column-status">WEB</td>
				<td scope="col" id="idIP" class="manage-column column-status">IP</td>
				<td scope="col" id="idPais" class="manage-column column-status">Pais</td>
				<td scope="col" id="idRegion" class="manage-column column-status">Region</td>
				<td scope="col" id="idCiudad" class="manage-column column-status">Ciudad</td>
				<td scope="col" id="idCodPostal" class="manage-column column-status">Codigo Postal</td>
				<td scope="col" id="idCoordenadas" class="manage-column column-status">Coordenadas</td>
				<td scope="col" id="idOs" class="manage-column column-status">OS</td>
				<td scope="col" id="idDispositivo" class="manage-column column-status">Dispositivo</td>
				<td scope="col" id="idNavegador" class="manage-column column-status">Navegador</td>
			</tr>
			
		</thead>
				
		<?php
		
			$resultados=consultaVisitasUsuarios($diasFiltrados);
			
			foreach ($resultados as $resultado) {
				echo '<tr>';
				echo '<td>'.$resultado->fecha.' '.$resultado->hora.'</td>';
				echo '<td>'.$resultado->web.'</td>';
				echo '<td>'.$resultado->ip.'</td>';
				echo '<td>'.$resultado->pais.'</td>';
				echo '<td>'.$resultado->region.'</td>';
				echo '<td>'.$resultado->ciudad.'</td>';
				echo '<td>'.$resultado->codigoPostal.'</td>';
				echo '<td>'.$resultado->coordenadas.'</td>';
				echo '<td>'.$resultado->os.'</td>';
				echo '<td>'.$resultado->dispositivo.'</td>';
				echo '<td>'.$resultado->navegador.'</td>';
				echo '</tr>';
			}
			
		?>
		
		</table>
		
	</div>

</body>

		<script>
			
			const $grafica = document.querySelector("#canvasGraficaVisitas");
			
			<?php
			
				$resultadosDias=consultaDiasVisitasUsuarios($diasFiltrados);
				
				$arrayContadorVisitas='[';
				
				$arrayDias='const etiquetas = [';
				$comprobarInicio=true;
				foreach ($resultadosDias as $dia) {
					if (!$comprobarInicio) {
						$arrayDias=$arrayDias.', ';
						$arrayContadorVisitas=$arrayContadorVisitas.', ';
					}
					$comprobarInicio=false;
					$arrayDias=$arrayDias.'"'.$dia->fecha.'"';
					
					$contadorVisitas=consultaNumeroVisitasUsuariosPorDia($dia->fecha);
					$arrayContadorVisitas=$arrayContadorVisitas.$contadorVisitas;
				}
				$arrayContadorVisitas=$arrayContadorVisitas.']';
				$arrayDias=$arrayDias.'];';
				
				echo $arrayDias;
			?>
			
			const datosVisitas = {
				label: "Visitas por día",
				data: <?=$arrayContadorVisitas;?>, // La data es un arreglo que debe tener la misma cantidad de valores que la cantidad de etiquetas
				backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de fondo
				borderColor: 'rgba(54, 162, 235, 1)', // Color del borde
				borderWidth: 1,// Ancho del borde
			};
			
			new Chart($grafica, {
				type: 'line',// Tipo de gráfica
				data: {
					labels: etiquetas,
					datasets: [
						datosVisitas,
						// Aquí más datos...
					]
				},
				options: {
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero: true
							}
						}],
					},
				}
			});
			
			<?php
			
				$contadorVisitasFirefox=contadorVisitasPorNavegador('Firefox', $diasFiltrados);
				$contadorVisitasChrome=contadorVisitasPorNavegador('Chrome', $diasFiltrados);
				$contadorVisitasEdge=contadorVisitasPorNavegador('MS-Edge-Chromium', $diasFiltrados);
				$contadorVisitasSafari=contadorVisitasPorNavegador('Safari', $diasFiltrados);
				$contadorVisitasUnknown=contadorVisitasPorNavegador('Unknown', $diasFiltrados);
			
			?>
			
			// Donut grafica por navegadores
			const $graficaNav = document.querySelector("#canvasGraficaNavegadores");
			
			const data = {
			  labels: [
				'Firefox',
				'Chrome',
				'Edge',
				'Safari',
				'No reconocido'
			  ],
			  datasets: [{
				label: 'Visitas por navegadores',
				data: [<?=$contadorVisitasFirefox;?>, <?=$contadorVisitasChrome;?>, <?=$contadorVisitasEdge;?>, <?=$contadorVisitasSafari;?>, <?=$contadorVisitasUnknown;?>],
				backgroundColor: [
				  'rgb(255, 99, 132)',
				  'rgb(54, 162, 235)',
				  'rgb(171, 0, 251)',
				  'rgb(3, 193, 78)',
				  'rgb(193, 3, 3)'
				],
				hoverOffset: 4
			  }]
			};
				
			new Chart($graficaNav, {
			  type: 'doughnut',
			  data: data,
			  options: {
				responsive: true,
				plugins: {
				  legend: {
					position: 'top',
				  },
				  title: {
					display: true,
					text: 'Visitas por Navegadores'
				  }
				}
			  },
			});
			
			<?php
			
				$contadorVisitasWindows=contadorVisitasPorSO('Windows', $diasFiltrados);
				$contadorVisitasLinux=contadorVisitasPorSO('Linux', $diasFiltrados);
				$contadorVisitasAndroid=contadorVisitasPorSO('Android', $diasFiltrados);
				$contadorVisitasMac=contadorVisitasPorSO('Mac', $diasFiltrados);
				$contadorVisitasiOS=contadorVisitasPorSO('iOS', $diasFiltrados);
				$contadorVisitasUnknownOS=contadorVisitasPorSO('Unknown', $diasFiltrados);
			
			?>
			
			// Donut grafica por Sistemas Operativos
			const $graficaSO = document.querySelector("#canvasGraficaOS");
			
			const dataSO = {
			  labels: [
				'Windows',
				'Linux',
				'Android',
				'Mac',
				'iOS',
				'No reconocido'
			  ],
			  datasets: [{
				label: 'Visitas por navegadores',
				data: [<?=$contadorVisitasWindows;?>, <?=$contadorVisitasLinux;?>, <?=$contadorVisitasAndroid;?>, <?=$contadorVisitasMac;?>, <?=$contadorVisitasiOS;?>, <?=$contadorVisitasUnknownOS;?>],
				backgroundColor: [
				  'rgb(255, 99, 132)',
				  'rgb(54, 162, 235)',
				  'rgb(171, 0, 251)',
				  'rgb(3, 193, 78)',
				  'rgb(193, 3, 3)',
				  'rgb(193, 3, 123)'
				],
				hoverOffset: 4
			  }]
			};
				
			new Chart($graficaSO, {
			  type: 'doughnut',
			  data: dataSO,
			  options: {
				responsive: true,
				plugins: {
				  legend: {
					position: 'top',
				  },
				  title: {
					display: true,
					text: 'Visitas por SO'
				  }
				}
			  },
			});
			
	</script> 

