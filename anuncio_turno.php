<?php
// Simulación de la generación de un nuevo turno
$turno = array(
    "service_code" => "ABC",
    "turno_numero" => "123",
    "module" => "Módulo 1"
);

// Devuelve el turno generado como respuesta JSON
header('Content-Type: application/json');
echo json_encode($turno);
?>
