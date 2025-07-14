# Requisitos de Contraseña - PNK Inmobiliaria

## Descripción General
Este documento centraliza los requisitos de contraseña para todos los módulos del sistema PNK Inmobiliaria.

## Requisitos Estándar de Contraseña

### Criterios Obligatorios
1. **Longitud mínima**: 8 caracteres
2. **Letras mayúsculas**: Al menos una letra mayúscula (A-Z)
3. **Letras minúsculas**: Al menos una letra minúscula (a-z)
4. **Números**: Al menos un dígito (0-9)
5. **Caracteres especiales**: Al menos uno de los siguientes: `!@#$%^&*`

### Expresión Regular
```javascript
/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/
```

### Mensaje de Error Estándar
**Título**: "Contraseña inválida"

**Mensaje**: 
```
La contraseña debe cumplir con los siguientes requisitos:
- Mínimo 8 caracteres
- Al menos una letra mayúscula (A-Z)
- Al menos una letra minúscula (a-z)
- Al menos un número (0-9)
- Al menos un carácter especial (!@#$%^&*)
```

## Implementación

### Frontend (JavaScript)
Utilizar la utilidad centralizada:
```javascript
// Incluir en todas las páginas con formularios de contraseña
<script src="js/password-validation.js"></script>

// Usar en validaciones
if (!validateAndShowPasswordErrors(password, confirmPassword)) {
    return false;
}
```

### Backend (PHP)
Utilizar la clase centralizada:
```php
// Incluir en archivos que procesan contraseñas
include("setup/PasswordValidator.php");

// Usar en validaciones
$passwordError = PasswordValidator::validateAndGetError($password, $confirmPassword);
if ($passwordError !== null) {
    // Manejar error
}
```

## Archivos Afectados

### Archivos JavaScript Actualizados
- `js/password-validation.js` (nuevo - utilidad centralizada)

### Archivos PHP Actualizados
- `setup/PasswordValidator.php` (nuevo - clase centralizada)
- `index.php` - Login de usuarios
- `registro_propietario.php` - Registro de propietarios
- `registro_gestor.php` - Registro de gestores
- `dashboard.php` - Panel administrativo
- `crudGestor.php` - CRUD de usuarios
- `ingreso_propietario.php` - Procesamiento registro propietarios
- `ingreso_gestor.php` - Procesamiento registro gestores

## Módulos Unificados

### 1. Login de Usuario (index.php)
- **Antes**: Validación con regex inconsistente
- **Ahora**: Utilidad centralizada `validateAndShowPasswordErrors()`

### 2. Registro de Propietario (registro_propietario.php)
- **Antes**: Regex duplicado, mensaje inconsistente
- **Ahora**: Utilidad centralizada con validación de coincidencia

### 3. Registro de Gestor (registro_gestor.php)
- **Antes**: Mismo regex pero mensaje diferente
- **Ahora**: Utilidad centralizada unificada

### 4. Panel Administrativo (dashboard.php)
- **Antes**: Validaciones individuales con caracteres especiales diferentes
- **Ahora**: Utilidad centralizada consistente

## Ventajas de la Unificación

1. **Consistencia**: Todos los módulos usan los mismos criterios
2. **Mantenibilidad**: Un solo lugar para cambiar requisitos
3. **Experiencia de Usuario**: Mensajes de error uniformes
4. **Seguridad**: Criterios estandarizados en cliente y servidor
5. **Documentación**: Requisitos centralizados y documentados

## Pruebas

### Contraseñas Válidas de Ejemplo
- `MiClave123!`
- `Password#2024`
- `Segura$789`

### Contraseñas Inválidas de Ejemplo
- `password` (no mayúscula, no número, no especial)
- `PASSWORD123` (no minúscula, no especial)
- `MiClave` (menos de 8 caracteres, no número, no especial)
- `MiClave123` (no carácter especial)

## Mantenimiento

Para modificar los requisitos de contraseña:

1. Actualizar `PASSWORD_CONFIG` en `js/password-validation.js`
2. Actualizar constantes en `setup/PasswordValidator.php`
3. Actualizar este documento
4. Probar en todos los módulos afectados

## Historial de Cambios

- **v1.0** (2024): Implementación inicial unificada
  - Centralización de validaciones
  - Estandarización de mensajes de error
  - Unificación de criterios de seguridad