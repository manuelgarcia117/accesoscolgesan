<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$correo = $_POST["correo"];
$clave = $_POST["clave"];

$use = $conexion->prepare("SELECT *
							FROM 
							usuario u, login l
							WHERE u.usua_id=l.usua_id
							AND (l.logi_usuario = ? OR u.usua_correo = ?)
							AND l.logi_clave = ?
							ORDER BY u.usua_id;");

$use->bindValue(1, $correo);
$use->bindValue(2, $correo);
$use->bindValue(3, $clave);
$use->execute();
$row = $use->fetchAll();
$data = (object) array();
$count = $use->rowCount();
if ($count > 0) {
	foreach($row as $registro){
		$object = (object) array();
		$object->usuario =  $registro['logi_usuario'];
		$object->nombre = explode(" ",$registro['usua_nombres'])[0]." ".explode(" ",$registro['usua_apellidos'])[0];
	}
	$data->usuario = $object;
	$data->estado = "OK";
	$data->mensaje = "Bienvenido al sistema de control de accesos";
	$_SESSION["idUsuario"] = $registro['usua_id'];
	$_SESSION["usuario"] = $registro['logi_usuario'];
	$_SESSION["nombreUsuario"] = explode(" ",$registro['usua_nombres'])[0]." ".explode(" ",$registro['usua_apellidos'])[0];
} 
else{
		$data->usuario = [];
		$data->estado = "ERROR";
		$data->mensaje = "Usuario o contraseña no válidos";
}
print_r(json_encode($data));
?>