<?php
include 'db.php';

$service_code = $_GET['service_code'];

$result = $conn->query("SELECT t.id, t.client_name, t.service_code, s.nombre_servicio, t.turno_numero, t.status
                        FROM turnos t
                        JOIN servicios s ON t.service_code = s.codigo
                        WHERE t.service_code = '$service_code' AND t.status = 'waiting'
                        ORDER BY t.turno_numero ASC
                        LIMIT 1");

$turno = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($turno);
?>
