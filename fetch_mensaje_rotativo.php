<?php
header('Content-Type: application/json');

// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'turnos_system';
$username = 'root';
$password = '';

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Establecer el modo de error de PDO para que lance excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para obtener el mensaje rotativo más reciente
    $stmt = $pdo->query("SELECT mensaje FROM rotating_messages ORDER BY id DESC LIMIT 1");
    $mensajeRotativo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se obtuvo un mensaje
    if ($mensajeRotativo) {
        echo json_encode($mensajeRotativo);
    } else {
        echo json_encode(['mensaje' => 'No hay mensajes disponibles']);
    }
} catch (PDOException $e) {
    // Manejar errores de conexión
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
}
?>
