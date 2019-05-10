<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$codigo=$_POST['codigo'];
$documento=$_POST['documento'];
$tipoDocumento=$_POST['tipoDocumento'];

$data = (object) array();

$use = $conexion->prepare("SELECT distinct u.* FROM usuario u, tipodocumento td where 
                            ((u.usua_codigo=?) OR (td.tido_id=u.tido_id AND u.usua_documento=? AND td.tido_id=?))");

$use->bindValue(1, $codigo);
$use->bindValue(2, $documento);
$use->bindValue(3, $tipoDocumento);
$use ->execute();
$count = $use->rowCount();
if($count>0){
	$use = $conexion->prepare("SELECT distinct ua.* FROM usuario u,acudiente a,usuario ua,tipodocumento td where 
	                            u.usua_id=a.usua_id AND a.usua_acudiente=ua.usua_id AND ((
	                            u.usua_codigo=?) OR (td.tido_id=u.tido_id AND u.usua_documento=? AND td.tido_id=?))");
	
	$use->bindValue(1, $codigo);
	$use->bindValue(2, $documento);
	$use->bindValue(3, $tipoDocumento);
	
	$use ->execute();
	$row = $use->fetchAll();
	$count = $use->rowCount();
	if($count>0){
		$array = array();
		foreach($row as $registro){
			$object = (object) array();
			$object->id =  $registro['usua_id'];
			$object->nombre =  $registro['usua_nombres']." ".$registro['usua_apellidos'];
			//$object->descripcion =  $registro['tiac_descripcion'];
			array_push($array, $object);
		}
		$data->acudientes = $array;
		$data->estado = "OK";
		$data->registrado = "1";
	}
	else{
		$data->acudientes = [];
		$data->estado = "ERROR";
		$data->mensaje = "Este usuario no tiene asociado a ningun acudiente";
		$data->registrado = "1";
	}
}
else{
	$data->acudientes = [];
	$data->estado = "ERROR";
	$data->mensaje = "Ingrese un documento o código de usuario válido";
	$data->registrado = "0";
}
print_r(json_encode($data));
?>