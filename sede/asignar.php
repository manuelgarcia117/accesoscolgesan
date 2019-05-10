<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$idSede = $_POST["idSede"];
$idAnio = $_POST["idAnio"];
$jornadas = $_POST["jornadas"];

$data = (object) array();

$use = $conexion->prepare("DELETE FROM sedejornada 
                            WHERE sede_id=? AND anle_id=?");
$use->bindValue(1, $idSede);
$use->bindValue(2, $idAnio);

$status=$use ->execute();
if($status){
    $sql = "INSERT INTO sedejornada(sede_id,anle_id,jorn_id)
            				VALUES";
    for($i=0;$i<count($jornadas);$i++){
        if($i<(count($jornadas)-1)){
            $sql.="($idSede,$idAnio,$jornadas[$i]),";
         }
         else
         if($i==(count($jornadas)-1)){
             $sql.="($idSede,$idAnio,$jornadas[$i])";
         }
    }
    $use = $conexion->prepare($sql);
    $status=$use ->execute();
    
    if($status){
        $data->estado = "OK";
    	$data->mensaje = "Jornadas asignadas exitosamente";
    }
    else{
        $data->estado = "ERROR";
	    $data->mensaje = "Error al asignar jornadas"; 
    }
}
else{
    $data->estado = "ERROR";
	$data->mensaje = "Error al asignar jornadas";    
}

print_r(json_encode($data));
?>