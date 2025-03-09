<?php
include_once('./php/conexionDB.php');
include_once('./php/consultas.php');

//si esta logueado manda al principal
if (isset($_SESSION['id_paciente'])) {
    $vUsuario = $_SESSION['id_paciente'];
    $row = consultarPaciente($link, $vUsuario);
    header("Location: ./principal.php");
} 

// Limpiar mensajes de sesión si no hay intento de login
if (!isset($_POST['ingresar'])) {
    $_SESSION['MensajeTexto'] = null;
    $_SESSION['MensajeTipo'] = null;
}

// Este punto luego de presionar el botón de login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ingresar'])) {
    $vUsuario = trim(htmlspecialchars($_POST['username'] ?? ''));
    $vClave = trim(htmlspecialchars($_POST['password'] ?? ''));
    $vTipo = trim(htmlspecialchars($_POST['tipo'] ?? ''));

    // Validar que los campos no estén vacíos antes de procesar
    if (empty($vUsuario) || empty($vClave) || empty($vTipo)) {
        $_SESSION['MensajeTexto'] = "Por favor, complete todos los campos.";
        $_SESSION['MensajeTipo'] = "p-3 mb-2 bg-danger text-white";
    } else {
        //echo "<br>  Hola " . $vUsuario . "-" . $vClave;
        validarLogin($link, $vUsuario, $vClave, $vTipo);
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>ODONTOLOGIA DR EMILY BERNAL</title>
    <!-- ICONO -->
    <link rel="icon" href="./src/img/logo.png" type="image/png" />
    <!-- Styles -->
    <link rel="stylesheet" href="src/css/login.css" />
    <!-- Bootstrap -->
    <link href="src/css/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="src/css/lib/fontawesome/css/all.css">
</head>

<body>
    <div class="container login-container">
        <div class="row">
            <div class="col-md-6 ads">
                <h1><span id="fl">EMILY</span><span id="fl"> BERNAL</span></h1>
            </div>
            <div class="col-md-6 login-form">
                <div class="profile-img">
                    <img src="src/img/logo.png" alt="profile_img" height="120px" width="120px;">
                </div>
                <h3>Iniciar sesión</h3>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="form-group">
                        <label for="username" class="font-weight-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" name="username" id="username" placeholder="Correo electrónico" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password" class="font-weight-bold">Contraseña </label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" required value="">
                        <button type="button" class="toggle-password" data-target="password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="role-selection">
                        <label class="role-option" for="Paciente">
                            <input type="radio" class="role-radio" name="tipo" id="Paciente" value="Paciente" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] == 'Paciente') ? 'checked' : ''; ?>>
                            <i class="fas fa-user"></i> Paciente
                        </label>

                        <label class="role-option" for="Doctor">
                            <input type="radio" class="role-radio" name="tipo" id="Doctor" value="Doctor" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] == 'Doctor') ? 'checked' : ''; ?>>
                            <i class="fas fa-user-md"></i> Doctor
                        </label>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="ingresar" value="ingresar" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                        </button>
                    </div>

                    <!-- botones inicio con facebook y google -->
                    <div class="form-group social-login">
                        <button type="button" class="btn btn-google" id="google-login">
                            <i class="fab fa-google"></i>
                        </button>
                        <button type="button" class="btn btn-facebook" id="facebook-login">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                    </div>
                    <div class="form-group">
                        <a href="registro.php"><i class="fas fa-sign-in-alt"></i> Registrarse
                        </a>
                    </div>

                    <?php if (isset($_SESSION['MensajeTexto'])) { ?>
                        <div class="card">
                            <div class="notification <?php echo $_SESSION['MensajeTipo'] ?>">
                                <?php echo $_SESSION['MensajeTexto'] ?>
                                <button class="delete"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Importaciones de Supabase para autenticar con Google -->
    <script type="module" src="./src/js/auth.js"></script>

    <!-- Configurar el botón de Google -->
    <script type="module">
        import {
            loginWithGoogle
        } from "./src/js/auth.js";
        document.getElementById("google-login").addEventListener("click", loginWithGoogle);
    </script>

    <!-- lógica de opción paciente-doctor -->
    <script>
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector("input").checked = true;
            });
        });
    </script>

    <script src="src/js/tooglePassword.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
                const $notification = $delete.parentNode;
                $delete.addEventListener('click', () => {
                    $notification.parentNode.removeChild($notification);
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
                const $notification = $delete.parentNode;

                $delete.addEventListener('click', () => {
                    $notification.parentNode.removeChild($notification);
                });
            });
        });
    </script>
</body>

</html>