<?php
include_once('../php/conexionDB.php');
include_once('../php/consultas.php');

// Verifica logueo del doctor
if (!isset($_SESSION['id_doctor'])) {
    $_SESSION['MensajeTexto'] = "Error: Acceso al sistema no registrado.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../index.php");
    exit();
}

// Obtener id paciente
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['MensajeTexto'] = "Error: ID de paciente no proporcionado.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: inicioAdmin.php");
    exit();
}

$patient_id = $_GET['id'];
$doctor_id = $_SESSION['id_doctor'];

// Obtener datos del paciente
$patient = consultarPaciente($link, $patient_id);

// Calcular la edad del paciente a partir de su fecha de nacimiento
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
$appointment = mysqli_fetch_assoc($result);

// Manejo formulario para actualizar los datos del paciente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_patient'])) {
    $telefono = mysqli_real_escape_string($link, $_POST['telefono']);
    $eps = mysqli_real_escape_string($link, $_POST['eps']);
    $ocupacion = mysqli_real_escape_string($link, $_POST['ocupacion']);
    $estado_civil = mysqli_real_escape_string($link, $_POST['estado_civil']);
    $cedula = mysqli_real_escape_string($link, $_POST['cedula']);
    $genero = mysqli_real_escape_string($link, $_POST['genero']);
    $emergencia_nombre = mysqli_real_escape_string($link, $_POST['emergencia_nombre']);
    $emergencia_telefono = mysqli_real_escape_string($link, $_POST['emergencia_telefono']);
    $menor_acompanante = isset($_POST['menor_acompanante']) ? mysqli_real_escape_string($link, $_POST['menor_acompanante']) : '';
    $menor_parentesco = isset($_POST['menor_parentesco']) ? mysqli_real_escape_string($link, $_POST['menor_parentesco']) : '';
    $menor_telefono = isset($_POST['menor_telefono']) ? mysqli_real_escape_string($link, $_POST['menor_telefono']) : '';
    $tipo_sangre = mysqli_real_escape_string($link, $_POST['tipo_sangre']);
    $alertas_medicas = mysqli_real_escape_string($link, $_POST['alertas_medicas']);

    // Actualizar los datos en la tabla pacientes
    $update_query = "UPDATE pacientes SET 
                     telefono = '$telefono', 
                     eps = '$eps', 
                     ocupacion = '$ocupacion', 
                     estado_civil = '$estado_civil', 
                     cedula = '$cedula', 
                     sexo = '$genero', 
                     emergencia_nombre = '$emergencia_nombre', 
                     emergencia_telefono = '$emergencia_telefono', 
                     menor_acompanante = '$menor_acompanante', 
                     menor_parentesco = '$menor_parentesco', 
                     menor_telefono = '$menor_telefono', 
                     tipo_sangre = '$tipo_sangre', 
                     alertas_medicas = '$alertas_medicas' 
                     WHERE id_paciente = '$patient_id'";

    if (mysqli_query($link, $update_query)) {
        $_SESSION['MensajeTexto'] = "Datos del paciente actualizados correctamente.";
        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-success text-white";
        header("Location: informe.php?id=$patient_id");
        exit();
    } else {
        $_SESSION['MensajeTexto'] = "Error al actualizar los datos del paciente.";
        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    }
}

// Manejo del formulario para el informe médico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_medical'])) {
    $examen_intraoral = mysqli_real_escape_string($link, $_POST['examen_intraoral']);
    $examen_extraoral = mysqli_real_escape_string($link, $_POST['examen_extraoral']);
    $examen_atm = mysqli_real_escape_string($link, $_POST['examen_atm']);
    $evolucion = mysqli_real_escape_string($link, $_POST['evolucion']);
    $diagnostico = mysqli_real_escape_string($link, $_POST['diagnostico']);
    $plan_tratamiento = mysqli_real_escape_string($link, $_POST['plan_tratamiento']);
    $costo = mysqli_real_escape_string($link, $_POST['costo']);

    // Lógica de archivos fotos paciente
    $radiografia = '';
    $foto_boca = '';
    if (isset($_FILES['radiografia']) && $_FILES['radiografia']['error'] == 0) {
        $radiografia_path = "../uploads/radiografias/";
        $radiografia_name = $patient_id . "_radiografia_" . time() . "." . pathinfo($_FILES['radiografia']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['radiografia']['tmp_name'], $radiografia_path . $radiografia_name);
        $radiografia = $radiografia_name;
    }
    if (isset($_FILES['foto_boca']) && $_FILES['foto_boca']['error'] == 0) {
        $foto_boca_path = "../uploads/fotos_boca/";
        $foto_boca_name = $patient_id . "_boca_" . time() . "." . pathinfo($_FILES['foto_boca']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['foto_boca']['tmp_name'], $foto_boca_path . $foto_boca_name);
        $foto_boca = $foto_boca_name;
    }

    // Insertar o actualizar el informe médico 
    $check_query = "SELECT * FROM informe_medico WHERE id_cita = '{$appointment['id_cita']}'";
    $check_result = mysqli_query($link, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $update_medical_query = "UPDATE informe_medico SET 
                                 examen_intraoral = '$examen_intraoral', 
                                 examen_extraoral = '$examen_extraoral', 
                                 examen_atm = '$examen_atm', 
                                 evolucion = '$evolucion', 
                                 diagnostico = '$diagnostico', 
                                 plan_tratamiento = '$plan_tratamiento', 
                                 costo = '$costo'" . 
                                 ($radiografia ? ", radiografia = '$radiografia'" : "") . 
                                 ($foto_boca ? ", foto_boca = '$foto_boca'" : "") . 
                                 " WHERE id_cita = '{$appointment['id_cita']}'";
        mysqli_query($link, $update_medical_query);
    } else {
        // Insertar nuevo registro
        $insert_medical_query = "INSERT INTO informe_medico (id_cita, id_paciente, examen_intraoral, examen_extraoral, examen_atm, evolucion, diagnostico, plan_tratamiento, costo, radiografia, foto_boca) 
                                 VALUES ('{$appointment['id_cita']}', '$patient_id', '$examen_intraoral', '$examen_extraoral', '$examen_atm', '$evolucion', '$diagnostico', '$plan_tratamiento', '$costo', '$radiografia', '$foto_boca')";
        mysqli_query($link, $insert_medical_query);
    }

    $_SESSION['MensajeTexto'] = "Informe médico actualizado correctamente.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-success text-white";
    header("Location: informe.php?id=$patient_id");
    exit();
}

// Obtener el informe médico actual
$medical_report_query = "SELECT * FROM informe_medico WHERE id_cita = '{$appointment['id_cita']}'";
$medical_report_result = mysqli_query($link, $medical_report_query);
$medical_report = mysqli_num_rows($medical_report_result) > 0 ? mysqli_fetch_assoc($medical_report_result) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Informe del Paciente - Odontólogo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../src/css/lib/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../src/css/lib/fontawesome/css/all.css">
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="../src/css/admin.css">
    <link rel="stylesheet" href="../src/css/informe_paciente.css"> 
</head>
<body>
    <aside class="sidebar">
        <!-- Contenido de la barra lateral -->
        <div class="toggle">
            <a href="#" class="burger js-menu-toggle" data-toggle="collapse" data-target="#main-navbar">
                <span></span>
            </a>
        </div>
        <div class="side-inner">
            <div class="profile">
                <?php
                $doctor = consultarDoctor($link, $doctor_id);
                if ($doctor['sexo'] == 'Masculino') {
                    echo '<img src="../src/img/odontologo.png" class="rounded-circle" width="150">';
                } elseif ($doctor['sexo'] == 'Femenino') {
                    echo '<img src="../src/img/odontologa.png" class="rounded-circle" width="150">';
                }
                ?>
                <h3 class="name"><?php echo utf8_decode($doctor['nombreD'] . ' ' . $doctor['apellido']); ?></h3>
                <span class="country">Barbosa Santander</span>
            </div>
            <div class="nav-menu">
                <ul>
                    <li><a href="inicioAdmin.php"><span class="icon-location-arrow mr-3"></span> <i class="far fa-calendar-check"></i> Citas pendientes</a></li>
                    <li><a href="doctores.php"><span class="icon-location-arrow mr-3"></span><i class="fas fa-user-md"></i> Dentistas</a></li>
                    <li><a href="calendar.php"><span class="icon-pie-chart mr-3"></span> <i class="far fa-calendar-alt"></i> Calendario</a></li>
                    <li><a href="consultarrepor.php"><span class="icon-pie-chart mr-3"></span> <i class="far fa-calendar-alt"></i> Reportes citas</a></li>
                    <li><a href="consultarrepor.php"><span class="icon-pie-chart mr-3"></span> <i class="far fa-calendar-alt"></i> Historia Clinica</a></li>
                    <li><a href="../php/cerrar.php"><span class="icon-sign-out mr-3"></span><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </aside>

    <main class="bg bg-white">
        <div class="site-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="content-box-large">
                            <ol class="breadcrumb bg-white">
                                <li class="breadcrumb-item"><a href="inicioAdmin.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Informe del Paciente</li>
                            </ol>

                            <!-- Mostrar Mensajes -->
                            <?php if (isset($_SESSION['MensajeTexto'])) { ?>
                                <div class="alert <?php echo $_SESSION['MensajeTipo']; ?>" role="alert">
                                    <?php echo $_SESSION['MensajeTexto']; ?>
                                    <button class="delete"><i class="fa fa-times"></i></button>
                                </div>
                                <?php
                                $_SESSION['MensajeTexto'] = null;
                                $_SESSION['MensajeTipo'] = null;
                                ?>
                            <?php } ?>

                            <!-- Información del Paciente -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user"></i> Información del Paciente
                                </div>
                                <div class="card-body patient-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Datos No Editables</h5>
                                            <p><strong>Nombre:</strong> <?php echo $patient['nombre'] . ' ' . $patient['apellido']; ?></p>
                                            <p><strong>Edad:</strong> <?php echo $age; ?> años</p>
                                            <p><strong>Fecha de Nacimiento:</strong> <?php echo $patient['fecha_nacimiento']; ?></p>
                                            <p><strong>Correo Electrónico:</strong> <?php echo $patient['correo_electronico']; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Datos Editables</h5>
                                            <form method="POST" action="">
                                                <div class="form-group">
                                                    <label for="telefono">Teléfono:</label>
                                                    <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $patient['telefono'] ?? ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="eps">EPS:</label>
                                                    <input type="text" class="form-control" id="eps" name="eps" value="<?php echo $patient['eps'] ?? ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="ocupacion">Ocupación:</label>
                                                    <input type="text" class="form-control" id="ocupacion" name="ocupacion" value="<?php echo $patient['ocupacion'] ?? ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="estado_civil">Estado Civil:</label>
                                                    <input type="text" class="form-control" id="estado_civil" name="estado_civil" value="<?php echo $patient['estado_civil'] ?? ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="cedula">Cédula:</label>
                                                    <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo $patient['cedula'] ?? ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="genero">Género:</label>
                                                    <select class="form-control" id="genero" name="genero">
                                                        <option value="Masculino" <?php echo (isset($patient['sexo']) && $patient['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                                                        <option value="Femenino" <?php echo (isset($patient['sexo']) && $patient['sexo'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="emergencia_nombre">En caso de emergencia, llamar a:</label>
                                                    <input type="text" class="form-control" id="emergencia_nombre" name="emergencia_nombre" value="<?php echo $patient['emergencia_nombre'] ?? ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="emergencia_telefono">Teléfono de Emergencia:</label>
                                                    <input type="text" class="form-control" id="emergencia_telefono" name="emergencia_telefono" value="<?php echo $patient['emergencia_telefono'] ?? ''; ?>">
                                                </div>
                                                <?php if ($age !== 'N/A' && $age < 18) { ?>
                                                    <div class="form-group">
                                                        <label for="menor_acompanante">Nombre del Acompañante (Menor de Edad):</label>
                                                        <input type="text" class="form-control" id="menor_acompanante" name="menor_acompanante" value="<?php echo $patient['menor_acompanante'] ?? ''; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="menor_parentesco">Parentesco:</label>
                                                        <input type="text" class="form-control" id="menor_parentesco" name="menor_parentesco" value="<?php echo $patient['menor_parentesco'] ?? ''; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="menor_telefono">Teléfono del Acompañante:</label>
                                                        <input type="text" class="form-control" id="menor_telefono" name="menor_telefono" value="<?php echo $patient['menor_telefono'] ?? ''; ?>">
                                                    </div>
                                                <?php } ?>
                                                <div class="form-group">
                                                    <label for="tipo_sangre">Tipo de Sangre:</label>
                                                    <input type="text" class="form-control" id="tipo_sangre" name="tipo_sangre" value="<?php echo $patient['tipo_sangre'] ?? ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="alertas_medicas">Alertas Médicas:</label>
                                                    <textarea class="form-control" id="alertas_medicas" name="alertas_medicas"><?php echo $patient['alertas_medicas'] ?? ''; ?></textarea>
                                                </div>
                                                <button type="submit" name="update_patient" class="btn btn-primary">Actualizar Datos</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de la Cita -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="far fa-calendar-check"></i> Información de la Cita
                                </div>
                                <div class="card-body patient-info">
                                    <p><strong>Fecha:</strong> <?php echo $appointment['fecha_cita']; ?></p>
                                    <p><strong>Hora:</strong> <?php echo $appointment['hora_cita']; ?></p>
                                    <p><strong>Doctor:</strong> <?php echo $appointment['nombreD']; ?></p>
                                    <p><strong>Motivo de Consulta:</strong> <?php echo $appointment['tipo']; ?></p>
                                    <p><strong>Estado:</strong> <?php echo $appointment['estado'] == 'A' ? 'Realizada' : 'Pendiente'; ?></p>
                                </div>
                            </div>

                            <!-- Informe Médico -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-notes-medical"></i> Informe Médico
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="examen_intraoral">Examen Clínico Intraoral:</label>
                                            <textarea class="form-control" id="examen_intraoral" name="examen_intraoral"><?php echo $medical_report['examen_intraoral'] ?? ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="examen_extraoral">Examen Clínico Extraoral:</label>
                                            <textarea class="form-control" id="examen_extraoral" name="examen_extraoral"><?php echo $medical_report['examen_extraoral'] ?? ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="examen_atm">Examen ATM:</label>
                                            <textarea class="form-control" id="examen_atm" name="examen_atm"><?php echo $medical_report['examen_atm'] ?? ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="radiografia">Radiografía:</label>
                                            <input type="file" class="form-control-file" id="radiografia" name="radiografia">
                                            <?php if (isset($medical_report['radiografia']) && $medical_report['radiografia']) { ?>
                                                <img src="../uploads/radiografias/<?php echo $medical_report['radiografia']; ?>" class="uploaded-image" alt="Radiografía">
                                            <?php } ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="foto_boca">Foto de la Boca:</label>
                                            <input type="file" class="form-control-file" id="foto_boca" name="foto_boca">
                                            <?php if (isset($medical_report['foto_boca']) && $medical_report['foto_boca']) { ?>
                                                <img src="../uploads/fotos_boca/<?php echo $medical_report['foto_boca']; ?>" class="uploaded-image" alt="Foto de la Boca">
                                            <?php } ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="evolucion">Evolución:</label>
                                            <textarea class="form-control" id="evolucion" name="evolucion"><?php echo $medical_report['evolucion'] ?? ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="diagnostico">Diagnóstico:</label>
                                            <textarea class="form-control" id="diagnostico" name="diagnostico"><?php echo $medical_report['diagnostico'] ?? ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="plan_tratamiento">Plan de Tratamiento:</label>
                                            <textarea class="form-control" id="plan_tratamiento" name="plan_tratamiento"><?php echo $medical_report['plan_tratamiento'] ?? ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="costo">Costo:</label>
                                            <input type="number" class="form-control" id="costo" name="costo" value="<?php echo $medical_report['costo'] ?? ''; ?>">
                                        </div>
                                        <button type="submit" name="update_medical" class="btn btn-primary">Guardar Informe Médico</button>
                                    </form>
                                    <form action="generate_informe_pdf.php" method="post">
                                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                                        <button type="submit" class="btn btn-success">Guardar Informe en PDF</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../src/js/jquery.js"></script>
    <script src="../src/css/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="../src/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            (document.querySelectorAll('.alert .delete') || []).forEach(($delete) => {
                const $notification = $delete.parentNode;
                $delete.addEventListener('click', () => {
                    $notification.parentNode.removeChild($notification);
                });
            });
        });
    </script>
</body>
</html>