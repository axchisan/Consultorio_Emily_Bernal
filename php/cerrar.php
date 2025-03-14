<?php
session_start();
include_once('./conexionDB.php');

// Limpiar el token en la base de datos si es un paciente
if (isset($_SESSION['id_paciente'])) {
    $idUsuario = $_SESSION['id_paciente'];
    $updateQuery = "UPDATE pacientes SET session_token = NULL WHERE id_paciente = ?";
    $stmt = mysqli_prepare($link, $updateQuery);
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


session_unset();
session_destroy();


header("Location: ../index.php");
exit();
?>