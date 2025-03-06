<?php
ob_start(); // Iniciar buffer de salida

// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'fpdf/fpdf.php';
require_once '../php/conexionDB.php';
require_once '../php/consultas.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_paciente'])) {
    $_SESSION['MensajeTexto'] = "Error: Acceso al sistema no registrado.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../index.php");
    exit();
}

// Obtener datos del usuario
$vUsuario = $_SESSION['id_paciente'] ?? null;
$Usuario = $_SESSION['nombre'] ?? 'Usuario Desconocido';

if (!$vUsuario) {
    die("Error: No se pudo obtener el ID del paciente.");
}

// Consultar datos del paciente
$row1 = consultarPaciente($link, $vUsuario) ?? [];
$resultado = CitasRealizadasFPDF($link, $vUsuario);

if (!$row1) {
    die("Error: No se pudo obtener la información del paciente.");
}

if (!$resultado) {
    die("Error: No se pudieron obtener las consultas realizadas.");
}

// Clase para generar el PDF
class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image('../src/img/logo.png', 10, 10, 25);
        
        // Título del reporte
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 15, 'Historial Clinico', 0, 1, 'C');

        // Línea separadora
        $this->SetDrawColor(100, 100, 100);
        $this->Line(10, 35, 200, 35);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Fecha de generacion: ' . date("d/m/Y H:i"), 0, 0, 'L');
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function DatosPaciente($row1)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "Informacion del Paciente", 0, 1, 'L');

        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 7, "Nombre:", 0, 0, 'L');
        $this->Cell(60, 7, utf8_decode($row1['nombre'] ?? 'N/A'), 0, 1, 'L');

        $this->Cell(40, 7, "Apellido:", 0, 0, 'L');
        $this->Cell(60, 7, utf8_decode($row1['apellido'] ?? 'N/A'), 0, 1, 'L');

        $this->Cell(40, 7, "Sexo:", 0, 0, 'L');
        $this->Cell(60, 7, utf8_decode($row1['sexo'] ?? 'N/A'), 0, 1, 'L');

        $this->Cell(40, 7, "Fecha de nacimiento:", 0, 0, 'L');
        $this->Cell(60, 7, utf8_decode($row1['fecha_nacimiento'] ?? 'N/A'), 0, 1, 'L');

        $this->Ln(5);
    }

    function GenerarTabla($resultado)
    {
        // Encabezado de la tabla
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(30, 144, 255); // Azul
        $this->SetTextColor(255, 255, 255);

        $this->Cell(50, 10, 'Consulta', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Fecha', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Hora', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Doctor', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Descripcion', 1, 1, 'C', true);

        // Datos de la tabla
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);

        while ($row = $resultado->fetch_assoc()) {
            $this->Cell(50, 8, utf8_decode($row['tipo'] ?? 'N/A'), 1, 0, 'C');
            $this->Cell(30, 8, $row['fecha_cita'] ?? 'N/A', 1, 0, 'C');
            $this->Cell(30, 8, $row['hora_cita'] ?? 'N/A', 1, 0, 'C');
            $this->Cell(50, 8, utf8_decode($row['nombreD'] ?? 'N/A'), 1, 0, 'C');
            $this->Cell(40, 8, utf8_decode($row['descripcion'] ?? 'N/A'), 1, 1, 'C');
        }
    }
}

// Generar PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Agregar datos del paciente
$pdf->DatosPaciente($row1);

// Generar tabla con citas realizadas
$pdf->GenerarTabla($resultado);

// Limpiar buffer antes de generar PDF
ob_end_clean();
$pdf->Output();
?>
