<?php
session_start();
include("setup/config.php"); // Incluye el archivo de configuración para la conexión a la base de datos

// Verificar que el usuario esté logueado y sea tipo 2 (Dueño de Inmueble)
if (isset($_SESSION['usuario']) && isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2)
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

    // Obtener el ID del usuario logueado desde la sesión
    $id_usuario = $_SESSION['id'];
    
    // Verificar que tenemos un ID de usuario válido
    if (!$id_usuario) {
        header("Location: error.html");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Propiedades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/crud_galeria.css">
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
                </div>
                <div class="texto-icono">
                    <img src="img/exit.png" alt="Cerrar sesión">
                    <a href="cerrar.php">Cerrar sesión</a>
                </div>
            </div>
        </div>

        <div class="container mt-4 mx-auto">
            <div class="row">
                <div class="col-md-12">
                    <h2>Mis Propiedades</h2>
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalPropiedad">
                        Nueva Propiedad
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Precio (UF)</th>
                                    <th>Precio (CLP)</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPropiedades">
                                <?php
                                // Consulta para obtener solo las propiedades del usuario logueado
                                // Obtener el npropiedad del usuario actual
                                $sql_user = "SELECT npropiedad FROM usuarios WHERE id = '$id_usuario'";
                                $result_user = mysqli_query(conectar(), $sql_user);
                                $user_data = mysqli_fetch_array($result_user);
                                
                                // Verificar que se obtuvo la información del usuario y manejar npropiedad null
                                $npropiedad = ($user_data && isset($user_data['npropiedad'])) ? $user_data['npropiedad'] : 0;
                                
                                if ($npropiedad > 0) {
                                    $sql = "SELECT p.*, tp.tipo as tipo_propiedad 
                                           FROM propiedades p 
                                           INNER JOIN tipo_propiedad tp ON p.idtipo_propiedad = tp.idtipo_propiedad 
                                           WHERE p.idpropiedades = $npropiedad";
                                } else {
                                    // Si el usuario no tiene propiedades asignadas, mostrar consulta vacía
                                    $sql = "SELECT p.*, tp.tipo as tipo_propiedad 
                                           FROM propiedades p 
                                           INNER JOIN tipo_propiedad tp ON p.idtipo_propiedad = tp.idtipo_propiedad 
                                           WHERE 1=0";
                                }
                                $result = mysqli_query(conectar(), $sql);
                                while($row = mysqli_fetch_array($result)) {
                                    $estadoTexto = ($row['estado'] == 1)
                                        ? '<img src="img/check.png" width="24px" style="cursor:pointer" onclick="cambiarEstadoPropiedad(' . $row['idpropiedades'] . ', 0)">' 
                                        : '<img src="img/ina.png" width="24px" style="cursor:pointer" onclick="cambiarEstadoPropiedad(' . $row['idpropiedades'] . ', 1)">';
                                    echo "<tr>";
                                    echo "<td>".$row['idpropiedades']."</td>";
                                    echo "<td>".$row['titulopropiedad']."</td>";
                                    echo "<td>".$row['tipo_propiedad']."</td>";
                                    echo "<td>".$row['precio_uf']."</td>";
                                    echo "<td>".$row['precio_pesos']."</td>";
                                    echo "<td>".$estadoTexto."</td>";
                                    echo "<td>
                                            <a href=\"#\" onclick='editarPropiedad(".$row['idpropiedades'].")'><img src=\"img/update.png\" alt=\"Editar\" style=\"width: 24px; height: 24px;\"></a>
                                            <a href=\"#\" onclick='eliminarPropiedad(".$row['idpropiedades'].")'><img src=\"img/borrar.png\" alt=\"Eliminar\" style=\"width: 24px; height: 24px;\"></a>
                                          </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nueva/Editar Propiedad -->
        <div class="modal fade" id="modalPropiedad" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Propiedad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formPropiedad">
                            <input type="hidden" id="idpropiedad" name="idpropiedad">
                            <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $id_usuario; ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tipo de Propiedad</label>
                                    <select class="form-select" id="tipo_propiedad" name="tipo_propiedad" required onchange="mostrarCamposSegunTipo()">
                                        <?php
                                        $sql = "SELECT * FROM tipo_propiedad WHERE estado = 1";
                                        $result = mysqli_query(conectar(), $sql);
                                        while($row = mysqli_fetch_array($result)) {
                                            echo "<option value='".$row['idtipo_propiedad']."'>".$row['tipo']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Precio UF</label>
                                    <input type="number" class="form-control" id="precio_uf" name="precio_uf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio CLP</label>
                                    <input type="number" class="form-control" id="precio_pesos" name="precio_pesos" required>
                                </div>
                            </div>

                            <div class="row mb-3 campos-construccion">
                                <div class="col-md-6">
                                    <label class="form-label">Baños</label>
                                    <input type="number" class="form-control" id="cant_banos" name="cant_banos">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dormitorios</label>
                                    <input type="number" class="form-control" id="cant_dormitorios" name="cant_dormitorios">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Área Total (m²)</label>
                                    <input type="number" class="form-control" id="area_total" name="area_total" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Área Construida (m²)</label>
                                    <input type="number" class="form-control" id="area_construida" name="area_construida">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Región</label>
                                    <select class="form-select" id="region" name="region" required>
                                        <option value="">Seleccionar</option>
                                        <?php
                                        $sql = "SELECT * FROM regiones WHERE estado = 1";
                                        $result = mysqli_query(conectar(), $sql);
                                        while($row = mysqli_fetch_array($result)) {
                                            echo "<option value='".$row['idregiones']."'>".$row['region']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Provincia</label>
                                    <select class="form-select" id="provincia" name="provincia" required>
                                        <option value="">Seleccionar</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Comuna</label>
                                    <select class="form-select" id="comuna" name="comuna" required>
                                        <option value="">Seleccionar</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sector</label>
                                    <select class="form-select" id="sector" name="sector" required>
                                        <option value="">Seleccionar</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                                </div>
                            </div>

                            <!-- Sección de imágenes -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="seccion-imagenes">
                                        <h5>Imágenes de la Propiedad</h5>
                                        <div class="contador-imagenes">
                                            <span id="contador">0</span>/10 imágenes
                                        </div>
                                        <div class="dropzone-area" id="dropzone">
                                            <div class="dropzone-text">Arrastra y suelta imágenes aquí</div>
                                            <div class="dropzone-subtext">o haz clic para seleccionar (máximo 10 imágenes)</div>
                                            <input type="file" id="fileInput" class="file-input-hidden" multiple accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                        <div class="error-message" id="errorMessage"></div>
                                        <div class="preview-container" id="previewContainer"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="estado_propiedad" name="estado_propiedad" checked>
                                        <label class="form-check-label" for="estado_propiedad">Propiedad Activa</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 seccion-caracteristicas">
                                <div class="col-md-12">
                                    <h5>Características</h5>
                                    <div class="form-check form-check-inline campos-construccion">
                                        <input class="form-check-input" type="checkbox" id="bodega" name="bodega">
                                        <label class="form-check-label">Bodega</label>
                                    </div>
                                    <div class="form-check form-check-inline campos-construccion">
                                        <input class="form-check-input" type="checkbox" id="estacionamiento" name="estacionamiento">
                                        <label class="form-check-label">Estacionamiento</label>
                                    </div>
                                    <div class="form-check form-check-inline campos-construccion">
                                        <input class="form-check-input" type="checkbox" id="logia" name="logia">
                                        <label class="form-check-label">Logia</label>
                                    </div>
                                    <div class="form-check form-check-inline campos-construccion">
                                        <input class="form-check-input" type="checkbox" id="cocinaamoblada" name="cocinaamoblada">
                                        <label class="form-check-label">Cocina Amoblada</label>
                                    </div>
                                    <div class="form-check form-check-inline campos-exterior">
                                        <input class="form-check-input" type="checkbox" id="antejardin" name="antejardin">
                                        <label class="form-check-label">Antejardín</label>
                                    </div>
                                    <div class="form-check form-check-inline campos-exterior">
                                        <input class="form-check-input" type="checkbox" id="patiotrasero" name="patiotrasero">
                                        <label class="form-check-label">Patio Trasero</label>
                                    </div>
                                    <div class="form-check form-check-inline campos-construccion">
                                        <input class="form-check-input" type="checkbox" id="piscina" name="piscina">
                                        <label class="form-check-label">Piscina</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarPropiedad()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Cargar provincias al seleccionar región
        document.getElementById('region').addEventListener('change', function() {
            const regionId = this.value;
            const provinciaSelect = document.getElementById('provincia');
            const comunaSelect = document.getElementById('comuna');
            const sectorSelect = document.getElementById('sector');
            
            // Limpiar todos los selectores dependientes
            provinciaSelect.innerHTML = '<option value="">Seleccionar</option>';
            comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
            sectorSelect.innerHTML = '<option value="">Seleccionar</option>';
            
            if (!regionId) return;
            
            fetch('procesa_propiedades.php?accion=cargarProvincias&region=' + regionId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(provincia => {
                        provinciaSelect.innerHTML += `<option value="${provincia.idprovincias}">${provincia.provincia}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar provincias:', error);
                    Swal.fire('Error', 'No se pudieron cargar las provincias', 'error');
                });
        });

        // Cargar comunas al seleccionar provincia
        document.getElementById('provincia').addEventListener('change', function() {
            const provinciaId = this.value;
            const comunaSelect = document.getElementById('comuna');
            const sectorSelect = document.getElementById('sector');
            
            // Limpiar selectores dependientes
            comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
            sectorSelect.innerHTML = '<option value="">Seleccionar</option>';
            
            if (!provinciaId) return;
            
            fetch('procesa_propiedades.php?accion=cargarComunas&provincia=' + provinciaId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(comuna => {
                        comunaSelect.innerHTML += `<option value="${comuna.idcomunas}">${comuna.comuna}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar comunas:', error);
                    Swal.fire('Error', 'No se pudieron cargar las comunas', 'error');
                });
        });

        // Cargar sectores al seleccionar comuna
        document.getElementById('comuna').addEventListener('change', function() {
            const comunaId = this.value;
            const sectorSelect = document.getElementById('sector');
            
            // Limpiar selector de sectores
            sectorSelect.innerHTML = '<option value="">Seleccionar</option>';
            
            if (!comunaId) return;
            
            fetch('procesa_propiedades.php?accion=cargarSectores&comuna=' + comunaId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(sector => {
                        sectorSelect.innerHTML += `<option value="${sector.idsectores}">${sector.sector}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar sectores:', error);
                    Swal.fire('Error', 'No se pudieron cargar los sectores', 'error');
                });
        });

        // Función para cargar los datos al editar
        function cargarDatosUbicacion(regionId, provinciaId, comunaId, sectorId) {
            if (regionId) {
                document.getElementById('region').value = regionId;
                document.getElementById('region').dispatchEvent(new Event('change'));
                
                // Esperar a que se carguen las provincias
                setTimeout(() => {
                    if (provinciaId) {
                        document.getElementById('provincia').value = provinciaId;
                        document.getElementById('provincia').dispatchEvent(new Event('change'));
                        
                        // Esperar a que se carguen las comunas
                        setTimeout(() => {
                            if (comunaId) {
                                document.getElementById('comuna').value = comunaId;
                                document.getElementById('comuna').dispatchEvent(new Event('change'));
                                
                                // Esperar a que se carguen los sectores
                                setTimeout(() => {
                                    if (sectorId) {
                                        document.getElementById('sector').value = sectorId;
                                    }
                                }, 500);
                            }
                        }, 500);
                    }
                }, 500);
            }
        }

        // Función para mostrar/ocultar campos según el tipo de propiedad
        function mostrarCamposSegunTipo() {
            const tipoPropiedad = document.getElementById('tipo_propiedad').value;
            const camposConstruccion = document.querySelectorAll('.campos-construccion');
            const camposExterior = document.querySelectorAll('.campos-exterior');
            const seccionCaracteristicas = document.querySelector('.seccion-caracteristicas');
            
            // Obtener el texto del tipo de propiedad seleccionado
            const tipoTexto = document.getElementById('tipo_propiedad').options[document.getElementById('tipo_propiedad').selectedIndex].text.toLowerCase();
            
            // Por defecto, mostrar todo
            camposConstruccion.forEach(campo => { campo.style.display = 'inline-block'; });
            camposExterior.forEach(campo => { campo.style.display = 'inline-block'; });
            if (seccionCaracteristicas) { seccionCaracteristicas.style.display = 'block'; }

            // Si es terreno, ocultar campos de construcción y la sección de características completa
            if (tipoTexto.includes('terreno')) {
                camposConstruccion.forEach(campo => {
                    campo.style.display = 'none';
                    campo.querySelectorAll('input').forEach(input => {
                        if (input.type === 'number') { input.value = ''; } else if (input.type === 'checkbox') { input.checked = false; }
                    });
                });
                camposExterior.forEach(campo => {
                    campo.style.display = 'none';
                    campo.querySelectorAll('input').forEach(input => { input.checked = false; });
                });
                if (seccionCaracteristicas) {
                    seccionCaracteristicas.style.display = 'none';
                }
            } else if (tipoTexto.includes('departamento')) {
                // Si es departamento, ocultar campos de exterior (antejardin, patio trasero)
                camposExterior.forEach(campo => {
                    campo.style.display = 'none';
                    campo.querySelectorAll('input').forEach(input => { input.checked = false; });
                });
            } 
        }

        // Llamar a la función al cargar la página y también al abrir el modal para asegurar el estado correcto
        document.addEventListener('DOMContentLoaded', function() {
            mostrarCamposSegunTipo();
            var modalPropiedad = document.getElementById('modalPropiedad');
            modalPropiedad.addEventListener('shown.bs.modal', function () {
                mostrarCamposSegunTipo(); // Asegura que los campos se muestren/oculten al abrir el modal
            });
        });

        function editarPropiedad(id) {
            // Verificar que la propiedad pertenece al usuario actual
            fetch('procesa_propiedades.php?accion=verificarPropietario&id=' + id + '&usuario_id=<?php echo $id_usuario; ?>')
                .then(response => response.json())
                .then(verificacion => {
                    if (!verificacion.es_propietario) {
                        Swal.fire('Error', 'No tienes permisos para editar esta propiedad', 'error');
                        return;
                    }
                    
                    // Si es propietario, cargar los datos
                    fetch('procesa_propiedades.php?accion=obtenerPropiedad&id=' + id)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('idpropiedad').value = data.idpropiedades;
                            document.getElementById('titulo').value = data.titulopropiedad;
                            document.getElementById('tipo_propiedad').value = data.idtipo_propiedad;
                            document.getElementById('precio_uf').value = data.precio_uf;
                            document.getElementById('precio_pesos').value = data.precio_pesos;
                            document.getElementById('area_total').value = data.area_total;
                            document.getElementById('descripcion').value = data.descripcion;
                            document.getElementById('estado_propiedad').checked = data.estado == 1;
                            
                            // Primero, establece el tipo de propiedad para que mostrarCamposSegunTipo funcione correctamente
                            document.getElementById('tipo_propiedad').value = data.idtipo_propiedad;
                            mostrarCamposSegunTipo();
                            
                            // Obtener el tipo de propiedad actual (texto)
                            const tipoTextoActual = document.getElementById('tipo_propiedad').options[document.getElementById('tipo_propiedad').selectedIndex].text.toLowerCase();

                            // Establecer valores solo si los campos son relevantes para el tipo de propiedad
                            if (!tipoTextoActual.includes('terreno')) {
                                document.getElementById('cant_banos').value = data.cant_banos;
                                document.getElementById('cant_dormitorios').value = data.cant_domitorios;
                                document.getElementById('area_construida').value = data.area_construida;
                                document.getElementById('bodega').checked = data.bodega == 1;
                                document.getElementById('estacionamiento').checked = data.estacionamiento == 1;
                                document.getElementById('logia').checked = data.logia == 1;
                                document.getElementById('cocinaamoblada').checked = data.cocinaamoblada == 1;
                                document.getElementById('piscina').checked = data.piscina == 1;
                            }
                            
                            if (!tipoTextoActual.includes('terreno') && !tipoTextoActual.includes('departamento')) {
                                // Estos solo se establecen si no es terreno y no es departamento (es decir, es una casa)
                                document.getElementById('antejardin').checked = data.antejardin == 1;
                                document.getElementById('patiotrasero').checked = data.patiotrasero == 1;
                            }
                            
                            // Cargar ubicación
                            cargarDatosUbicacion(data.idregiones, data.idprovincias, data.idcomunas, data.sectores_idsectores);

                            // Cargar imágenes existentes
                            cargarImagenesExistentes(data.imagenes);

                            new bootstrap.Modal(document.getElementById('modalPropiedad')).show();
                        });
                });
        }

        // Función para cargar imágenes existentes
        function cargarImagenesExistentes(imagenesData) {
            images = [];
            principalIndex = -1;
            
            if (imagenesData && imagenesData.length > 0) {
                imagenesData.forEach((img, idx) => {
                    const imgObj = {
                        name: img.foto,
                        url: 'propiedades/' + img.foto,
                        idgaleria: img.id,
                        principal: img.principal === "1"
                    };
                    images.push(imgObj);
                    if (img.principal === "1") {
                        principalIndex = idx;
                    }
                });
                renderizarPreviews();
            }
        }

        function eliminarPropiedad(id) {
            // Verificar que la propiedad pertenece al usuario actual
            fetch('procesa_propiedades.php?accion=verificarPropietario&id=' + id + '&usuario_id=<?php echo $id_usuario; ?>')
                .then(response => response.json())
                .then(verificacion => {
                    if (!verificacion.es_propietario) {
                        Swal.fire('Error', 'No tienes permisos para eliminar esta propiedad', 'error');
                        return;
                    }
                    
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('procesa_propiedades.php?accion=eliminarPropiedad&id=' + id)
                                .then(response => response.json())
                                .then(data => {
                                    if(data.success) {
                                        Swal.fire('¡Eliminado!', 'La propiedad ha sido eliminada.', 'success')
                                        .then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', 'No se pudo eliminar la propiedad.', 'error');
                                    }
                                });
                        }
                    });
                });
        }

        // Modificar la función guardarPropiedad para manejar campos ocultos
        function guardarPropiedad() {
            const formData = new FormData(document.getElementById('formPropiedad'));
            formData.append('accion', 'guardarPropiedad');
            
            // Añadir el estado de la propiedad al formData
            formData.append('estado', document.getElementById('estado_propiedad').checked ? 1 : 0);

            // Agregar imágenes al formData
            const imagenesNuevas = [];
            const imagenesExistentes = [];
            
            images.forEach((img, index) => {
                if (img instanceof File) {
                    imagenesNuevas.push({
                        file: img,
                        principal: index === principalIndex ? 1 : 0
                    });
                } else {
                    imagenesExistentes.push({
                        idgaleria: img.idgaleria,
                        principal: index === principalIndex ? 1 : 0
                    });
                }
            });

            // Agregar imágenes nuevas
            imagenesNuevas.forEach(img => {
                formData.append('imagenes_nuevas[]', img.file);
                formData.append('imagenes_nuevas_principal[]', img.principal);
            });

            // Agregar información de imágenes existentes
            formData.append('imagenes_existentes', JSON.stringify(imagenesExistentes));

            const tipoTexto = document.getElementById('tipo_propiedad').options[document.getElementById('tipo_propiedad').selectedIndex].text.toLowerCase();
            
            // Si es terreno, establecer valores por defecto para todos los campos de construcción y características
            if (tipoTexto.includes('terreno')) {
                formData.set('cant_banos', '0');
                formData.set('cant_dormitorios', '0');
                formData.set('area_construida', '0');
                formData.set('bodega', '0');
                formData.set('estacionamiento', '0');
                formData.set('logia', '0');
                formData.set('cocinaamoblada', '0');
                formData.set('piscina', '0');
                formData.set('antejardin', '0'); 
                formData.set('patiotrasero', '0');
            } else if (tipoTexto.includes('departamento')) {
                // Si es departamento, establecer valores por defecto para campos de exterior
                formData.set('antejardin', '0');
                formData.set('patiotrasero', '0');
            }

            fetch('procesa_propiedades.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('¡Guardado!', 'La propiedad ha sido guardada correctamente.', 'success')
                    .then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al guardar',
                        text: data.error || 'No se pudo guardar la propiedad.',
                        footer: data.sql ? `SQL: ${data.sql}` : ''
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Ocurrió un error al procesar la solicitud.', 'error');
                console.error('Error:', error);
            });
        }

        function cambiarEstadoPropiedad(id, nuevoEstado) {
            // Verificar que la propiedad pertenece al usuario actual
            fetch('procesa_propiedades.php?accion=verificarPropietario&id=' + id + '&usuario_id=<?php echo $id_usuario; ?>')
                .then(response => response.json())
                .then(verificacion => {
                    if (!verificacion.es_propietario) {
                        Swal.fire('Error', 'No tienes permisos para modificar esta propiedad', 'error');
                        return;
                    }
                    
                    Swal.fire({
                        title: nuevoEstado == 1 ? '¿Activar propiedad?' : '¿Desactivar propiedad?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('procesa_propiedades.php?accion=toggleEstadoPropiedad&id=' + id + '&estado=' + nuevoEstado)
                                .then(response => response.json())
                                .then(data => {
                                    if(data.success) {
                                        Swal.fire('¡Éxito!', 'El estado de la propiedad ha sido actualizado.', 'success')
                                        .then(() => { location.reload(); });
                                    } else {
                                        Swal.fire('Error', data.error || 'No se pudo cambiar el estado.', 'error');
                                    }
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Ocurrió un error al procesar la solicitud.', 'error');
                                });
                        }
                    });
                });
        }
    </script>

    <script>
        // Variables para manejo de imágenes
        const fileInput = document.getElementById('fileInput');
        const dropzone = document.getElementById('dropzone');
        const previewContainer = document.getElementById('previewContainer');
        const contador = document.getElementById('contador');
        const errorMessage = document.getElementById('errorMessage');
        const maxImages = 10;
        let images = [];
        let principalIndex = -1;

        // Función para actualizar contador
        function actualizarContador() {
            contador.textContent = images.length;
            if (images.length >= maxImages) {
                dropzone.classList.add('limite-alcanzado');
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'Has alcanzado el límite máximo de 10 imágenes.';
            } else {
                dropzone.classList.remove('limite-alcanzado');
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
            }
        }

        // Función para validar formato
        function validarFormato(file) {
            const formatosPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
            return formatosPermitidos.includes(file.type);
        }

        // Función para crear miniatura
        function crearMiniatura(imgObj, index) {
            const div = document.createElement('div');
            div.classList.add('preview-item');
            if (index === principalIndex) {
                div.classList.add('principal');
            }
            div.dataset.index = index;

            // Si imgObj es un objeto File (nueva imagen)
            if (imgObj instanceof File) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Imagen ${index + 1}" class="preview-image">
                        <div class="preview-controls">
                            <button type="button" class="btn-preview btn-principal ${index === principalIndex ? 'active' : ''}" title="Seleccionar como principal">&#9733;</button>
                            <button type="button" class="btn-preview btn-eliminar" title="Eliminar imagen">&times;</button>
                        </div>
                        ${index === principalIndex ? '<div class="principal-badge">Principal</div>' : ''}
                    `;

                    // Eventos botones
                    const btnPrincipal = div.querySelector('.btn-principal');
                    btnPrincipal.addEventListener('click', () => {
                        principalIndex = index;
                        renderizarPreviews();
                    });

                    const btnEliminar = div.querySelector('.btn-eliminar');
                    btnEliminar.addEventListener('click', () => {
                        images.splice(index, 1);
                        if (principalIndex === index) {
                            principalIndex = -1;
                        } else if (principalIndex > index) {
                            principalIndex--;
                        }
                        renderizarPreviews();
                    });

                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(imgObj);
            } else {
                // Imagen existente con url
                div.innerHTML = `
                    <img src="${imgObj.url}" alt="Imagen ${index + 1}" class="preview-image">
                    <div class="preview-controls">
                        <button type="button" class="btn-preview btn-principal ${index === principalIndex ? 'active' : ''}" title="Seleccionar como principal">&#9733;</button>
                        <button type="button" class="btn-preview btn-eliminar" title="Eliminar imagen">&times;</button>
                    </div>
                    ${index === principalIndex ? '<div class="principal-badge">Principal</div>' : ''}
                `;

                const btnPrincipal = div.querySelector('.btn-principal');
                btnPrincipal.addEventListener('click', () => {
                    principalIndex = index;
                    renderizarPreviews();
                });

                const btnEliminar = div.querySelector('.btn-eliminar');
                btnEliminar.addEventListener('click', () => {
                    images.splice(index, 1);
                    if (principalIndex === index) {
                        principalIndex = -1;
                    } else if (principalIndex > index) {
                        principalIndex--;
                    }
                    renderizarPreviews();
                });

                previewContainer.appendChild(div);
            }
        }

        // Función para renderizar todas las miniaturas
        function renderizarPreviews() {
            previewContainer.innerHTML = '';
            images.forEach((imgObj, idx) => {
                crearMiniatura(imgObj, idx);
            });
            actualizarContador();
        }

        // Manejo de selección de archivos
        fileInput.addEventListener('change', (e) => {
            const archivos = Array.from(e.target.files);
            agregarArchivos(archivos);
            fileInput.value = '';
        });

        // Manejo de drag and drop
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            const archivos = Array.from(e.dataTransfer.files);
            agregarArchivos(archivos);
        });

        // Click en dropzone abre selector de archivos
        dropzone.addEventListener('click', () => {
            if (images.length < maxImages) {
                fileInput.click();
            }
        });

        // Función para agregar archivos validando
        function agregarArchivos(archivos) {
            for (const archivo of archivos) {
                if (images.length >= maxImages) {
                    break;
                }
                if (!validarFormato(archivo)) {
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Solo se permiten archivos JPG, PNG y WEBP.';
                    continue;
                }
                images.push(archivo);
            }
            renderizarPreviews();
        }
    </script>
</body>
</html>

<?php
}else{
    header("Location: error.html");
}
?>
