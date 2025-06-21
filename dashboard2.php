<?php
session_start();
include("setup/config.php"); // Incluye el archivo de configuración para la conexión a la base de datos

// Manejar mensajes de error
if (isset($_GET['error']) && $_GET['error'] === 'pdf_only') {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Tipo de archivo inválido',
            text: 'Solo se permiten archivos PDF'
        });
    </script>";
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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/jquery.Rut.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <script>
        // Verificar si hay un mensaje de login exitoso
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('login') === 'exito') {
            Swal.fire({
                icon: 'success',
                title: '¡Sesión iniciada!',
                text: 'Has iniciado sesión correctamente.',
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
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>
    </header>

    <main class="main">
        <div class="dashboard">
            <div class="contenido-dashboard">
                <div class="texto-icono">
                    <span><img src="img/dash.png" alt="Dashboard">  Bienvenido <br><?php echo $_SESSION['usuario']; ?><br><?php echo $tipoTexto; ?></span>
                    </span>
                </div>
                <div class="texto-icono">
                    <img src="img/exit.png" alt="Cerrar sesión">
                    <a href="cerrar.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
        <div class="botones-panel">
            <a href= "dashboard.php" class="boton-icono">
                <img src="img/usuario.png" alt="Usuarios">
                <span>Mantenedor Usuarios</span>
            </a>
            <a href="crud_galeria.php" class="boton-icono">
                <img src="img/iccasa.png" alt="Propiedades">
                <span>Mantenedor Propiedades</span>
            </a>
        </div>
    </main>
</body>
</html>


<?php
}else{
    header("Location: error.html");
}
?>