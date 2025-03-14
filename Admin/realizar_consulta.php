<?php
session_start();

// Verificar si se recibió el ID y cargar datos necesarios
if (!empty($_GET['id'])) {
    include_once('../php/conexionDB.php');
    include_once('../php/consultas.php');
    $id = $_GET['id'];
    $row = ConsultarCitas($link, $id);
    $vUsuario = $_SESSION['id_doctor'];
    $row1 = consultarDoctor($link, $vUsuario);
} else {
    // No se man eja el caso de ID vacío..
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../src/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../src/css/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/css/admin.css">
    <link rel="stylesheet" href="../src/realizar_consulta.css">
    <link rel="stylesheet" href="../src/css/lib/fontawesome/css/all.css">
    <link rel="stylesheet" href="../src/css/custom_styles.css">
    <link rel="stylesheet" href="../src/js/lib/datatable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../src/js/lib/datatable/css/responsive.dataTables.min.css">
    <title>Odontología Dra. Emily Bernal</title>
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
                <?php if ($row1['sexo'] == 'Masculino') { ?>
                    <img src="../src/img/odontologo.png" class="rounded-circle" width="150">
                <?php } elseif ($row1['sexo'] == 'Femenino') { ?>
                    <img src="../src/img/odontologa.png" class="rounded-circle" width="150">
                <?php } ?>
                <h3 class="name"><?php echo utf8_decode($row['nombreD'] . ' ' . $row['apellido']); ?></h3>
                <span class="country">Barbosa Santander</span>
            </div>
            <div class="nav-menu">
                <ul>
                    <li><a href="inicioAdmin.php"><span class="icon-location-arrow mr-3"></span><i class="far fa-calendar-check"></i> Citas pendientes</a></li>
                    <li><a href="doctores.php"><span class="icon-location-arrow mr-3"></span><i class="fas fa-user-md"></i> Dentistas</a></li>
                    <li><a href="calendar.php"><span class="icon-pie-chart mr-3"></span><i class="far fa-calendar-alt"></i> Calendario</a></li>
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
                                <li class="breadcrumb-item active">Inicio</li>
                            </ol>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="container">
                                        <div class="p-3 mb-2 bg-info text-white text-center">Realizar diagnóstico sobre la cita</div>
                                        <form action="../crud/realizar_consultasUPDATE.php?accion=UDT" method="POST" enctype="multipart/form-data" autocomplete="off" class="form-horizontal">
                                            <input type="hidden" name="id" id="id" value="<?php echo $row['id_cita']; ?>">

                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Diagnóstico</h5>
                                                    <textarea class="form-control" name="Diagnostico" placeholder="Escribe el diagnóstico aquí..." rows="5" required></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row" style="margin-top: 5%;">
                                                    <div class="col-md-4">
                                                        <label for="descripcion">Descripción</label>
                                                        <textarea class="form-control" name="Descripción" placeholder="Escribe la descripción aquí..." rows="5" required></textarea>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="medicina">Medicina</label>
                                                        <textarea class="form-control" name="Medicina" placeholder="Escribe la medicina opcional aquí..." rows="5"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-4" style="margin-top: 5%;">
                                                    <button class="btn btn-success btn-lg" type="submit" name="guardar" value="Guardar">
                                                        <i class="far fa-save"></i> Guardar
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2 col-md-offset-5">
                                                        <a href="./inicioAdmin.php"><i class="fas fa-history"></i> Atrás</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
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
    <script src="../src/js/admin.js"></script>
</body>
</html>