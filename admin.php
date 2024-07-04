<?php
include 'db.php';
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Función para mostrar mensajes de éxito temporalmente
function showMessage($message) {
    echo '<script>
            var alertMessage = $("<div class=\"alert alert-success\">" + \'' . $message . '\' + "</div>").appendTo("body").fadeIn();
            setTimeout(function() {
                alertMessage.fadeOut("slow");
            }, 2000);
          </script>';
}

// Función para borrar el nombre de cliente cuando el turno sea atendido
function clearClientName($conn, $turnoId) {
    $sql = "UPDATE turnos SET client_name = '' WHERE id = $turnoId";
    if ($conn->query($sql) === TRUE) {
        // Éxito al borrar el nombre del cliente
    } else {
        echo "Error al limpiar el nombre del cliente: " . $conn->error;
    }
}

// Procesar formularios enviados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_turno'])) {
        $client_name = $_POST['client_name'];
        $service_code = $_POST['service_code'];
        $sql = "INSERT INTO turnos (client_name, service_code, status) VALUES ('$client_name', '$service_code', 'nuevo')";
        if ($conn->query($sql) === TRUE) {
            showMessage('Turno creado con éxito');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['create_user'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            showMessage('Usuario creado con éxito');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['create_service'])) {
        $name = $_POST['name'];
        $code = $_POST['code'];
        $sql = "INSERT INTO services (name, code) VALUES ('$name', '$code')";
        if ($conn->query($sql) === TRUE) {
            showMessage('Servicio creado con éxito');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['reset_turnos'])) {
        $sql = "UPDATE turno_counter SET current_turno = 0 WHERE id = 1";
        if ($conn->query($sql) === TRUE) {
            showMessage('Turnos reiniciados con éxito');
        } else {
            echo "Error al reiniciar turnos: " . $conn->error;
        }
    } elseif (isset($_POST['upload_file']) && isset($_FILES['file'])) {
        $target_dir = "videos/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_name = $_FILES["file"]["name"];
            $file_path = $target_file;
            $sql = "INSERT INTO videos (file_name, file_path) VALUES ('$file_name', '$file_path')";
            if ($conn->query($sql) === TRUE) {
                showMessage('Archivo cargado con éxito');
            } else {
                echo "Error al guardar en la base de datos: " . $conn->error;
            }
        } else {
            echo "Error al cargar el archivo.";
        }
    } elseif (isset($_POST['create_rotating_message'])) {
        $message = $_POST['rotating_message'];
        $sql = "INSERT INTO rotating_messages (message) VALUES ('$message')";
        if ($conn->query($sql) === TRUE) {
            showMessage('Mensaje rotativo creado con éxito');
        } else {
            echo "Error al crear el mensaje rotativo: " . $conn->error;
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $sql = "DELETE FROM users WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Usuario eliminado con éxito');
        } else {
            echo "Error al eliminar usuario: " . $conn->error;
        }
    } elseif (isset($_POST['delete_service'])) {
        $service_id = $_POST['service_id'];
        $sql = "DELETE FROM services WHERE id='$service_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Servicio eliminado con éxito');
        } else {
            echo "Error al eliminar servicio: " . $conn->error;
        }
    } elseif (isset($_POST['delete_video'])) {
        $video_id = $_POST['video_id'];
        $sql = "DELETE FROM videos WHERE id='$video_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Video eliminado con éxito');
        } else {
            echo "Error al eliminar video: " . $conn->error;
        }
    } elseif (isset($_POST['delete_rotating_message'])) {
        $message_id = $_POST['message_id'];
        $sql = "DELETE FROM rotating_messages WHERE id='$message_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Mensaje rotativo eliminado con éxito');
        } else {
            echo "Error al eliminar mensaje rotativo: " . $conn->error;
        }
    } elseif (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $sql = "UPDATE users SET username='$username', role='$role' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Usuario actualizado con éxito');
        } else {
            echo "Error al actualizar usuario: " . $conn->error;
        }
    } elseif (isset($_POST['edit_service'])) {
        $service_id = $_POST['service_id'];
        $name = $_POST['name'];
        $code = $_POST['code'];
        $sql = "UPDATE services SET name='$name', code='$code' WHERE id='$service_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Servicio actualizado con éxito');
        } else {
            echo "Error al actualizar servicio: " . $conn->error;
        }
    } elseif (isset($_POST['edit_video'])) {
        $video_id = $_POST['video_id'];
        $file_name = $_POST['file_name'];
        $file_path = $_POST['file_path'];
        $sql = "UPDATE videos SET file_name='$file_name', file_path='$file_path' WHERE id='$video_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Video actualizado con éxito');
        } else {
            echo "Error al actualizar video: " . $conn->error;
        }
    } elseif (isset($_POST['edit_rotating_message'])) {
        $message_id = $_POST['message_id'];
        $mensaje = $_POST['mensaje'];
        $sql = "UPDATE rotating_messages SET mensaje='$mensaje' WHERE id='$message_id'";
        if ($conn->query($sql) === TRUE) {
            showMessage('Mensaje rotativo actualizado con éxito');
        } else {
            echo "Error al actualizar mensaje rotativo: " . $conn->error;
        }
    }
}

// Borrar el nombre del cliente cuando el turno sea atendido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atender_turno'])) {
    $turnoId = $_POST['turno_id'];
    clearClientName($conn, $turnoId);
}

$turnos_result = $conn->query("SELECT * FROM turnos");
$users_result = $conn->query("SELECT * FROM users");
$services_result = $conn->query("SELECT * FROM services");
$videos_result = $conn->query("SELECT * FROM videos");
$messages_result = $conn->query("SELECT * FROM rotating_messages");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
            border-color: #007bff;
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .modal-header {
            background-color: #007bff;
            color: #fff;
        }
        .modal-footer .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        #alert-message {
            display: none;
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }

    </style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Administra tu Mundo: Sistema de Gestión de Turnos</h1>
        <img src="logo/logo.png" alt="Logotipo" style="height: 150px;">
    </div>

    <div class="alert alert-success" id="alert-message"></div>

    <div class="card">
        <div class="card-header">
            <h2>Crear Usuarios</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="admin.php">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select class="form-control" id="role" name="role">
                        <option value="admin">Administrador</option>
                        <option value="asesor">Asesor</option>
                    </select>
                </div>
                <button type="submit" name="create_user" class="btn btn-primary">Crear Usuario</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Crear Servicio</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="admin.php">
                <div class="form-group">
                    <label for="name">Nombre del Servicio</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="code">Código del Servicio (3 letras)</label>
                    <input type="text" class="form-control" id="code" name="code" maxlength="3" required>
                </div>
                <button type="submit" name="create_service" class="btn btn-primary">Crear Servicio</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Crear Mensaje Rotativo</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="admin.php">
                <div class="form-group">
                    <label for="rotating_message">Mensaje Rotativo</label>
                    <input type="text" class="form-control" id="rotating_message" name="rotating_message" required>
                </div>
                <button type="submit" name="create_rotating_message" class="btn btn-primary">Crear Mensaje Rotativo</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Cargar Video o Imagen</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="admin.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Seleccionar Archivo</label>
                    <input type="file" class="form-control" id="file" name="file" accept="video/*,image/*" required>
                </div>
                <button type="submit" name="upload_file" class="btn btn-primary">Cargar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Reiniciar Turnos</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="admin.php">
                <button type="submit" name="reset_turnos" class="btn btn-warning">Reiniciar Turnos</button>
            </form>
            <form method="POST" action="reset_turnos.php">
            <button type="submit" name="resetear_turnos" class="btn btn-danger">Resetear Turnos</button>
        </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Turnos</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Cliente</th>
                    <th>Código del Servicio</th>
                    <th>Número de Turno</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $turnos_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['client_name'] ?></td>
                        <td><?= $row['service_code'] ?></td>
                        <td><?= $row['turno_numero'] ?></td>
                        <td><?= $row['status'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Usuarios</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['role'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="editUser(<?= $row['id'] ?>, '<?= $row['username'] ?>', '<?= $row['role'] ?>')">Editar</button>
                            <form method="POST" action="admin.php" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Servicios</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $services_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['code'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="editService(<?= $row['id'] ?>, '<?= $row['name'] ?>', '<?= $row['code'] ?>')">Editar</button>
                            <form method="POST" action="admin.php" style="display:inline-block;">
                                <input type="hidden" name="service_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_service" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Videos</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Archivo</th>
                    <th>Ruta del Archivo</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $videos_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['file_name'] ?></td>
                        <td><?= $row['file_path'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="editVideo(<?= $row['id'] ?>, '<?= $row['file_name'] ?>', '<?= $row['file_path'] ?>')">Editar</button>
                            <form method="POST" action="admin.php" style="display:inline-block;">
                                <input type="hidden" name="video_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_video" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Mensajes Rotativos</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Mensaje</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $messages_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['mensaje'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="editMensaje(<?= $row['id'] ?>, '<?= $row['mensaje'] ?>')">Editar</button>
                            <form method="POST" action="admin.php" style="display:inline-block;">
                                <input type="hidden" name="message_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_rotating_message" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="admin.php">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="form-group">
                        <label for="editUsername">Usuario</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editRole">Rol</label>
                        <select class="form-control" id="editRole" name="role">
                            <option value="admin">Administrador</option>
                            <option value="asesor">Asesor</option>
                        </select>
                    </div>
                    <button type="submit" name="edit_user" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Servicio -->
<div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">Editar Servicio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="admin.php">
                    <input type="hidden" name="service_id" id="editServiceId">
                    <div class="form-group">
                        <label for="editServiceName">Nombre del Servicio</label>
                        <input type="text" class="form-control" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServiceCode">Código del Servicio</label>
                        <input type="text" class="form-control" id="editServiceCode" name="code" maxlength="3" required>
                    </div>
                    <button type="submit" name="edit_service" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Video -->
<div class="modal fade" id="editVideoModal" tabindex="-1" role="dialog" aria-labelledby="editVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVideoModalLabel">Editar Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="admin.php">
                    <input type="hidden" name="video_id" id="editVideoId">
                    <div class="form-group">
                        <label for="editFileName">Nombre del Archivo</label>
                        <input type="text" class="form-control" id="editFileName" name="file_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editFilePath">Ruta del Archivo</label>
                        <input type="text" class="form-control" id="editFilePath" name="file_path" required>
                    </div>
                    <button type="submit" name="edit_video" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal Editar Mensaje Rotativo -->
<div class="modal fade" id="editMessageModal" tabindex="-1" role="dialog" aria-labelledby="editMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMessageModalLabel">Editar Mensaje Rotativo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="admin.php">
                    <input type="hidden" name="message_id" id="editMessageId">
                    <div class="form-group">
                        <label for="editMensaje">Mensaje</label>
                        <input type="text" class="form-control" id="editMessage" name="mensaje" required>
                    </div>
                    <button type="submit" name="edit_rotating_message" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
    
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    function editUser(id, username, role) {
        $('#editUserId').val(id);
        $('#editUsername').val(username);
        $('#editRole').val(role);
        $('#editUserModal').modal('show');
    }

    function editService(id, name, code) {
        $('#editServiceId').val(id);
        $('#editServiceName').val(name);
        $('#editServiceCode').val(code);
        $('#editServiceModal').modal('show');
    }

    function editVideo(id, fileName, filePath) {
        $('#editVideoId').val(id);
        $('#editFileName').val(fileName);
        $('#editFilePath').val(filePath);
        $('#editVideoModal').modal('show');
    }

    function editMensaje(id, mensaje) {
        $('#editMessageId').val(id);
        $('#editMessage').val(mensaje);
        $('#editMessageModal').modal('show');
    }

    function showMessage(message) {
        var alertMessage = $('<div class="alert alert-success">' + message + '</div>').appendTo('body').fadeIn();
        setTimeout(function() {
            alertMessage.fadeOut('slow');
        }, 2000);
}

</script>
</body>
</html>
