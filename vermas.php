<?php


include("setup/config.php");
$idpro = intval($_GET['idpro']);
$sql = "SELECT p.*, tp.tipo as tipo_propiedad FROM propiedades p LEFT JOIN tipo_propiedad tp ON p.idtipo_propiedad = tp.idtipo_propiedad WHERE p.idpropiedades = '$idpro' LIMIT 1";
$result = mysqli_query(conectar(), $sql);
$prop = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($prop['titulopropiedad']); ?> | PNK INMOBILIARIA</title>
    <link rel="stylesheet" href="css/inicio.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .detalle-propiedad {
            max-width: 1200px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 30px 30px 10px 30px;
            font-family: Arial, sans-serif;
        }
        .detalle-titulo {
            font-size: 2rem;
            font-weight: bold;
            color: #222;
        }
        .detalle-descripcion {
            color: #444;
            margin-bottom: 20px;
        }
        .detalle-iconos {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 20px;
        }
        .detalle-icono {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            color: #222;
            min-width: 160px;
        }
        .detalle-icono i {
            font-size: 1.5rem;
            color: #AC1754;
        }
        .detalle-precio {
            font-size: 2rem;
            color: #e72b73;
            font-weight: bold;
            margin-bottom: 20px;
        }
        @media (max-width: 900px) {
            .detalle-propiedad { padding: 15px; }
            .detalle-iconos { gap: 15px; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<header class="header">
    <div class="header-izquierda">
        <img src="img/Logo.png" alt="Logo PNK" class="logo">
        <div class="titulo">PNK INMOBILIARIA</div>
    </div>
    <nav class="header-derecha">
        <a href="index.php" class="header-botones">Inicio</a>
        <a href="registro_propietario.php" class="header-botones">Registro Propietario</a>
        <a href="registro_gestor.php" class="header-botones">Registro Gestor</a>
    </nav>
</header>
<main>
    <div class="detalle-propiedad">
        <div class="detalle-titulo"><?php echo htmlspecialchars($prop['titulopropiedad']); ?></div>
        <div class="detalle-descripcion"><?php echo nl2br(htmlspecialchars($prop['descripcion'])); ?></div>
        <div class="detalle-precio">CLP $ <?php echo number_format($prop['precio_pesos'], 0, ',', '.'); ?></div>
        <div class="detalle-iconos">
            <?php
            $tipoPropiedad = strtolower($prop['tipo_propiedad']);
            $esTerreno = strpos($tipoPropiedad, 'terreno') !== false;
            $esDepartamento = strpos($tipoPropiedad, 'departamento') !== false;
            ?>
            
            <!-- Información básica -->
            <div class="detalle-icono"><i class="fa-solid fa-home"></i> <?php echo htmlspecialchars($prop['tipo_propiedad']); ?></div>
            <div class="detalle-icono"><i class="fa-solid fa-expand-arrows-alt"></i> <?php echo number_format($prop['area_total'], 0, ',', '.'); ?> m² totales</div>
            
            <!-- Características de construcción -->
            <div class="detalle-icono">
                <i class="fa-solid fa-ruler-combined"></i> 
                <?php echo $prop['area_construida'] > 0 ? number_format($prop['area_construida'], 0, ',', '.') : '0'; ?> m² construidos
            </div>
            
            <div class="detalle-icono">
                <i class="fa-solid fa-bed"></i> 
                <?php if ($esTerreno): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['cant_domitorios'] > 0 ? intval($prop['cant_domitorios']) : '0'; ?> dormitorios
                <?php endif; ?>
            </div>
            
            <div class="detalle-icono">
                <i class="fa-solid fa-bath"></i> 
                <?php if ($esTerreno): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['cant_banos'] > 0 ? intval($prop['cant_banos']) : '0'; ?> baños
                <?php endif; ?>
            </div>

            <!-- Características adicionales -->
            <div class="detalle-icono">
                <i class="fa-solid fa-car"></i> Estacionamiento: 
                <?php if ($esTerreno): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['estacionamiento'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </div>
            
            <div class="detalle-icono">
                <i class="fa-solid fa-warehouse"></i> Bodega: 
                <?php if ($esTerreno): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['bodega'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </div>
            
            <div class="detalle-icono">
                <i class="fa-solid fa-door-open"></i> Logia: 
                <?php if ($esTerreno): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['logia'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </div>
            
            <div class="detalle-icono">
                <i class="fa-solid fa-utensils"></i> Cocina Amoblada: 
                <?php if ($esTerreno): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['cocinaamoblada'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </div>
            
            <div class="detalle-icono">
                <i class="fa-solid fa-person-swimming"></i> Piscina: 
                <?php if ($esTerreno): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['piscina'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </div>
            
            <!-- Características de exterior -->
            <div class="detalle-icono">
                <i class="fa-solid fa-seedling"></i> Antejardín: 
                <?php if ($esTerreno || $esDepartamento): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['antejardin'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </div>
            
            <div class="detalle-icono">
                <i class="fa-solid fa-tree"></i> Patio Trasero: 
                <?php if ($esTerreno || $esDepartamento): ?>
                    <i class="fa-solid fa-ban text-danger"></i> No cuenta
                <?php else: ?>
                    <?php echo $prop['patiotrasero'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </div>

            <!-- Precio en UF -->
            <div class="detalle-icono"><i class="fa-solid fa-coins"></i> <?php echo $prop['precio_uf'] > 0 ? number_format($prop['precio_uf'], 0, ',', '.') : '0'; ?> UF</div>
        </div>
        <!-- Galería (no modificar) -->
        <div id="carruzel">
            <div id="demo" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php
                        $sql="SELECT * FROM galeria where idpropiedades = '$idpro'";
                        $result = mysqli_query(conectar(), $sql);
                        $c = 0;
                        while($datos = mysqli_fetch_array($result)) {
                    ?>
                    <button type="button" data-bs-target="#demo" data-bs-slide-to="<?php echo $c;?>" class="<?php if($datos['principal'] == 1) { ?> active <?php } ?>"></button>
                    <?php $c++; } ?>
                </div>
                <div class="carousel-inner">
                <?php
                        $sql1="SELECT * FROM galeria where idpropiedades = '$idpro'";
                        $result1 = mysqli_query(conectar(), $sql1);
                        while($datos1 = mysqli_fetch_array($result1)) {
                ?>
                    <div class="carousel-item <?php if($datos1['principal'] == 1) { ?> active <?php } ?>">
                        <img src="propiedades/<?php echo $datos1['foto'];?>"  class="d-block w-100">
                    </div>
                <?php } ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </div>
</main>
<footer class="footer">
    <div class="footer-izquierda">
        <img src="img/Logo.png" alt="Logo PNK" class="logo-footer">
        <div class="titulo-footer">PNK INMOBILIARIA</div>
    </div>
    <nav class="footer-centro">
        <a href="registro_propietario.php" class="enlace-footer">Registro Propietario</a>
        <a href="registro_gestor.php" class="enlace-footer">Registro Gestor</a>
        <a href="registro_usuario.html" class="enlace-footer">Registro Usuario</a>
    </nav>
    <nav class="footer-derecha">
        <a href="https://www.instagram.com/tioreneoficial_/?hl=es" target="_blank">
            <img src="img/logo-insta.png" alt="Logo instagram" class="logo-footer">
        </a>
        <a href="https://www.ticketmaster.cl/event/popin-un-show-muy-penca-centro-cultural-san-gines" target="_blank">
            <img src="img/linkedin.png" alt="Logo Linkedin" class="logo-footer">
        </a>
    </nav>
</footer>
<div class="copyright">
    &copy; 2025 Todos los derechos Reservados PNK Inmobiliaria
</div>
</body>
</html>