<?php
require_once '../php/conexionDB.php';
require_once '../php/consultas.php';

try {
    if (!isset($_GET['opciones'])) {
        die("Advertencia: Acción no permitida.");
    }

    $opcion = $_GET['opciones'];

    function insertarUsuario($link, $query, $params) {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($link);

        if ($success) {
            // Redirigir a registro.php con indicador de éxito
            header("Location: ../registro.php?success=1");
            exit();
        } else {
            die("Error insertando el contenido: " . mysqli_error($link));
        }
    }

    switch ($opcion) {
        case 'INS': // Registro de pacientes
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingresar'])) {
                $nombre = trim(filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS));
                $apellido = trim(filter_var($_POST['apellido'], FILTER_SANITIZE_SPECIAL_CHARS));
                $telefono = trim(filter_var($_POST['cell'], FILTER_SANITIZE_SPECIAL_CHARS));
                $sexo = trim(filter_var($_POST['sexo'], FILTER_SANITIZE_SPECIAL_CHARS));
                $fecha = trim(filter_var($_POST['nacimiento'], FILTER_SANITIZE_SPECIAL_CHARS));
                $correo = trim(filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL));
                $clave = password_hash($_POST['password'], PASSWORD_DEFAULT);

                $query = "INSERT INTO pacientes (nombre, apellido, telefono, sexo, fecha_nacimiento, correo_electronico, clave) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";

                insertarUsuario($link, $query, [$nombre, $apellido, $telefono, $sexo, $fecha, $correo, $clave]);
            }
            break;

        case 'INSDOCT': // Registro de doctores
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
                $nombre = trim(filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS));
                $apellido = trim(filter_var($_POST['apellido'], FILTER_SANITIZE_SPECIAL_CHARS));
                $sexo = trim(filter_var($_POST['sexo'], FILTER_SANITIZE_SPECIAL_CHARS));
                $fecha = trim(filter_var($_POST['nacimiento'], FILTER_SANITIZE_SPECIAL_CHARS));
                $correo = trim(filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL));
                $telefono = trim(filter_var($_POST['cell'], FILTER_SANITIZE_SPECIAL_CHARS));
                $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
                $especialidad = trim(filter_var($_POST['especialidad'], FILTER_SANITIZE_SPECIAL_CHARS));

                $query = "INSERT INTO doctor (nombreD, apellido, sexo, fecha_nacimiento, telefono, correo_eletronico, clave, id_especialidad) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                insertarUsuario($link, $query, [$nombre, $apellido, $sexo, $fecha, $telefono, $correo, $clave, $especialidad]);
            }
            break;

        default:
            die("Advertencia: No se pudo identificar la acción a realizar.");
    }
} catch (Exception $e) {
    error_log("Excepción no controlada: " . $e->getMessage());
    echo "Ha ocurrido un error. Estamos trabajando en corregir esta situación.";
} catch (Error $e) {
    error_log("Error no controlado: " . $e->getMessage());
    echo "Ha ocurrido un error. Estamos trabajando en corregir esta situación.";
}