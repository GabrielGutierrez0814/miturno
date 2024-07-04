<?php
require 'vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

function generarAudio($mensaje, $modulo) {
    // Instantiates a client
    $client = new TextToSpeechClient();

    // Set the text input to be synthesized
    $synthesisInputText = (new SynthesisInput())
        ->setText($mensaje);

    // Build the voice request; select the language code ("es-ES") and the ssml voice gender
    $voice = (new VoiceSelectionParams())
        ->setLanguageCode('es-ES')
        ->setSsmlGender(VoiceSelectionParams\SsmlVoiceGender::MALE);

    // Select the type of audio file you want returned
    $audioConfig = (new AudioConfig())
        ->setAudioEncoding(AudioEncoding::MP3);

    // Perform the text-to-speech request on the text input with selected voice parameters and audio file type
    $response = $client->synthesizeSpeech($synthesisInputText, $voice, $audioConfig);
    $audioContent = $response->getAudioContent();

    // Guardar el archivo de audio en una carpeta específica
    $rutaArchivo = 'audios/';
    $nombreArchivo = 'modulo_' . $modulo . '.mp3';
    file_put_contents($rutaArchivo . $nombreArchivo, $audioContent);

    // Cerrar el cliente TextToSpeech
    $client->close();
}

// Ejemplo de uso
$modulo = '1'; // Modificar esto con el módulo específico
$mensaje = 'Diríjase al módulo ' . $modulo . '.';
generarAudio($mensaje, $modulo);
?>
