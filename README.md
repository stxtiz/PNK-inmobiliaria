# PNK INMOBILIARIA 🏠

Sistema de gestión inmobiliaria desarrollado en PHP especializado en la **Región de Coquimbo, Chile**. Permite la administración completa de propiedades, usuarios y transacciones inmobiliarias con enfoque geográfico específico en las 3 provincias de Coquimbo: Elqui, Limarí y Choapa.

![PNK Inmobiliaria](img/Logo.png)

## 🚀 Características Principales

- **Gestión de Usuarios**: Sistema de roles (Administradores, Gestores Inmobiliarios Free, Propietarios)
- **Gestión de Propiedades**: CRUD completo con características detalladas (baños, dormitorios, amenidades)
- **Sistema de Autenticación**: Login seguro con encriptación bcrypt (PHP password_hash)
- **Filtros Geográficos**: Búsqueda jerárquica Región → Provincia → Comuna → Sector
- **Galería de Imágenes**: Sistema de fotos múltiples con imagen principal por propiedad
- **Validaciones Chilenas**: RUT con dígito verificador, teléfonos móviles (+569)
- **Tipos de Propiedades**: Casa, Departamento y Terreno
- **Precios Duales**: Manejo en Pesos Chilenos y UF (Unidad de Fomento)
- **Amenidades Detalladas**: Bodega, Estacionamiento, Logia, Cocina Amoblada, Antejardín, Patio Trasero, Piscina
- **Interfaz Responsiva**: Diseño adaptable con Bootstrap 5.0.2

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP**: Lenguaje principal del servidor
- **MySQL**: Base de datos relacional (BD: penka)
- **WAMP/XAMPP**: Servidor de desarrollo local

### Frontend
- **HTML5**: Estructura del contenido
- **CSS3**: Estilos personalizados
- **JavaScript**: Interactividad del cliente
- **Bootstrap 5.0.2**: Framework CSS responsivo
- **jQuery 3.7.1**: Biblioteca JavaScript
- **SweetAlert2**: Alertas elegantes
- **jQuery.Rut.js**: Validación de RUT chileno

## 📁 Estructura del Proyecto

```
pnk/
├── css/                    # Estilos CSS
│   ├── dashboard.css
│   ├── inicio.css
│   ├── login.css
│   └── ...
├── js/                     # JavaScript
│   ├── jquery-3.7.1.min.js
│   └── jquery.Rut.js
├── img/                    # Imágenes del sistema
├── setup/                  # Configuración
│   └── config.php         # Conexión a BD
├── propiedades/           # Archivos de propiedades
├── index.php              # Página principal
├── dashboard.php          # Panel de administración
├── procesa.php           # Procesamiento de login
└── ...
```

## 🗄️ Estructura de Base de Datos

La base de datos **`penka`** está diseñada específicamente para el mercado inmobiliario de la Región de Coquimbo:

### 📊 Tablas Principales

#### 🏠 **propiedades**
- Información completa de inmuebles
- Características: dormitorios, baños, áreas
- Precios en Pesos Chilenos y UF
- Amenidades: bodega, estacionamiento, piscina, etc.
- Relación con tipo de propiedad y ubicación

#### 👥 **usuarios**
- Sistema de roles: Administrador (3), Propietario (2), Gestor Free (1)
- Validación RUT chileno
- Encriptación bcrypt para contraseñas
- Certificados para gestores inmobiliarios

#### 🖼️ **galeria**
- Múltiples imágenes por propiedad
- Imagen principal destacada
- Gestión de estados de fotos

#### 🌎 **Jerarquía Geográfica**
- **regiones**: Región de Coquimbo
- **provincias**: Elqui, Limarí, Choapa
- **comunas**: 15 comunas (La Serena, Coquimbo, Ovalle, etc.)
- **sectores**: Barrios específicos por comuna

#### 🏢 **tipo_propiedad**
- Casa, Departamento, Terreno
- Estados activos/inactivos

### 📋 Esquema Detallado de Tablas

#### Tabla `propiedades`
```sql
CREATE TABLE propiedades (
  idpropiedades INT AUTO_INCREMENT PRIMARY KEY,
  titulopropiedad VARCHAR(50) NOT NULL,
  descripcion TEXT,
  cant_banos INT,
  cant_domitorios INT,  -- [sic] dormitorios
  area_total INT,
  area_construida INT,
  precio_pesos INT,
  precio_uf INT,
  fecha_publicacion DATE,
  estado INT,
  -- Amenidades Boolean (0/1)
  bodega INT,
  estacionamiento INT,
  logia INT,
  cocinaamoblada INT,
  antejardin INT,
  patiotrasero INT,
  piscina INT,
  -- Relaciones
  idtipo_propiedad INT,
  sectores_idsectores INT
);
```

#### Tabla `usuarios`
```sql
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rut VARCHAR(12) NOT NULL,
  nombres VARCHAR(50) NOT NULL,
  ap_paterno VARCHAR(50) NOT NULL,
  ap_materno VARCHAR(50) NOT NULL,
  usuario VARCHAR(50) NOT NULL,      -- Email
  clave VARCHAR(250) NOT NULL,       -- bcrypt hash
  sexo VARCHAR(4) NOT NULL,          -- M/F/0
  estado INT NOT NULL,               -- 1=activo, 0=inactivo
  npropiedad INT NOT NULL,           -- Número de propiedad
  telefono VARCHAR(20) NOT NULL,     -- +569XXXXXXXX
  fechanacimiento DATE NOT NULL,
  tipo INT NOT NULL,                 -- 1=Gestor, 2=Propietario, 3=Admin
  certificado VARCHAR(100)           -- Archivo PDF para gestores
);
```

#### Jerarquía Geográfica
```sql
-- Región (solo Coquimbo)
regiones: idregiones, region, estado

-- Provincias (Elqui, Limarí, Choapa)
provincias: idprovincias, provincia, estado, idregiones

-- 15 Comunas principales
comunas: idcomunas, comuna, estado, idprovincias

-- Sectores por comuna
sectores: idsectores, sector, estado, idcomunas
```

#### Galería de Imágenes
```sql
CREATE TABLE galeria (
  idgaleria INT AUTO_INCREMENT PRIMARY KEY,
  foto VARCHAR(45),
  estado INT,
  principal INT,                     -- 1=imagen principal
  idpropiedades INT
);
```

### 📍 Cobertura Geográfica

**Región de Coquimbo** con sus 3 provincias:

#### 🌊 **Provincia de Elqui**
- La Serena (Balmaceda, 4 Esquinas, La Florida, etc.)
- Coquimbo (Bosque San Carlos, Tierras Blancas, etc.)
- Andacollo, La Higuera, Paiguano, Vicuña

#### 🏔️ **Provincia de Limarí**
- Ovalle, Combarbalá, Monte Patria
- Punitaqui, Río Hurtado

#### 🌵 **Provincia de Choapa**
- Illapel, Canela, Los Vilos, Salamanca

## 🔧 Instalación

### Prerrequisitos
- WAMP, XAMPP o servidor con PHP 7.4+
- MySQL 5.7+ o MySQL 8.0+
- Navegador web moderno

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/pnk-inmobiliaria.git
   cd pnk-inmobiliaria
   ```

2. **Configurar la base de datos**
   ```sql
   -- Crear base de datos
   CREATE DATABASE penka CHARACTER SET latin1 COLLATE latin1_spanish_ci;
   
   -- Importar estructura y datos
   mysql -u root -p penka < "penka (5).sql"
   ```

3. **Configurar conexión**
   ```php
   // setup/config.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   define('DB_NAME', 'penka');
   define('DB_PORT', 3306);
   ```

4. **Verificar instalación**
   - Iniciar servidor local (WAMP/XAMPP)
   - Navegar a `http://localhost/pnk`
   - Probar con `test_conexion.php`

5. **Usuario por defecto**
   ```
   Email: admin@admin.cl
   Contraseña: [Ver en BD - encriptada con bcrypt]
   Tipo: Administrador (3)
   ```

## 👥 Tipos de Usuario

### 🔑 Administrador (Tipo 3)
- Acceso completo al sistema
- Gestión de usuarios y propiedades
- Panel de administración avanzado

### 🏢 Gestor Inmobiliario Free (Tipo 1)
- Gestión básica de propiedades
- Requiere certificado profesional
- Funcionalidades limitadas

### 🏠 Propietario (Tipo 2)
- Gestión de sus propias propiedades
- Vista de propiedades personales
- Registro simplificado

## 🔐 Características de Seguridad

- **Validación de Contraseñas**: Mínimo 8 caracteres, mayúsculas, minúsculas y caracteres especiales
- **Validación de RUT**: Formato y dígito verificador chileno
- **Validación de Email**: Formato estándar de correo electrónico
- **Control de Sesiones**: Gestión segura de sesiones de usuario
- **Sanitización de Datos**: Prevención de inyección SQL

## 📊 Funcionalidades Principales

### 🏠 Gestión de Propiedades
- Crear, editar, eliminar propiedades
- Subida de imágenes múltiples
- Filtros por ubicación y tipo
- Vista detallada de propiedades

### 👥 Gestión de Usuarios
- Registro de nuevos usuarios
- Validación de datos chilenos (RUT)
- Gestión de estados (activo/inactivo)
- Tipos de usuario diferenciados

### 🔍 Sistema de Filtros
- Filtro por región, provincia, comuna
- Filtro por tipo de propiedad
- Búsqueda dinámica con AJAX
- Paginación de resultados

## 🎨 Interfaz de Usuario

- **Diseño Responsivo**: Compatible con dispositivos móviles
- **Bootstrap 5**: Framework CSS moderno
- **SweetAlert2**: Alertas atractivas y funcionales
- **Iconografía**: Iconos intuitivos para navegación
- **Paleta de Colores**: Diseño profesional inmobiliario

## 📈 Diagrama de Dependencias

![Diagrama de Dependencias](dependency_diagram.svg)

El sistema está estructurado en módulos independientes que interactúan a través de:
- **Configuración Central**: `setup/config.php`
- **Autenticación**: `procesa.php`, `procesalogin.php`
- **CRUD Operations**: Módulos especializados
- **Frontend**: Páginas de interfaz de usuario

## 🔄 Flujo de Trabajo

1. **Autenticación**: Usuario accede mediante `index.php`
2. **Validación**: `procesa.php` valida credenciales
3. **Redirección**: `procesalogin.php` redirige según tipo de usuario
4. **Gestión**: Acceso a módulos según permisos
5. **Operaciones**: CRUD en base de datos MySQL

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE.md](LICENSE.md) para detalles.

## 📞 Contacto

**PNK Inmobiliaria**
- Email: contacto@pnkinmobiliaria.cl
- Instagram: [@tioreneoficial_](https://www.instagram.com/tioreneoficial_/?hl=es)
- LinkedIn: [PNK Inmobiliaria](https://www.ticketmaster.cl/event/popin-un-show-muy-penca-centro-cultural-san-gines)

## 🙏 Agradecimientos

- Bootstrap por el framework CSS
- jQuery por la biblioteca JavaScript
- SweetAlert2 por las alertas elegantes
- Comunidad PHP por el soporte continuo

---

⭐ **¡Dale una estrella al proyecto si te fue útil!**

```
© 2025 Todos los derechos Reservados PNK Inmobiliaria
```
