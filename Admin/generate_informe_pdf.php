<?php
session_start();
include_once('../php/conexionDB.php');
include_once('../php/consultas.php');

// Verificar si se recibió el ID del paciente
if (!isset($_POST['patient_id']) || empty($_POST['patient_id'])) {
    die("Error: ID de paciente no proporcionado.");
}

$patient_id = mysqli_real_escape_string($link, $_POST['patient_id']);
$doctor_id = $_SESSION['id_doctor'];

// Verificar si la sesión del doctor está activa
if (!isset($_SESSION['id_doctor'])) {
    die("Error: Sesión de doctor no encontrada.");
}

// Obtener datos del paciente
$patient = consultarPaciente($link, $patient_id);
if (!$patient) {
    die("Error: No se pudo obtener la información del paciente.");
}

// Calcular la edad del paciente para incluirla en el PDF
if (isset($patient['fecha_nacimiento']) && !empty($patient['fecha_nacimiento'])) {
    $birthDate = new DateTime($patient['fecha_nacimiento']);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;
} else {
    $age = 'N/A';
}

// Obtener datos de la cita más reciente del paciente con este doctor
$query = "SELECT c.*, con.tipo, d.nombreD 
          FROM citas c 
          LEFT JOIN consultas con ON con.id_consultas = c.id_consultas 
          LEFT JOIN doctor d ON d.id_doctor = c.id_doctor 
          WHERE c.id_paciente = '$patient_id' AND c.id_doctor = '$doctor_id' 
          ORDER BY c.fecha_cita DESC LIMIT 1";
$result = mysqli_query($link, $query);
if (!$result) {
    die("Error: No se pudieron obtener los datos de la cita.");
}
$appointment = mysqli_fetch_assoc($result);
if (!$appointment) {
    die("Error: No se encontraron citas para este paciente.");
}

// Obtener el informe médico existente
$medical_report_query = "SELECT * FROM informe_medico WHERE id_cita = '{$appointment['id_cita']}'";
$medical_report_result = mysqli_query($link, $medical_report_query);
if (!$medical_report_result) {
    die("Error: No se pudo obtener el informe médico.");
}
$medical_report = mysqli_num_rows($medical_report_result) > 0 ? mysqli_fetch_assoc($medical_report_result) : [];

require_once __DIR__ . '/../Reportes/fpdf/fpdf.php';

// Configurar FPDF para manejar caracteres especiales (ñ, tildes)
define('EURO', chr(128));
define('EURO_T', chr(129));

// Clase personalizada de FPDF
class PDF extends FPDF
{
    function Header()
    {
        if (file_exists(__DIR__ . '/../src/img/logo.png')) {
            $this->Image(__DIR__ . '/../src/img/logo.png', 10, 10, 25);
        }
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 15, utf8_decode('Informe Médico'), 0, 1, 'C');
        $this->Line(10, 35, 200, 35); // Línea separadora
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Fecha de generación: ' . date("d/m/Y H:i"), 0, 0, 'L');
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function DatosPaciente($patient, $age)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode("Información del Paciente"), 0, 1, 'L');
        $this->Ln(5);

        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 7, utf8_decode("Nombre:"), 0, 0, 'L');
        $this->Cell(60, 7, utf8_decode($patient['nombre'] . ' ' . $patient['apellido']), 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Edad:"), 0, 0, 'L');
        $this->Cell(60, 7, $age . ($age !== 'N/A' ? ' años' : ''), 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Fecha de Nacimiento:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['fecha_nacimiento'], 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Correo Electrónico:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['correo_electronico'], 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Teléfono:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['telefono'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("EPS:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['eps'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Ocupación:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['ocupacion'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Estado Civil:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['estado_civil'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Cédula:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['cedula'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Género:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['sexo'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Emergencia (Nombre):"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['emergencia_nombre'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Teléfono de Emergencia:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['emergencia_telefono'] ?? 'N/A', 0, 1, 'L');
        if ($age !== 'N/A' && $age < 18) {
            $this->Cell(40, 7, utf8_decode("Acompañante (Nombre):"), 0, 0, 'L');
            $this->Cell(60, 7, $patient['menor_acompanante'] ?? 'N/A', 0, 1, 'L');
            $this->Cell(40, 7, utf8_decode("Parentesco:"), 0, 0, 'L');
            $this->Cell(60, 7, $patient['menor_parentesco'] ?? 'N/A', 0, 1, 'L');
            $this->Cell(40, 7, utf8_decode("Teléfono Acompañante:"), 0, 0, 'L');
            $this->Cell(60, 7, $patient['menor_telefono'] ?? 'N/A', 0, 1, 'L');
        }
        $this->Cell(40, 7, utf8_decode("Tipo de Sangre:"), 0, 0, 'L');
        $this->Cell(60, 7, $patient['tipo_sangre'] ?? 'N/A', 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Alertas Médicas:"), 0, 0, 'L');
        $this->MultiCell(150, 7, utf8_decode($patient['alertas_medicas'] ?? 'N/A'), 0, 'L');
        $this->Ln(5);
    }

    function InformeCita($appointment, $medical_report)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode("Información de la Cita"), 0, 1, 'L');
        $this->Ln(5);

        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 7, utf8_decode("Fecha:"), 0, 0, 'L');
        $this->Cell(60, 7, $appointment['fecha_cita'], 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Hora:"), 0, 0, 'L');
        $this->Cell(60, 7, $appointment['hora_cita'], 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Doctor:"), 0, 0, 'L');
        $this->Cell(60, 7, utf8_decode($appointment['nombreD']), 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Motivo de Consulta:"), 0, 0, 'L');
        $this->Cell(60, 7, utf8_decode($appointment['tipo']), 0, 1, 'L');
        $this->Cell(40, 7, utf8_decode("Estado:"), 0, 0, 'L');
        $this->Cell(60, 7, $appointment['estado'] == 'A' ? 'Realizada' : 'Pendiente', 0, 1, 'L');
        $this->Ln(5);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode("Informe Médico"), 0, 1, 'L');
        $this->Ln(5);

        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 7, utf8_decode("Examen Intraoral:"), 0, 0, 'L');
        $this->MultiCell(150, 7, utf8_decode($medical_report['examen_intraoral'] ?? 'N/A'), 0, 'L');
        $this->Ln(2);

        $this->Cell(40, 7, utf8_decode("Examen Extraoral:"), 0, 0, 'L');
        $this->MultiCell(150, 7, utf8_decode($medical_report['examen_extraoral'] ?? 'N/A'), 0, 'L');
        $this->Ln(2);

        $this->Cell(40, 7, utf8_decode("Examen ATM:"), 0, 0, 'L');
        $this->MultiCell(150, 7, utf8_decode($medical_report['examen_atm'] ?? 'N/A'), 0, 'L');
        $this->Ln(2);

        // Agregar imágenes si existen
        if (isset($medical_report['radiografia']) && !empty($medical_report['radiografia']) && file_exists(__DIR__ . '/../uploads/radiografias/' . $medical_report['radiografia'])) {
            $this->Cell(40, 7, utf8_decode("Radiografía:"), 0, 0, 'L');
            $this->Image(__DIR__ . '/../uploads/radiografias/' . $medical_report['radiografia'], 50, $this->GetY(), 100); // Ajusta tamaño y posición
            $this->Ln(80); // Espacio después de la imagen
        }

        if (isset($medical_report['foto_boca']) && !empty($medical_report['foto_boca']) && file_exists(__DIR__ . '/../uploads/fotos_boca/' . $medical_report['foto_boca'])) {
            $this->Cell(40, 7, utf8_decode("Foto de la Boca:"), 0, 0, 'L');
            $this->Image(__DIR__ . '/../uploads/fotos_boca/' . $medical_report['foto_boca'], 50, $this->GetY(), 100); // Ajusta tamaño y posición
            $this->Ln(80); // Espacio después de la imagen
        }

        $this->Cell(40, 7, utf8_decode("Evolución:"), 0, 0, 'L');
        $this->MultiCell(150, 7, utf8_decode($medical_report['evolucion'] ?? 'N/A'), 0, 'L');
        $this->Ln(2);

        $this->Cell(40, 7, utf8_decode("Diagnóstico:"), 0, 0, 'L');
        $this->MultiCell(150, 7, utf8_decode($medical_report['diagnostico'] ?? 'N/A'), 0, 'L');
        $this->Ln(2);

        $this->Cell(40, 7, utf8_decode("Plan de Tratamiento:"), 0, 0, 'L');
        $this->MultiCell(150, 7, utf8_decode($medical_report['plan_tratamiento'] ?? 'N/A'), 0, 'L');
        $this->Ln(2);

        $this->Cell(40, 7, utf8_decode("Costo:"), 0, 0, 'L');
        $this->Cell(60, 7, '$' . ($medical_report['costo'] ?? 'N/A'), 0, 1, 'L');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P', 'A4'); // Orientación vertical, tamaño A4
$pdf->DatosPaciente($patient, $age);
$pdf->InformeCita($appointment, $medical_report);

// Limpiar buffer de salida
if (ob_get_length()) {
    ob_end_clean();
}

// Forzar la descarga del PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Informe_Medico_' . $patient_id . '.pdf"');
$pdf->Output('Informe_Medico_' . $patient_id . '.pdf', 'D');
exit();