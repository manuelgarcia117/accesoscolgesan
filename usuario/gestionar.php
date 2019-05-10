<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
date_default_timezone_set("America/Bogota");
$conexion = new Conexion();

$id = $_POST["id"];
$nombres = ucwords(strtolower($_POST["nombres"]));
$apellidos = ucwords(strtolower($_POST["apellidos"]));
$tipoDocumento = $_POST["tipoDocumento"];
$documento = strtoupper($_POST["documento"]);
$codigo = $_POST["codigo"];
$correo = strtolower($_POST["correo"]);
$clave = $_POST["clave"];
$telefono = $_POST["telefono"];
$roles = $_POST["roles"];
$fecha = date("Y-m-d H:i:s");

//para registro de cursos
$guardarCurso = $_POST["guardarCurso"];
$idAnio = $_POST["anioLectivo"];
$idSede = $_POST["sede"];
$idJornada = $_POST["jornada"];
$idGrado = $_POST["grado"];
$idCurso = $_POST["curso"];

$data = (object) array();

if($id==""){
    $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE tido_id = ? AND usua_documento=?");
    $use->bindValue(1, $tipoDocumento);
    $use->bindValue(2, $documento);
}
else{
    $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE tido_id = ? AND usua_documento=?
    							AND usua_id<>?");
    $use->bindValue(1, $tipoDocumento);
    $use->bindValue(2, $documento);
    $use->bindValue(3, $id);
}

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count==0){
    if($id==""){
        $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE usua_correo = ? AND usua_correo<>''");
        $use->bindValue(1, $correo);
    }
    else{
        $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE usua_correo = ? AND usua_id<>? AND usua_correo<>''");
        $use->bindValue(1, $correo);
        $use->bindValue(2, $id);
    }
    $use ->execute();
    $row = $use->fetchAll();
    $count = $use->rowCount();
    if($count==0){
        if($id==""){
            $use = $conexion->prepare("INSERT INTO usuario(usua_nombres,
                                                            usua_apellidos,
                                                            usua_documento,
                                                            usua_fecharegistro,
                                                            usua_codigo,
                                                            usua_correo,
                                                            usua_telefono,
                                                            tido_id)
                                    VALUES(?,?,?,?,?,?,?,?)");
            $use->bindValue(1, $nombres);
            $use->bindValue(2, $apellidos);
            $use->bindValue(3, $documento);
            $use->bindValue(4, $fecha);
            $use->bindValue(5, $codigo);
            $use->bindValue(6, $correo);
            $use->bindValue(7, $telefono);
            $use->bindValue(8, $tipoDocumento);
        }
        else{
            $use = $conexion->prepare("UPDATE usuario set usua_nombres=?,
                                                            usua_apellidos=?,
                                                            usua_documento=?,
                                                            usua_fecharegistro=?,
                                                            usua_codigo=?,
                                                            usua_correo=?,
                                                            usua_telefono=?,
                                                            tido_id=?
                                        WHERE usua_id=?");
            $use->bindValue(1, $nombres);
            $use->bindValue(2, $apellidos);
            $use->bindValue(3, $documento);
            $use->bindValue(4, $fecha);
            $use->bindValue(5, $codigo);
            $use->bindValue(6, $correo);
            $use->bindValue(7, $telefono);
            $use->bindValue(8, $tipoDocumento);
            $use->bindValue(9, $id);
        }
        $status = $use->execute();
        $ultimoId= $conexion->lastInsertId();
        if($status){
            if($id==""){
                $use = $conexion->prepare("INSERT INTO login(logi_clave,
                                                            logi_usuario,
                                                            usua_id)
                                    VALUES(?,?,?)");
                $use->bindValue(1, $clave);
                $use->bindValue(2, $correo);
                $use->bindValue(3, $ultimoId);
            }
            else{
                if($clave!=""){
                    $use = $conexion->prepare("UPDATE login SET logi_clave=?,
                                                                logi_usuario=?
                                                                WHERE usua_id=?");
                    $use->bindValue(1, $clave);
                    $use->bindValue(2, $correo);
                    $use->bindValue(3, $id); 
                }
            }
            $status = $use->execute();
            if($status){
                if($id!=""){
                    $use = $conexion->prepare("DELETE FROM usuariorol WHERE usua_id=?");
                    $use->bindValue(1, $id);
                    $use->execute();
                }else{
                    $id=$ultimoId;
                    $cm="0";
                }
                $sql = "INSERT INTO usuariorol(usua_id,rol_id)
            				VALUES";
                for($i=0;$i<count($roles);$i++){
                    if($i<(count($roles)-1)){
                        $sql.="($id,$roles[$i]),";
                     }
                     else
                     if($i==(count($roles)-1)){
                         $sql.="($id,$roles[$i])";
                     }
                }
                $use = $conexion->prepare($sql);
                $status = $use->execute();
                if($status){
                    $data->estado = "OK";
                    $data->curso = $idCurso;
                    if($cm=="0"){
                        //registrar curso guardado
                        if($guardarCurso&&$idCurso!=""){
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
                            $use->bindValue(6, $id);
                            $use->execute();
                        }
                        $data->mensaje = "Usuario registrado exitosamente";
                    }
                    else{
                        $data->mensaje = "Usuario modificado exitosamente";    
                    }
                }
                else{
                    $data->estado = "ERROR";
                    if($cm=="0"){
                        $data->mensaje = "Error al registrar usuario, intente nuevamente";
                    }
                    else{
                        $data->mensaje = "Error al modificar usuario, intente nuevamente";    
                    }    
                }
            }
            else{
                $data->estado = "ERROR";
                if($id==""){
                    $data->mensaje = "Error al registrar usuario, intente nuevamente";
                }
                else{
                    $data->mensaje = "Error al modificar usuario, intente nuevamente";    
                }    
            }
        }
        else{
            $data->estado = "ERROR";
            if($id==""){
                $data->mensaje = "Error al registrar el usuario, intente nuevamente";
            }
            else{
                $data->mensaje = "Error al modificar el usuario, intente nuevamente";    
            }    
        }
    }
    else{
        $data->estado = "ERROR";
	    $data->mensaje = "Ya se encuentra registrado un usuario con el correo electrónico ingresado";    
    }
}
else{
	$data->estado = "ERROR";
	$data->mensaje = "Ya se encuentra registrado un usuario con la combinación tipo y número de documento ingresados";
}
print_r(json_encode($data));
?>