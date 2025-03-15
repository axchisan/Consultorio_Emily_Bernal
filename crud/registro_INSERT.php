<?php
require_once '../php/conexionDB.php';
require_once '../php/consultas.php';

if (!isset($_GET['opciones'])) {
    die("Advertencia: Acción no permitida.");
}

$opcion = $_GET['opciones'];

function insertarUsuario($link, $query, $params, $tipo = 'paciente') {
    $stmt = mysqli_prepare($link, $query);
    if (!$stmt) {
        die("Error preparando la consulta: " . mysqli_error($link));
    }
    
    mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
    $success = mysqli_stmt_execute($stmt);

    if ($tipo === 'paciente' && $success) {
        // Iniciar sesión para pacientes
        session_start();
        $id_paciente = mysqli_insert_id($link);
        $token = bin2hex(random_bytes(32)); // Token de 32 bytes, igual que en validarLogin
        
        // Actualizar el session_token en la tabla pacientes
        $updateQuery = "UPDATE pacientes SET session_token = ? WHERE id_paciente = ?";
        $stmtUpdate = mysqli_prepare($link, $updateQuery);
        if ($stmtUpdate) {
            mysqli_stmt_bind_param($stmtUpdate, "si", $token, $id_paciente);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);
        } else {
            die("Error preparando actualización de token: " . mysqli_error($link));
        }
        
        $_SESSION['id_paciente'] = $id_paciente;
        $_SESSION['session_token'] = $token;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    return $success;
}

try {
    switch ($opcion) {
        case 'INS':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingresar'])) {
                $nombre = trim(filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS));
                $apellido = trim(filter_var($_POST['apellido'], FILTER_SANITIZE_SPECIAL_CHARS));
                $telefono = trim(filter_var($_POST['cell'], FILTER_SANITIZE_SPECIAL_CHARS));
                $sexo = trim(filter_var($_POST['sexo'], FILTER_SANITIZE_SPECIAL_CHARS));
                $fecha = trim(filter_var($_POST['nacimiento'], FILTER_SANITIZE_SPECIAL_CHARS));
                $correo = trim(filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL));
                $clave = password_hash($_POST['password'], PASSWORD_DEFAULT);

                $query = "INSERT INTO pacientes (nombre, apellido, telefono, sexo, fecha_nacimiento, correo_electronico, clave, session_token) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, NULL)"; // session_token se actualiza después

                $success = insertarUsuario($link, $query, [$nombre, $apellido, $telefono, $sexo, $fecha, $correo, $clave], 'paciente');
                
                if ($success) {
                    $_SESSION['MensajeTexto'] = "¡Cuenta creada exitosamente! Bienvenido(a) $nombre";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-success text-white";
                    header("Location: ../principal.php");
                    exit;
                } else {
                    die("Error insertando el contenido: " . mysqli_error($link));
                }
            }
            break;

        case 'INSDOCT':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
                $nombre = trim(filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS));
                $apellido = trim(filter_var($_POST['apellido'], FILTER_SANITIZE_SPECIAL_CHARS));
                $sexo = trim(filter_var($_POST['sexo'], FILTER_SANITIZE_SPECIAL_CHARS));
                $fecha = trim(filter_var($_POST['nacimiento'], FILTER_SANITIZE_SPECIAL_CHARS));
                $correo = trim(filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL));
                $telefono = trim(filter_var($_POST['cell'], FILTER_SANITIZE_SPECIAL_CHARS));
                $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
                $especialidad = trim(filter_var($_POST['especialidad'], FILTER_SANITIZE_SPECIAL_CHARS));

                $query = "INSERT INTO doctor (nombreD, apellido, sexo, fecha_nacimiento, telefono, correo_eletronico, clave, id_especialidad, session_token) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL)"; // session_token NULL para doctores

                $success = insertarUsuario($link, $query, [$nombre, $apellido, $sexo, $fecha, $telefono, $correo, $clave, $especialidad], 'doctor');
                
                session_start();
                if ($success) {
                    $_SESSION['MensajeTexto'] = "¡Doctor registrado exitosamente!";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-success text-white";
                } else {
                    $_SESSION['MensajeTexto'] = "Error al registrar el doctor: " . mysqli_error($link);
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
                }
                header("Location: ../registro_doctores.php");
                exit;
            }
            break;

        default:
            die("Advertencia: No se pudo identificar la acción a realizar.");
    }
} catch (Exception $e) {
    error_log("Excepción no controlada: " . $e->getMessage());
    session_start();
    $_SESSION['MensajeTexto'] = "Error: " . $e->getMessage();
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../registro.php");
    exit;
} catch (Error $e) {
    error_log("Error no controlado: " . $e->getMessage());
    session_start();
    $_SESSION['MensajeTexto'] = "Error grave: " . $e->getMessage();
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../registro.php");
    exit;
}