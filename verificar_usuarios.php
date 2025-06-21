<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Verificación de Tabla Usuarios</h2>";

include("setup/config.php");

try {
    $conexion = conectar();
    echo "✓ Conexión a base de datos exitosa<br><br>";
    
    // Verificar si la tabla usuarios existe
    $result = mysqli_query($conexion, "SHOW TABLES LIKE 'usuarios'");
    if (mysqli_num_rows($result) > 0) {
        echo "✓ Tabla 'usuarios' existe<br><br>";
        
        // Mostrar estructura de la tabla
        echo "<h3>Estructura de la tabla usuarios:</h3>";
        $result = mysqli_query($conexion, "DESCRIBE usuarios");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Contar registros existentes
        $result = mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuarios");
        $row = mysqli_fetch_array($result);
        echo "Total de usuarios registrados: " . $row['total'] . "<br><br>";
        
        // Mostrar algunos usuarios de ejemplo (sin mostrar contraseñas)
        echo "<h3>Usuarios existentes:</h3>";
        $result = mysqli_query($conexion, "SELECT rut, nombres, ap_paterno, usuario, tipo FROM usuarios LIMIT 5");
        if (mysqli_num_rows($result) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>RUT</th><th>Nombres</th><th>Apellido</th><th>Email</th><th>Tipo</th></tr>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['rut'] . "</td>";
                echo "<td>" . $row['nombres'] . "</td>";
                echo "<td>" . $row['ap_paterno'] . "</td>";
                echo "<td>" . $row['usuario'] . "</td>";
                echo "<td>" . $row['tipo'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No hay usuarios registrados aún.";
        }
        
    } else {
        echo "❌ La tabla 'usuarios' NO existe<br>";
        echo "Necesitas crear la tabla usuarios con la siguiente estructura:<br><br>";
        echo "<pre>";
        echo "CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rut VARCHAR(12) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    ap_paterno VARCHAR(50) NOT NULL,
    ap_materno VARCHAR(50) NOT NULL,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    sexo CHAR(1) NOT NULL,
    estado TINYINT DEFAULT 1,
    npropiedad VARCHAR(50),
    telefono VARCHAR(20),
    fechanacimiento DATE,
    tipo TINYINT NOT NULL
);";
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
