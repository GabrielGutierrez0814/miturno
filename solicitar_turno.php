<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Turno</title>
</head>
<body>
    <h1>Seleccionar Turno</h1>
    <form action="tomar_turno.php" method="post">
        <label for="turno">Seleccione un Turno:</label>
        <select id="turno" name="turno" required>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "sistema_turnos";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM nombre_turnos";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $nombre = $row["nombre"];
                    $identificacion = $row["identificacion"];
                    echo "<option value='$identificacion'>$nombre ($identificacion)</option>";
                }
            } else {
                echo "<option value=''>No hay turnos disponibles</option>";
            }

            $conn->close();
            ?>
        </select>
        <br><br>
        <button type="submit">Tomar Turno</button>
    </form>
</body>
</html>
