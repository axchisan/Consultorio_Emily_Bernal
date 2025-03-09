<?php
session_start();
include_once('../php/conexionDB.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_doctor'])) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

// Verificar si se recibieron los datos necesarios
if (!isset($_POST['type']) || !isset($_POST['file_name']) || !isset($_POST['id_cita'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$type = $_POST['type']; // 'radiografia' o 'foto_boca'
$file_name = $_POST['file_name'];
$id_cita = mysqli_real_escape_string($link, $_POST['id_cita']);

// Validar el tipo de imagen
if (!in_array($type, ['radiografia', 'foto_boca'])) {
    echo json_encode(['success' => false, 'message' => 'Tipo de imagen no válido']);
    exit();
}

// Determinar la ruta del archivo
$path = $type === 'radiografia' ? '../uploads/radiografias/' : '../uploads/fotos_boca/';
$file_path = $path . $file_name;

// Verificar si el archivo existe y eliminarlo
if (file_exists($file_path)) {
    if (!unlink($file_path)) {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el archivo']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Archivo no encontrado']);
    exit();
}


$update_query = "UPDATE informe_medico SET $type = NULL WHERE id_cita = '$id_cita'";
if (mysqli_query($link, $update_query)) {
    echo json_encode(['success' => true, 'message' => 'Imagen eliminada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la base de datos']);
}

exit();