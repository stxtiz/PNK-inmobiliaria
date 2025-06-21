<?php
// Script de diagnóstico para identificar problemas
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico del Sistema</h2>";

// 1. Verificar configuración PHP
echo "<h3>1. Configuración PHP</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQL Extension: " . (extension_loaded('mysqli') ? 'Cargada' : 'NO CARGADA') . "<br>";
echo "Error Reporting: " . error_reporting() . "<br>";

// 2. Verificar archivos
echo "<h3>2. Verificación de Archivos</h3>";
$archivos = ['setup/config.php', 'filtrar_propiedades.php', 'js/jquery-3.7.1.min.js'];
foreach($archivos as $archivo) {
    echo "$archivo: " . (file_exists($archivo) ? 'Existe' : 'NO EXISTE') . "<br>";
}

// 3. Verificar directorio de imágenes
echo "<h3>3. Directorio de Propiedades</h3>";
echo "Directorio propiedades/: " . (is_dir('propiedades') ? 'Existe' : 'NO EXISTE') . "<br>";
if (is_dir('propiedades')) {
    echo "Permisos: " . substr(sprintf('%o', fileperms('propiedades')), -4) . "<br>";
    $imagenes = glob('propiedades/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    echo "Imágenes encontradas: " . count($imagenes) . "<br>";
}

// 4. Probar conexión a base de datos
echo "<h3>4. Conexión a Base de Datos</h3>";
try {
    include("setup/config.php");
    $con = conectar();
    echo "Conexión: EXITOSA<br>";
    
    // Verificar tablas
    $tablas = ['propiedades', 'galeria', 'tipo_propiedad', 'regiones', 'provincias', 'comunas', 'sectores'];
    foreach($tablas as $tabla) {
        $sql = "SELECT COUNT(*) as total FROM $tabla";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $row = mysqli_fetch_array($result);
            echo "Tabla $tabla: " . $row['total'] . " registros<br>";
        } else {
            echo "Tabla $tabla: ERROR - " . mysqli_error($con) . "<br>";
        }
    }
    
    // Probar consulta de propiedades
    echo "<h4>Consulta de Propiedades:</h4>";
    $sql = "SELECT p.idpropiedades, p.titulopropiedad, g.foto 
            FROM propiedades p 
            LEFT JOIN galeria g ON p.idpropiedades = g.idpropiedades AND g.principal = 1 
            WHERE p.estado = 1 
            LIMIT 3";
    $result = mysqli_query($con, $sql);
    if ($result) {
        echo "Consulta exitosa, " . mysqli_num_rows($result) . " propiedades encontradas<br>";
        while($row = mysqli_fetch_array($result)) {
            echo "- " . $row['titulopropiedad'] . " (Foto: " . ($row['foto'] ?: 'Sin foto') . ")<br>";
        }
    } else {
        echo "ERROR en consulta: " . mysqli_error($con) . "<br>";
    }
    
} catch (Exception $e) {
    echo "ERROR de conexión: " . $e->getMessage() . "<br>";
}

// 5. Verificar logs de error
echo "<h3>5. Logs de Error</h3>";
$error_log = ini_get('error_log');
echo "Archivo de log: " . ($error_log ?: 'No configurado') . "<br>";

if (file_exists('error.log')) {
    echo "error.log local existe<br>";
    $errors = file_get_contents('error.log');
    echo "<pre>" . htmlspecialchars(substr($errors, -1000)) . "</pre>";
}
?>
