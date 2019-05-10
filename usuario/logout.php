<?php
session_start();
header('Access-Control-Allow-Origin: *');
session_destroy();
$data = (object) array();
$data->estado = "OK";
print_r(json_encode($data));
?>