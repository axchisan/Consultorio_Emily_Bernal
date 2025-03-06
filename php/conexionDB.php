<?php
include_once('configuracion.php');

// Verificar si la sesión ya está iniciada antes de llamarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Crear objeto de conexión a la base de datos
$link = new mysqli(host, user, password, database);

// Verificar si hay un error en la conexión
if ($link->connect_errno) {
    $_SESSION['MensajeTexto'] = "El sistema está en mantenimiento, intente más tarde.";
    $_SESSION['MensajeTipo'] = "bg-warning text-dark";
    
    // Mostrar error detallado en desarrollo (quitar en producción)
}

// Establecer el conjunto de caracteres a UTF-8
$link->set_charset("utf8");
?>
