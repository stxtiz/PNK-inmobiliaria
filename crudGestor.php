<?php
include("setup/config.php");

if(isset($_GET['idusu']))
{
    $sql="DELETE FROM usuarios WHERE id = ".intval($_GET['idusu']);
    $con = conectar();
    if(!mysqli_query($con,$sql)){
        error_log("crudGestor.php - Error al eliminar usuario: " . mysqli_error($con));
    }
    header("Location: dashboard.php?eliminado=1");
    exit();
}

switch($_POST['opoculto']){
    case "Ingresar": ingresar();
        break;
    case "Modificar": modificar();
        break;
    case "Eliminar": eliminar();
        break;
    case "Cancelar": cancelar();
        break;
}

function ingresar()
{
    try {
        $con = conectar();
        error_log("crudGestor.php - Inicio de función ingresar");

        // Validar que el archivo sea PDF
        if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
            $fileType = $_FILES['frm_certificado']['type'];
            error_log("crudGestor.php - Tipo de archivo certificado: " . $fileType);
            if ($fileType !== 'application/pdf') {
                error_log("crudGestor.php - Archivo certificado no es PDF");
                header("Location: dashboard.php?error=pdf_only");
                exit();
            }
        }

        // Verificar si el correo ya existe
        $correo = mysqli_real_escape_string($con, $_POST['usuario']);
        error_log("crudGestor.php - Verificando correo: " . $correo);
        $query_verificar = "SELECT COUNT(*) as total FROM usuarios WHERE usuario = '$correo'";
        $result_verificar = mysqli_query($con, $query_verificar);

        if (!$result_verificar) {
            error_log("crudGestor.php - Error en consulta de verificación: " . mysqli_error($con));
            header("Location: dashboard.php?error=db_error");
            exit();
        }

        $row_verificar = mysqli_fetch_assoc($result_verificar);

        if ($row_verificar['total'] > 0) {
            error_log("crudGestor.php - Correo ya existe");
            header("Location: dashboard.php?error=email_exists");
            exit();
        }

        // Si el correo no existe, proceder con el registro
        $rut = mysqli_real_escape_string($con, $_POST['rut']);
        $nombres = mysqli_real_escape_string($con, $_POST['nombres']);
        $appaterno = mysqli_real_escape_string($con, $_POST['appaterno']);
        $apmaterno = mysqli_real_escape_string($con, $_POST['apmaterno']);
        $clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
        $sexo = mysqli_real_escape_string($con, $_POST['sexo']);
        $estado = mysqli_real_escape_string($con, $_POST['estado']);
        $telefono = mysqli_real_escape_string($con, $_POST['telefono']);
        $fechanacimiento = mysqli_real_escape_string($con, $_POST['fechanacimiento']);
        $tipo = mysqli_real_escape_string($con, $_POST['tipo']);

        // Condicional para npropiedad y certificado según tipo
        $npropiedad = 0;
        $certificado = '';

        if ($tipo == '2') { // Propietario necesita npropiedad
            $npropiedad = intval(mysqli_real_escape_string($con, $_POST['npropiedad']));
        } else {
            $npropiedad = 0;
        }

        if ($tipo == '1') { // Gestor Free necesita certificado
            if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
                $certificado = mysqli_real_escape_string($con, $_FILES['frm_certificado']['name']);
            } else {
                $certificado = '';
            }
        } else {
            $certificado = '';
        }

        $sql = "INSERT INTO usuarios (rut, nombres, ap_paterno, ap_materno, usuario, clave, sexo, estado, npropiedad, certificado, telefono, fechanacimiento, tipo) 
                VALUES ('$rut', '$nombres', '$appaterno', '$apmaterno', '$correo', '$clave', '$sexo', '$estado', $npropiedad, '$certificado', '$telefono', '$fechanacimiento', '$tipo')";
        error_log("crudGestor.php - Consulta SQL: " . $sql);

        $result = mysqli_query($con, $sql);

        if ($result) {
            error_log("crudGestor.php - Registro exitoso");
            if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
                if (!move_uploaded_file($_FILES['frm_certificado']['tmp_name'], "file/certificados/" . $_FILES['frm_certificado']['name'])) {
                    error_log("crudGestor.php - Error al mover archivo certificado: " . $_FILES['frm_certificado']['error']);
                } else {
                    error_log("crudGestor.php - Archivo certificado movido correctamente");
                }
            }
            header("Location: dashboard.php?registrado=1");
        } else {
            error_log("crudGestor.php - Error en inserción: " . mysqli_error($con));
            header("Location: dashboard.php?error=db_error");
        }
        exit();
    } catch (Exception $e) {
        error_log("crudGestor.php - Excepción: " . $e->getMessage());
        header("Location: dashboard.php?error=db_error");
        exit();
    }
}

function modificar()
{
    try {
        $con = conectar();
        error_log("crudGestor.php - Inicio de función modificar");

        // Verificar si el correo ya existe (excluyendo el correo actual del usuario)
        $correo = mysqli_real_escape_string($con, $_POST['usuario']);
        $id = mysqli_real_escape_string($con, $_POST['idoculto']);
        error_log("crudGestor.php - Verificando correo para modificar: " . $correo . ", id: " . $id);

        $query_verificar = "SELECT COUNT(*) as total FROM usuarios WHERE usuario = '$correo' AND id != '$id'";
        $result_verificar = mysqli_query($con, $query_verificar);

        if (!$result_verificar) {
            error_log("crudGestor.php - Error en consulta de verificación modificar: " . mysqli_error($con));
            header("Location: dashboard.php?error=db_error");
            exit();
        }

        $row_verificar = mysqli_fetch_assoc($result_verificar);

        if ($row_verificar['total'] > 0) {
            error_log("crudGestor.php - Correo ya existe en modificar");
            header("Location: dashboard.php?error=email_exists");
            exit();
        }

        // Condicional para npropiedad y certificado según tipo
        $npropiedad = 0;
        $certificado = '';

        $tipo = mysqli_real_escape_string($con, $_POST['tipo']);

        if ($tipo == '2') { // Propietario necesita npropiedad
            $npropiedad = intval(mysqli_real_escape_string($con, $_POST['npropiedad']));
        } else {
            $npropiedad = 0;
        }

        if ($tipo == '1') { // Gestor Free necesita certificado
            if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
                $certificado = mysqli_real_escape_string($con, $_FILES['frm_certificado']['name']);
            } else {
                $certificado = '';
            }
        } else {
            $certificado = '';
        }

        // Validar que el archivo sea PDF si se sube uno nuevo
        if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
            $fileType = $_FILES['frm_certificado']['type'];
            error_log("crudGestor.php - Tipo de archivo certificado modificar: " . $fileType);
            if ($fileType !== 'application/pdf') {
                error_log("crudGestor.php - Archivo certificado no es PDF modificar");
                header("Location: dashboard.php?error=pdf_only");
                exit();
            }
        }

        if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
            $sql = "UPDATE usuarios SET rut = '" . mysqli_real_escape_string($con, $_POST['rut']) . "', nombres = '" . mysqli_real_escape_string($con, $_POST['nombres']) . "', ap_paterno = '" . mysqli_real_escape_string($con, $_POST['appaterno']) . "', ap_materno = '" . mysqli_real_escape_string($con, $_POST['apmaterno']) . "', usuario = '" . mysqli_real_escape_string($con, $_POST['usuario']) . "', sexo = '" . mysqli_real_escape_string($con, $_POST['sexo']) . "', estado = '" . mysqli_real_escape_string($con, $_POST['estado']) . "', npropiedad = $npropiedad, certificado = '$certificado', telefono = '" . mysqli_real_escape_string($con, $_POST['telefono']) . "', fechanacimiento = '" . mysqli_real_escape_string($con, $_POST['fechanacimiento']) . "', tipo = '" . $tipo . "' WHERE id =" . intval($_POST['idoculto']);
            if (!move_uploaded_file($_FILES['frm_certificado']['tmp_name'], "file/certificados/" . $_FILES['frm_certificado']['name'])) {
                error_log("crudGestor.php - Error al mover archivo certificado modificar: " . $_FILES['frm_certificado']['error']);
            } else {
                error_log("crudGestor.php - Archivo certificado movido correctamente modificar");
            }
        } else {
            $sql = "UPDATE usuarios SET rut = '" . mysqli_real_escape_string($con, $_POST['rut']) . "', nombres = '" . mysqli_real_escape_string($con, $_POST['nombres']) . "', ap_paterno = '" . mysqli_real_escape_string($con, $_POST['appaterno']) . "', ap_materno = '" . mysqli_real_escape_string($con, $_POST['apmaterno']) . "', usuario = '" . mysqli_real_escape_string($con, $_POST['usuario']) . "', sexo = '" . mysqli_real_escape_string($con, $_POST['sexo']) . "', estado = '" . mysqli_real_escape_string($con, $_POST['estado']) . "', npropiedad = $npropiedad, telefono = '" . mysqli_real_escape_string($con, $_POST['telefono']) . "', fechanacimiento = '" . mysqli_real_escape_string($con, $_POST['fechanacimiento']) . "', tipo = '" . $tipo . "' WHERE id =" . intval($_POST['idoculto']);
        }

        error_log("crudGestor.php - Consulta SQL modificar: " . $sql);

        $result = mysqli_query($con, $sql);

        if ($result) {
            error_log("crudGestor.php - Modificación exitosa");
            header("Location: dashboard.php?modificado=1");
        } else {
            error_log("crudGestor.php - Error en actualización: " . mysqli_error($con));
            header("Location: dashboard.php?error=db_error");
        }
        exit();
    } catch (Exception $e) {
        error_log("crudGestor.php - Excepción: " . $e->getMessage());
        header("Location: dashboard.php?error=db_error");
        exit();
    }
}

function eliminar()
{
    $con = conectar();
    $sql="DELETE FROM usuarios WHERE id = ".intval($_POST['idoculto']);
    if(!mysqli_query($con,$sql)){
        error_log("crudGestor.php - Error al eliminar usuario: " . mysqli_error($con));
    }
    header("Location: dashboard.php?eliminado=1");
    exit();
}

function cancelar()
{
    header("Location: dashboard.php");
    exit();
}