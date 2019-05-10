<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$idSede = $_POST["idSede"];
$idAnio = $_POST["idAnio"];

$data = (object) array();

if($idSede==""||$idAnio==""){
    $use = $conexion->prepare("SELECT * FROM jornada
							ORDER BY jorn_descripcion DESC");    
}
else{
    $use = $conexion->prepare("SELECT j.jorn_id, j.jorn_descripcion, ISNULL((
                                    SELECT j.jorn_descripcion
                                    FROM sedejornada sj
                                    WHERE j.jorn_id = sj.jorn_id
                                    AND sj.sede_id = ?
                    				AND sj.anle_id=?
                                )) AS esta
                                FROM jornada j");
    
    $use->bindValue(1, $idSede);
    $use->bindValue(2, $idAnio);
}
$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['jorn_id'];
		$object->descripcion =  $registro['jorn_descripcion'];
		$object->esta =  $registro['esta'];
		array_push($array, $object);
	}
	$data->jornadas = $array;
	$data->estado = "OK";
}
else{
	$data->jornadas = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar datos de la jornada";
}
print_r(json_encode($data));
?>