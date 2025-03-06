<?php
session_start();
require_once '../php/conexionDB.php';
require_once '../php/consultas.php';

try {
    if (!isset($_GET['accion'])) {
        $_SESSION['MensajeTexto'] = "Advertencia: Acción no permitida.";
        $_SESSION['MensajeTipo'] = "is-warning";
        header("Location: ../admin/inicioAdmin.php");
        exit();
    }

    $opcion = $_GET['accion'];

    switch ($opcion) {
        case 'UDT': 
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
                $descripcion = trim(filter_var($_POST['Descripción'], FILTER_SANITIZE_STRING));
                $medicina = trim(filter_var($_POST['Medicina'], FILTER_SANITIZE_STRING));

                // Preparar consulta para evitar SQL Injection
                $query1 = "INSERT INTO paciente_diagnostico (id_cita, descripcion, medicina) VALUES (?, ?, ?)";
                $query2 = "UPDATE citas SET estado = 'A' WHERE id_cita = ?";

                $stmt1 = mysqli_prepare($link, $query1);
                mysqli_stmt_bind_param($stmt1, "iss", $id, $descripcion, $medicina);

                $stmt2 = mysqli_prepare($link, $query2);
                mysqli_stmt_bind_param($stmt2, "i", $id);

                $success1 = mysqli_stmt_execute($stmt1);
                $success2 = mysqli_stmt_execute($stmt2);

                if (!$success1 || !$success2) {
                    $_SESSION['MensajeTexto'] = "Error actualizando la cita.";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
                    error_log("Error en actualización: " . mysqli_error($link));
                } else {
                    $_SESSION['MensajeTexto'] = "Cita actualizada con éxito.";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-info text-white";
                }

                mysqli_stmt_close($stmt1);
                mysqli_stmt_close($stmt2);
                mysqli_close($link);

                header("Location: ../admin/inicioAdmin.php");
                exit();
            }
            break;

        case 'DLT':
            if (isset($_GET['id'], $_GET['estado'])) {
                $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
                $estado = filter_var($_GET['estado'], FILTER_SANITIZE_STRING);

                if ($estado !== "I") {
                    $query1 = "DELETE FROM paciente_diagnostico WHERE id_cita = ?";
                    $query2 = "DELETE FROM citas WHERE id_cita = ?";

                    $stmt1 = mysqli_prepare($link, $query1);
                    mysqli_stmt_bind_param($stmt1, "i", $id);

                    $stmt2 = mysqli_prepare($link, $query2);
                    mysqli_stmt_bind_param($stmt2, "i", $id);

                    $success1 = mysqli_stmt_execute($stmt1);
                    $success2 = mysqli_stmt_execute($stmt2);

                    if (!$success1 || !$success2) {
                        $_SESSION['MensajeTexto'] = "Error borrando la cita.";
                        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
                        error_log("Error al borrar: " . mysqli_error($link));
                    } else {
                        $_SESSION['MensajeTexto'] = "Cita borrada con éxito.";
                        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-info text-white";
                    }

                    mysqli_stmt_close($stmt1);
                    mysqli_stmt_close($stmt2);
                } else {
                    $_SESSION['MensajeTexto'] = "No se puede borrar la cita porque aún no se ha realizado.";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
                }

                mysqli_close($link);
                header("Location: ../admin/inicioAdmin.php");
                exit();
            }
            break;

        default:
            $_SESSION['MensajeTexto'] = "Advertencia: No se pudo identificar la acción a realizar.";
            $_SESSION['MensajeTipo'] = "is-warning";
            header("Location: ../admin/inicioAdmin.php");
            exit();
    }
} catch (Exception $e) {
    error_log("Excepción no controlada: " . $e->getMessage());
    echo "Ha ocurrido un error. Estamos trabajando en corregir esta situación.";
} catch (Error $e) {
    error_log("Error no controlado: " . $e->getMessage());
    echo "Ha ocurrido un error. Estamos trabajando en corregir esta situación.";
}
