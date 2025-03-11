<?php
ob_start();
session_start();

require_once './conexionDB.php';

header('Content-Type: application/json');

// Verificar conexión a la base de datos
if (!$link) {
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    ob_end_flush();
    exit;
}

// Recibir y sanitizar datos del frontend
$data = json_decode(file_get_contents('php://input'), true);
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
$name = filter_var($data['name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

if (empty($email)) {
    echo json_encode(['error' => 'Email no proporcionado']);
    ob_end_flush();
    exit;
}

// Consultar si el usuario existe en la base de datos
$query = "SELECT * FROM pacientes WHERE correo_electronico = ?";
$stmt = mysqli_prepare($link, $query);
if (!$stmt) {
    echo json_encode(['error' => 'Error preparando consulta: ' . mysqli_error($link)]);
    ob_end_flush();
    exit;
}
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$response = [];

if (mysqli_num_rows($result) > 0) {
    // Usuario existe, iniciar sesión
    $user = mysqli_fetch_assoc($result);
    
    // Generar un token único para esta sesión
    $sessionToken = bin2hex(random_bytes(32)); // Token seguro de 64 caracteres

    // Actualizar la tabla pacientes con el nuevo session_token
    $updateQuery = "UPDATE pacientes SET session_token = ? WHERE id_paciente = ?";
    $stmtUpdate = mysqli_prepare($link, $updateQuery);
    if (!$stmtUpdate) {
        echo json_encode(['error' => 'Error preparando actualización de token: ' . mysqli_error($link)]);
        ob_end_flush();
        exit;
    }
    mysqli_stmt_bind_param($stmtUpdate, "si", $sessionToken, $user['id_paciente']);
    mysqli_stmt_execute($stmtUpdate);
    mysqli_stmt_close($stmtUpdate);

    // Guardar datos en la sesión
    $_SESSION['id_paciente'] = $user['id_paciente'];
    $_SESSION['session_token'] = $sessionToken; // Almacenar token en la sesión
    $response = ['redirect' => './principal.php'];
} else {
    // Usuario no existe, redirigir a registro
    session_regenerate_id(true); // Regenerar ID de sesión para asegurar consistencia
    $_SESSION['google_email'] = $email;
    $_SESSION['google_name'] = $name;
    $response = ['redirect' => './registro.php'];
}

mysqli_stmt_close($stmt);
mysqli_close($link);

// Forzar escritura de la sesión antes de la respuesta
session_write_close();

echo json_encode($response);
ob_end_flush();
exit;