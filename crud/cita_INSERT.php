<?php
session_start();
require_once '../php/conexionDB.php';
require_once '../php/consultas.php';

if (!isset($_GET['opciones'])) {
    $_SESSION['MensajeTexto'] = "Advertencia: Acción no permitida.";
    $_SESSION['MensajeTipo'] = "is-warning";
    header("Location: ../index.php");
    exit;
}

$opcion = $_GET['opciones'];

function convertTo12Hour($time24) {
    if (empty($time24)) return "";
    $time = DateTime::createFromFormat('H:i', $time24);
    return $time ? $time->format('h:i A') : $time24;
}

try {
    switch ($opcion) {
        case 'INS':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar'])) {
                $id_paciente = $_SESSION['id_paciente'];
                $id_doctor = filter_var($_POST['dentistas'], FILTER_SANITIZE_NUMBER_INT);
                $fecha_cita = mysqli_real_escape_string($link, trim($_POST['fecha_cita']));
                $hora_24 = mysqli_real_escape_string($link, trim($_POST['hora']));
                $hora_12 = convertTo12Hour($hora_24);
                $id_consultas = filter_var($_POST['consultas'], FILTER_SANITIZE_NUMBER_INT);

                $ip = $_SERVER['REMOTE_ADDR'];
                $captcha = $_POST['g-recaptcha-response'];
                $secretKey = "6LezIuwqAAAAAEwjDrlb4Vc-CmG_VwqU-sARMVMI";

                $respuesta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=$ip");
                $atributos = json_decode($respuesta, true);

                if (!$atributos["success"]) {
                    $_SESSION['CaptchaError'] = "Por favor, verifica que eres un humano.";
                    $_SESSION['FormData'] = [
                        'name' => $_POST['name'],
                        'email' => $_POST['email'],
                        'fecha_cita' => $_POST['fecha_cita'],
                        'hora' => $_POST['hora'],
                        'consultas' => $_POST['consultas'],
                        'dentistas' => $_POST['dentistas'],
                        'phone' => $_POST['phone'],
                        'apellido' => $_POST['apellido']
                    ];
                    header("Location: ../principal.php#appointment");
                    exit;
                }

                $query = "INSERT INTO citas (id_paciente, id_doctor, fecha_cita, hora_cita, id_consultas, estado) 
                          VALUES (?, ?, ?, ?, ?, 'I')";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "iissi", $id_paciente, $id_doctor, $fecha_cita, $hora_12, $id_consultas);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['MensajeTexto'] = "Cita realizada con éxito!";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-info text-white";
                    unset($_SESSION['FormData']);
                    header("Location: ../principal.php#top");
                } else {
                    $_SESSION['MensajeTexto'] = "Error al insertar la cita.";
                    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
                    error_log("Error al insertar la cita: " . mysqli_error($link));
                    header("Location: ../principal.php#appointment");
                }

                mysqli_stmt_close($stmt);
                mysqli_close($link);
                exit;
            }
            break;

        default:
            $_SESSION['MensajeTexto'] = "Advertencia: No se pudo identificar la acción a realizar.";
            $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-warning text-white";
            header("Location: ../principal.php");
            exit;
    }
} catch (Exception $e) {
    error_log("Excepción no controlada: " . $e->getMessage());
    $_SESSION['MensajeTexto'] = "Ha ocurrido un error inesperado.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../principal.php");
    exit;
} catch (Error $e) {
    error_log("Error no controlado: " . $e->getMessage());
    $_SESSION['MensajeTexto'] = "Ha ocurrido un error inesperado.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../principal.php");
    exit;
}