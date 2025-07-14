<?php
session_start();
include("setup/config.php");
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNK INMOBILIARIA</title>
    <link rel="stylesheet" href="css/inicio.css">
    <link rel="icon" type="image/x-icon" href="img/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/password-validation.js"></script>
    <script>
        function enviar()
        {
            let usuario = document.forms.form.usuario.value;
            let password = document.forms.form.password.value;
            let camposVacios = [];

            // Validar campos vacíos
            if(usuario === "") camposVacios.push("Usuario");
            if(password === "") camposVacios.push("Contraseña");

            if(camposVacios.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos vacíos',
                    html: `Por favor complete los siguientes campos:<br><b>${camposVacios.join(", ")}</b>`,
                    customClass: {
                        popup: 'swal2-popup-arial'
                    }
                });
                return false;
            }

            // Validar formato de correo
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(usuario)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Correo inválido',
                    text: 'Por favor ingrese un correo electrónico válido',
                    customClass: {
                        popup: 'swal2-popup-arial'
                    }
                });
                return false;
            }

            // Validar contraseña usando utilidad centralizada
            if (!validateAndShowPasswordErrors(password)) {
                return false;
            }

            // Si todo está correcto, enviar el formulario
            document.forms.form.submit();
        }
    </script>
    
    <style>
        .swal2-popup-arial {
            font-family: Arial, sans-serif !important;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>

        <nav class="header-derecha">
            <a href="registro_propietario.php" class="header-botones">Registro Propietario</a>
            <a href="registro_gestor.php" class="header-botones">Registro Gestor</a>
        </nav>

    </header>

    <main class="main">
        <div class="login-contenedor">
            <h2>Autenticación</h2>
            <img src="img/key.png" alt="imagen" class="img-login">
            <form action="procesa.php"  name="form" method="post">
                <div class="input-group">
                    <label for="fname">Usuario:</label>
                    <input type="email" name="usuario" id="fname" placeholder="ingrese email" required>
                </div>
                <div class="input-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" id="password" placeholder="Ingrese Contraseña" required>
                </div>
                <button type="button" onclick="enviar();">Ingresar</button>
            </form>            
            <br>
            <div><a href="recuperar.html" class="recuperar">Recuperar Contraseña</a></div>
        </div>
        <br>
        <div id="busqueda" class="d-flex justify-content-center mt-4">
            <div class="card" style="width: 80%;"> 
                <div class="card-header text-center">Filtro de Propiedades</div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-sm-2">
                                Tipo
                                <select id="Tipo" class="form-select">
                                    <option value="0">Seleccionar</option>
                                    <?php
                                    $sql = "SELECT * FROM tipo_propiedad WHERE estado=1";
                                    $result = mysqli_query(conectar(), $sql);
                                    while ($datos = mysqli_fetch_array($result)) {
                                    ?>
                                    <option value="<?php echo $datos['idtipo_propiedad'];?>"><?php echo $datos['tipo'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                Región
                                <select id="regiones" class="form-select">
                                    <option value="0">Seleccionar</option>
                                    <?php
                                        $sql = "SELECT * FROM regiones where estado=1";
                                        $result = mysqli_query(conectar(), $sql);
                                        while ($datos = mysqli_fetch_array($result)) {
                                    ?>
                                    <option value="<?php echo $datos['idregiones'];?>"><?php echo $datos['region'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                Provincia
                                <select id="provincias" class="form-select">
                                    <option value="0">Seleccionar</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                Comuna
                                <select id="comunas" class="form-select">
                                    <option value="0">Seleccionar</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                Sector
                                <select id="sectores" class="form-select">
                                    <option value="0">Seleccionar</option>
                                </select>
                            </div>
                            <div class="col-sm-2 d-flex align-items-end">
                                <button id="btn-filtrar" class="btn-galeria w-100" type="button">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="galeria" id="galeria-propiedades">
            <!-- Aquí se cargarán las propiedades filtradas por AJAX -->
        </div>

    </main>

    <footer class="footer">
        <div class="footer-izquierda">
            <img src="img/Logo.png" alt="Logo PNK" class="logo-footer">
            <div class="titulo-footer">PNK INMOBILIARIA</div>
        </div>

        <nav class="footer-centro">
            <a href="registro_propietario.html" class="enlace-footer">Registro Propietario</a>
            <a href="registro_gestor.html" class="enlace-footer">Registro Gestor</a>
            <a href="registro_usuario.html" class="enlace-footer">Registro Usuario</a>
        </nav>

        <nav class="footer-derecha">
            <a href="https://www.instagram.com/tioreneoficial_/?hl=es" target="_blank">
                <img src="img/logo-insta.png" alt="Logo instagram" class="logo-footer">
            </a>
            <a href="https://www.ticketmaster.cl/event/popin-un-show-muy-penca-centro-cultural-san-gines" target="_blank">
                <img src="img/linkedin.png" alt="Logo Linkedin" class="logo-footer">
            </a>
        </nav>

    </footer>

    <div class="copyright">
        &copy; 2025 Todos los derechos Reservados PNK Inmobiliaria
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('registro') === 'exito') {
            Swal.fire({
                icon: 'success',
                title: '¡Registro exitoso!',
                text: 'Tu cuenta ha sido creada correctamente.'
            });
        }
        if (urlParams.get('login') === 'exito') {
            Swal.fire({
                icon: 'success',
                title: '¡Sesión iniciada!',
                text: 'Has iniciado sesión correctamente.'
            });
        }
        if (urlParams.get('error') === 'usuario') {
            Swal.fire({
                icon: 'error',
                title: 'Usuario no encontrado',
                text: 'El correo electrónico ingresado no está registrado en el sistema.'
            });
        }
        if (urlParams.get('error') === 'password') {
            Swal.fire({
                icon: 'error',
                title: 'Contraseña incorrecta',
                text: 'La contraseña ingresada no es correcta.'
            });
        }
        if (urlParams.get('error') === 'inactivo') {
            Swal.fire({
                icon: 'error',
                title: 'Cuenta inactiva',
                text: 'Tu cuenta está inactiva. Por favor contacta al administrador.'
            });
        }
        if (urlParams.get('sesion') === 'cerrada') {
            Swal.fire({
                icon: 'success',
                title: 'Sesión cerrada',
                text: 'Has cerrado sesión correctamente.',
                customClass: {
                    popup: 'swal2-popup-arial'
                }
            }).then(() => {
                // Limpiar la URL después de mostrar el mensaje
                if (window.history.replaceState) {
                    const url = window.location.origin + window.location.pathname;
                    window.history.replaceState(null, null, url);
                }
            });
        }
    </script>
<script>
    $(document).ready(function() {
        // Función para cargar propiedades con paginación
        function cargarPropiedades(pagina = 1) {
            let tipo = $('#Tipo').val();
            let region = $('#regiones').val();
            let provincia = $('#provincias').val();
            let comuna = $('#comunas').val();
            let sector = $('#sectores').val();
            
            $.ajax({
                type: 'POST',
                url: 'filtrar_propiedades.php',
                data: {
                    tipo: tipo,
                    region: region,
                    provincia: provincia,
                    comuna: comuna,
                    sector: sector,
                    pagina: pagina
                },
                success: function(respuesta) {
                    $('#galeria-propiedades').html(respuesta);
                }
            });
        }

        // Cargar primera página al iniciar
        cargarPropiedades(1);

        // Función global para cambiar de página
        window.cargarPagina = function(pagina) {
            cargarPropiedades(pagina);
            // Scroll suave hacia arriba
            $('html, body').animate({
                scrollTop: $('#galeria-propiedades').offset().top - 100
            }, 500);
        };

        // Cargar provincias, comunas y sectores igual que antes
        $("#regiones").on( "change", function() {
            provincias();
            $("#provincias").val('0');
            $("#comunas").html('<option value="0">Seleccionar</option>');
            $("#sectores").html('<option value="0">Seleccionar</option>');
        });
        $("#provincias").on( "change", function() {
            comunas();
            $("#comunas").html('<option value="0">Seleccionar</option>');
            $("#sectores").html('<option value="0">Seleccionar</option>');
        });
        $("#comunas").on( "change", function() {
            sectores();
            $("#sectores").html('<option value="0">Seleccionar</option>');
        });

        function provincias(){
            $.ajax({
                type: "POST",
                data: "idregion="+$("#regiones").val()+"&op=1",
                url:"procesafiltro.php",
                success: function(respuesta){
                    $("#provincias").html(respuesta);
                }
            });
        }
        function comunas(){
            $.ajax({
                type: "POST",
                data: "idprovincias="+$("#provincias").val()+"&op=2",
                url:"procesafiltro.php",
                success: function(respuesta){
                    $("#comunas").html(respuesta);
                }
            });
        }
        function sectores(){
            $.ajax({
                type: "POST",
                data: "idcomunas="+$("#comunas").val()+"&op=3",
                url:"procesafiltro.php",
                success: function(respuesta){
                    $("#sectores").html(respuesta);
                }
            });
        }

        // Filtrar propiedades
        $('#btn-filtrar').on('click', function() {
            cargarPropiedades(1); // Volver a la primera página al filtrar
        });
    });
</script>
</body>
</html>

