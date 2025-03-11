<?php
function validarLogin($link, $user, $pass, $tipo)
{
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($tipo == "Paciente") {
        // Consulta preparada para evitar inyección SQL
        $query = "SELECT * FROM pacientes WHERE correo_electronico = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) == 1) {
            $row = mysqli_fetch_assoc($resultado);
            // Verificar la contraseña hasheada
            if (password_verify($pass, $row['clave'])) {
                // Generar un token único para esta sesión
                $sessionToken = bin2hex(random_bytes(32)); // Token seguro de 64 caracteres

                // Actualizar la tabla pacientes con el nuevo session_token
                $updateQuery = "UPDATE pacientes SET session_token = ? WHERE id_paciente = ?";
                $stmtUpdate = mysqli_prepare($link, $updateQuery);
                mysqli_stmt_bind_param($stmtUpdate, "si", $sessionToken, $row['id_paciente']);
                mysqli_stmt_execute($stmtUpdate);
                mysqli_stmt_close($stmtUpdate);

                // Guardar datos en la sesión
                $_SESSION['id_paciente'] = $row['id_paciente'];
                $_SESSION['session_token'] = $sessionToken; // Almacenar token en la sesión
                $_SESSION['MensajeTexto'] = null;
                $_SESSION['MensajeTipo'] = null;
                header("Location: principal.php");
                exit(); // Asegurar que el script termine tras redirigir
            } else {
                $_SESSION['MensajeTexto'] = "Error validando datos del paciente: Contraseña incorrecta";
                $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
            }
        } else {
            $_SESSION['MensajeTexto'] = "Error validando datos del paciente: Usuario no encontrado";
            $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
        }

        mysqli_stmt_close($stmt);
    } else {
        // Consulta preparada para doctores
        $query = "SELECT * FROM doctor WHERE correo_eletronico = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) == 1) {
            $row = mysqli_fetch_assoc($resultado);
            // Verificar la contraseña hasheada
            if (password_verify($pass, $row['clave'])) {
                // Generar un token único para esta sesión
                $sessionToken = bin2hex(random_bytes(32)); // Token seguro de 64 caracteres

                // Actualizar la tabla doctor con el nuevo session_token (si tiene la columna)
                $updateQuery = "UPDATE doctor SET session_token = ? WHERE id_doctor = ?";
                $stmtUpdate = mysqli_prepare($link, $updateQuery);
                mysqli_stmt_bind_param($stmtUpdate, "si", $sessionToken, $row['id_doctor']);
                mysqli_stmt_execute($stmtUpdate);
                mysqli_stmt_close($stmtUpdate);

                // Guardar datos en la sesión
                $_SESSION['id_doctor'] = $row['id_doctor'];
                $_SESSION['session_token'] = $sessionToken; // Almacenar token en la sesión
                $_SESSION['MensajeTexto'] = null;
                $_SESSION['MensajeTipo'] = null;
                header("Location: Admin/inicioAdmin.php");
                exit(); // Asegurar que el script termine tras redirigir
            } else {
                $_SESSION['MensajeTexto'] = "Error validando datos del administrador: Contraseña incorrecta";
                $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
            }
        } else {
            $_SESSION['MensajeTexto'] = "Error validando datos del administrador: Usuario no encontrado";
            $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
        }

        mysqli_stmt_close($stmt);
    }
}

function consultarPaciente($link, $id)
{
    $query = "SELECT * FROM `pacientes` WHERE `id_paciente` = '$id'";
    $resultado = mysqli_query($link, $query);

    if (mysqli_num_rows($resultado) == 1) {
        $row = $resultado->fetch_assoc();
        return $row;
    } else {
        $_SESSION['MensajeTexto'] = "Error validando datos de usuario";
        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
        header("Location: ./index.php");
    }
}

function consultarDoctor($link, $id)
{
    $query = "SELECT * FROM `doctor` WHERE `id_doctor` = '$id'";
    $resultado = mysqli_query($link, $query);

    if (mysqli_num_rows($resultado) == 1) {
        $row = $resultado->fetch_assoc();
        return $row;
    } else {
        $_SESSION['MensajeTexto'] = "Error validando datos de usuario";
        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
        header("Location: ../index.php");
    }
}

// Nueva función para validar el token de sesión
function validarToken($link, $idUsuario, $tipoUsuario, $sessionToken)
{
    $tabla = ($tipoUsuario == 'Paciente') ? 'pacientes' : 'doctor';
    $campoId = ($tipoUsuario == 'Paciente') ? 'id_paciente' : 'id_doctor';

    $query = "SELECT session_token FROM $tabla WHERE $campoId = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) == 1) {
        $row = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);
        return $row['session_token'] === $sessionToken;
    }
    mysqli_stmt_close($stmt);
    return false;
}

function MostrarConsultas($link)
{
    $query = "SELECT * FROM `consultas` ";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}
function MostrarEspecialidad($link)
{
    $query = "SELECT * FROM `especialidad` ";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}

function MostrarDentistas($link)
{
    $query = "SELECT * FROM `doctor` ";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}
function MostrarPacientes($link)
{
    $query = "SELECT * FROM `pacientes` ";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}
function MostrarCitas1($link)
{
    $query = "SELECT FROM `citas`, `pacientes` ";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}
function MostrarCitas($link, $id)
{
    $query = "SELECT
c.id_cita,
p.id_paciente,
p.nombre,
p.apellido,
d.nombreD,
p.fecha_nacimiento,
c.fecha_cita,
c.hora_cita,
con.tipo,
c.estado,
year(curdate()), year(p.fecha_nacimiento) ,year(CURDATE())-year(p.fecha_nacimiento) as años,
pd.descripcion

FROM
`citas` as c
LEFT JOIN `pacientes` as p ON p.id_paciente = c.id_paciente
LEFT JOIN `doctor` as d ON d.id_doctor = c.id_doctor
LEFT JOIN `consultas` as con ON con.id_consultas = c.id_consultas
LEFT JOIN `paciente_diagnostico` as pd ON pd.id_cita = c.id_cita
WHERE d.id_doctor = $id
;";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}


function ConsultarCitas($link, $id)
{
    $query = "SELECT * FROM `citas` WHERE `id_cita` = '$id'";
    $resultado = mysqli_query($link, $query);

    if (mysqli_num_rows($resultado) == 1) {
        # code...
        $row = mysqli_fetch_array($resultado);
        return $row;
    } else {
        # code...
        $_SESSION['MensajeTexto'] = "Error consultando datos";
        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    }
}


function CitasPendientesFPDF($link, $id)
{
    $query = "SELECT
c.id_cita,
c.estado,
p.nombre,
p.apellido,
d.nombreD,
p.fecha_nacimiento,
c.fecha_cita,
c.hora_cita,
con.tipo,
c.estado,
year(curdate()), year(p.fecha_nacimiento) ,year(CURDATE())-year(p.fecha_nacimiento) as años,
pd.descripcion

FROM
`citas` as c
LEFT JOIN `pacientes` as p ON p.id_paciente = c.id_paciente
LEFT JOIN `doctor` as d ON d.id_doctor = c.id_doctor
LEFT JOIN `consultas` as con ON con.id_consultas = c.id_consultas
LEFT JOIN `paciente_diagnostico` as pd ON pd.id_cita = c.id_cita
WHERE c.estado = 'I' and p.id_paciente = $id;
;
;";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}


function CitasRealizadasFPDF($link, $id)
{
    $query = "SELECT
c.id_cita,
c.estado,
p.nombre,
p.apellido,
d.nombreD,
p.fecha_nacimiento,
c.fecha_cita,
c.hora_cita,
con.tipo,
c.estado,
year(curdate()), year(p.fecha_nacimiento) ,year(CURDATE())-year(p.fecha_nacimiento) as años,
pd.descripcion,
pd.medicina

FROM
`citas` as c
LEFT JOIN `pacientes` as p ON p.id_paciente = c.id_paciente
LEFT JOIN `doctor` as d ON d.id_doctor = c.id_doctor
LEFT JOIN `consultas` as con ON con.id_consultas = c.id_consultas
LEFT JOIN `paciente_diagnostico` as pd ON pd.id_cita = c.id_cita
WHERE c.estado = 'A' and p.id_paciente = $id;
;
;";
    $resultado = mysqli_query($link, $query);
    return $resultado;
}
