<?php
header('Access-Control-Allow-Origin: *');
session_start();
date_default_timezone_set("America/Bogota");
require_once("../conexion.php");
$conexion = new Conexion();
$codigo=$_POST['codigo'];
$documento=$_POST['documento'];
$tipoDocumento=$_POST['tipoDocumento'];
$documentoAcudiente=$_POST['documentoAcudiente'];
$tipoDocumentoAcudiente=$_POST['tipoDocumentoAcudiente'];
$nombresAcudiente=$_POST['nombresAcudiente'];
$apellidosAcudiente=$_POST['apellidosAcudiente'];
$telefonoAcudiente=$_POST['telefonoAcudiente'];
$fecha = date("Y-m-d H:i:s");
$data = (object) array();
$use = $conexion->prepare("INSERT INTO usuario(usua_nombres,
                                              usua_apellidos,
                                              usua_documento,
                                              tido_id,
                                              usua_fecharegistro,
                                              usua_telefono) 
                            SELECT ?,?,?,?,?,? FROM dual WHERE NOT EXISTS(SELECT * FROM usuario WHERE tido_id = ?
                            AND usua_documento = ?)");

$use->bindValue(1, $nombresAcudiente);
$use->bindValue(2, $apellidosAcudiente);
$use->bindValue(3, $documentoAcudiente);
$use->bindValue(4, $tipoDocumentoAcudiente);
$use->bindValue(5, $fecha);
$use->bindValue(6, $telefonoAcudiente);
$use->bindValue(7, $tipoDocumentoAcudiente);
$use->bindValue(8, $documentoAcudiente);
$state = $use->execute();
$row = $use->fetchAll();
$data = (object) array();
$count = $use->rowCount();
if($state){
    $use = $conexion->prepare("INSERT INTO acudiente(usua_id, usua_acudiente) 
                            SELECT (select usua_id from usuario where (tido_id = ? AND usua_documento=?) OR (usua_codigo=?)),
                                   (select usua_id from usuario where tido_id = ? AND usua_documento=?) 
                                   FROM dual WHERE NOT EXISTS(SELECT * FROM acudiente 
                                   WHERE usua_id = (select usua_id from usuario where (tido_id = ? AND usua_documento=?) OR (usua_codigo=?))
                                   AND usua_acudiente = (select usua_id from usuario where tido_id = ? AND usua_documento=?))");            
    $use->bindValue(1, $tipoDocumento);
    $use->bindValue(2, $documento);
    $use->bindValue(3, $codigo);
    $use->bindValue(4, $tipoDocumentoAcudiente);
    $use->bindValue(5, $documentoAcudiente);
    $use->bindValue(6, $tipoDocumento);
    $use->bindValue(7, $documento);
    $use->bindValue(8, $codigo);
    $use->bindValue(9, $documentoAcudiente);
    $use->bindValue(10, $tipoDocumentoAcudiente);
    $state = $use->execute();
    if($state){
        $data->estado = "OK";
	    $data->mensaje = "Acudiente registrado con éxito";    
    }else{
        $data->estado = "ERROR";
	    $data->mensaje = "Error al registrar acudiente";   
    }
}else{
	$data->estado = "ERROR";
	$data->mensaje = "Error al registrar acudiente";
}
print_r(json_encode($data));
?>