<?php
include 'db.php';

// Verificar si se recibió el ID del turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['turno_id'])) {
    $turno_id = $_POST['turno_id'];

    // Cambiar el estado del turno a "llamado" nuevamente en la base de datos
    $sql = "UPDATE turnos SET status = 'llamado' WHERE id = $turno_id";

    if ($conn->query($sql) === TRUE) {
        echo "Turno vuelto a llamar correctamente";
    } else {
        echo "Error al volver a llamar el turno: " . $conn->error;
    }
}

$conn->close();
?>
