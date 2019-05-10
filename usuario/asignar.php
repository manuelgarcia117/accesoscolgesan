<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$idUsua = $_POST["idUsua"];
$idAnio = $_POST["idAnio"];
$idSede = $_POST["idSede"];
$idJornada = $_POST["idJornada"];
$idGrado = $_POST["idGrado"];
$idCurso = $_POST["idCurso"];

$data = (object) array();

$use = $conexion->prepare("DELETE FROM usuariocurso
                            WHERE usua_id=? AND anle_id=?");
$use->bindValue(1, $idUsua);
$use->bindValue(2, $idAnio);
$status = $use->execute();

$use = $conexion->prepare("INSERT INTO usuariocurso(anle_id,
                                                    sede_id,
                                                    jorn_id,
                                                    grad_id,
                                                    curs_id,
                                                    usua_id)
                            VALUES(?,?,?,?,?,?)");

$use->bindValue(1, $idAnio);
$use->bindValue(2, $idSede);
$use->bindValue(3, $idJornada);
$use->bindValue(4, $idGrado);
$use->bindValue(5, $idCurso);
$use->bindValue(6, $idUsua);
$status = $use->execute();
if($status){
    $data->estado = "OK";
    $data->mensaje = "Curso asignado exitosamente";   
}else{
    $data->estado = "ERROR";
    $data->mensaje = "Error al asignar el curso, intente nuevamente";    
}

print_r(json_encode($data));
?>