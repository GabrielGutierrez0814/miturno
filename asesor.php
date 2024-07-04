<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asesor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f0f0f0;
            padding-top: 20px;
        }
        .container {
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .card {
            margin-bottom: 15px;
        }
        .card-body {
            padding: 15px;
        }
        .card-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #333333;
        }
        .card-subtitle {
            font-size: 1rem;
            color: #666666;
        }
        .btn {
            margin-right: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-logout {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .btn-logout:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
    <script>
        $(document).ready(function () {
            let previousTurnos = {};

            // Crear un objeto de audio
            const audio = new Audio('sonido/notificacio.mp3');

            // Función para obtener los turnos y actualizar los contenedores
            function fetchTurnos() {
                $.ajax({
                    url: 'fetch_turnos.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        let servicios = {};
                        let nuevosTurnos = {};

                        // Agrupar turnos por servicio
                        data.forEach(function (turno) {
                            if (!servicios[turno.service_code]) {
                                servicios[turno.service_code] = {
                                    id: turno.service_code,
                                    nombre: turno.service_name,
                                    total: 0,
                                    turnos: []
                                };
                            }
                            servicios[turno.service_code].total++;
                            servicios[turno.service_code].turnos.push(turno);

                            // Verificar si el turno es nuevo
                            if (!previousTurnos[turno.id]) {
                                nuevosTurnos[turno.id] = turno;
                            }
                        });

                        $("#servicios-container").empty(); // Vaciar el contenedor antes de agregar nuevos servicios

                        // Crear contenedor para cada servicio
                        for (let serviceCode in servicios) {
                            let servicio = servicios[serviceCode];
                            let serviceContainer = `
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Servicio: ${servicio.id}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted"> ${servicio.nombre}</h6>
                                        <h6 class="card-subtitle mb-2 text-muted">Total de Turnos: ${servicio.total}</h6>
                                        <div id="turnos-${serviceCode}">
                                            ${servicio.turnos.map(turno => `
                                                <div class="card mb-2" id="turno-${turno.id}">
                                                    <div class="card-body">
                                                        <form>
                                                            <input type="hidden" name="turno_id" value="${turno.id}">
                                                            <button type="button" onclick="atenderTurno(${turno.id})" class="btn btn-primary">Atender</button>
                                                            <button type="button" onclick="volverLlamar(${turno.id})" class="btn btn-warning">Volver a Llamar</button>
                                                            <button type="button" onclick="finalizarAtencion(${turno.id})" class="btn btn-success">Finalizar Atención</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>`;
                            $("#servicios-container").append(serviceContainer);
                        }

                        // Mostrar alerta y reproducir sonido si hay nuevos turnos
                        if (Object.keys(nuevosTurnos).length > 0) {
                            showNotification(); // Mostrar notificación emergente
                        }

                        // Actualizar el estado de previousTurnos
                        previousTurnos = {};
                        data.forEach(function (turno) {
                            previousTurnos[turno.id] = turno;
                        });
                    }
                });
            }

            // Función para mostrar la notificación emergente
            function showNotification() {
                alert("¡Nuevo turno ha llegado!");
                audio.play();

                // Cerrar la alerta después de 1 segundo
                setTimeout(function () {
                    $(".alert").alert('close');
                }, 1000);

                // Mostrar la lista de nuevos turnos en una alerta separada
                setTimeout(function () {
                    let nuevosTurnosMsg = "Nuevos turnos:\n" + Object.values(nuevosTurnos).map(turno => `${turno.service_code}`).join("\n");
                    alert(nuevosTurnosMsg);
                }, 1000); // Mostrar después de cerrar la alerta principal
            }

            // Llamar a fetchTurnos() una vez al cargar la página
            fetchTurnos();

            // Llamar a fetchTurnos() cada 5 segundos para actualizar automáticamente
            setInterval(fetchTurnos, 5000);
        });

        // Función para atender un turno
        function atenderTurno(turnoId) {
            $.ajax({
                url: 'atender_turno.php',
                method: 'POST',
                data: { turno_id: turnoId, action: 'atender_turno' },
                success: function (response) {
                    if (response.success) {
                        alert(response.message); // Mostrar mensaje de éxito
                    } else {
                        alert("Error al atender el turno: " + response.message);
                    }
                    fetchTurnos(); // Actualizar la lista de turnos después de atender uno
                },
                error: function () {
                    alert("Error de conexión al atender el turno. Inténtalo de nuevo.");
                }
            });
        }

        // Función para volver a llamar un turno
        function volverLlamar(turnoId) {
            $.ajax({
                url: 'volver_llamar.php',
                method: 'POST',
                data: { turno_id: turnoId, action: 'volver_llamar' },
                success: function (response) {
                    if (response.success) {
                        alert(response.message); // Mostrar mensaje de éxito
                        fetchTurnos(); // Actualizar la lista de turnos después de volver a llamar
                    } else {
                        alert("Error al volver a llamar el turno: " + response.message);
                    }
                },
                error: function () {
                    alert("Error de conexión al volver a llamar el turno. Inténtalo de nuevo.");
                }
            });
        }

        // Función para finalizar la atención de un turno
        function finalizarAtencion(turnoId) {
            $.ajax({
                url: 'finalizar_atencion.php',
                method: 'POST',
                data: { turno_id: turnoId, action: 'finalizar_atencion' },
                success: function (response) {
                    if (response.success) {
                        alert(response.message); // Mostrar mensaje de éxito
                        $("#turno-" + turnoId).remove(); // Eliminar el turno del contenedor después de finalizar la atención
                    } else {
                        alert("Error al finalizar la atención del turno: " + response.message);
                    }
                },
                error: function () {
                    alert("Error de conexión al finalizar la atención del turno. Inténtalo de nuevo.");
                }
            });
        }

        // Función para cerrar sesión
        function cerrarSesion() {
            window.location.href = 'login.php'; // Redirigir a la página de login.php
        }
    </script>
</head>
<body>
<div class="container">
    <button class="btn btn-logout" onclick="cerrarSesion()">Cerrar Sesión</button>
    <h1 class="text-center mt-3 mb-5">Atender Turnos</h1>
    <div id="servicios-container">
        <!-- Aquí se agregarán dinámicamente los contenedores de servicios -->
    </div>
</div>
</body>
</html>
