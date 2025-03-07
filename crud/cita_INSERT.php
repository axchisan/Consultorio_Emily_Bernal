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

                // Validación del reCAPTCHA v2
                $ip = $_SERVER['REMOTE_ADDR'];
                $captcha = $_POST['g-recaptcha-response'];
                $secretKey = "6LezIuwqAAAAAEwjDrlb4Vc-CmG_VwqU-sARMVMI"; // Clave secreta

                $respuesta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=$ip");
                $atributos = json_decode($respuesta, true);

                if (!$atributos["success"]) {
                    // Guardar el mensaje de error y los datos del formulario en la sesión
                    $_SESSION['CaptchaError'] = "Por favor, verifica que eres un humano.";
                    $_SESSION['FormData'] = [
                        'name' => $_POST['name'],
                        'email' => $_POST['email'],
                        'fecha_cita' => $_POST['fecha_cita'],
                        'hora' => $_POST['hora'],
                        'consultas' => $_POST['consultas'],
                        'dentistas' => $_POST['dentistas'],
                        'phone' => $_POST['phone'],
                        'apellido' => $_POST['apellido'] // Incluido por si lo necesitas más adelante
                    ];
                    header("Location: ../principal.php#appointment");
                    exit();
                }

                // Si el CAPTCHA es válido, procede con la inserción
                $query = "INSERT INTO citas (id_paciente, id_doctor, fecha_cita, hora_cita, id_consultas, estado) 
                          VALUES (?, ?, ?, ?, ?, 'I')";

                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "iissi", $id_paciente, $id_doctor, $fecha_cita, $hora, $id_consultas);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['MensajeTexto'] = "Cita realizada con éxito!";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-info text-white";
                    // Limpiar los datos del formulario tras éxito
                    unset($_SESSION['FormData']);
                    // Redirigir al inicio de la página
                    header("Location: ../principal.php#top");
                    exit();
                } else {
                    $_SESSION['MensajeTexto'] = "Error al insertar la cita.";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
                    error_log("Error al insertar la cita: " . mysqli_error($link));
                    header("Location: ../principal.php#appointment");
                    exit();
                }

                // Cerrar la consulta y la conexión
                mysqli_stmt_close($stmt);
                mysqli_close($link);

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
    $_SESSION['MensajeTexto'] = "Ha ocurrido un error inesperado.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../principal.php");
    exit();
} catch (Error $e) {
    error_log("Error no controlado: " . $e->getMessage());
    $_SESSION['MensajeTexto'] = "Ha ocurrido un error inesperado.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../principal.php");
    exit();
}
?>