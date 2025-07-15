<?php
session_start();
include("setup/config.php");

if(isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    
    switch($accion) {
        case 'cargarProvincias':
            $region = $_GET['region'];
            $sql = "SELECT * FROM provincias WHERE idregiones = $region AND estado = 1";
            $result = mysqli_query(conectar(), $sql);
            $provincias = array();
            while($row = mysqli_fetch_array($result)) {
                $provincias[] = $row;
            }
            echo json_encode($provincias);
            break;

        case 'cargarComunas':
            $provincia = $_GET['provincia'];
            $sql = "SELECT * FROM comunas WHERE idprovincias = $provincia AND estado = 1";
            $result = mysqli_query(conectar(), $sql);
            $comunas = array();
            while($row = mysqli_fetch_array($result)) {
                $comunas[] = $row;
            }
            echo json_encode($comunas);
            break;

        case 'cargarSectores':
            $comuna = $_GET['comuna'];
            $sql = "SELECT * FROM sectores WHERE idcomunas = $comuna AND estado = 1";
            $result = mysqli_query(conectar(), $sql);
            $sectores = array();
            while($row = mysqli_fetch_array($result)) {
                $sectores[] = $row;
            }
            echo json_encode($sectores);
            break;

        case 'obtenerPropiedad':
            $id = $_GET['id'];
            $conn = conectar();
            
            // Obtener datos de la propiedad
            $sql = "SELECT p.*, s.idcomunas, c.idprovincias, pr.idregiones 
                   FROM propiedades p 
                   INNER JOIN sectores s ON p.sectores_idsectores = s.idsectores 
                   INNER JOIN comunas c ON s.idcomunas = c.idcomunas 
                   INNER JOIN provincias pr ON c.idprovincias = pr.idprovincias 
                   WHERE p.idpropiedades = $id";
            $result = mysqli_query($conn, $sql);
            $propiedad = mysqli_fetch_array($result);
            
            // Obtener imágenes asociadas
            $sqlImagenes = "SELECT * FROM galeria WHERE idpropiedades = $id AND estado = 1 ORDER BY principal DESC";
            $resultImagenes = mysqli_query($conn, $sqlImagenes);
            $imagenes = [];
            while($img = mysqli_fetch_array($resultImagenes)) {
                $imagenes[] = [
                    'id' => $img['idgaleria'],
                    'foto' => $img['foto'],
                    'principal' => $img['principal']
                ];
            }
            
            $propiedad['imagenes'] = $imagenes;
            echo json_encode($propiedad);
            break;

        case 'eliminarPropiedad':
            $id = $_GET['id'];
            $conn = conectar();

            // Primero obtener todas las imágenes asociadas
            $sqlImagenes = "SELECT foto FROM galeria WHERE idpropiedades = $id";
            $resultImagenes = mysqli_query($conn, $sqlImagenes);
            
            // Eliminar archivos físicos
            while($img = mysqli_fetch_array($resultImagenes)) {
                $rutaArchivo = 'propiedades/' . $img['foto'];
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }

            // Eliminar registros de galería
            $sqlGaleria = "DELETE FROM galeria WHERE idpropiedades = $id";
            $resultGaleria = mysqli_query($conn, $sqlGaleria);

            // Finalmente eliminar la propiedad
            $sqlPropiedad = "DELETE FROM propiedades WHERE idpropiedades = $id";
            $resultPropiedad = mysqli_query($conn, $sqlPropiedad);

            if (!$resultPropiedad) {
                $error = ErrorHandler::handleDatabaseError('delete property', mysqli_error($conn), 'procesa_propiedades.php', $sqlPropiedad);
                echo ErrorHandler::jsonResponse($error);
            } else {
                ErrorHandler::logSuccess("Property deleted successfully, ID: $id", 'procesa_propiedades.php');
                echo json_encode(['success' => true]);
            }
            break;

        case 'toggleEstadoPropiedad':
            $id = $_GET['id'];
            $estado = (int)$_GET['estado'];
            $sql = "UPDATE propiedades SET estado = $estado WHERE idpropiedades = $id";
            $result = mysqli_query(conectar(), $sql);
            
            if (!$result) {
                $error = ErrorHandler::handleDatabaseError('toggle property status', mysqli_error(conectar()), 'procesa_propiedades.php', $sql);
                echo ErrorHandler::jsonResponse($error);
            } else {
                ErrorHandler::logSuccess("Property status toggled successfully, ID: $id, new status: $estado", 'procesa_propiedades.php');
                echo json_encode(['success' => true]);
            }
            break;

        case 'eliminarImagen':
            $idgaleria = (int)$_GET['idgaleria'];
            $conn = conectar();
            
            // Primero obtener la información de la imagen
            $sqlSelect = "SELECT foto FROM galeria WHERE idgaleria = $idgaleria";
            $resultSelect = mysqli_query($conn, $sqlSelect);
            $imagen = mysqli_fetch_array($resultSelect);
            
            if ($imagen) {
                // Desactivar la imagen en la base de datos
                $sqlUpdate = "UPDATE galeria SET estado = 0 WHERE idgaleria = $idgaleria";
                $resultUpdate = mysqli_query($conn, $sqlUpdate);
                
                if ($resultUpdate) {
                    // Intentar eliminar el archivo físico
                    $rutaArchivo = 'propiedades/' . $imagen['foto'];
                    if (file_exists($rutaArchivo)) {
                        unlink($rutaArchivo);
                    }
                    ErrorHandler::logSuccess("Image deleted successfully, ID: $idgaleria", 'procesa_propiedades.php');
                    echo json_encode(['success' => true]);
                } else {
                    $error = ErrorHandler::handleDatabaseError('delete image', mysqli_error($conn), 'procesa_propiedades.php', $sqlUpdate);
                    echo ErrorHandler::jsonResponse($error);
                }
            } else {
                $error = ErrorHandler::handleError('Image not found', ErrorHandler::ERROR_NOT_FOUND, 'procesa_propiedades.php');
                echo ErrorHandler::jsonResponse($error);
            }
            break;

        case 'verificarPropietario':
            $id = (int)$_GET['id'];
            $usuario_id = (int)$_GET['usuario_id'];
            $conn = conectar();
            
            // Verificar si la propiedad corresponde al npropiedad del usuario
            $sql = "SELECT u.npropiedad FROM usuarios u WHERE u.id = $usuario_id";
            $result = mysqli_query($conn, $sql);
            $usuario = mysqli_fetch_array($result);
            
            if ($usuario && $usuario['npropiedad'] == $id) {
                echo json_encode(['es_propietario' => true]);
            } else {
                echo json_encode(['es_propietario' => false]);
            }
            break;
    }
}

if(isset($_POST['accion']) && $_POST['accion'] == 'guardarPropiedad') {
    $id = $_POST['idpropiedad'];
    $titulo = mysqli_real_escape_string(conectar(), $_POST['titulo']);
    $tipo_propiedad = (int)$_POST['tipo_propiedad'];
    $precio_uf = (int)$_POST['precio_uf'];
    $precio_pesos = (int)$_POST['precio_pesos'];
    $cant_banos = (int)$_POST['cant_banos'];
    $cant_dormitorios = (int)$_POST['cant_dormitorios'];
    $area_total = (int)$_POST['area_total'];
    $area_construida = (int)$_POST['area_construida'];
    $descripcion = mysqli_real_escape_string(conectar(), $_POST['descripcion']);
    $sector = (int)$_POST['sector'];
    $usuario_id = (int)$_POST['usuario_id'];
    
    // Características
    $bodega = isset($_POST['bodega']) ? 1 : 0;
    $estacionamiento = isset($_POST['estacionamiento']) ? 1 : 0;
    $logia = isset($_POST['logia']) ? 1 : 0;
    $cocinaamoblada = isset($_POST['cocinaamoblada']) ? 1 : 0;
    $antejardin = isset($_POST['antejardin']) ? 1 : 0;
    $patiotrasero = isset($_POST['patiotrasero']) ? 1 : 0;
    $piscina = isset($_POST['piscina']) ? 1 : 0;
    $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

    $principal = isset($_POST['principal']) ? (int)$_POST['principal'] : -1;

    $imagenes = isset($_FILES['imagenes']) ? $_FILES['imagenes'] : null;

    $conn = conectar();

    if($id == '') {
        // Insertar nueva propiedad
        $sql = "INSERT INTO propiedades (titulopropiedad, descripcion, cant_banos, cant_domitorios, 
                area_total, area_construida, precio_pesos, precio_uf, fecha_publicacion, estado, 
                idtipo_propiedad, sectores_idsectores, bodega, estacionamiento, logia, cocinaamoblada, 
                antejardin, patiotrasero, piscina) 
                VALUES ('$titulo', '$descripcion', $cant_banos, $cant_dormitorios, $area_total, 
                $area_construida, $precio_pesos, $precio_uf, CURDATE(), $estado, $tipo_propiedad, $sector, 
                $bodega, $estacionamiento, $logia, $cocinaamoblada, $antejardin, $patiotrasero, $piscina)";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            $error = ErrorHandler::handleDatabaseError('insert new property', mysqli_error($conn), 'procesa_propiedades.php', $sql);
            echo ErrorHandler::jsonResponse($error);
            exit;
        }
        $id = mysqli_insert_id($conn);
        
        // Actualizar el npropiedad del usuario con el ID de la nueva propiedad
        $sqlUpdateUser = "UPDATE usuarios SET npropiedad = $id WHERE id = $usuario_id";
        $resultUpdateUser = mysqli_query($conn, $sqlUpdateUser);
        if (!$resultUpdateUser) {
            $error = ErrorHandler::handleDatabaseError('associate property to user', mysqli_error($conn), 'procesa_propiedades.php', $sqlUpdateUser);
            echo ErrorHandler::jsonResponse($error);
            exit;
        }
        ErrorHandler::logSuccess("New property created successfully, ID: $id", 'procesa_propiedades.php');
    } else {
        // Actualizar propiedad existente
        $sql = "UPDATE propiedades SET 
                titulopropiedad = '$titulo',
                descripcion = '$descripcion',
                cant_banos = $cant_banos,
                cant_domitorios = $cant_dormitorios,
                area_total = $area_total,
                area_construida = $area_construida,
                precio_pesos = $precio_pesos,
                precio_uf = $precio_uf,
                idtipo_propiedad = $tipo_propiedad,
                sectores_idsectores = $sector,
                bodega = $bodega,
                estacionamiento = $estacionamiento,
                logia = $logia,
                cocinaamoblada = $cocinaamoblada,
                antejardin = $antejardin,
                patiotrasero = $patiotrasero,
                piscina = $piscina,
                estado = $estado
                WHERE idpropiedades = $id";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            $error = ErrorHandler::handleDatabaseError('update property', mysqli_error($conn), 'procesa_propiedades.php', $sql);
            echo ErrorHandler::jsonResponse($error);
            exit;
        }
        ErrorHandler::logSuccess("Property updated successfully, ID: $id", 'procesa_propiedades.php');
    }

    // Manejo de imágenes
    $imagenesNuevas = isset($_FILES['imagenes_nuevas']) ? $_FILES['imagenes_nuevas'] : null;
    $imagenesNuevasPrincipal = isset($_POST['imagenes_nuevas_principal']) ? $_POST['imagenes_nuevas_principal'] : [];
    $imagenesExistentes = isset($_POST['imagenes_existentes']) ? json_decode($_POST['imagenes_existentes'], true) : [];

    // Primero desactivar todas las imágenes existentes
    $sqlDesactivar = "UPDATE galeria SET estado = 0 WHERE idpropiedades = $id";
    mysqli_query($conn, $sqlDesactivar);

    // Procesar imágenes existentes
    if (!empty($imagenesExistentes)) {
        foreach ($imagenesExistentes as $img) {
            $idgaleria = (int)$img['idgaleria'];
            $principal = (int)$img['principal'];
            
            $sqlUpdate = "UPDATE galeria SET estado = 1, principal = $principal WHERE idgaleria = $idgaleria";
            $resUpdate = mysqli_query($conn, $sqlUpdate);
            
            if (!$resUpdate) {
                $error = ErrorHandler::handleDatabaseError('update existing image', mysqli_error($conn), 'procesa_propiedades.php', $sqlUpdate);
                echo ErrorHandler::jsonResponse($error);
                exit;
            }
        }
    }

    // Procesar imágenes nuevas
    if ($imagenesNuevas && $imagenesNuevas['name'][0] != '') {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $uploadDir = 'propiedades/';
        $numFiles = count($imagenesNuevas['name']);

        // Validar cantidad máxima total (existentes + nuevas)
        $totalImagenes = count($imagenesExistentes) + $numFiles;
        if ($totalImagenes > 10) {
            $error = ErrorHandler::handleValidationError('images', 'No se pueden tener más de 10 imágenes en total.', 'procesa_propiedades.php');
            echo ErrorHandler::jsonResponse($error);
            exit;
        }

        for ($i = 0; $i < $numFiles; $i++) {
            $tmpName = $imagenesNuevas['tmp_name'][$i];
            $name = basename($imagenesNuevas['name'][$i]);
            $type = $imagenesNuevas['type'][$i];
            $error = $imagenesNuevas['error'][$i];

            if ($error !== UPLOAD_ERR_OK) {
                $fileError = ErrorHandler::handleFileError($name, $error, 'procesa_propiedades.php');
                echo ErrorHandler::jsonResponse($fileError);
                exit;
            }

            if (!in_array($type, $allowedTypes)) {
                $validationError = ErrorHandler::handleValidationError('file_type', "Formato no permitido para la imagen $name.", 'procesa_propiedades.php');
                echo ErrorHandler::jsonResponse($validationError);
                exit;
            }

            // Generar nombre único
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $newName = uniqid('img_') . '.' . $ext;
            $destino = $uploadDir . $newName;

            if (!move_uploaded_file($tmpName, $destino)) {
                $fileError = ErrorHandler::handleError("No se pudo guardar la imagen $name.", ErrorHandler::ERROR_FILE_UPLOAD, 'procesa_propiedades.php');
                echo ErrorHandler::jsonResponse($fileError);
                exit;
            }

            // Insertar en BD
            $esPrincipal = isset($imagenesNuevasPrincipal[$i]) ? (int)$imagenesNuevasPrincipal[$i] : 0;
            $sqlInsert = "INSERT INTO galeria (foto, estado, principal, idpropiedades) VALUES ('$newName', 1, $esPrincipal, $id)";
            $resInsert = mysqli_query($conn, $sqlInsert);
            
            if (!$resInsert) {
                $error = ErrorHandler::handleDatabaseError('insert image', mysqli_error($conn), 'procesa_propiedades.php', $sqlInsert);
                echo ErrorHandler::jsonResponse($error);
                exit;
            }
        }
    }

    echo json_encode(['success' => true]);
}
?> 