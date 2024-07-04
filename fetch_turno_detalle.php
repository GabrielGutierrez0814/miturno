<?php
include 'db.php';

// Obtener el ID del turno desde la solicitud GET
$turno_id = $_GET['turno_id'];

// Obtener los detalles del turno desde la base de datos
$result = $conn->query("SELECT t.*, s.nombre AS nombre_servicio FROM turnos t INNER JOIN servicios s ON t.service_code = s.codigo WHERE t.id = $turno_id");

$turno = $result->fetch_assoc();

// Devolver los detalles del turno como respuesta JSON
header('Content-Type: application/json');
echo json_encode($turno);
?>
