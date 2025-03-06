<?php
session_start();
require_once '../php/conexionDB.php';
require_once '../php/consultas.php';

try {
    // Verificar si se recibió la acción
    if (empty($_GET['accion'])) {
        $_SESSION['MensajeTexto'] = "Advertencia: Acción no permitida.";
        $_SESSION['MensajeTipo'] = "is-warning";
        header("Location: ../principal.php");
        exit();
    }

    $opcion = $_GET['accion'];

    switch ($opcion) {
        case 'UDT':
            // Validar y sanitizar los datos de entrada
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $nombre = mysqli_real_escape_string($link, trim($_POST['name']));
            $apellido = mysqli_real_escape_string($link, trim($_POST['apellido']));
            $telefono = mysqli_real_escape_string($link, trim($_POST['cell']));
            $sexo = mysqli_real_escape_string($link, trim($_POST['sexo']));
            $fecha = mysqli_real_escape_string($link, trim($_POST['nacimiento']));
            $correo = mysqli_real_escape_string($link, trim($_POST['correo']));
            $clave = $_POST['clave'];

            // Encriptar clave si es necesario
            $claveEncriptada = !empty($clave) ? password_hash($clave, PASSWORD_BCRYPT) : null;

            // Construir la consulta SQL con seguridad
            $query = "UPDATE pacientes SET 
                        nombre = ?, 
                        apellido = ?, 
                        telefono = ?,  
                        sexo = ?,  
                        fecha_nacimiento = ?,  
                        correo_electronico = ?";
            $params = [$nombre, $apellido, $telefono, $sexo, $fecha, $correo];

            if ($claveEncriptada) {
                $query .= ", clave = ?";
                $params[] = $claveEncriptada;
            }

            $query .= " WHERE id_paciente = ?";
            $params[] = $id;

            // Preparar la consulta segura con `mysqli_stmt`
            $stmt = mysqli_prepare($link, $query);

            // Construcción de los tipos de datos para `bind_param`
            $paramTypes = str_repeat('s', count($params) - 1) . 'i';
            mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['MensajeTexto'] = "Registro actualizado con éxito.";
                $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-info text-white";
            } else {
                $_SESSION['MensajeTexto'] = "Error al actualizar el registro.";
                $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
            }

            // Cerrar la conexión
            mysqli_stmt_close($stmt);
            mysqli_close($link);

            header("Location: ../principal.php");
            exit();

        default:
            $_SESSION['MensajeTexto'] = "Advertencia: No se pudo identificar la acción a realizar.";
            $_SESSION['MensajeTipo'] = "is-warning";
            header("Location: ../principal.php");
            exit();
    }
} catch (Exception $e) {
    error_log("Excepción no controlada: " . $e->getMessage());
    echo "Ha ocurrido un error. Estamos trabajando en corregir esta situación.";
} catch (Error $e) {
    error_log("Error no controlado: " . $e->getMessage());
    echo "Ha ocurrido un error. Estamos trabajando en corregir esta situación.";
}
