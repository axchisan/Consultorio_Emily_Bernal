<?php
// Verificar si la sesión ya está iniciada antes de llamarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../php/conexionDB.php';
require_once __DIR__ . '/../php/consultas.php';

// Ajustar la ruta correcta de fpdf.php
$fpdfPath = __DIR__ . '/../Reportes/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    die("Error: No se encontró el archivo FPDF en '$fpdfPath'. Verifica la ruta.");
}
require_once $fpdfPath;

// Verificar si se ha enviado el ID del paciente
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['id_paciente'])) {
    $idPaciente = $_POST['id_paciente'];
    $_SESSION['id_paciente'] = $idPaciente; // Guardar el ID en sesión

    // Consultar datos del paciente y citas realizadas
    $datosPaciente = consultarPaciente($link, $idPaciente);
    $resultado = CitasRealizadasFPDF($link, $idPaciente);

    if (!$datosPaciente) {
        die("Error: No se pudo obtener la información del paciente.");
    }

    if (!$resultado) {
        die("Error: No se pudieron obtener las consultas realizadas.");
    }

    // Generar el PDF
    class PDF extends FPDF
    {
        function Header()
        {
            $this->Image(__DIR__ . '/../src/img/logo.png', 10, 10, 25);
            $this->SetFont('Arial', 'B', 18);
            $this->Cell(0, 15, 'Historial Clínico', 0, 1, 'C');
            $this->Line(10, 35, 200, 35);
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-20);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Fecha de generación: ' . date("d/m/Y H:i"), 0, 0, 'L');
            $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
        }

        function DatosPaciente($datosPaciente)
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, "Información del Paciente", 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
            
            foreach (["nombre", "apellido", "sexo", "fecha_nacimiento"] as $campo) {
                $this->Cell(40, 7, ucfirst($campo) . ":", 0, 0, 'L');
                $this->Cell(60, 7, utf8_decode($datosPaciente[$campo] ?? 'N/A'), 0, 1, 'L');
            }
            $this->Ln(5);
        }

        function GenerarTabla($resultado)
        {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(30, 144, 255);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(50, 10, 'Consulta', 1, 0, 'C', true);
            $this->Cell(30, 10, 'Fecha', 1, 0, 'C', true);
            $this->Cell(30, 10, 'Hora', 1, 0, 'C', true);
            $this->Cell(50, 10, 'Doctor', 1, 0, 'C', true);
            $this->Cell(40, 10, 'Descripción', 1, 1, 'C', true);

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
    $pdf->DatosPaciente($datosPaciente);
    $pdf->GenerarTabla($resultado);
    
    // Limpiar el buffer solo si está activo
    if (ob_get_length()) {
        ob_end_clean();
    }

    $pdf->Output();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Historial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }
        input, button {
            padding: 10px;
            margin: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h2>Buscar Historial del Paciente</h2>

<form method="post">
    <label for="id_paciente">ID del Paciente:</label>
    <input type="text" id="id_paciente" name="id_paciente" required>
    <button type="submit">Buscar Reporte</button>
</form>

</body>
</html>
