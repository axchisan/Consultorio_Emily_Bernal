<?php
include_once('configuracion.php');

// Verificar si la sesión ya está iniciada antes de llamarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Crear objeto de conexión a la base de datos
$link = new mysqli(host, user, password, database, port);

// Configurar SSL (necesario para Aiven)
$link->ssl_set(null, null, null, null, null);

// Verificar si hay un error en la conexión
if ($link->connect_errno) {
    $_SESSION['MensajeTexto'] = "El sistema está en mantenimiento, intente más tarde.";
    $_SESSION['MensajeTipo'] = "bg-warning text-dark";
    die("Error de conexión: " . $link->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8
$link->set_charset("utf8mb4"); // Cambiamos a utf8mb4 para que coincida con Aiven
?>