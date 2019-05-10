<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$idUsua = $_POST["idUsua"];
$idAnio = $_POST["idAnio"];

$data = (object) array();

$use = $conexion->prepare("SELECT uc.*
							FROM 
							usuariocurso uc WHERE uc.usua_id = ? AND uc.anle_id=?");

$use->bindValue(1, $idUsua);
$use->bindValue(2, $idAnio);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		
		$object->anio =  $registro['anle_id'];
		$object->sede =  $registro['sede_id'];
		$object->jornada =  $registro['jorn_id'];
		$object->grado =  $registro['grad_id'];
		$object->curso =  $registro['curs_id'];
		array_push($array, $object);
	}
	$data->curso = $array;
	$data->estado = "OK";
}
else{
	$data->curso = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar datos del curso";
}
print_r(json_encode($data));
?>