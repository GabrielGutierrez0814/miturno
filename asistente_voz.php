<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente de Voz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }
        textarea {
            width: 100%;
            height: 150px;
            resize: vertical;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Asistente de Voz</h1>
    <form id="voiceForm">
        <label for="textToSpeak">Escribe algo para que sea dicho en voz alta:</label>
        <textarea id="textToSpeak" name="textToSpeak" placeholder="Escribe aquí..." required></textarea>
        <button type="submit">Hablar</button>
    </form>
</div>

<script>
    document.getElementById('voiceForm').addEventListener('submit', function(event) {
        event.preventDefault();
        let text = document.getElementById('textToSpeak').value.trim();

        // Validar que haya texto para hablar
        if (text !== '') {
            // Crear instancia del habla
            let utterance = new SpeechSynthesisUtterance(text);

            // Configurar opciones (idioma, volumen, velocidad, etc.)
            utterance.lang = 'es-ES'; // Idioma español de España
            utterance.volume = 1; // Volumen (0 a 1)
            utterance.rate = 1; // Velocidad de habla (0.1 a 10)
            utterance.pitch = 1; // Tono de la voz (0 a 2)

            // Reproducir el habla
            speechSynthesis.speak(utterance);
        } else {
            alert('Por favor, escribe algo para que sea dicho en voz alta.');
        }
    });
</script>
</body>
</html>
