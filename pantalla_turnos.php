<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Turnos</title>
    <style>
        /* Estilos para la pantalla de turnos */
        .container {
            display: flex;
            flex-direction: row; /* Dividir la pantalla verticalmente */
            height: 85vh; /* Altura de la pantalla menos el texto rotativo */
        }

        /* Estilos para el contenedor de videos e imágenes */
        .videos-imagenes {
            flex: 1.2; /* Ocupa la mitad izquierda */
            background-color: #f2f2f2; /* Color de fondo gris claro */
            display: flex;
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
            flex-direction: column; /* Mostrar elementos en columna */
            overflow: hidden; /* Ocultar desbordamiento */
            position: relative; /* Posicionamiento relativo para el logotipo */
        }

        video, img {
            max-width: 100%;
            max-height: 100%;
            display: none; /* Ocultar por defecto */
        }

        /* Estilos para el contenedor de los turnos */
        .turnos-container {
            flex: 1; /* Ocupa la mitad derecha */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrar verticalmente */
            justify-content: flex-start; /* Alinear los turnos hacia arriba */
            padding-top: 10px; /* Espacio en la parte superior */
            background-color: #0c6ef3; /* Color azul */
            border-left: 15px solid #FFD700; /* Borde amarillo borde de la mitad */
        }

        /* Estilos para el cuadro de turno llamado */
        .turno-llamado {
            font-size: 106px; /* Tamaño de fuente más grande */
            font-weight: bold;
            color: white;
            background-color: #333fff; /* Color azul */
            padding: 40px; /* Aumentar el padding para hacerlo más grande */
            border-radius: 20px; /* Aumentar el radio de borde para hacerlo más redondeado */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            animation: parpadeo 3s infinite; /* Animación de parpadeo */
            z-index: 1000;
        }

        /* Animación de parpadeo */
        @keyframes parpadeo {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }

        /* Estilos para el listado de turnos */
        .lista-turnos {
            width: 100%; /* Ancho del contenedor */
            max-height: 60vh; /* Altura máxima para el scroll */
            overflow-y: auto; /* Habilitar scroll vertical si es necesario */
            padding: 10px;
        }

        /* Estilos para cada elemento de la lista de turnos */
        .turno-item {
            margin-bottom: 10px;
            padding: 25px;
            background-color: #f5ee09; /* Color amarillo */
            border-radius: 10px;
            font-size: 40px;
            text-align: center;
            color: #333fff;
            font-weight: bold; /* Letras en negrita */
        }

        /* Estilos para el logotipo */
        .logotipo {
            width: 100px; /* Ancho del logotipo */
            height: auto;
            position: absolute;
            top: 20px; /* Posición desde arriba */
            left: 50%;
            transform: translateX(-50%);
            margin-top: 20px; /* Margen superior */
        }

        /* Estilos para el contenedor del mensaje rotativo */
        .mensaje-rotativo {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #0c6ef3; /*color del fondo del mensaje rotativo (azul)*/
            color: white;
            font-size: 54px;
            white-space: nowrap;
            overflow: hidden;
        }

        .mensaje-rotativo span {
            display: inline-block;
            padding-left: 100%;
            animation: rotarTexto 30s linear infinite; /* Cambiar la duración de la animación a 30 segundos */
        }

        /* Animación del texto rotativo */
        @keyframes rotarTexto {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        /* Nuevos estilos para los contenedores de turnos y módulos */
        .turnos-modulos-container {
            display: flex;
            width: 70%; /* separacion de modulo y turno */
            justify-content: space-around;
            margin-top: 120px; /* Espacio para el logotipo */
        }

        .turnos, .modulos {
            width: 35%; /* Ancho de cada contenedor donde esta el turno y el logo color amarrillo */
            text-align: center;
        }

        .turnos h2, .modulos h2 {
            margin-bottom: 10px; /* Reducir el espacio entre los subtítulos */
            font-size: 44px; /* Cambiar el tamaño de los subtítulos */
            color: #FFD700; /* Cambiar el color de los subtítulos */
        }

        h1 {
            margin-bottom: -120px; /* Reducir el espacio entre el título y los subtítulos */
            color: white; /* Cambiar el color del título */
            font-size: 54px; /* Cambiar el tamaño de los titulo */
        }

        #hora-fecha {
            color: #333fff; /* Cambia el color del texto */
            font-size: 78px; /* Cambia el tamaño de la fuente */
            font-weight: bold; /* Pone el texto en negrita */
        }
    </style>
    <script>
        var turnoActual = null; // Variable para almacenar el último turno llamado
        var turnosLlamados = []; // Array para almacenar los turnos llamados

        // Función para leer el texto del turno en voz alta una sola vez
        function leerTextoEnVoz(texto) {
            var synth = window.speechSynthesis;
            var utterance = new SpeechSynthesisUtterance(texto);
            utterance.lang = 'es-LA'; // Establecer el idioma en español latino
            utterance.voiceURI = 'Google español de América Latina'; // Especificar la voz masculina en español latino
            synth.speak(utterance);
        }

        // Función para cargar y mostrar el turno llamado en pantalla
        function cargarTurnoLlamado() {
            fetch('fetch_turno_llamado.php')
                .then(response => response.json())
                .then(data => {
                    if (data && turnoActual !== data.id) {
                        // Actualizar el turno actual
                        turnoActual = data.id;
                        // Crear el texto del turno con el módulo al principio
                        var textoTurno = ` ${data.service_code} ${data.turno_numero}`;
                        var textoModulo = ` ${data.module}`;
                        var textoVoz = `Atención, turno ${textoTurno}, diríjase al  ${data.module}`;
                        
                        // Leer el turno en voz alta
                        leerTextoEnVoz(textoVoz);
                        // Mostrar el turno en un cuadro en la mitad de la pantalla
                        var turnoLlamado = document.getElementById("turno-llamado");
                        turnoLlamado.innerText = `${textoTurno} - ${textoModulo}`;
                        turnoLlamado.style.opacity = "1";
                        // Ocultar el turno después de 5 segundos
                        setTimeout(function() {
                            turnoLlamado.style.opacity = "0";
                        }, 5000);
                        // Agregar el turno al listado de turnos llamados
                        turnosLlamados.unshift({ turno: textoTurno, modulo: textoModulo }); // Agregar al inicio del array
                        if (turnosLlamados.length > 6) {
                            turnosLlamados.pop(); // Eliminar el último turno si hay más de 10
                        }
                        // Actualizar la lista de turnos llamados en el DOM
                        actualizarListaTurnos();
                    }
                })
                .catch(error => console.error('Error al cargar el turno:', error));
        }

        // Función para actualizar la lista de turnos llamados en el DOM
        function actualizarListaTurnos() {
            var turnosLlamadosContainer = document.getElementById("lista-turnos");
            var modulosLlamadosContainer = document.getElementById("lista-modulos");
            turnosLlamadosContainer.innerHTML = ''; // Limpiar la lista actual de turnos
            modulosLlamadosContainer.innerHTML = ''; // Limpiar la lista actual de módulos

            turnosLlamados.forEach(function(item) {
                var turnoItem = document.createElement("div");
                turnoItem.className = "turno-item";
                turnoItem.innerText = item.turno;
                turnosLlamadosContainer.appendChild(turnoItem);

                var moduloItem = document.createElement("div");
                moduloItem.className = "turno-item";
                moduloItem.innerText = item.modulo;
                modulosLlamadosContainer.appendChild(moduloItem);
            });
        }

        // Función para cargar y mostrar el mensaje rotativo
        function cargarMensajeRotativo() {
            fetch('fetch_mensaje_rotativo.php')
                .then(response => response.json())
                .then(data => {
                    var mensajeRotativo = document.getElementById("mensaje-rotativo");
                    mensajeRotativo.innerHTML = `<span>${data.mensaje}</span>`;
                })
                .catch(error => console.error('Error al cargar el mensaje rotativo:', error));
        }

        // Función para obtener la hora y la fecha actual y actualizar el contenido HTML
        function actualizarHoraFecha() {
            var fechaHoraActual = new Date();
            var diaSemana = fechaHoraActual.toLocaleDateString('es-ES', { weekday: 'long' });
            var dia = fechaHoraActual.getDate();
            var mes = fechaHoraActual.toLocaleDateString('es-ES', { month: 'long' });
            var año = fechaHoraActual.getFullYear();
            var hora = fechaHoraActual.getHours();
            var minutos = fechaHoraActual.getMinutes();
            var segundos = fechaHoraActual.getSeconds();

            // Formatear los minutos y los segundos para que siempre tengan dos dígitos
            minutos = minutos < 10 ? '0' + minutos : minutos;
            segundos = segundos < 10 ? '0' + segundos : segundos;

            // Construir la cadena de texto con la hora y la fecha
            var horaFechaTexto = diaSemana + ', ' + dia + ' de ' + mes + ' de ' + año + ' - ' + hora + ':' + minutos + ':' + segundos;

            // Actualizar el contenido HTML con la hora y la fecha actual
            document.getElementById('hora-fecha').innerText = horaFechaTexto;
        }

        // Actualizar la hora y la fecha cada segundo
        setInterval(actualizarHoraFecha, 1000);

        // Función para cargar y mostrar los videos e imágenes
        function cargarVideosImagenes() {
            fetch('fetch_videos_imagenes.php')
                .then(response => response.json())
                .then(data => {
                    var contenedor = document.querySelector('.videos-imagenes');
                    var index = 0;

                    function mostrarSiguiente() {
                        // Ocultar el elemento actual
                        var elementos = contenedor.children;
                        for (var i = 0; i < elementos.length; i++) {
                            elementos[i].style.display = 'none';
                        }

                        // Mostrar el siguiente elemento
                        if (data.length > 0) {
                            var archivo = data[index];
                            var extension = archivo.split('.').pop().toLowerCase();
                            var elemento;

                            if (['mp4', 'webm', 'ogg'].includes(extension)) {
                                elemento = document.createElement('video');
                                elemento.src = 'videos/' + archivo;
                                elemento.autoplay = true;
                                elemento.loop = false; // No loopear el video
                                elemento.muted = false; // Silenciar el video
                                elemento.style.display = 'block';

                                // Event listener for when the video ends
                                elemento.addEventListener('ended', function() {
                                    index = (index + 1) % data.length; // Move to the next index
                                    mostrarSiguiente(); // Show the next element
                                });
                            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                                elemento = document.createElement('img');
                                elemento.src = 'videos/' + archivo;
                                elemento.style.display = 'block';

                                setTimeout(function() {
                                    index = (index + 1) % data.length; // Move to the next index
                                    mostrarSiguiente(); // Show the next element
                                }, 10000); // Show each image for 10 seconds
                            }

                            contenedor.appendChild(elemento);
                        }
                    }

                    mostrarSiguiente();
                })
                .catch(error => console.error('Error al cargar los videos e imágenes:', error));
        }

        // Cargar el turno llamado, el mensaje rotativo y los videos e imágenes al cargar la página y actualizar periódicamente
        window.onload = function() {
            cargarTurnoLlamado();
            cargarMensajeRotativo();
            cargarVideosImagenes();
            setInterval(cargarTurnoLlamado, 5000);
            setInterval(cargarMensajeRotativo, 30000); // Actualizar el mensaje rotativo cada 30 segundos
        };
    </script>
</head>
<body>
    <div class="container">
        <!-- Contenedor de videos e imágenes -->
        <div class="videos-imagenes">
            <img src="logo/logo.png" alt="Logotipo" class="logotipo"> <!-- Añadir el logotipo aquí -->
            <!-- Aquí se insertarán dinámicamente los videos e imágenes -->
        </div>
        <!-- Contenedor de turnos -->
        <div class="turnos-container">
            <h1>SALUD DARIEN IPS</h1>
            <div class="turnos-modulos-container">
                <div class="turnos">
                    <h2>Turno</h2>
                    <div id="lista-turnos" class="lista-turnos"></div>
                </div>
                <div class="modulos">
                    <h2>Módulo</h2>
                    <div id="lista-modulos" class="lista-turnos"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cuadro para mostrar el turno llamado en el centro de la pantalla -->
    <div id="turno-llamado" class="turno-llamado"></div>
    <!-- Contenedor del mensaje rotativo -->
    <div class="mensaje-rotativo" id="mensaje-rotativo"></div>
    <!-- Contenedor para mostrar la hora y la fecha -->
    <div id="hora-fecha" style="text-align: center; font-size: 24px; margin-top: 20px;"></div>
</body>
</html>
