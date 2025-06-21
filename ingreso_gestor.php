<?php
session_start();
include("setup/config.php");
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
        $rut = $_POST['rut'];
        $nombres = $_POST['nombres'];
        $appaterno = $_POST['appaterno'];
        $apmaterno = $_POST['apmaterno'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['usuario'];
        $clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
        $fechanacimiento = $_POST['fechanacimiento'];
        $sexo = $_POST['sexo'];
        $certificado = $_FILES['frm_certificado']['name'];

        // Validar que el archivo sea PDF
        if ($_FILES['frm_certificado']['name'] != '') {
            $fileType = $_FILES['frm_certificado']['type'];
            if ($fileType !== 'application/pdf') {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Tipo de archivo inválido',
                        text: 'Solo se permiten archivos PDF'
                    }).then(function() {
                        window.location = 'registro_gestor.php';
                    });
                </script>";
                exit();
            }
        }

        // Verificar si el correo ya existe
        $query_verificar = "SELECT COUNT(*) as total FROM usuarios WHERE usuario = '$correo'";
        $result_verificar = mysqli_query(conectar(), $query_verificar);
        $row_verificar = mysqli_fetch_assoc($result_verificar);

        if($row_verificar['total'] > 0) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Correo ya registrado',
                    text: 'Este correo electrónico ya está registrado en el sistema'
                }).then(function() {
                    window.location = 'registro_gestor.php';
                });
            </script>";
            exit();
        }

        // Si el correo no existe, continuar con el registro
        $query = "INSERT INTO usuarios (rut, nombres, ap_paterno, ap_materno, usuario, clave, sexo, estado, npropiedad, telefono, fechanacimiento, tipo, certificado) 
                 VALUES ('$rut', '$nombres', '$appaterno', '$apmaterno', '$correo', '$clave', '$sexo', '0', 'NULL', '$telefono', '$fechanacimiento', '1', '$certificado')";
        $result = mysqli_query(conectar(), $query);
        move_uploaded_file($_FILES['frm_certificado']['tmp_name'], "file/certificados/".$_FILES['frm_certificado']['name']);
        if($result) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Registro exitoso',
                    text: 'El Gestor ha sido registrado correctamente'
                }).then(function() {
                    window.location = 'index.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al registrar el Gestor'
                }).then(function() {
                    window.location = 'registro_gestor.php';
                });
            </script>";
        }
    }
}

function cancelarP()
{
    header("Location: registro_gestor.php");
}
?>
</body>
</html>