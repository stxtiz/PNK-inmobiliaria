<?php


include("setup/config.php"); // Incluye el archivo de configuración para la conexión a la base de datos

$sql = "SELECT * FROM usuarios where usuario='".$_POST['usuario']."'";
$result = mysqli_query(conectar(), $sql); // Ejecuta la consulta SQL "query"
$datos = mysqli_fetch_array($result); // Obtiene el resultado de la consulta SQL 
$contador = mysqli_num_rows($result); // Cuenta el número de filas devueltas por la consulta SQL

if ($contador == 0) {
    // El usuario no existe
    header("Location: index.php?error=usuario");
} else if (!password_verify($_POST['password'], $datos['clave'])) {
    // La contraseña es incorrecta
    header("Location: index.php?error=password");
} else if ($datos['estado'] == 0) {
    // El usuario está inactivo
    header("Location: index.php?error=inactivo");
} else {
    // Login exitoso
    session_start(); // Inicia la sesión
    $_SESSION['usuario'] = $datos['usuario']; // Almacena el email del usuario en la sesión
    $_SESSION['nombres'] = $datos['nombres']; // Almacena el nombre del usuario en la sesión
    $_SESSION['tipo'] = $datos['tipo']; // Almacena el tipo de usuario en la sesión
    $_SESSION['id'] = $datos['id']; // Almacena el ID del usuario en la sesión
    
    header("Location: procesalogin.php");
}

?>