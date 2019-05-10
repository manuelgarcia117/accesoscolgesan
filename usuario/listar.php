<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$textoBusqueda = $_POST["textoBusqueda"];
//$textoBusqueda = "curso:6B";
$textocurso = explode(":",$textoBusqueda);
$arrayTextoBusqueda = explode(" ",$textoBusqueda);

$data = (object) array();

$sql = "SELECT * FROM usuario u,tipodocumento td";
if(strtolower($textocurso[0])=="curso"){
	$sql.=" ,usuariocurso uc,curso c, grado g";	
}
$sql.=" WHERE ";
if(strtolower($textocurso[0])!="curso"){
	for($i=0;$i<count($arrayTextoBusqueda);$i++){
	   if($i!=0){
	       $sql.=" AND (u.usua_nombres LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_apellidos LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_documento LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_correo LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_telefono LIKE '%".$arrayTextoBusqueda[$i]."%')";
	   }
	   else{
	       $sql.=" (u.usua_nombres LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_apellidos LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_documento LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_correo LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_telefono LIKE '%".$arrayTextoBusqueda[$i]."%')";
	   }
	}
}
else{
	$sql.=" CONCAT(g.grad_numeracion,c.curs_descripcion) = '".$textocurso[1]."'";
}
$sql.=" AND u.tido_id=td.tido_id";
if(strtolower($textocurso[0])=="curso"){
	$sql.=" AND u.usua_id=uc.usua_id AND uc.grad_id=g.grad_id AND uc.curs_id=c.curs_id";	
}
$sql.=" AND u.usua_id<>0 AND u.usua_id<>1 ORDER by usua_nombres ASC";

$use = $conexion->prepare($sql);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['usua_id'];
		$object->nombre =  explode(" ", $registro['usua_nombres'])[0]." ".explode(" ", $registro['usua_apellidos'])[0];
		$object->documento =  $registro['tido_abreviacion']." ".$registro['usua_documento'];;
		array_push($array, $object);
	}
	$data->usuarios = $array;
	$data->estado = "OK";
}
else{
	$data->usuarios = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar usuarios";
}
print_r(json_encode($data));
?>