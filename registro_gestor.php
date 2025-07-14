<?php
session_start();
include("setup/config.php"); // Incluye el archivo de configuración para la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Gestor</title>
    <link rel="stylesheet" href="css/registro.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/jquery.Rut.js"></script>
    <script src="js/password-validation.js"></script>

<script>
    function enviar(op)
    {
        let rut = $('#rut').val();
        let nombres = $('#nombres').val();
        let appaterno = $('#appaterno').val();
        let apmaterno = $('#apmaterno').val();
        let telefono = $('#telefono').val();
        let correo = $('#usuario').val();
        let clave = $('#clave').val();
        let clave2 = $('#cclave').val();
        let fechanacimiento = $('#fechanacimiento').val();
        let sexo = $('input[name="sexo"]:checked').length === 0;
        let certificado = $('#frm_certificado').val();
        let camposVacios = [];
        // Validar campos vacíos

        if (nombres === "") camposVacios.push("Nombres");
        if (appaterno === "") camposVacios.push("Apellido Paterno");
        if (apmaterno === "") camposVacios.push("Apellido Materno");
        if (telefono === "") camposVacios.push("Teléfono");
        if (correo === "") camposVacios.push("Correo");
        if (fechanacimiento === "") camposVacios.push("Fecha de Nacimiento");
        if (clave === "") camposVacios.push("Clave");
        if (clave2 === "") camposVacios.push("Repetir Clave");
        if (rut === "") camposVacios.push("RUT");
        if (sexo) camposVacios.push("Sexo");
        if (certificado === "") camposVacios.push("Certificado");
        if (camposVacios.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Campos vacíos',
                html: `Por favor complete los siguientes campos:<br><b>${camposVacios.join(", ")}</b>`
            });
    return; // Evita que se ejecute el submit
}
        // Validar RUT usando el plugin
        if (!$.Rut.validar(rut)) {
            Swal.fire({
                icon: 'error',
                title: 'RUT inválido',
                text: 'Por favor ingrese un RUT válido'
            });
            return; // Evita que se ejecute el submit
        }

        // Validar fecha de nacimiento (solo fechas pasadas)
        let fechaNac = new Date(fechanacimiento);
        let fechaActual = new Date();
        if (fechaNac >= fechaActual) {
            Swal.fire({
                icon: 'error',
                title: 'Fecha inválida',
                text: 'La fecha de nacimiento debe ser una fecha pasada'
            });
            return;
        }

        // Validar formato de correo electrónico
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            Swal.fire({
                icon: 'error',
                title: 'Correo inválido',
                text: 'Por favor ingrese un correo electrónico válido'
            });
            return;
        }

        // Validar formato de teléfono
        let telefonoRegex = /^\+569\d{8}$/;
        if (!telefonoRegex.test(telefono)) {
            Swal.fire({
                icon: 'error',
                title: 'Teléfono inválido',
                text: 'El teléfono debe tener el formato +569XXXXXXXX (8 dígitos después del +569)'
            });
            return;
        }

        // Validar contraseña usando utilidad centralizada
        if (!validateAndShowPasswordErrors(clave, clave2)) {
            return;
        }

        // Validar que el archivo sea PDF
        if (certificado) {
            let file = $('#frm_certificado')[0].files[0];
            if (file && file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'Tipo de archivo inválido',
                    text: 'Solo se permiten archivos PDF'
                });
                return;
            }
        }
        
        document.formulario.opoculto.value = op;
        document.formulario.submit();
    }

    $(document).ready(function() {
        $('#rut').Rut({
            on_error: function(){
            }
        });
    });

    function validarTelefono(input) {
        let valor = input.value;
        // Solo permitir números y el símbolo +
        valor = valor.replace(/[^\d+]/g, '');
        input.value = valor;
    }
</script>
</head>
<body>
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>
    </header>  
    <div id="formulario">
        <div class="card">
            <div class="card-header-c"><center><b>Formulario registro</b> <br><b>de</b><br> <b>Gestor</b></center></div>
            <div class="card-body">
                <form action="ingreso_gestor.php" name="formulario" method="post" enctype="multipart/form-data">
                        <div class="campos">
                            <div class="row">
                                <div class="col-sm">Rut</label></div>
                                <div class="col-sm"><input type="text" class="form-control" id="rut" name="rut"></div>
                                <div class="col-sm">Nombres</div>
                                <div class="col-sm"><input type="text" class="form-control" id="nombres" name="nombres"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Apellido Paterno</div>
                                <div class="col-sm"><input type="text" class="form-control" id="appaterno" name="appaterno"></div>
                                <div class="col-sm">Apellido Materno</div>
                                <div class="col-sm"><input type="text" class="form-control" id="apmaterno" name="apmaterno"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Correo</label></div>
                                <div class="col-sm"><input type="email" class="form-control" id="usuario" name="usuario"></div>
                                <div class="col-sm">Fecha de Nacimiento</div>
                                <div class="col-sm"><input type="date" class="form-control" id="fechanacimiento" name="fechanacimiento"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Sexo</div>
                                <div class="col-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexo" id="sexoM" value="M">
                                        <label class="form-check-label" for="sexoM">Masculino</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexo" id="sexoF" value="F">
                                        <label class="form-check-label" for="sexoF">Femenino</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexo" id="sexo0" value="0">
                                        <label class="form-check-label" for="sexo0">Prefiero no decirlo</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Telefono</div>
                                <div class="col-sm"><input type="text" class="form-control" id="telefono" name="telefono" oninput="validarTelefono(this)"></div>
                                <div class="col-sm">Certificado</div>
                                <div class="col-sm"><input type="file" name="frm_certificado" id="frm_certificado"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Contraseña</div>
                                <div class="col-sm"><input type="password" class="form-control" id="clave" name="clave"></div>
                                <div class="col-sm">Confirme Contraseña</div>
                                <div class="col-sm"><input type="password" class="form-control" id="cclave" name="cclave"></div>
                            </div>
                        </div>
                        <center><button type="button" class="boton-formulario" onclick="enviar(this.value);" value="Ingresar">Registrar</button>
                        <button type="button" class="boton-formulario" onclick="window.location.href='index.php'">Cancelar</button></center>
                        <br>
                        <input type="hidden" name="opoculto">
                </form>
            </div>
        </div>
    </div>
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

</body>
</html>
