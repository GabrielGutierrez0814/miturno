<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>LLamar Turno</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            function callTurno() {
                $.ajax({
                    url: 'anuncio_turno.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        // No es necesario hacer nada aquí, ya que el anuncio se maneja en pantalla_turnos.php
                    }
                });
            }

            // Llama al turno automáticamente cada cierto tiempo
            setInterval(callTurno, 10000); // Cada 10 segundos
        });
    </script>
</head>
<body>
    <h1>Llamar Turno</h1>
    <!-- Este archivo no contiene contenido visible para el usuario, solo hace la llamada automática del turno -->
</body>
</html>
