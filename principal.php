<?php
include_once('./php/conexionDB.php');
include_once('./php/consultas.php');
$resultado = MostrarConsultas($link); //mostrar las consultas
$resultadoDentistas = MostrarDentistas($link); //mostrar dentistas

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

     <title>DOCTORA EMILY BERNAL</title>
     <!-- ICONO -->
     <link rel="icon" href="./src/img/logo.png" type="image/png" />

     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=Edge">
     <meta name="description" content="">
     <meta name="keywords" content="">
     <meta name="author" content="Tooplate">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

     <link rel="stylesheet" href="src/css/bootstrap.min.css">
     <link rel="stylesheet" href="src/css/font-awesome.min.css">
     <link rel="stylesheet" href="src/css/animate.css">
     <link rel="stylesheet" href="src/css/owl.carousel.css">
     <link rel="stylesheet" href="src/css/owl.theme.default.min.css">
     <!-- MAIN CSS -->
     <link rel="stylesheet" href="src/css/tooplate-style.css">
     <!-- Datepicker libreria jqueryui para calendario -->
     <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <!-- importacion css indefinido <link rel="stylesheet" href="/resources/demos/style.css"> -->
     <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
     <script src="./src/js/Datepicker.js">
     </script>
</head>

<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">

     <!-- PRE LOADER -->
     <section class="preloader">
          <div class="spinner">

               <span class="spinner-rotate"></span>

          </div>
     </section>


     <!-- HEADER -->
     <header>
          <div class="container">
               <div class="row">
                    <div class="col-md-4 col-sm-10">
                         <p><?php if ($row['sexo'] == 'Masculino') {
                                   echo "Bienvenido" . ' ' . $row['nombre'] . ' ' . $row['apellido'];
                              } else {
                                   echo "Bienvenida" . ' ' . $row['nombre'] . ' ' . $row['apellido'];
                              } ?> </p>
                    </div>

                    <div class="col-md-8 col-sm-10 ">
                         <span class="phone-icon"><i class="fa fa-phone"></i>310-5547320</span>
                         <span class="date-icon"><i class="fa fa-calendar-plus-o"></i> 8:00 AM - 6:00 PM
                              (Lunes-Viernes)</span>
                         <span class="email-icon"><i class="fa fa-envelope-o"></i> <a href="#">emilybernal@hotmail.com</a></span>

                         <!-- boton logout-->
                         <span><i class="fa fa-sign-out"></i><a href="./php/cerrar.php">Cerrar Sesíon </a></span>
                    </div>

               </div>

          </div>
     </header>


     <!-- MENU -->
     <section class="navbar navbar-default navbar-static-top" role="navigation">
          <div class="container">

               <div class="navbar-header">
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                    </button>

                    <!-- lOGO TEXT HERE -->
                    <a href=" principal.php" class="navbar-brand"><img src="src/img/logo.png" width="20px" height="20px" alt="Logo"></a>
                    <a href=" principal.php" class="navbar-brand">EMILY BERNAL </a>

               </div>

               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                         <li><a href="#top" class="smoothScroll">Inicio</a></li>
                         <li><a href="#about" class="smoothScroll">Nosotros</a></li>
                         <li><a href="#team" class="smoothScroll">Dentistas</a></li>
                         <li><a href="#perfil" class="smoothScroll">Perfil</a></li>
                         <li><a href="#google-map" class="smoothScroll">Conctato</a></li>
                         <li class="appointment-btn"><a href="#appointment">Realizar una Cita</a></li>

                    </ul>
               </div>

          </div>
     </section>
     <!-- Mensaje de alerta -->


     <div class="row">
          <div class="col-md-3 col-md-offset-5">

               <?php if (isset($_SESSION['MensajeTexto'])) { ?>
                    <div class="alert <?php echo $_SESSION['MensajeTipo'] ?>" role="alert">
                         <?php echo $_SESSION['MensajeTexto'] ?>
                         <button class="delete"><i class="fa fa-times"></i></button>
                    </div>

               <?php $_SESSION['MensajeTexto'] = null;
                    $_SESSION['MensajeTipo'] = null;
               }
               ?>

          </div>
     </div>



     <!-- HOME -->
     <section id="home" class="slider" data-stellar-background-ratio="0.5">
          <div class="container">
               <div class="row">

                    <div class="owl-carousel owl-theme">
                         <div class="item item-first">
                              <div class="caption">
                                   <div class="col-md-offset-1 col-md-10">
                                        <h3>Soy un dentista. Yo creo sonrisas. ¿Cuál es tu súper poder? </h3>
                                        <h1>Vida saludable</h1>
                                        <a href="#team" class="section-btn btn btn-default smoothScroll">Conoce a
                                             nuestros dentista</a>
                                   </div>
                              </div>
                         </div>

                         <div class="item item-second">
                              <div class="caption">
                                   <div class="col-md-offset-1 col-md-10">
                                        <h3>vamos a hacer tu vida más feliz</h3>
                                        <h1>Nuevo estilo de vida</h1>
                                        <a href="#about" class="section-btn btn btn-default btn-gray smoothScroll">Más
                                             Sobre nosotros</a>
                                   </div>
                              </div>
                         </div>

                         <div class="item item-third">
                              <div class="caption">
                                   <div class="col-md-offset-1 col-md-10">
                                        <h3>La odontología no es cara, lo que es caro es el descuido.</h3>
                                        <h1>Información personal</h1>
                                        <a href="#perfil" class="section-btn btn btn-default btn-blue smoothScroll">Perfil</a>
                                   </div>
                              </div>
                         </div>
                    </div>

               </div>
          </div>
     </section>


     <!-- ABOUT -->
     <section id="about">
          <div class="container">
               <div class="row">

                    <div class="col-md-6 col-sm-6">
                         <div class="about-info">
                              <h2 class="wow fadeInUp" data-wow-delay="0.6s">Bienvenido al Consultorio </h2>
                              <div class="wow fadeInUp" data-wow-delay="0.8s">
                                   <p>En esta Clínica se ofrecen Servicios Odontológicos a niños y adultos en las
                                        diferentes ramas de la Odontología. Entre ellas podemos incluir: Diagnóstico,
                                        Emergencias, Radiología, Periodoncia, Operatoria Dental, Odontopediatría,
                                        Endodoncia, Prótesis (Fija, Parcial Removible y Total), Cirugía y Ortodoncia.
                                   </p>

                                   <h5>Visión</h5>
                                   <p> servicios odontológicos en Santander y Boyaca
                                        logrando la expansión a otros municipios, la mejora continúa de los
                                        procesos y garantizando calidad y profesionalidad.</p>
                                   <h5>Misión</h5>
                                   <p>Brindar un servicio de excelencia en el área de salud oral, basado en
                                        conocimientos, alta tecnología y calidez humana que cubran las necesidades y
                                        expectativas de nuestros pacientes e interesados.</p>
                              </div>
                              <figure class="profile wow fadeInUp" data-wow-delay="1s">
                                   <img src="src/img/author-image.jpg" class="img-responsive" alt="">
                                   <figcaption>
                                        <h3>Dra. EMILY BERNAL </h3>
                                        <p> Odontóloga general con Enfasis Clinico en Odonotologia Biologica y Quirurgica</p>
                                   </figcaption>
                              </figure>
                         </div>
                    </div>

               </div>
          </div>
     </section>


     <!-- TEAM -->
     <section id="team" data-stellar-background-ratio="1">
          <div class="container">
               <div class="row">

                    <div class="col-md-6 col-sm-6">
                         <div class="about-info">
                              <h2 class="wow fadeInUp" data-wow-delay="0.1s">Nuestros dentistas</h2>
                         </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-4 col-sm-6">
                         <div class="team-thumb wow fadeInUp" data-wow-delay="0.2s">
                              <img src="src/img/team-image1.jpg" class="img-responsive" alt="">

                              <div class="team-info">
                                   <h3>esposo </h3>
                                   <p>Odontologo</p>
                                   <div class="team-contact-info">
                                        <p><i class="fa fa-phone"></i>celular</p>
                                        <p><i class="fa fa-envelope-o"></i> <a href="#">correo@hotmail.com</a></p>
                                   </div>
                              </div>

                         </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                         <div class="team-thumb wow fadeInUp" data-wow-delay="0.4s">
                              <img src="src/img/team-image2.jpg" class="img-responsive" alt="">

                              <div class="team-info">
                                   <h3>odontologos</h3>
                                   <p>Ortodoncista/Endodoncista</p>
                                   <div class="team-contact-info">
                                        <p><i class="fa fa-phone"></i>celular</p>
                                        <p><i class="fa fa-envelope-o"></i> <a href="#">correo</a></p>
                                   </div>
                              </div>

                         </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                         <div class="team-thumb wow fadeInUp" data-wow-delay="0.6s">
                              <img src="src/img/team-image3.jpg" class="img-responsive" alt="">

                              <div class="team-info">
                                   <h3>odontologia </h3>
                                   <p> </p>
                                   <div class="team-contact-info">
                                        <p><i class="fa fa-phone"></i>telefono</p>
                                        <p><i class="fa fa-envelope-o"></i> <a href="#">correo</a></p>
                                   </div>

                              </div>

                         </div>
                    </div>

               </div>
          </div>
     </section>
     <!-- MAKE AN APPOINTMENT -->
     <section id="appointment" data-stellar-background-ratio="3">
          <div class="container">
               <div class="row">

                    <div class="col-md-6 col-sm-6">
                         <img src="src/img/appointment-image.jpg" class="img-responsive" alt="">
                    </div>

                    <div class="col-md-6 col-sm-6">
                         <!-- CONTACT FORM HERE -->
                         <form action="./crud/cita_INSERT.php?opciones=INS" method="POST" enctype="multipart/form-data" autocomplete="off" id="appointment-form">

                              <!-- SECTION TITLE -->
                              <div class="section-title wow fadeInUp" data-wow-delay="0.4s">
                                   <h2>Realizar una Cita</h2>
                              </div>

                              <div class="wow fadeInUp" data-wow-delay="0.8s">
                                   <div class="col-md-6 col-sm-6">
                                        <label for="name">Nombre</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Nombre " require value=" <?php echo $row['nombre']; ?>">
                                   </div>
                                   <div class="col-md-6 col-sm-6">
                                        <label for="name">Apellido</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Apellido" require value=" <?php echo $row['apellido']; ?>">
                                   </div>


                                   <div class="col-md-12 col-sm-6">
                                        <label for="email">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" require value=" <?php echo $row['correo_electronico']; ?>">
                                   </div>

                                   </script>
                                   <div class="col-md-6 col-sm-6">
                                        <label for="fecha_cita">Fecha de la cita</label>
                                        <input type="text" class="form-control" name="fecha_cita" id="fecha_cita" required>

                                   </div>
                                   <div class="col-md-6 col-sm-6">
                                        <label for="hora">Hora de la cita</label>
                                        <input type="time" class="form-control" min="08:00" max="19:00" name="hora" id="hora" required>

                                   </div>
                                   <div class="col-md-6 col-sm-6">
                                        <label for="consultas">Consultas</label> <br>
                                        <select name="consultas" id="consultas" require>
                                             <?php while ($row1 = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                                                  echo "<option value = " . $row1['id_consultas'] . ">" . $row1['tipo'] . "</option>";
                                             }   ?>
                                        </select>
                                   </div>

                                   <div class="col-md-6 col-sm-6">
                                        <label for="dentistas">Dentistas</label> <br>
                                        <select name="dentistas" id="dentistas" require>
                                             <?php while ($row2 = mysqli_fetch_array($resultadoDentistas, MYSQLI_ASSOC)) {
                                                  echo "<option value = " . $row2['id_doctor'] . ">" . $row2['nombreD'] . "</option>";
                                             }   ?>
                                        </select>
                                   </div>

                                   <div class="col-md-12 col-sm-12">
                                        <br> <label for="phone">Teléfono</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Teléfono" required value=" <?php echo $row['telefono']; ?>">
                                   </div>
                                   <div class="col-md-12 col-sm-12">
                                        <br> <button type="submit" name="enviar" value="enviar" class="form-control" id="cf-submit">Enviar</button>
                                   </div>
                              </div>
                         </form>

                    </div>

               </div>
          </div>
     </section>

     <!-- perfil -->
     <section id="perfil" style="margin-top: 10%;">
          <div class="container">
               <div class="main-body">
                    <div class="row gutters-sm">
                         <div class="col-md-4 mb-3">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex flex-column align-items-center text-center">
                                        <?php
                                             if ($row['sexo'] == 'Masculino') {
                                             ?>
                                                  <img src="./src/img/iconoH.jpg" class="rounded-circle" width="150">

                                             <?php
                                             } elseif ($row['sexo'] == 'Femenino') {
                                             ?>
                                                  <img src="./src/img/iconoM.jpg" class="rounded-circle" width="150">
                                             <?php
                                             }
                                             ?>
                                             <div class="mt-3">
                                                  <h3 class="name"><?php echo $row['nombre'] . ' ' . $row['apellido']; ?> </h3>
                                                  <p class="text-secondary mb-1">Consultorio Odontologico Emily Bernal</p>
                                                  <p class="text-muted font-size-sm"><?php echo $row['correo_electronico']; ?></p>

                                                  <div class="card  bg-light" style="margin-top: 20%;">
                                                       <div class="card-header">
                                                            <h4> <strong>Acciones</strong></h4>
                                                       </div>
                                                       <div class="card-body">

                                                            <label>Editar perfil</label>
                                                            <a class="btn btn-primary " href="./editar_paciente.php"> <i class="fa fa-edit"></i> </a> <br>
                                                            <label>Vizualizar consultas pendientes</label>
                                                            <a class="btn btn-success " target="__blank" href="./Reportes/reporte.php"><i class="fa fa-eye"></i> </a> <br>
                                                            <label>Vizualizar historial</label>
                                                            <a class="btn btn-success text-dark " target="__blank" href="./Reportes/reporteH.php"><i class="fa fa-history"></i></a> <br>
                                                       </div>
                                                       <div class="card-footer text-muted">

                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>

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
                    </div>
               </div>
          </div>
     </section>

     <!-- GOOGLE MAP -->
     <section id="google-map">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3968.44774838783!2d-73.61959973934803!3d5.932847479720224!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e41e51b70759ac5%3A0xf0a976a60eeb1784!2sOdontolog%C3%ADa%20Dra.Emily%20Bernal!5e0!3m2!1ses-419!2sco!4v1740097163838!5m2!1ses-419!2sco" width="100%" height="350" frameborder="0" style="border:0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
     </section>

     <!-- FOOTER -->
     <footer data-stellar-background-ratio="5">
          <div class="container">
               <div class="row">

                    <div class="col-md-4 col-sm-4">
                         <div class="footer-thumb">
                              <h4 class="wow fadeInUp" data-wow-delay="0.4s"> Dirrecion</h4>
                              <p>Barbosa Santander</p>

                              <div class="contact-info">
                                   <p><i class="fa fa-phone"></i> 310-5547320</p>
                                   <p><i class="fa fa-envelope-o"></i> <a href="#">correo</a></p>
                              </div>
                         </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                         <div class="footer-thumb">
                              <h4 class="wow fadeInUp" data-wow-delay="0.4s">Últimas noticias</h4>
                              <div class="latest-stories">
                                   <div class="stories-image">
                                        <a href="" target="_blank"><img src="src/img/blanqueamiento.jpg" class="img-responsive" alt=""></a>
                                   </div>
                                   <div class="stories-info">
                                        <a href="" target="_blank">
                                             <h5>Blanqueamiento dental</h5>
                                        </a>
                                        <span>fecha</span>
                                   </div>
                              </div>

                              <div class="latest-stories">
                                   <div class="stories-image">
                                        <a href="" target="_blank"></a><img src="src/img/evolucion.jpg" class="img-responsive" alt=""></a>
                                   </div>
                                   <div class="stories-info">
                                        <a href="" target="_blank">
                                             <h5> Evolución de la odontología moderna</h5>
                                        </a>
                                        <span>fecha</span>
                                   </div>
                              </div>
                         </div>
                    </div>

                    <div class=" col-md-4 col-sm-4">
                         <div class="footer-thumb">
                              <div class="opening-hours">
                                   <h4 class="wow fadeInUp" data-wow-delay="0.4s">Horario de
                                        apertura</h4>
                                   <p>Lunes - Viernes <span>08:00 AM - 7:00 PM</span></p>
                                   <p>Sábado <span>Cerrado</span></p>
                                   <p>Domingo <span>Cerrado</span></p>
                              </div>

                              <ul class="social-icon">
                                   <li><a href="https://www.facebook.com" target=" _blank"" class=" fa fa-facebook-square" attr="facebook icon"></a>
                                   </li>

                              </ul>
                         </div>
                    </div>

                    <div class="col-md-12 col-sm-12 border-top">
                         <div class="col-md-4 col-sm-6">
                              <div class="copyright-text">
                                   <p>ODONTOLOGIA &copy; Dr.Emily Bernal

                                        | Barbosa Santander</p>
                              </div>
                         </div>
                         <div class="col-md-6 col-sm-6">
                              <div class="footer-link">
                                   <a>Politica de privacidad </a>
                                   <a>Cookies</a>
                                   <a>Avisos legales </a>
                                   <a href="https://www.facebook.com" target="_blank">Facebook
                                   </a>
                              </div>
                         </div>
                         <div class="col-md-2 col-sm-2 text-align-center">
                              <div class="angle-up-btn">
                                   <a href="#top" class="smoothScroll wow fadeInUp" data-wow-delay="1.2s"><i class="fa fa-angle-up"></i></a>
                              </div>
                         </div>
                    </div>

               </div>
          </div>
     </footer>
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
     <!-- SCRIPTS -->
     <script src="src/js/bootstrap.min.js"></script>
     <script src="src/js/jquery.sticky.js"></script>
     <script src="src/js/jquery.stellar.min.js"></script>
     <script src="src/js/wow.min.js"></script>
     <script src="src/js/smoothscroll.js"></script>
     <script src="src/js/owl.carousel.min.js"></script>
     <script src="src/js/custom.js"></script>
     <script type="module" src="src/js/auth.js"></script>


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