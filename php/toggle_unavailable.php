<?php
include_once 'conexionDB.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');

$response = ['status' => 'error', 'message' => ''];

if (isset($_POST['date']) && isset($_POST['id_doctor']) && isset($_POST['action'])) {
    $date = $_POST['date'];
    $id_doctor = (int)$_POST['id_doctor'];
    $action = $_POST['action'];

    if (empty($date) || empty($id_doctor) || empty($action)) {
        $response['message'] = 'Datos incompletos o inválidos.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        mysqli_close($link);
        exit;
    }

    // Obtener nombre del doctor
    $stmtDoctor = $link->prepare("SELECT nombreD, apellido FROM doctor WHERE id_doctor = ?");
    $stmtDoctor->bind_param("i", $id_doctor);
    $stmtDoctor->execute();
    $resultDoctor = $stmtDoctor->get_result();
    $doctor = $resultDoctor->fetch_assoc();
    $doctorName = $doctor ? $doctor['nombreD'] . ' ' . $doctor['apellido'] : 'Desconocido';
    $stmtDoctor->close();

    if ($action === 'add') {
        $stmt = $link->prepare("INSERT INTO unavailable_dates (id_doctor, unavailable_date) VALUES (?, ?)");
        $stmt->bind_param("is", $id_doctor, $date);
        $response['status'] = $stmt->execute() ? 'success' : 'error';
        $response['message'] = $stmt->error ?: '';
        $stmt->close();
    } elseif ($action === 'remove') {
        $stmt = $link->prepare("DELETE FROM unavailable_dates WHERE id_doctor = ? AND unavailable_date = ?");
        $stmt->bind_param("is", $id_doctor, $date);
        $response['status'] = $stmt->execute() ? 'success' : 'error';
        $response['message'] = $stmt->error ?: '';
        $stmt->close();
    } elseif ($action === 'cancel') {
        $stmt = $link->prepare("
            SELECT c.id_cita, p.nombre, p.correo_electronico, co.tipo AS tipo_cita, c.hora_cita 
            FROM citas c 
            JOIN pacientes p ON c.id_paciente = p.id_paciente 
            JOIN consultas co ON c.id_consultas = co.id_consultas 
            WHERE c.id_doctor = ? AND c.fecha_cita = ?
        ");
        $stmt->bind_param("is", $id_doctor, $date);
        $stmt->execute();
        $resultAppointments = $stmt->get_result();

        if ($resultAppointments->num_rows > 0) {
            $appointmentsToDelete = [];
            while ($row = $resultAppointments->fetch_assoc()) {
                $appointmentsToDelete[] = [
                    'id_cita' => $row['id_cita'],
                    'nombre' => $row['nombre'],
                    'correo_electronico' => $row['correo_electronico'],
                    'tipo_cita' => $row['tipo_cita'],
                    'hora_cita' => $row['hora_cita']
                ];
            }
            $stmt->close();

            $stmtInsert = $link->prepare("INSERT INTO unavailable_dates (id_doctor, unavailable_date) VALUES (?, ?)");
            $stmtInsert->bind_param("is", $id_doctor, $date);
            if ($stmtInsert->execute()) {
                $mail = new PHPMailer(true);
                $allEmailsSent = true;
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = '#email';
                    $mail->Password = '#contraseña privada';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;
                    $mail->CharSet = 'UTF-8';
                    $mail->setFrom('#email', 'Clínica Dental Perfect Teeth');
                    $mail->isHTML(true);

                    foreach ($appointmentsToDelete as $appointment) {
                        $mail->addAddress($appointment['correo_electronico']);
                        $mail->Subject = 'Cancelación de Cita - Perfect Teeth';
                        $mail->Body = "
                            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px;'>
                                <div style='text-align: center;'>
                                    <img src='https://i.imgur.com/vVp6wUp.png' alt='Perfect Teeth Logo' style='max-width: 150px;'>
                                </div>
                                <h2 style='color: #6f42c1; text-align: center;'>Cancelación de Cita</h2>
                                <p>Estimado/a <strong>" . htmlspecialchars($appointment['nombre'], ENT_QUOTES, 'UTF-8') . "</strong>,</p>
                                <p>Lamentamos informarte que tu cita con el Dr./Dra. <strong>" . htmlspecialchars($doctorName, ENT_QUOTES, 'UTF-8') . "</strong> ha sido cancelada debido a un imprevisto.</p>
                                <h3 style='color: #333;'>Detalles de la Cita Cancelada:</h3>
                                <ul style='list-style: none; padding: 0;'>
                                    <li><strong>Fecha:</strong> " . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . "</li>
                                    <li><strong>Hora:</strong> " . htmlspecialchars($appointment['hora_cita'], ENT_QUOTES, 'UTF-8') . "</li>
                                    <li><strong>Tipo de Consulta:</strong> " . htmlspecialchars($appointment['tipo_cita'], ENT_QUOTES, 'UTF-8') . "</li>
                                </ul>
                                <p>Te invitamos a registrar tu cita en otra fecha lo antes posible. Puedes hacerlo aquí:</p>
                                <div style='text-align: center; margin: 20px 0;'>
                                    <a href='http://localhost:3000/sistema_de_cita_odontologica-main/principal.php' style='background-color: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Registrar Nueva Cita</a>
                                </div>
                                <p>Si tienes preguntas, contáctanos:</p>
                                <ul style='list-style: none; padding: 0;'>
                                    <li><strong>Teléfono:</strong> +573105547320</li>
                                    <li><strong>WhatsApp:</strong> <a href='https://wa.me/message/WZSLOAVLHOAJB1'>Contactar a la Dra. Emily Bernal</a></li>
                                    <li><strong>Correo:</strong> emilybernal902@gmail.com</li>
                                </ul>
                                <p>Gracias por tu comprensión.</p>
                                <p style='text-align: center; color: #888; font-size: 12px;'>Atentamente,<br>El equipo de <strong>Perfect Teeth</strong></p>
                            </div>
                        ";
                        if (!$mail->send()) {
                            $allEmailsSent = false;
                            $response['message'] .= "Error al enviar correo a " . htmlspecialchars($appointment['correo_electronico'], ENT_QUOTES, 'UTF-8') . ": {$mail->ErrorInfo}\n";
                        }
                        $mail->clearAddresses();
                    }

                    if ($allEmailsSent) {
                        $stmtDelete = $link->prepare("DELETE FROM citas WHERE id_doctor = ? AND fecha_cita = ?");
                        $stmtDelete->bind_param("is", $id_doctor, $date);
                        $response['status'] = $stmtDelete->execute() ? 'success' : 'error';
                        $response['message'] = $stmtDelete->execute() ? 'Citas canceladas y notificaciones enviadas.' : 'Error al eliminar las citas: ' . $stmtDelete->error;
                        $stmtDelete->close();
                    } else {
                        $response['message'] .= 'No se pudieron enviar todos los correos.';
                    }
                } catch (Exception $e) {
                    $response['message'] = "Error al enviar correos: {$mail->ErrorInfo}";
                }
            } else {
                $response['message'] = 'Error al registrar la cancelación: ' . $stmtInsert->error;
            }
            $stmtInsert->close();
        } else {
            $response['message'] = 'No hay citas para cancelar.';
        }
    }
} else {
    $response['message'] = 'Datos incompletos.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
mysqli_close($link);
?>
