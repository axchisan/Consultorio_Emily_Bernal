<?php
session_start();
include_once('../php/conexionDB.php');
include_once('../php/consultas.php');

// Validar sesión y token del doctor
if (!isset($_SESSION['id_doctor']) || !isset($_SESSION['session_token'])) {
    $_SESSION['MensajeTexto'] = "Error acceso al sistema: Sesión no iniciada.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../index.php");
    exit();
}

$vUsuario = $_SESSION['id_doctor'];

// Validar token contra la base de datos para evitar accesos concurrentes
if (!validarToken($link, $vUsuario, 'Doctor', $_SESSION['session_token'])) {
    session_unset();
    session_destroy();
    $_SESSION['MensajeTexto'] = "Tu sesión ha sido cerrada por inicio en otro dispositivo.";
    $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    header("Location: ../index.php");
    exit();
}

$row = consultarDoctor($link, $vUsuario);
$resultadoCitas = MostrarCitasCompletadas($link, $vUsuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../src/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../src/css/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/css/admin.css">
    <link rel="stylesheet" href="../src/css/custom_styles.css">
    <link rel="stylesheet" href="../src/css/lib/fontawesome/css/all.css">
    <link rel="stylesheet" href="../src/js/lib/datatable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../src/js/lib/datatable/css/responsive.dataTables.min.css">
    <title>Historia Clínica - Odontólogo</title>
</head>
<body>
    <aside class="sidebar">
        <div class="toggle">
            <a href="#" class="burger js-menu-toggle" data-toggle="collapse" data-target="#main-navbar">
                <span></span>
            </a>
        </div>
        <div class="side-inner">
            <div class="profile">
                <?php if ($row['sexo'] == 'Masculino') { ?>
                    <img src="../src/img/odontologo.png" class="rounded-circle" width="150">
                <?php } elseif ($row['sexo'] == 'Femenino') { ?>
                    <img src="../src/img/odontologa.png" class="rounded-circle" width="150">
                <?php } ?>
                <h3 class="name"><?php echo utf8_decode($row['nombreD'] . ' ' . $row['apellido']); ?></h3>
                <span class="country">Barbosa Santander</span>
            </div>
            <div class="nav-menu">
                <ul>
                    <li><a href="inicioAdmin.php"><span class="icon-location-arrow mr-3"></span><i class="far fa-calendar-check"></i> Citas</a></li>
                    <li><a href="doctores.php"><span class="icon-location-arrow mr-3"></span><i class="fas fa-user-md"></i> Dentistas</a></li>
                    <li><a href="calendar.php"><span class="icon-pie-chart mr-3"></span><i class="far fa-calendar-alt"></i> Calendario</a></li>
                    <li class="active"><a href="historia_clinica.php"><span class="icon-pie-chart mr-3"></span><i class="far fa-calendar-alt"></i> Historia Clínica</a></li>
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
                                <li class="breadcrumb-item"><a href="./inicioAdmin.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Historia Clínica</li>
                            </ol>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 text-info">
                                        <div class="p-3 mb-2 bg-primary text-white text-center">Historias Clínicas</div>
                                        <table id="example" class="table table-striped nowrap responsive">
                                            <thead>
                                                <tr>
                                                    <th>Nombre completo</th>
                                                    <th>Edad</th>
                                                    <th>Consulta</th>
                                                    <th>Fecha</th>
                                                    <th>Hora</th>
                                                    <th>Estado</th>
                                                    <th>Diagnóstico</th>
                                                    <th>Ver Historia</th>
                                                    <th>Descargar PDF</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = mysqli_fetch_array($resultadoCitas, MYSQLI_ASSOC)) { ?>
                                                    <tr>
                                                        <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                                                        <td><?php echo $row['años'] ?? 'N/A'; ?></td>
                                                        <td><?php echo $row['tipo'] ?? 'N/A'; ?></td>
                                                        <td><?php echo $row['fecha_cita'] ?? 'N/A'; ?></td>
                                                        <td><?php echo $row['hora_cita'] ?? 'N/A'; ?></td>
                                                        <td>Realizada</td>
                                                        <td><?php echo $row['descripcion'] ?? 'N/A'; ?></td>
                                                        <td><a class="btn btn-info" href="ver_historia.php?id_cita=<?php echo $row['id_cita']; ?>&id_paciente=<?php echo $row['id_paciente']; ?>">Ver</a></td>
                                                        <td>
                                                            <form action="descargar_historia.php" method="POST">
                                                                <input type="hidden" name="id_cita" value="<?php echo $row['id_cita']; ?>">
                                                                <input type="hidden" name="id_paciente" value="<?php echo $row['id_paciente']; ?>">
                                                                <button type="submit" class="btn btn-success">Descargar</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../src/js/jquery.js"></script>
    <script src="../src/css/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="../src/js/admin.js"></script>
    <script src="../src/js/lib/datatable/js/jquery-3.5.1.js"></script>
    <script src="../src/js/lib/datatable/js/jquery.dataTables.min.js"></script>
    <script src="../src/js/lib/datatable/js/dataTables.responsive.min.js"></script>
    <script src="../src/js/lib/datatable/datatable.js"></script>
</body>
</html>