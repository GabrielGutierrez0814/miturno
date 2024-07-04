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

    // Actualizar el estado del turno a 'called'
    $sql = "UPDATE turnos SET status='called', called_by='$module' WHERE id=$turno_id";

    if ($conn->query($sql) === TRUE) {
        // Obtener información del turno
        $result = $conn->query("SELECT * FROM turnos WHERE id=$turno_id");
        $row = $result->fetch_assoc();
        $turno_info = [
            "id" => $row['id'],
            "service_code" => $row['service_code'],
            "turno_numero" => $row['turno_numero'],
            "module" => $module
        ];

        // Guardar información del turno en el archivo
        file_put_contents('turno_llamado.txt', json_encode($turno_info));

        // Construir el mensaje de respuesta con el módulo que está llamando el turno
        $response['success'] = true;
        $response['message'] = "Turno vuelto a llamar desde el módulo $module: {$row['service_code']}{$row['turno_numero']}";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al volver a llamar el turno: " . $conn->error;
    }
} else {
    $response['success'] = false;
    $response['message'] = "No se ha recibido el ID del turno.";
}

echo json_encode($response);

$conn->close();
?>
