<?php
session_start();

// Asegura que haya una sesión válida
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: index.php?error=sesion");
    exit();
}

switch ($_SESSION['tipo']) {
    case 3: // Admin
        header("Location: dashboard2.php?login=exito");
        break;
    case 2: // Dueño de inmueble
        header("Location: mis_propiedades.php?login=exito");
        break;
    default: // Otro tipo
        header("Location: index.php?login=sin_redireccion");
        break;
}

exit();
?>