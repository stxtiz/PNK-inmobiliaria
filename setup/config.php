<?php

// Configuración de la base de datos
// IMPORTANTE: Actualiza estos valores con los datos de tu servidor AWS
define('DB_HOST', 'localhost');  // Cambia por tu endpoint de AWS
define('DB_USER', 'root');       // Cambia por tu usuario de AWS
define('DB_PASS', '');           // Cambia por tu contraseña de AWS
define('DB_NAME', 'penka');      // Cambia por el nombre de tu base de datos en AWS
define('DB_PORT', 3306);         // Puerto de la base de datos

// Include centralized error handler
require_once __DIR__ . '/error_handler.php';

function conectar() 
{
    // Habilitar reporte de errores de MySQL
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try {
        $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        
        // Verificar la conexión
        if (!$con) {
            ErrorHandler::logError("Database connection failed: " . mysqli_connect_error(), ErrorHandler::LEVEL_CRITICAL, 'config.php');
            die("Error de conexión a la base de datos");
        }
        
        // Establecer charset UTF-8
        mysqli_set_charset($con, "utf8");
        
        return $con;
    } catch (Exception $e) {
        ErrorHandler::logError("Database connection exception: " . $e->getMessage(), ErrorHandler::LEVEL_CRITICAL, 'config.php');
        die("Error de conexión a la base de datos");
    }
}

function contarusu()
{
    try {
        $sql = "select * from usuarios";
        $result = mysqli_query(conectar(), $sql);
        
        if (!$result) {
            ErrorHandler::logError("Error in contarusu query: " . mysqli_error(conectar()), ErrorHandler::LEVEL_ERROR, 'config.php');
            return 0;
        }
        
        $contador = mysqli_num_rows($result);
        return $contador;
    } catch (Exception $e) {
        ErrorHandler::logError("Exception in contarusu: " . $e->getMessage(), ErrorHandler::LEVEL_ERROR, 'config.php');
        return 0;
    }
}

// Función para verificar la conexión y las tablas
function verificarBaseDatos() {
    try {
        $con = conectar();
        
        // Verificar que existan las tablas necesarias
        $tablas = ['regiones', 'provincias', 'comunas', 'sectores'];
        $resultados = [];
        
        foreach ($tablas as $tabla) {
            $sql = "SELECT COUNT(*) as total FROM $tabla";
            $result = mysqli_query($con, $sql);
            
            if ($result) {
                $row = mysqli_fetch_array($result);
                $resultados[$tabla] = $row['total'];
            } else {
                $resultados[$tabla] = "ERROR: " . mysqli_error($con);
            }
        }
        
        return $resultados;
    } catch (Exception $e) {
        return ["error" => $e->getMessage()];
    }
}

