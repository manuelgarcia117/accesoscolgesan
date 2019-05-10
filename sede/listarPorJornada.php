<?php
    header('Access-Control-Allow-Origin: *');
    session_start();
    require_once("../conexion.php");
    $conexion = new Conexion();
    
    $idJornada = $_POST["idJornada"];
    $idAnio = $_POST["idAnio"];
    
    $data = (object) array();
    
    $use = $conexion->prepare("SELECT s.* FROM sede s, sedejornada sj
                            WHERE s.sede_id=sj.sede_id 
                            AND sj.jorn_id=?
                            AND sj.anle_id=?");
                            
    $use->bindValue(1, $idJornada);
    $use->bindValue(2, $idAnio);
    
    $use ->execute();
    $row = $use->fetchAll();
    $count = $use->rowCount();
    if($count!=0){
        $array = array();
    	foreach($row as $registro){
    		$object = (object) array();
    		$object->id =  $registro['sede_id'];
    		$object->descripcion =  $registro['sede_descripcion'];
    		array_push($array, $object);
    	}
    	$data->sedes = $array;
    	$data->estado = "OK";    
    }
    else{
        $data->sedes = [];
    	$data->mensaje = "No hay sedes que tengan asignada la jornada en el año lectivo seleccionado";     
    }
print_r(json_encode($data));
?>