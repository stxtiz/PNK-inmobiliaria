<?php
// Habilitar reporte de errores para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Log de inicio
error_log("filtrar_propiedades.php - Inicio de ejecución");

try {
    include("setup/config.php");
    error_log("filtrar_propiedades.php - Config incluido correctamente");
} catch (Exception $e) {
    error_log("filtrar_propiedades.php - Error al incluir config: " . $e->getMessage());
    die("Error de configuración");
}

// Configuración de paginación
$propiedadesPorPagina = 6; // 3x2 grid
$paginaActual = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
$offset = ($paginaActual - 1) * $propiedadesPorPagina;

$where = [];
if (!empty($_POST['tipo']) && $_POST['tipo'] != '0') {
    $where[] = "p.idtipo_propiedad = '".intval($_POST['tipo'])."'";
}
if (!empty($_POST['region']) && $_POST['region'] != '0') {
    $where[] = "r.idregiones = '".intval($_POST['region'])."'";
}
if (!empty($_POST['provincia']) && $_POST['provincia'] != '0') {
    $where[] = "pr.idprovincias = '".intval($_POST['provincia'])."'";
}
if (!empty($_POST['comuna']) && $_POST['comuna'] != '0') {
    $where[] = "c.idcomunas = '".intval($_POST['comuna'])."'";
}
if (!empty($_POST['sector']) && $_POST['sector'] != '0') {
    $where[] = "s.idsectores = '".intval($_POST['sector'])."'";
}
$where[] = "p.estado = 1";

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Consulta para contar el total de propiedades
$sqlCount = "SELECT COUNT(*) as total
FROM propiedades p
LEFT JOIN sectores s ON p.sectores_idsectores = s.idsectores
LEFT JOIN comunas c ON s.idcomunas = c.idcomunas
LEFT JOIN provincias pr ON c.idprovincias = pr.idprovincias
LEFT JOIN regiones r ON pr.idregiones = r.idregiones
LEFT JOIN tipo_propiedad tp ON p.idtipo_propiedad = tp.idtipo_propiedad
$where_sql";

try {
    $conexion = conectar();
    $resultCount = mysqli_query($conexion, $sqlCount);
    
    if (!$resultCount) {
        ErrorHandler::logError("Error in property count query: " . mysqli_error($conexion), ErrorHandler::LEVEL_ERROR, 'filtrar_propiedades.php', ['sql' => $sqlCount]);
        $totalPropiedades = 0;
        $totalPaginas = 1;
    } else {
        $row = mysqli_fetch_assoc($resultCount);
        $totalPropiedades = $row['total'];
        $totalPaginas = ceil($totalPropiedades / $propiedadesPorPagina);
        ErrorHandler::logError("Property count successful: $totalPropiedades properties, $totalPaginas pages", ErrorHandler::LEVEL_DEBUG, 'filtrar_propiedades.php');
    }
} catch (Exception $e) {
    ErrorHandler::logError("Exception in property count: " . $e->getMessage(), ErrorHandler::LEVEL_ERROR, 'filtrar_propiedades.php');
    $totalPropiedades = 0;
    $totalPaginas = 1;
}

// Consulta principal con LIMIT para paginación
$sql = "SELECT p.idpropiedades, p.titulopropiedad AS titulo, p.precio_uf, p.precio_pesos, g.foto, tp.tipo as tipo_propiedad
FROM propiedades p
LEFT JOIN galeria g ON p.idpropiedades = g.idpropiedades AND g.principal = 1
LEFT JOIN sectores s ON p.sectores_idsectores = s.idsectores
LEFT JOIN comunas c ON s.idcomunas = c.idcomunas
LEFT JOIN provincias pr ON c.idprovincias = pr.idprovincias
LEFT JOIN regiones r ON pr.idregiones = r.idregiones
LEFT JOIN tipo_propiedad tp ON p.idtipo_propiedad = tp.idtipo_propiedad
$where_sql
LIMIT $offset, $propiedadesPorPagina";

try {
    $conexion = conectar();
    error_log("filtrar_propiedades.php - Conexión establecida");
    
    $result = mysqli_query($conexion, $sql);
    
    if (!$result) {
        ErrorHandler::logError("Error in main property query: " . mysqli_error($conexion), ErrorHandler::LEVEL_ERROR, 'filtrar_propiedades.php', ['sql' => $sql]);
        echo "<div class='alert alert-danger'>Error al cargar propiedades. Por favor, intente nuevamente.</div>";
        exit;
    }
    
    ErrorHandler::logError("Property query successful, rows: " . mysqli_num_rows($result), ErrorHandler::LEVEL_DEBUG, 'filtrar_propiedades.php');
    
} catch (Exception $e) {
    ErrorHandler::logError("Exception in property query: " . $e->getMessage(), ErrorHandler::LEVEL_ERROR, 'filtrar_propiedades.php');
    echo "<div class='alert alert-danger'>Error al cargar propiedades. Por favor, intente nuevamente.</div>";
    exit;
}

echo '<div class="propiedades-grid">';
while($datos = mysqli_fetch_array($result)) {
    // Formatear precio sin usar numfmt_create (que requiere extensión intl)
    $precio = '$' . number_format($datos['precio_pesos'], 0, ',', '.');
    $foto = (!empty($datos['foto']) && file_exists('propiedades/' . $datos['foto'])) ? $datos['foto'] : 'defecto.jpg';
    echo '<div class="propiedad">';
    echo '<img src="propiedades/' . $foto . '" alt="' . htmlspecialchars($datos['titulo']) . '">';
    echo '<div class="info-propiedad">';
    echo '<h3>' . htmlspecialchars($datos['titulo']) . '</h3>';
    echo '<div class="tipo-propiedad uf">Tipo: ' . htmlspecialchars($datos['tipo_propiedad']) . '</div>';
    echo '<div class="precios">';
    echo '<span class="uf">UF ' . number_format($datos['precio_uf'], 0, ',', '.') . '</span>';
    echo '<span class="clp">' . $precio . '</span>';
    echo '</div>';
    echo '<center><a href="vermas.php?idpro=' . $datos['idpropiedades'] . '" class="btn-galeria">Ver Detalle</a></center>';
    echo '</div></div>';
}
echo '</div>';

// Paginación
if ($totalPaginas > 1) {
    echo '<div class="paginacion">';
    if ($paginaActual > 1) {
        echo '<button class="btn-pagina" onclick="cargarPagina('.($paginaActual-1).')">&laquo; Anterior</button>';
    }
    
    for ($i = 1; $i <= $totalPaginas; $i++) {
        $clase = $i == $paginaActual ? 'btn-pagina active' : 'btn-pagina';
        echo '<button class="'.$clase.'" onclick="cargarPagina('.$i.')">'.$i.'</button>';
    }
    
    if ($paginaActual < $totalPaginas) {
        echo '<button class="btn-pagina" onclick="cargarPagina('.($paginaActual+1).')">Siguiente &raquo;</button>';
    }
    echo '</div>';
}
?>
