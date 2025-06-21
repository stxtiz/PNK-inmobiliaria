<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Conexión y Diagnóstico</h2>";

// 1. Verificar extensiones PHP
echo "<h3>Extensiones PHP:</h3>";
echo "mysqli: " . (extension_loaded('mysqli') ? 'Instalada' : 'NO instalada') . "<br>";
echo "gd: " . (extension_loaded('gd') ? 'Instalada' : 'NO instalada') . "<br>";

// 2. Verificar permisos de directorios
echo "<h3>Permisos de directorios:</h3>";
$dirs = ['propiedades', 'img'];
foreach($dirs as $dir) {
    if(is_dir($dir)) {
        echo "$dir/: " . substr(sprintf('%o', fileperms($dir)), -4) . "<br>";
        echo "$dir/ es escribible: " . (is_writable($dir) ? 'Sí' : 'No') . "<br>";
    } else {
        echo "$dir/: No existe<br>";
    }
}

// 3. Probar conexión a base de datos
echo "<h3>Conexión a base de datos:</h3>";
include("setup/config.php");
try {
    $conexion = conectar();
    echo "Conexión exitosa<br>";
    
    // Probar consulta simple
    $sql = "SELECT COUNT(*) as total FROM propiedades WHERE estado = 1";
    $result = mysqli_query($conexion, $sql);
    if($result) {
        $row = mysqli_fetch_assoc($result);
        echo "Total de propiedades activas: " . $row['total'] . "<br>";
    }
    
    // Verificar tablas necesarias
    $tablas = ['propiedades', 'galeria', 'tipo_propiedad', 'regiones', 'provincias', 'comunas', 'sectores'];
    foreach($tablas as $tabla) {
        $result = mysqli_query($conexion, "SHOW TABLES LIKE '$tabla'");
        echo "Tabla $tabla: " . (mysqli_num_rows($result) > 0 ? 'Existe' : 'NO existe') . "<br>";
    }
    
} catch (Exception $e) {
    echo "Error de conexión: " . $e->getMessage() . "<br>";
}

// 4. Verificar rutas de archivos
echo "<h3>Rutas de archivos:</h3>";
echo "Directorio actual: " . getcwd() . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// 5. Verificar configuración del servidor
echo "<h3>Configuración del servidor:</h3>";
echo "PHP version: " . phpversion() . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
?>
