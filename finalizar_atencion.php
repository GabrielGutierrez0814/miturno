<?php
include 'db.php';
session_start();

// Verificar si el usuario es asesor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'asesor') {
    header("Location: login.php");
    exit();
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['turno_id'])) {
    $turno_id = $_POST['turno_id'];
    $module = $_SESSION['username']; // Usar el nombre de usuario como módulo

    // Eliminar el turno de la base de datos
    $sql = "DELETE FROM turnos WHERE id=$turno_id";

    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = "Turno finalizado y eliminado por el módulo $module.";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al finalizar el turno: " . $conn->error;
    }
} else {
    $response['success'] = false;
    $response['message'] = "No se ha recibido el ID del turno.";
}

echo json_encode($response);

$conn->close();
?>
