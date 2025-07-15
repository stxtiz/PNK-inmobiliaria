<?php
include("setup/config.php");

if(isset($_GET['idusu']))
{
    $sql="DELETE FROM usuarios WHERE id = ".intval($_GET['idusu']);
    $con = conectar();
    if(!mysqli_query($con,$sql)){
        $error = ErrorHandler::handleDatabaseError('delete user', mysqli_error($con), 'crudGestor.php', $sql);
        ErrorHandler::logError("Failed to delete user ID: " . intval($_GET['idusu']), ErrorHandler::LEVEL_ERROR, 'crudGestor.php');
        header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
        exit();
    }
    ErrorHandler::logSuccess("User deleted successfully, ID: " . intval($_GET['idusu']), 'crudGestor.php');
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
        ErrorHandler::logError("Starting user registration process", ErrorHandler::LEVEL_INFO, 'crudGestor.php');

        // Validar que el archivo sea PDF
        if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
            $fileType = $_FILES['frm_certificado']['type'];
            ErrorHandler::logError("Certificate file type: " . $fileType, ErrorHandler::LEVEL_DEBUG, 'crudGestor.php');
            if ($fileType !== 'application/pdf') {
                ErrorHandler::logError("Invalid certificate file type: " . $fileType, ErrorHandler::LEVEL_WARNING, 'crudGestor.php');
                header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_INVALID_FILE));
                exit();
            }
        }

        // Verificar si el correo ya existe
        $correo = mysqli_real_escape_string($con, $_POST['usuario']);
        ErrorHandler::logError("Checking email existence: " . $correo, ErrorHandler::LEVEL_DEBUG, 'crudGestor.php');
        $query_verificar = "SELECT COUNT(*) as total FROM usuarios WHERE usuario = '$correo'";
        $result_verificar = mysqli_query($con, $query_verificar);

        if (!$result_verificar) {
            $error = ErrorHandler::handleDatabaseError('email verification', mysqli_error($con), 'crudGestor.php', $query_verificar);
            header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
            exit();
        }

        $row_verificar = mysqli_fetch_assoc($result_verificar);

        if ($row_verificar['total'] > 0) {
            ErrorHandler::logError("Email already exists: " . $correo, ErrorHandler::LEVEL_WARNING, 'crudGestor.php');
            header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_EMAIL_EXISTS));
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
        ErrorHandler::logError("Executing user insert SQL", ErrorHandler::LEVEL_DEBUG, 'crudGestor.php');

        $result = mysqli_query($con, $sql);

        if ($result) {
            ErrorHandler::logSuccess("User registered successfully: " . $correo, 'crudGestor.php', ['user_type' => $tipo]);
            if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
                if (!move_uploaded_file($_FILES['frm_certificado']['tmp_name'], "file/certificados/" . $_FILES['frm_certificado']['name'])) {
                    ErrorHandler::logError("Failed to move certificate file: " . $_FILES['frm_certificado']['error'], ErrorHandler::LEVEL_WARNING, 'crudGestor.php');
                } else {
                    ErrorHandler::logSuccess("Certificate file uploaded successfully", 'crudGestor.php');
                }
            }
            header("Location: dashboard.php?registrado=1");
        } else {
            $error = ErrorHandler::handleDatabaseError('user insertion', mysqli_error($con), 'crudGestor.php', $sql);
            header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
        }
        exit();
    } catch (Exception $e) {
        ErrorHandler::logError("Exception in ingresar function: " . $e->getMessage(), ErrorHandler::LEVEL_CRITICAL, 'crudGestor.php');
        header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
        exit();
    }
}

function modificar()
{
    try {
        $con = conectar();
        ErrorHandler::logError("Starting user modification process", ErrorHandler::LEVEL_INFO, 'crudGestor.php');

        // Verificar si el correo ya existe (excluyendo el correo actual del usuario)
        $correo = mysqli_real_escape_string($con, $_POST['usuario']);
        $id = mysqli_real_escape_string($con, $_POST['idoculto']);
        ErrorHandler::logError("Checking email for modification: " . $correo . ", id: " . $id, ErrorHandler::LEVEL_DEBUG, 'crudGestor.php');

        $query_verificar = "SELECT COUNT(*) as total FROM usuarios WHERE usuario = '$correo' AND id != '$id'";
        $result_verificar = mysqli_query($con, $query_verificar);

        if (!$result_verificar) {
            $error = ErrorHandler::handleDatabaseError('email verification for modification', mysqli_error($con), 'crudGestor.php', $query_verificar);
            header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
            exit();
        }

        $row_verificar = mysqli_fetch_assoc($result_verificar);

        if ($row_verificar['total'] > 0) {
            ErrorHandler::logError("Email already exists during modification: " . $correo, ErrorHandler::LEVEL_WARNING, 'crudGestor.php');
            header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_EMAIL_EXISTS));
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
            ErrorHandler::logError("Certificate file type for modification: " . $fileType, ErrorHandler::LEVEL_DEBUG, 'crudGestor.php');
            if ($fileType !== 'application/pdf') {
                ErrorHandler::logError("Invalid certificate file type during modification: " . $fileType, ErrorHandler::LEVEL_WARNING, 'crudGestor.php');
                header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_INVALID_FILE));
                exit();
            }
        }

        if (isset($_FILES['frm_certificado']) && $_FILES['frm_certificado']['name'] != '') {
            $sql = "UPDATE usuarios SET rut = '" . mysqli_real_escape_string($con, $_POST['rut']) . "', nombres = '" . mysqli_real_escape_string($con, $_POST['nombres']) . "', ap_paterno = '" . mysqli_real_escape_string($con, $_POST['appaterno']) . "', ap_materno = '" . mysqli_real_escape_string($con, $_POST['apmaterno']) . "', usuario = '" . mysqli_real_escape_string($con, $_POST['usuario']) . "', sexo = '" . mysqli_real_escape_string($con, $_POST['sexo']) . "', estado = '" . mysqli_real_escape_string($con, $_POST['estado']) . "', npropiedad = $npropiedad, certificado = '$certificado', telefono = '" . mysqli_real_escape_string($con, $_POST['telefono']) . "', fechanacimiento = '" . mysqli_real_escape_string($con, $_POST['fechanacimiento']) . "', tipo = '" . $tipo . "' WHERE id =" . intval($_POST['idoculto']);
            if (!move_uploaded_file($_FILES['frm_certificado']['tmp_name'], "file/certificados/" . $_FILES['frm_certificado']['name'])) {
                ErrorHandler::logError("Failed to move certificate file during modification: " . $_FILES['frm_certificado']['error'], ErrorHandler::LEVEL_WARNING, 'crudGestor.php');
            } else {
                ErrorHandler::logSuccess("Certificate file uploaded successfully during modification", 'crudGestor.php');
            }
        } else {
            $sql = "UPDATE usuarios SET rut = '" . mysqli_real_escape_string($con, $_POST['rut']) . "', nombres = '" . mysqli_real_escape_string($con, $_POST['nombres']) . "', ap_paterno = '" . mysqli_real_escape_string($con, $_POST['appaterno']) . "', ap_materno = '" . mysqli_real_escape_string($con, $_POST['apmaterno']) . "', usuario = '" . mysqli_real_escape_string($con, $_POST['usuario']) . "', sexo = '" . mysqli_real_escape_string($con, $_POST['sexo']) . "', estado = '" . mysqli_real_escape_string($con, $_POST['estado']) . "', npropiedad = $npropiedad, telefono = '" . mysqli_real_escape_string($con, $_POST['telefono']) . "', fechanacimiento = '" . mysqli_real_escape_string($con, $_POST['fechanacimiento']) . "', tipo = '" . $tipo . "' WHERE id =" . intval($_POST['idoculto']);
        }

        ErrorHandler::logError("Executing user update SQL", ErrorHandler::LEVEL_DEBUG, 'crudGestor.php');

        $result = mysqli_query($con, $sql);

        if ($result) {
            ErrorHandler::logSuccess("User modified successfully: " . $correo, 'crudGestor.php', ['user_id' => $id]);
            header("Location: dashboard.php?modificado=1");
        } else {
            $error = ErrorHandler::handleDatabaseError('user modification', mysqli_error($con), 'crudGestor.php', $sql);
            header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
        }
        exit();
    } catch (Exception $e) {
        ErrorHandler::logError("Exception in modificar function: " . $e->getMessage(), ErrorHandler::LEVEL_CRITICAL, 'crudGestor.php');
        header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
        exit();
    }
}

function eliminar()
{
    $con = conectar();
    $id = intval($_POST['idoculto']);
    $sql="DELETE FROM usuarios WHERE id = " . $id;
    
    if(!mysqli_query($con,$sql)){
        $error = ErrorHandler::handleDatabaseError('delete user via form', mysqli_error($con), 'crudGestor.php', $sql);
        ErrorHandler::logError("Failed to delete user ID: " . $id, ErrorHandler::LEVEL_ERROR, 'crudGestor.php');
        header("Location: " . ErrorHandler::generateErrorRedirect("dashboard.php", ErrorHandler::ERROR_DB_QUERY));
        exit();
    }
    
    ErrorHandler::logSuccess("User deleted successfully via form, ID: " . $id, 'crudGestor.php');
    header("Location: dashboard.php?eliminado=1");
    exit();
}

function cancelar()
{
    header("Location: dashboard.php");
    exit();
}