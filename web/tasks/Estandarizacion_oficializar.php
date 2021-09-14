<?php
$serverName = "192.168.3.1"; //serverName\instanceName
$connectionInfo = array( "Database"=>"estandarizacion", "UID"=>"estdrzn", "PWD"=>"4wq+Lf-@dBd");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Conexión establecida.";
}else{
     echo "Conexión no se pudo establecer.";
     die( print_r( sqlsrv_errors(), true));
}



////////////////////////////////////////////////////////////////////////////////////////
/*$sql = "SELECT * FROM asignarprocesoversion WHERE estado = 'DESARROLLO'";
//$params = array(1, "some data");

$stmt = sqlsrv_query( $conn, $sql);//, $params);
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

$hoy = date("Y-m-d");

while($f = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){

	$fecha = $f['fecha_inicio'];
	$id = $f['id'];

	if ($fecha == $hoy){
		print_r("dentro if");
		//$change = $conn->query("UPDATE configuracionlinea SET estado = 'PRODUCCION' WHERE id = '$id'");
	
	} else {
		print_r("dentro else");
	} 
}
sqlsrv_free_stmt($stmt);*/

////////////////////////////////////////////////////////////////////////////////////////


$lts = "SELECT * FROM asignarprocesoversion WHERE estado = 'PRE-PRODUCCION'";

$stmt = sqlsrv_query( $conn, $lts);//, $params);
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

$hoy = date("Y-m-d");

while($f = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC))
{
	if($f['fecha_inicio'])
	{
		$fecha = $f['fecha_inicio']->format('Y-m-d');
	
		$id = $f['id'];
		$nombre = $f['nombre_lt'];

		if ($fecha == $hoy){

			$change = "UPDATE asignarprocesoversion SET estado = 'OBSOLETO' WHERE nombre_lt = '$nombre' AND estado = 'PRODUCCION'";
			$stmt_change = sqlsrv_query( $conn, $change);//, $params);
			if( $stmt_change === false) {
			    die( print_r( sqlsrv_errors(), true) );
			}
			sqlsrv_free_stmt($stmt_change);

			$change2 = "UPDATE asignarprocesoversion SET estado = 'PRODUCCION' WHERE id = '$id'";
			$stmt_change2 = sqlsrv_query( $conn, $change2);//, $params);
			if( $stmt_change2 === false) {
			    die( print_r( sqlsrv_errors(), true) );
			}
			sqlsrv_free_stmt($stmt_change2);
		} 
	}
}
sqlsrv_free_stmt($stmt);


$configs = "SELECT * FROM configuracionlinea WHERE estado = 'PRE-PRODUCCION'";
$stmt_configs = sqlsrv_query( $conn, $configs);//, $params);
if( $stmt_configs === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while($c = sqlsrv_fetch_array( $stmt_configs, SQLSRV_FETCH_ASSOC))
{
	if($c['fecha_inicio'])
	{
		$fecha_2 = $c['fecha_inicio']->format('Y-m-d');
		$id_2 = $c['id'];
		$id_asignarproceso = $c['id_asignarproceso'];
		$operarios = $c['operarios'];
		$estaciones = $c['estaciones'];

		if ($fecha_2 == $hoy){

			$change3 = "UPDATE configuracionlinea SET estado = 'OBSOLETO' WHERE id_asignarproceso = '$id_asignarproceso' AND operarios = '$operarios' AND estaciones = '$estaciones' AND estado = 'PRODUCCION'";
			$stmt_change3 = sqlsrv_query( $conn, $change3);//, $params);
			if( $stmt_change3 === false) {
			    die( print_r( sqlsrv_errors(), true) );
			}
			sqlsrv_free_stmt($stmt_change3);
			$change4 = "UPDATE configuracionlinea SET estado = 'PRODUCCION' WHERE id = '$id_2'";
			$stmt_change4 = sqlsrv_query( $conn, $change4);//, $params);
			if( $stmt_change4 === false) {
			    die( print_r( sqlsrv_errors(), true) );
			}
			sqlsrv_free_stmt($stmt_change4);
		}
	}
}
sqlsrv_free_stmt($stmt_configs);

?>