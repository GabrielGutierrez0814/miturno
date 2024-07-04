<?php
include 'db.php';

$turnos_result = $conn->query("SELECT id, service_name, service_code, turno_numero FROM turnos WHERE status='waiting' ORDER BY id ASC");
$turnos = [];

while ($row = $turnos_result->fetch_assoc()) {
    $turnos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($turnos);

$conn->close();
?>
