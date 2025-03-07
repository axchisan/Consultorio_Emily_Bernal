<?php
include_once('./php/conexionDB.php');
include_once('./php/consultas.php');


if (isset($_SESSION['id_paciente'])) {
     $vUsuario = $_SESSION['id_paciente'];
     $row = consultarPaciente($link, $vUsuario);
} else {
     header("Location: ./index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <h5 class="mb-0"> Nombre </h5>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <?php echo $row['nombre'] ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h5 class="mb-0"> Apellido</h5>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <?php echo $row['apellido']; ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h5 class="mb-0"> Sexo</h5>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <?php echo $row['sexo']; ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h5 class="mb-0">Correo electrónico</h5>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <?php echo $row['correo_electronico']; ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h5 class="mb-0">Télefono</h5>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <?php echo $row['telefono']; ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h5 class="mb-0">Fecha de nacimiento</h5>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <?php echo $row['fecha_nacimiento']; ?>
                    </div>
                </div>
                <hr>
            </div>
            <br>
        </div>
        <br>
    </div>
</body>

</html>