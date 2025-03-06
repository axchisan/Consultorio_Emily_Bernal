<?php
session_start();
require_once '../php/conexionDB.php';
require_once '../php/consultas.php';

try {
    if (!isset($_GET['opciones'])) {
        $_SESSION['MensajeTexto'] = "Advertencia: Acción no permitida.";
        $_SESSION['MensajeTipo'] = "is-warning";
        header("Location: ../index.php");
        exit();
    }

    $opcion = $_GET['opciones'];

    switch ($opcion) {
        case 'INS':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar'])) {
                
                // Validar y sanitizar los datos
                $id_paciente = $_SESSION['id_paciente'];
                $id_doctor = filter_var($_POST['dentistas'], FILTER_SANITIZE_NUMBER_INT);
                $fecha_cita = mysqli_real_escape_string($link, trim($_POST['fecha_cita']));
                $hora = mysqli_real_escape_string($link, trim($_POST['hora']));
                $id_consultas = filter_var($_POST['consultas'], FILTER_SANITIZE_NUMBER_INT);
                
                // Consulta preparada para evitar SQL Injection
                $query = "INSERT INTO citas (id_paciente, id_doctor, fecha_cita, hora_cita, id_consultas, estado) 
                          VALUES (?, ?, ?, ?, ?, 'I')";

                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "iissi", $id_paciente, $id_doctor, $fecha_cita, $hora, $id_consultas);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['MensajeTexto'] = "Cita realizada con éxito!";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-info text-white";
                } else {
                    $_SESSION['MensajeTexto'] = "Error al insertar la cita.";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
                    error_log("Error al insertar la cita: " . mysqli_error($link));
                }

                // Cerrar la consulta y la conexión
                mysqli_stmt_close($stmt);
                mysqli_close($link);

                header("Location: ../principal.php");
                exit();
            }

            break;

        default:
            $_SESSION['MensajeTexto'] = "Advertencia: No se pudo identificar la acción a realizar.";
            $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-warning text-white";
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
