<?php
header('Access-Control-Allow-Origin: *');
session_start();
date_default_timezone_set("America/Bogota");
require_once("../conexion.php");
$conexion = new Conexion();
$acudiente=$_POST['acudiente'];
if($acudiente==""){
    $acudiente =0;   
}
$codigo=$_POST['codigo'];
$tipoDocumento=$_POST['tipoDocumento'];
$documento=$_POST['documento'];
$observaciones=$_POST['observaciones'];
$puntualidad=$_POST['puntualidad'];
$tipoAcceso=$_POST['tipoAcceso'];
$tipoBusqueda=$_POST['tipoBusqueda'];
$fecha = date("Y-m-d H:i:s");

$data = (object) array();
if($codigo!=""){
    $use = $conexion->prepare("INSERT INTO acceso(acce_observacion,
                                                usua_id,
                                                usua_acudiente,
                                                acce_fecha,
                                                tiac_id,
                                                acce_puntualidad)
                                        VALUES(?,(SELECT distinct u.usua_id FROM usuario u where 
                                                u.usua_codigo=?),?,?,?,?)");

    $use->bindValue(1, $observaciones);
    $use->bindValue(2, $codigo);
    $use->bindValue(3, $acudiente);
    $use->bindValue(4, $fecha);
    $use->bindValue(5, $tipoAcceso);
    $use->bindValue(6, $puntualidad);        
}
else{
    $use = $conexion->prepare("INSERT INTO acceso(acce_observacion,
                                                    usua_id,
                                                    usua_acudiente,
                                                    acce_fecha,
                                                    tiac_id,
                                                    acce_puntualidad)
                                            VALUES(?,(SELECT distinct u.usua_id FROM usuario u where 
                                                     u.usua_documento=? 
                                                     AND u.tido_id=?),?,?,?,?)");
    
    $use->bindValue(1, $observaciones);
    $use->bindValue(2, $documento);
    $use->bindValue(3, $tipoDocumento);
    $use->bindValue(4, $acudiente);
    $use->bindValue(5, $fecha);
    $use->bindValue(6, $tipoAcceso);
    $use->bindValue(7, $puntualidad);
}
$state = $use->execute();
$row = $use->fetchAll();
$data = (object) array();
$count = $use->rowCount();
if($state){
    if($codigo!=""){
        $use = $conexion->prepare("SELECT distinct u.* FROM usuario u where u.usua_codigo=?");
    
        $use->bindValue(1, $codigo);
        
    }
    else{
        $use = $conexion->prepare("SELECT distinct u.* FROM usuario u where 
                                    u.usua_documento=? AND u.tido_id=?");
        $use->bindValue(1, $documento);
        $use->bindValue(2, $tipoDocumento);    
    }
    $use ->execute();
    $row = $use->fetchAll();
    $data->estado = "OK";
	$data->mensaje = "Acceso del usuario <b>".$row[0]["usua_nombres"]." ".$row[0]["usua_apellidos"]."</b> registrado con éxito";    
}else{
	$data->estado = "ERROR";
	$data->mensaje = "Error al registrar acceso";
}
print_r(json_encode($data));
?>