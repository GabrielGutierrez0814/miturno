<?php
include 'db.php';

// Verificar si se recibió el ID del turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['turno_id'])) {
    $turno_id = $_POST['turno_id'];

    // Cambiar el estado del turno a "atendido" en la base de datos
    $sql = "UPDATE turnos SET status = 'atendido' WHERE id = $turno_id";

    if ($conn->query($sql) === TRUE) {
        echo "Turno finalizado correctamente";
    } else {
        echo "Error al finalizar el turno: " . $conn->error;
    }
}

$conn->close();
?>
