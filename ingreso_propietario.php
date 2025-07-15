<?php
// Habilitar reporte de errores para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

session_start();

try {
    include("setup/config.php");
    error_log("ingreso_propietario.php - Config incluido correctamente");
} catch (Exception $e) {
    error_log("ingreso_propietario.php - Error al incluir config: " . $e->getMessage());
    die("Error de configuración");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
if(isset($_POST['opoculto'])) {
    $op = $_POST['opoculto'];
    
    if($op == "Ingresar") {
        try {
            $conexion = conectar();
            
            // Obtener y limpiar datos del formulario
            $rut = mysqli_real_escape_string($conexion, $_POST['rut']);
            $nombres = mysqli_real_escape_string($conexion, $_POST['nombres']);
            $appaterno = mysqli_real_escape_string($conexion, $_POST['appaterno']);
            $apmaterno = mysqli_real_escape_string($conexion, $_POST['apmaterno']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
            $correo = mysqli_real_escape_string($conexion, $_POST['usuario']);
            $clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
            $fechanacimiento = mysqli_real_escape_string($conexion, $_POST['fechanacimiento']);
            $sexo = mysqli_real_escape_string($conexion, $_POST['sexo']);
            $npropiedad = mysqli_real_escape_string($conexion, $_POST['npropiedad']);

            error_log("ingreso_propietario.php - Datos recibidos: RUT=$rut, Email=$correo");
            
            // Verificar si el correo ya existe
            $query_verificar = "SELECT COUNT(*) as total FROM usuarios WHERE usuario = '$correo'";
            $result_verificar = mysqli_query($conexion, $query_verificar);
            
            if (!$result_verificar) {
                $error = ErrorHandler::handleDatabaseError('email verification for property owner', mysqli_error($conexion), 'ingreso_propietario.php', $query_verificar);
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la base de datos',
                        text: 'Error en la base de datos. Por favor, intente nuevamente.'
                    }).then(function() {
                        window.location = 'registro_propietario.php';
                    });
                </script>";
                exit();
            }
            
            $row_verificar = mysqli_fetch_assoc($result_verificar);

            if($row_verificar['total'] > 0) {
                ErrorHandler::logError("Email already exists for property owner registration: " . $correo, ErrorHandler::LEVEL_WARNING, 'ingreso_propietario.php');
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Correo ya registrado',
                        text: 'Este correo electrónico ya está registrado en el sistema'
                    }).then(function() {
                        window.location = 'registro_propietario.php';
                    });
                </script>";
                exit();
            }

            // Si el correo no existe, continuar con el registro
$query = "INSERT INTO usuarios (rut, nombres, ap_paterno, ap_materno, usuario, clave, sexo, estado, npropiedad, telefono, fechanacimiento, tipo, certificado) 
         VALUES ('$rut', '$nombres', '$appaterno', '$apmaterno', '$correo', '$clave', '$sexo', '1', '$npropiedad', '$telefono', '$fechanacimiento', '2', '')";
            
            ErrorHandler::logError("Executing property owner registration", ErrorHandler::LEVEL_DEBUG, 'ingreso_propietario.php');
            $result = mysqli_query($conexion, $query);
            
            if($result) {
                ErrorHandler::logSuccess("Property owner registered successfully: " . $correo, 'ingreso_propietario.php');
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro exitoso',
                        text: 'El propietario ha sido registrado correctamente'
                    }).then(function() {
                        window.location = 'index.php';
                    });
                </script>";
            } else {
                $error = ErrorHandler::handleDatabaseError('property owner insertion', mysqli_error($conexion), 'ingreso_propietario.php', $query);
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error en la base de datos. Por favor, intente nuevamente.'
                    }).then(function() {
                        window.location = 'registro_propietario.php';
                    });
                </script>";
            }
            
        } catch (Exception $e) {
            error_log("ingreso_propietario.php - Excepción: " . $e->getMessage());
            $errorMsg = addslashes($e->getMessage());
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error del sistema',
                    html: 'Ha ocurrido un error interno. Detalles: <br><pre style=\"text-align:left;\">{$errorMsg}</pre>',
                    customClass: {
                        popup: 'swal2-popup-arial'
                    }
                }).then(function() {
                    window.location = 'registro_propietario.php';
                });
            </script>";
        }
    }
}

function cancelarP()
{
    header("Location: registro_propietario.php");
}
?>
</body>
</html>