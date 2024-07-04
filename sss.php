<?php
$logoPath = __DIR__ . "/logo/logo.png";
if (file_exists($logoPath)) {
    echo "El archivo de logo existe y está en la ubicación correcta.";
} else {
    echo "El archivo de logo no se encuentra en la ubicación esperada.";
}
?>
