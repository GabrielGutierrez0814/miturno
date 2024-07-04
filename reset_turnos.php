<?php
include 'db.php';

// Resetea el contador de turnos para todos los servicios
$reset_sql = "UPDATE services SET turno_counter = 0";
if ($conn->query($reset_sql) === TRUE) {
    echo "Contadores de turnos reseteados con éxito.";
} else {
    echo "Error al resetear los contadores de turnos: " . $conn->error;
}

$conn->close();
// Redirigir de vuelta a admin.php
header("Location: admin.php");
exit();
?>
