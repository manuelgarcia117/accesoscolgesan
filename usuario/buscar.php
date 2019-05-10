<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("SELECT u.*
							FROM 
							usuario u WHERE u.usua_id = ?");

$use->bindValue(1, $id);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['usua_id'];
		$object->nombres =  $registro['usua_nombres'];
		$object->apellidos =  $registro['usua_apellidos'];
		$object->tipoDocumento =  $registro['tido_id'];
		$object->documento =  $registro['usua_documento'];
		$object->codigo =  $registro['usua_codigo'];
		$object->correo =  $registro['usua_correo'];
		$object->telefono =  $registro['usua_telefono'];
		array_push($array, $object);
	}
	$data->usuario = $array;
	$data->estado = "OK";
}
else{
	$data->usuario = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar datos del usuario";
}
print_r(json_encode($data));
?>