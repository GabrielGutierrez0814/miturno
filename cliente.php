<?php
require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

include 'db.php';

// Variable para verificar si se ha enviado el formulario
$form_submitted = false;

// Verificar si se ha enviado un formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['service_code'])) {
    // Establecer la variable de formulario enviado a true
    $form_submitted = true;

    // Código de procesamiento del formulario
    // Obtener el nombre del cliente y el código del servicio del formulario
    $client_name = isset($_POST['client_name']) ? $_POST['client_name'] : ''; // Obtener nombre de cliente si está presente
    $service_code = $_POST['service_code'];

    // Obtener el nombre del servicio
    $service_name = getServiceName($service_code);

    // Obtener el contador de turnos actual para el servicio seleccionado
    $result = $conn->query("SELECT turno_counter FROM services WHERE code='$service_code'");
    if ($result === false) {
        die("Error al ejecutar la consulta SQL: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $current_turno = ($row['turno_counter'] ? $row['turno_counter'] : 0);
    
    // Incrementar el número de turno para el servicio
    $current_turno++;
    $next_turno_str = str_pad($current_turno, 3, '0', STR_PAD_LEFT);

    // Actualizar el contador de turnos para el servicio
    $conn->query("UPDATE services SET turno_counter=$current_turno WHERE code='$service_code'");

    // Insertar el nuevo turno en la base de datos
    $sql = "INSERT INTO turnos (client_name, service_code, service_name, turno_numero) VALUES ('$client_name', '$service_code','$service_name', '$next_turno_str')";
    if ($conn->query($sql) === TRUE) {
        echo "Turno creado con éxito. Número: " . $next_turno_str;

        // Imprimir el turno
        try {
            $connector = new WindowsPrintConnector("EPSONDISPENSADOR");
            $printer = new Printer($connector);
            $date = date("Y-m-d H:i:s");

            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // Cambiar la fuente para aumentar el tamaño
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer->text("Turno\n");
            $printer->text("Número: $next_turno_str\n");

            // Restaurar la fuente normal
            $printer->selectPrintMode();

            // Imprimir el resto del texto
            $printer->text("Cliente: $client_name\n");
            $printer->text("Servicio: " . $service_name . " ($service_code)\n");
            $printer->text("Fecha y Hora: $date\n");

            // Imprimir texto adicional
            $printer->text("Salud Darien IPS - Compromiso Social Con Calidez\n");

            $printer->cut();

            $printer->close();
        } catch (Exception $e) {
            echo "No se pudo imprimir el turno: " . $e->getMessage();
        }
    } else {
        echo "Error al crear el turno: " . $conn->error;
    }
}

// Función para obtener el nombre de un servicio dado su código
function getServiceName($code) {
    global $conn;
    $result = $conn->query("SELECT name FROM services WHERE code='$code'");
    if ($result === false) {
        die("Error al ejecutar la consulta SQL: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    return ($row ? $row['name'] : "");
}

// Obtener la lista de servicios
$services_result = $conn->query("SELECT * FROM services");
if ($services_result === false) {
    die("Error al ejecutar la consulta SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tomar un Turno</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-service {
            width: 100%;
            font-size: 30px; /* Tamaño de fuente más grande para los botones de servicio */
            padding: 20px;
        }
        .logo-container img {
            max-width: 600px; /* Tamaño máximo del logo */
            width: 100%; /* Asegura que el logo ocupe el tamaño máximo permitido */
            height: auto; /* Mantiene la proporción del logo */
        }
        .service-name {
            font-size: 30px; /* Cambiar el tamaño de fuente de los nombres de los servicios */
        }
        #loading-gif {
            position: absolute;
            top: 10px; /* Ajustar la posición superior a 0 */
            left: 10px; /* Ajustar la posición izquierda a 0 */
            transform: translate(-8%, -30%);
            width: 120%; /* Establecer el ancho al 100% */
            height: 290%; /* Establecer la altura al 100% */
            background: url('imagenes/DAR-CLICK.gif') no-repeat center center;
            background-size: 30%; /* Redimensionar el GIF al 30% del tamaño original */
            z-index: 9999; /* Asegurar que el gif de carga esté sobre todo lo demás */
        }
        .services-container {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Dos columnas de igual tamaño */
            gap: 10px; /* Espacio entre los botones */
        }
    </style>
</head>
<body>
<div id="loading-gif"></div> <!-- Gif de carga -->
<div class="container" id="form-container"> <!-- Contenedor del formulario -->
    <div class="logo-container text-center mb-4">
        <img src="logo/logo.png" alt="Logo">
    </div>
    <h1 class="text-center mb-4">Toma un Turno</h1>
    <form id="turno-form" method="POST" action="cliente.php">
        <div class="form-group services-container">
            <?php while ($row = $services_result->fetch_assoc()): ?>
                <button type="submit" class="btn btn-primary btn-service" name="service_code" value="<?= $row['code'] ?>">
                    <span class="service-name"><?= $row['name'] ?> (<?= $row['code'] ?>)</span>
                </button>
            <?php endwhile; ?>
        </div>
    </form>
</div>

<script>
    // Función para ocultar el gif de carga y mostrar el contenido del formulario
    function showForm() {
        document.getElementById('loading-gif').style.display = 'none';
        document.getElementById('form-container').style.display = 'block';
    }

    // Agregar un evento click al documento para mostrar el formulario cuando se haga clic
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', showForm);
    });
</script>
</body>
</html>
<?php $conn->close(); ?>
