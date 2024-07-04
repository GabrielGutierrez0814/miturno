<?php
if (file_exists('turno_llamado.txt')) {
    $turno_llamado = file_get_contents('turno_llamado.txt');
    header('Content-Type: application/json');
    echo $turno_llamado;
} else {
    echo json_encode(null);
}
?>
