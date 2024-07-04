<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        if ($user['role'] == 'admin') {
            header("Location: admin.php");
        } elseif ($user['role'] == 'asesor') {
            header("Location: asesor.php");
        }
    } else {
        echo "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Color de fondo */
            font-family: Arial, sans-serif; /* Tipo de fuente */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Altura total de la ventana */
        }
        .logo-container {
            width: 50%; /* Ancho del contenedor del logo */
            text-align: center; /* Centra horizontalmente el contenido del contenedor */
            margin-right: 20px; /* Espacio a la derecha del contenedor del logo */
        }
        .logo {
            max-width: 100%; /* Ancho máximo del logo dentro del contenedor */
            height: auto; /* Altura automática */
        }
        .form-container {
            width: 50%; /* Ancho del contenedor del formulario */
            padding: 20px;
            border: 1px solid #ced4da; /* Borde del formulario */
            border-radius: 10px;
            background-color: #fff; /* Color de fondo del formulario */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra */
            text-align: center; /* Centra el contenido del formulario */
        }
        .card-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            height: 50px; /* Altura de los campos de entrada */
        }
        .btn-primary {
            background-color: #007bff; /* Color de fondo del botón */
            border-color: #007bff; /* Color del borde del botón */
            width: 100%; /* Ancho completo del botón */
            height: 50px; /* Altura del botón */
            font-size: 18px; /* Tamaño de fuente del botón */
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Color de fondo del botón al pasar el mouse */
            border-color: #0056b3; /* Color del borde del botón al pasar el mouse */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo-container">
        <img src="imagenes/logo.png" alt="Logo" class="logo">
    </div>
    <div class="form-container">
        <div class="card">
            <h2 class="card-title">Login</h2>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
