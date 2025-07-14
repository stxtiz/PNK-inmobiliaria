<?php
session_start();
include("setup/config.php"); // Incluye el archivo de configuración para la conexión a la base de datos

// Manejar mensajes de error y éxito
if (isset($_GET['error']) || isset($_GET['registrado']) || isset($_GET['modificado']) || isset($_GET['eliminado'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {";
    
    if (isset($_GET['error'])) {
        switch($_GET['error']) {
            case 'pdf_only':
                echo "Swal.fire({
                    icon: 'error',
                    title: 'Tipo de archivo inválido',
                    text: 'Solo se permiten archivos PDF'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });";
                break;
            case 'email_exists':
                echo "Swal.fire({
                    icon: 'error',
                    title: 'Correo ya registrado',
                    text: 'Este correo electrónico ya está registrado en el sistema'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });";
                break;
            case 'db_error':
                echo "Swal.fire({
                    icon: 'error',
                    title: 'Error en la base de datos',
                    text: 'Ocurrió un error al procesar la solicitud'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });";
                break;
            case 'password_format':
                echo "Swal.fire({
                    icon: 'error',
                    title: 'Contraseña inválida',
                    html: 'La contraseña debe cumplir con los siguientes requisitos:<br>- Mínimo 8 caracteres<br>- Al menos una letra mayúscula (A-Z)<br>- Al menos una letra minúscula (a-z)<br>- Al menos un número (0-9)<br>- Al menos un carácter especial (!@#$%^&*)'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });";
                break;
            case 'password_mismatch':
                echo "Swal.fire({
                    icon: 'error',
                    title: 'Contraseñas no coinciden',
                    text: 'Las contraseñas ingresadas no coinciden. Por favor verifíquelas.'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });";
                break;
        }
    } else if (isset($_GET['registrado']) && $_GET['registrado'] == '1') {
        echo "Swal.fire({
            icon: 'success',
            title: '¡Usuario registrado!',
            text: 'El usuario ha sido registrado exitosamente.'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });";
    } else if (isset($_GET['modificado']) && $_GET['modificado'] == '1') {
        echo "Swal.fire({
            icon: 'success',
            title: '¡Modificación exitosa!',
            text: 'Los cambios se han guardado correctamente.'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });";
    } else if (isset($_GET['eliminado']) && $_GET['eliminado'] == '1') {
        echo "Swal.fire({
            icon: 'success',
            title: '¡Eliminación exitosa!',
            text: 'El usuario ha sido eliminado correctamente.'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });";
    }
    
    echo "});</script>";
}

if (isset($_SESSION['usuario']) && isset($_SESSION['tipo']) && $_SESSION['tipo'] == 3)
{

    $tipoTexto = '';

    switch ($_SESSION['tipo']) {
        case 1:
            $tipoTexto = 'Gestor Inmobiliario Free';
            break;
        case 2:
            $tipoTexto = 'Dueño de Inmueble';
            break;
        case 3:
            $tipoTexto = 'Administrador';
            break;
        default:
            $tipoTexto = 'Desconocido';
    }

if(isset($_GET['idusu']))
{
    $sql = "select * from usuarios where id = ".$_GET['idusu'];
    $result = mysqli_query(conectar(), $sql); // Ejecuta la consulta SQL "query"
    $datosusu = mysqli_fetch_array($result);
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crud Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/jquery.Rut.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/password-validation.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    

<script>
    function enviar(op)
    {
        // Solo validar si no es Cancelar
        if (op !== 'Cancelar') {
            let rut = $('#rut').val();
            let nombres = $('#nombres').val();
            let appaterno = $('#appaterno').val();
            let apmaterno = $('#apmaterno').val();
            let telefono = $('#telefono').val();
            let correo = $('#usuario').val();
            let fechanacimiento = $('#fechanacimiento').val();
            let npropiedad = $('#npropiedad').val();
            let sexo = $('#sexo').val();
            let tipo = $('#tipo').val();
            let estado = $('#estado').val();
            let clave = $('#clave').val();
            let cclave = $('#cclave').val();
            let camposVacios = [];

            // Validar campos vacíos
            if (rut === "") camposVacios.push("RUT");            
            if (nombres === "") camposVacios.push("Nombres");
            if (appaterno === "") camposVacios.push("Apellido Paterno");
            if (apmaterno === "") camposVacios.push("Apellido Materno");
            if (correo === "") camposVacios.push("Correo");
            if (fechanacimiento === "") camposVacios.push("Fecha de Nacimiento"); 
            if (sexo === "") camposVacios.push("Sexo");                       
            if (tipo === "") camposVacios.push("Tipo");
            if (telefono === "") camposVacios.push("Teléfono");
            
            // Validar campos específicos según el tipo de usuario
            if (tipo === "2" && npropiedad === "") {
                camposVacios.push("Número de Propiedad");
            }
            if (tipo === "1" && !$('#frm_certificado').val() && op === 'Ingresar') {
                camposVacios.push("Certificado");
            }
            if (clave === "") camposVacios.push("Contraseña");
            if (cclave === "") camposVacios.push("Confirme Contraseña");
            if (estado === "") camposVacios.push("Estado");

            if (camposVacios.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos vacíos',
                    html: `Por favor complete los siguientes campos:<br><b>${camposVacios.join(", ")}</b>`
                });
                return;
            }

            // Validar contraseñas usando utilidad centralizada
            if (op === 'Ingresar') {
                let clave = $('#clave').val();
                let cclave = $('#cclave').val();
                
                if (!validateAndShowPasswordErrors(clave, cclave)) {
                    return;
                }
            }

            // Validar RUT usando el plugin
            if (!$.Rut.validar(rut)) {
                Swal.fire({
                    icon: 'error',
                    title: 'RUT inválido',
                    text: 'Por favor ingrese un RUT válido'
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

            // Validar que el número de propiedad sea numérico
            if (isNaN(npropiedad)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Número de propiedad inválido',
                    text: 'El número de propiedad debe ser un valor numérico'
                });
                return;
            }

            // Validar que el archivo sea PDF
            if (tipo === "1" && $('#frm_certificado').val()) {
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

            // Validar que se haya seleccionado un estado válido
            if (estado === "" || estado === "3") {
                Swal.fire({
                    icon: 'error',
                    title: 'Estado no seleccionado',
                    text: 'Por favor seleccione un estado válido (Activo o Inactivo)'
                });
                return;
            }

            // Si es una operación de eliminación, mostrar confirmación
            if (op === 'Eliminar') {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esta acción!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.formulario.opoculto.value = op;
                        document.formulario.submit();
                    }
                });
                return;
            }

            // Si es una operación de modificación, mostrar confirmación
            if (op === 'Modificar') {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas guardar los cambios?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.formulario.opoculto.value = op;
                        document.formulario.submit();
                    }
                });
                return;
            }

            // Si es una operación de ingreso, mostrar confirmación
            if (op === 'Ingresar') {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas registrar este propietario?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, registrar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.formulario.opoculto.value = op;
                        document.formulario.submit();
                    }
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

        // Función para mostrar/ocultar campos según el tipo de usuario
        function actualizarCamposAdicionales() {
            let tipo = $('#tipo').val();
            
            // Ocultar todos los campos adicionales primero
            $('#campo_npropiedad').hide();
            $('#campo_certificado').hide();
            
            // Mostrar el campo correspondiente según el tipo
            if (tipo === "2") {
                $('#campo_npropiedad').show();
            } else if (tipo === "1") {
                $('#campo_certificado').show();
            }
        }

        // Ejecutar al cargar la página
        actualizarCamposAdicionales();

        // Ejecutar cuando cambie el tipo de usuario
        $('#tipo').change(function() {
            actualizarCamposAdicionales();
        });
    });

    function validarTelefono(input) {
        let valor = input.value;
        if (!valor.startsWith('+569')) {
            input.value = '+569' + valor.replace(/[^0-8]/g, '');
        }
    }
</script>
</head>
<body>
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>
        <div class="header-derecha">
            <a href="dashboard2.php" class="boton-icono">Volver</a>
        </div>
    </header>

    <main class="main">
        <div id="formulario"> 
            <div class="card">
                <div class="card-header"><b>CRUD Usuarios</b> </div>
                <div class="card-body">
                    <form action="crudGestor.php" name="formulario" method="post" enctype="multipart/form-data">
                        <div class="campos">
                            <div class="row">
                                <div class="col-sm">Rut</label></div>
                                <div class="col-sm"><input type="text" class="form-control" id="rut" name="rut" value="<?php if(isset($_GET['idusu'])){echo $datosusu['rut'];}?>"></div>
                                <div class="col-sm">Nombres</div>
                                <div class="col-sm"><input type="text" class="form-control" id="nombres" name="nombres" value="<?php if(isset($_GET['idusu'])){echo $datosusu['nombres'];}?>"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Apellido Paterno</div>
                                <div class="col-sm"><input type="text" class="form-control" id="appaterno" name="appaterno" value="<?php if(isset($_GET['idusu'])){echo $datosusu['ap_paterno'];}?>"></div>
                                <div class="col-sm">Apellido Materno</div>
                                <div class="col-sm"><input type="text" class="form-control" id="apmaterno" name="apmaterno" value="<?php if(isset($_GET['idusu'])){echo $datosusu['ap_materno'];}?>"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Correo</label></div>
                                <div class="col-sm"><input type="email" class="form-control" id="usuario" name="usuario" value="<?php if(isset($_GET['idusu'])){echo $datosusu['usuario'];}?>"></div>
                                <div class="col-sm">Fecha de Nacimiento</div>
                                <div class="col-sm"><input type="date" class="form-control" id="fechanacimiento" name="fechanacimiento" value="<?php if(isset($_GET['idusu'])){echo $datosusu['fechanacimiento'];}?>"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Sexo</div>
                                <div class="col-sm">
                                    <select class="form-select" name="sexo" id="sexo">
                                        <option value="">Seleccionar</option>
                                        <option value="M" <?php if(isset($_GET['idusu'])){ if($datosusu['sexo']=="M"){?> selected <?php }} ?>>Masculino</option>
                                        <option value="F" <?php if(isset($_GET['idusu'])){ if($datosusu['sexo']=="F"){?> selected <?php }} ?>>Femenino</option>
                                        <option value="0" <?php if(isset($_GET['idusu'])){ if($datosusu['sexo']==0){?> selected <?php }} ?>>No especificado</option>
                                    </select>
                                </div>
                                <div class="col-sm">Tipo</div>
                                <div class="col-sm">
                                    <select class="form-select" name="tipo" id="tipo">
                                        <option value="">Seleccionar</option>
                                        <option value="3" <?php if(isset($_GET['idusu'])){ if($datosusu['tipo']==3){?> selected <?php }} ?>>Administrador</option>
                                        <option value="2" <?php if(isset($_GET['idusu'])){ if($datosusu['tipo']==2){?> selected <?php }} ?>>Propietario</option>
                                        <option value="1" <?php if(isset($_GET['idusu'])){ if($datosusu['tipo']==1){?> selected <?php }} ?>>Gestro Free</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm">Telefono</div>
                                <div class="col-sm"><input type="text" class="form-control" id="telefono" name="telefono" value="<?php if(isset($_GET['idusu'])){echo $datosusu['telefono'];}?>"></div>
                                <div class="col-sm"></div>
                                <div class="col-sm"></div>
                            </div>
                            <br>
                            <div class="row">
                                <div id="campo_npropiedad" style="display: none;">
                                    <div class="col-sm">N° Propiedad</div>
                                    <div class="col-sm"><input type="number" class="form-control" id="npropiedad" name="npropiedad" value="<?php if(isset($_GET['idusu'])){echo $datosusu['npropiedad'];}?>"></div>
                                    <div class="col-sm"></div>
                                    <div class="col-sm"></div>
                                </div>
                                <div id="campo_certificado" style="display: none;">
                                    <div class="col-sm">Certificado</div>
                                    <div class="col-sm"><input type="file" name="frm_certificado" id="frm_certificado"></div>
                                    <div class="col-sm"></div>
                                    <div class="col-sm"></div>
                                </div>
                            </div>
                            <br>
                            <?php 
                            if(!isset($_GET['idusu'])){
                            ?>  
                                <div class="row">
                                    <div class="col-sm">Contraseña</div>
                                    <div class="col-sm"><input type="password" class="form-control" id="clave" name="clave"></div>
                                    <div class="col-sm">Confirme Contraseña</div>
                                    <div class="col-sm"><input type="password" class="form-control" id="cclave" name="cclave"></div>
                                </div>
                            <?php
                                }
                            ?>
                            <br>
                            <div class="row">
                                <div class="col-sm">Estado</div>
                                <div class="col-sm">
                                <select class="form-select" name="estado" id="estado">
                                    <option value="">Seleccionar</option>
                                    <option value="1" <?php if(isset($_GET['idusu'])){ if($datosusu['estado']==1){?> selected <?php }} ?>>Activo</option>
                                    <option value="0" <?php if(isset($_GET['idusu'])){ if($datosusu['estado']==0){?> selected <?php }} ?>>Inactivo</option>
                                </select>
                                </div>
                            
                                <div class="col-sm"></div>
                                <div class="col-sm"></div>
                            </div>
                        </div>
                        <br><center>

                        <?php
                        if(!isset($_GET['idusu']))
                        {
                            ?>
                            <button type="button" class="boton-formulario" onclick="enviar(this.value);" value="Ingresar">Ingresar</button>
                            <?php
                        }else{
?>
                            <button type="button" class="boton-formulario" onclick="enviar(this.value);" value="Modificar">Modificar</button>
                            <button type="button" class="boton-formulario" onclick="enviar(this.value);" value="Eliminar">Eliminar</button>
<?php   
                        }
                        ?>

                        <button type="button" class="boton-formulario" onclick="enviar(this.value);" value="Cancelar">Cancelar</button></center>
                        <br>
                        <input type="hidden" name="opoculto">
                        <input type="hidden" name="idoculto" value="<?php if(isset($_GET['idusu'])){echo $_GET['idusu'];}?>">
                    </form>
                </div>
            </div>
        </div>
                <br>
        <div id="mostrarusuarios">
            <div class="card">
                <div class="card-header"> (<b>Total de Usuarios en la BD <?php echo contarusu();?></b>)</div>
                <div class="card-body">
                    <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Rut</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Usuario</th>
                                    <th>Fecha de nacimiento</th>
                                    <th>Teléfono Móvil</th>
                                    <th>N° de propiedad</th>
                                    <th>Certificado</th>
                                    <th>Sexo</th>
                                    <th>Tipo de usuario</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $num = 1;
                                $sql = "select * from usuarios";
                                $result = mysqli_query(conectar(), $sql); // Ejecuta la consulta SQL "query"
                                while($datos=mysqli_fetch_array($result))
                                {
                            ?>
                                <tr>
                                    <td><?php echo $num;?></td>
                                    <td><?php echo $datos['rut'];?></td>
                                    <td><?php echo $datos['nombres'];?></td>
                                    <td><?php echo $datos['ap_paterno']." ".$datos['ap_materno'];?></td>
                                    <td><?php echo $datos['usuario'];?></td>
                                    <td><?php echo $datos['fechanacimiento'];?></td>
                                    <td><?php echo $datos['telefono'];?></td>
                                    <td><?php echo $datos['npropiedad'];?></td>
                                    <td><?php 
                                            if($datos['certificado']==''){
                                                echo "No figura certificado";
                                            }else{
                                                ?>
                                                <img src="img/usuarios/PDF.png">
                                                <?php
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            if($datos['sexo']=='M')
                                            {
                                                echo "Masculino";
                                            }elseif($datos['sexo']=='F')
                                            {
                                                echo "Femenino";
                                            }else{
                                                echo "No especificado";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            if($datos['tipo']==1)
                                            {
                                                echo "Gestor Inmobiliario Free";
                                            }elseif($datos['tipo']==2)
                                            {
                                                echo "Propietario";
                                            }else{
                                                echo "Administrador";
                                            }
                                        ?>
                                    </td>

                                    <td>
                                            <?php
                                            if($datos['estado']==1)
                                            {
                                            ?>
                                                <img src="img/check.png" width="16px">
                                            <?php
                                            }else{
                                            ?>
                                                <img src="img/ina.png" width="16px">
                                            <?php    
                                            }
                                            ?>
                                    </td>
                                    <td><a href="dashboard.php?idusu=<?php echo $datos['id'];?>"><img src="img/update.png" width="16px"></a>
                                        <a href="#" onclick="confirmarEliminacion(<?php echo $datos['id']; ?>); return false;"><img src="img/borrar.png" width="16px"></a>
                                    </td>
                                </tr>
                            <?php
                                    $num++;
                                }
                            ?>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script>
        // Verificar todos los parámetros de URL
        const urlParams = new URLSearchParams(window.location.search);
        
        // Verificar login exitoso
        if (urlParams.get('login') === 'exito') {
            Swal.fire({
                icon: 'success',
                title: '¡Sesión iniciada!',
                text: 'Has iniciado sesión correctamente.'
            }).then(() => {
                // Limpiar la URL después de mostrar el mensaje
                if (window.history.replaceState) {
                    const url = window.location.origin + window.location.pathname;
                    window.history.replaceState(null, null, url);
                }
            });
        }

        // Verificar registro exitoso
        if (urlParams.get('registrado') === '1') {
            Swal.fire({
                icon: 'success',
                title: '¡Usuario registrado!',
                text: 'El usuario ha sido registrado exitosamente.'
            }).then(() => {
                // Limpiar la URL después de mostrar el mensaje
                if (window.history.replaceState) {
                    const url = window.location.origin + window.location.pathname;
                    window.history.replaceState(null, null, url);
                }
            });
        }

        // Verificar modificación exitosa
        if (urlParams.get('modificado') === '1') {
            Swal.fire({
                icon: 'success',
                title: '¡Modificación exitosa!',
                text: 'Los cambios se han guardado correctamente.'
            }).then(() => {
                // Limpiar la URL después de mostrar el mensaje
                if (window.history.replaceState) {
                    const url = window.location.origin + window.location.pathname;
                    window.history.replaceState(null, null, url);
                }
            });
        }

        // Verificar eliminación exitosa
        if (urlParams.get('eliminado') === '1') {
            Swal.fire({
                icon: 'success',
                title: '¡Eliminación exitosa!',
                text: 'El usuario ha sido eliminado correctamente.'
            }).then(() => {
                // Limpiar la URL después de mostrar el mensaje
                if (window.history.replaceState) {
                    const url = window.location.origin + window.location.pathname;
                    window.history.replaceState(null, null, url);
                }
            });
        }

        function confirmarEliminacion(id) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esta acción!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "crudGestor.php?idusu=" + id + "&opoculto=Eliminar";
                }
            });
        }
    </script>
</body>
</html>

<?php
}else{
    header("Location: error.html");
}
?>