<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$textoBusqueda = $_POST["textoBusqueda"];
$arrayTextoBusqueda = explode(" ",$textoBusqueda);

$data = (object) array();

$sql = "SELECT * FROM sede WHERE ";
for($i=0;$i<count($arrayTextoBusqueda);$i++){
   if($i!=0){
       $sql.=" AND (sede_descripcion LIKE '%".$arrayTextoBusqueda[$i]."%' OR sede_codigo LIKE '%".$arrayTextoBusqueda[$i]."%' OR sede_abreviacion LIKE '%".$arrayTextoBusqueda[$i]."%')";
   }
   else{
       $sql.=" (sede_descripcion LIKE '%".$arrayTextoBusqueda[$i]."%' OR sede_codigo LIKE '%".$arrayTextoBusqueda[$i]."%' OR sede_abreviacion LIKE '%".$arrayTextoBusqueda[$i]."%')";
   }
}
$sql.=" ORDER by sede_descripcion DESC";

$use = $conexion->prepare($sql);

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
	$data->mensaje = "Error al cargar sedes";
}
print_r(json_encode($data));
?>