<?php
$directorio = 'videos';
$archivos = array_diff(scandir($directorio), array('..', '.'));

$archivos_validos = array();
foreach ($archivos as $archivo) {
    $tipo = mime_content_type("$directorio/$archivo");
    if (strpos($tipo, 'video') === 0 || strpos($tipo, 'image') === 0) {
        $archivos_validos[] = $archivo;
    }
}

echo json_encode($archivos_validos);
?>
