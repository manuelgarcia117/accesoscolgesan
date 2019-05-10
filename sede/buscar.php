<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("SELECT *
							FROM 
							sede WHERE sede_id = ?");

$use->bindValue(1, $id);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['sede_id'];
		$object->descripcion =  $registro['sede_descripcion'];
		$object->codigo =  $registro['sede_codigo'];
		$object->abreviacion =  $registro['sede_abreviacion'];
		array_push($array, $object);
	}
	$data->sedes = $array;
	$data->estado = "OK";
}
else{
	$data->sedes = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar datos de la sede";
}
print_r(json_encode($data));
?>