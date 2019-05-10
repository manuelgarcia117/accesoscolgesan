<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("DELETE FROM usuariorol
                            WHERE usua_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM login
                            WHERE usua_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM usuariocurso
                            WHERE usua_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM usuario
                            WHERE usua_registro=?");

$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM usuario
                            WHERE usua_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

if($status){
    $data->estado = "OK";
    $data->mensaje = "Usuario eliminado exitosamente";   
}else{
    $data->estado = "ERROR";
    $data->mensaje = "Error al eliminar el usuario, intente nuevamente";    
}

print_r(json_encode($data));
?>