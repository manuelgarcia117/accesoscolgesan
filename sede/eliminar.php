<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("DELETE FROM sedejornada
                            WHERE sede_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM usuariocurso
                            WHERE sede_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM gradocurso
                                WHERE sede_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM jornadagrado
                                WHERE sede_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM sede
                            WHERE sede_id=?");

$use->bindValue(1, $id);
$status = $use->execute();
if($status){
    $data->estado = "OK";
    $data->mensaje = "Sede eliminada exitosamente";   
}else{
    $data->estado = "ERROR";
    $data->mensaje = "Error al eliminar la sede, intente nuevamente";    
}

print_r(json_encode($data));
?>