<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$id = $_POST["id"];
$descripcion = ucwords(strtolower($_POST["descripcion"]));
$codigo = strtoupper($_POST["codigo"]);
$abreviacion = strtoupper($_POST["abreviacion"]);

$data = (object) array();

if($id==""){
    $use = $conexion->prepare("SELECT *
    							FROM 
    							sede WHERE sede_descripcion = ?");
    $use->bindValue(1, $descripcion);
}
else{
    $use = $conexion->prepare("SELECT *
    							FROM 
    							sede WHERE sede_descripcion = ? AND sede_id<>?");
    $use->bindValue(1, $descripcion); 
    $use->bindValue(2, $id);
}

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count==0){
    if($id==""){
        $use = $conexion->prepare("SELECT *
    							FROM 
    							sede WHERE sede_codigo = ?");
        $use->bindValue(1, $codigo);
    }
    else{
        $use = $conexion->prepare("SELECT *
    							FROM 
    							sede WHERE sede_codigo = ? AND sede_id<>?");
        $use->bindValue(1, $codigo);
        $use->bindValue(2, $id);
    }
    $use ->execute();
    $row = $use->fetchAll();
    $count = $use->rowCount();
    
    if($count==0){
        if($id==""){
            $use = $conexion->prepare("SELECT *
    							FROM 
    							sede WHERE sede_abreviacion = ?");
            $use->bindValue(1, $abreviacion);
        }
        else{
            $use = $conexion->prepare("SELECT *
    							FROM 
    							sede WHERE sede_abreviacion = ? AND sede_id<>?");
            $use->bindValue(1, $abreviacion);
            $use->bindValue(2, $id);    
        }
        $use ->execute();
        $row = $use->fetchAll();
        $count = $use->rowCount();
        if($count==0){
            if($id==""){
                $use = $conexion->prepare("INSERT INTO sede(sede_descripcion,sede_codigo,sede_abreviacion)
                                        VALUES(?,?,?)");
                $use->bindValue(1, $descripcion);
                $use->bindValue(2, $codigo);
                $use->bindValue(3, $abreviacion);
            }
            else{
                $use = $conexion->prepare("UPDATE sede set sede_descripcion=?,
                                                            sede_codigo=?,
                                                            sede_abreviacion=?
                                            WHERE sede_id=?");
                $use->bindValue(1, $descripcion);
                $use->bindValue(2, $codigo);
                $use->bindValue(3, $abreviacion); 
                $use->bindValue(4, $id);
            }
            $status = $use->execute();
            if($status){
                $data->estado = "OK";
                if($id==""){
                    $data->mensaje = "Sede registrada exitosamente";
                }
                else{
                    $data->mensaje = "Sede modificada exitosamente";    
                }
            }
            else{
                $data->estado = "ERROR";
                if($id==""){
                    $data->mensaje = "Error al registrar la sede, intente nuevamente";
                }
                else{
                    $data->mensaje = "Error al modificar la sede, intente nuevamente";    
                }    
            }
        }
        else{
            $data->estado = "ERROR";
    	    $data->mensaje = "Ya se encuentra registrada una sede con la abreviación ingresada";    
        }
    }
    else{
        $data->estado = "ERROR";
	    $data->mensaje = "Ya se encuentra registrada una sede con el código ingresado";    
    }
}
else{
	$data->estado = "ERROR";
	$data->mensaje = "Ya se encuentra registrada una sede con la descripción ingresada";
}
print_r(json_encode($data));
?>