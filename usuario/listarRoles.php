<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$idUsua = $_POST["idUsua"];


$data = (object) array();

if($idUsua==""){
    $use = $conexion->prepare("SELECT * FROM rol WHERE rol_id<>7
							ORDER BY rol_prioridad DESC");    
}
else{
    $use = $conexion->prepare("SELECT r.rol_id, r.rol_descripcion, ISNULL((
                                    SELECT r.rol_descripcion
                                    FROM usuariorol ur
                                    WHERE r.rol_id = ur.rol_id
                                    AND ur.usua_id = ?
                                )) AS esta
                                FROM rol r WHERE rol_id<>7 ORDER by rol_prioridad DESC");
    
    $use->bindValue(1, $idUsua);
}
$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['rol_id'];
		$object->descripcion =  $registro['rol_descripcion'];
		$object->esta =  $registro['esta'];
		array_push($array, $object);
	}
	$data->roles = $array;
	$data->estado = "OK";
}
else{
	$data->roles = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar datos de la jornada";
}
print_r(json_encode($data));
?>