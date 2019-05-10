<?php
header('Access-Control-Allow-Origin: *');
session_start();
date_default_timezone_set("America/Bogota");
$anioactual = date("Y");
require_once("../conexion.php");
$conexion = new Conexion();
$fecha1 = $_POST["fecha1"];
$fecha2 = $_POST["fecha2"];
$tipoDocumento = $_POST["tipoDocumento"];
$documento = $_POST["documento"];
$tipoAcceso = $_POST["tipoAcceso"];
$puntualidad = $_POST["puntualidad"];
$jornada = $_POST["jornada"];
$sede = $_POST["sede"];
$grado = $_POST["grado"];
$curso = $_POST["curso"];

$data = (object) array();

$sql = "SELECT DISTINCT ta.tiac_descripcion,a.acce_id,u.usua_nombres,u.usua_apellidos,td.tido_abreviacion,u.usua_documento,a.acce_fecha
							FROM acceso a, usuario u,tipodocumento td, tipoacceso ta";
if($sede!=""||$jornada!=""||$grado!=""||$curso!=""){
	$sql.=",usuariocurso uc ";
}
$sql.=" WHERE a.usua_id=u.usua_id AND u.tido_id=td.tido_id AND a.tiac_id=ta.tiac_id ";
if($sede!=""||$jornada!=""||$grado!=""||$curso!=""){
	$sql.=" AND u.usua_id=uc.usua_id";
}
if($fecha1!=""&&$fecha2!=""){
	$sql.=" AND (date(a.acce_fecha) BETWEEN '$fecha1' AND '$fecha2') ";	
}
if($tipoDocumento!=""){
	$sql.=" AND u.tido_id = $tipoDocumento ";	
}
if($tipoAcceso!=""){
	$sql.=" AND a.tiac_id = $tipoAcceso ";	
}
if($puntualidad!=""){
	$sql.=" AND a.acce_puntualidad = $puntualidad ";	
}
if($documento!=""){
	$sql.=" AND u.usua_documento LIKE '%$documento%' ";	
}
if($jornada!=""){
	$sql.=" AND uc.jorn_id = $jornada ";	
}
if($sede!=""){
	$sql.=" AND uc.sede_id = $sede ";	
}
if($grado!=""){
	$sql.=" AND uc.grad_id = $grado ";	
}
if($curso!=""){
	$sql.=" AND uc.curs_id = $curso ";	
}
$sql.= "ORDER BY a.acce_fecha DESC";

$use = $conexion->prepare($sql);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['acce_id'];
		$object->usuario =  explode(" ",$registro['usua_nombres'])[0]." ".explode(" ",$registro['usua_apellidos'])[0];
		$object->documento = $registro["tido_abreviacion"]." ".$registro["usua_documento"];
		$object->fecha = date('d/m/Y H:i:s',strtotime($registro['acce_fecha']));
		$object->tipo =  $registro['tiac_descripcion'];
		array_push($array, $object);
	}
	$data->accesos = $array;
	$data->estado = "OK";
	$data->sql = $sql;
}
else{
	$data->accesos = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar accesos";
}
print_r(json_encode($data));
?>