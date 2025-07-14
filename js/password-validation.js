/**
 * Utilidad centralizada para validación de contraseñas
 * PNK Inmobiliaria - Validación unificada de contraseñas
 */

// Configuración unificada de requisitos de contraseña
const PASSWORD_CONFIG = {
    minLength: 8,
    requireUppercase: true,
    requireLowercase: true,
    requireNumber: true,
    requireSpecialChar: true,
    specialChars: '!@#$%^&*',
    regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/
};

// Mensajes de error unificados
const PASSWORD_MESSAGES = {
    title: 'Contraseña inválida',
    requirements: 'La contraseña debe cumplir con los siguientes requisitos:<br>' +
                 '- Mínimo 8 caracteres<br>' +
                 '- Al menos una letra mayúscula (A-Z)<br>' +
                 '- Al menos una letra minúscula (a-z)<br>' +
                 '- Al menos un número (0-9)<br>' +
                 '- Al menos un carácter especial (!@#$%^&*)',
    mismatch: {
        title: 'Contraseñas no coinciden',
        text: 'Las contraseñas ingresadas no coinciden. Por favor verifíquelas.'
    }
};

/**
 * Valida si una contraseña cumple con todos los requisitos
 * @param {string} password - La contraseña a validar
 * @returns {boolean} true si la contraseña es válida, false en caso contrario
 */
function validatePassword(password) {
    if (!password || typeof password !== 'string') {
        return false;
    }
    return PASSWORD_CONFIG.regex.test(password);
}

/**
 * Valida si dos contraseñas son iguales
 * @param {string} password - Primera contraseña
 * @param {string} confirmPassword - Segunda contraseña (confirmación)
 * @returns {boolean} true si las contraseñas coinciden, false en caso contrario
 */
function validatePasswordMatch(password, confirmPassword) {
    return password === confirmPassword;
}

/**
 * Realiza validación completa de contraseña incluyendo confirmación
 * @param {string} password - Contraseña principal
 * @param {string} confirmPassword - Contraseña de confirmación (opcional)
 * @returns {object} Objeto con resultado de validación
 */
function validatePasswordComplete(password, confirmPassword = null) {
    const result = {
        isValid: true,
        errors: []
    };

    // Validar formato de contraseña
    if (!validatePassword(password)) {
        result.isValid = false;
        result.errors.push({
            type: 'format',
            title: PASSWORD_MESSAGES.title,
            message: PASSWORD_MESSAGES.requirements
        });
    }

    // Validar coincidencia si se proporciona confirmación
    if (confirmPassword !== null && !validatePasswordMatch(password, confirmPassword)) {
        result.isValid = false;
        result.errors.push({
            type: 'mismatch',
            title: PASSWORD_MESSAGES.mismatch.title,
            message: PASSWORD_MESSAGES.mismatch.text
        });
    }

    return result;
}

/**
 * Muestra error de validación usando SweetAlert2
 * @param {object} error - Objeto de error con title y message
 */
function showPasswordError(error) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: error.title,
            html: error.message
        });
    } else {
        // Fallback para navegadores sin SweetAlert2
        alert(error.title + '\n\n' + error.message.replace(/<br>/g, '\n').replace(/<[^>]*>/g, ''));
    }
}

/**
 * Función principal para validar contraseñas y mostrar errores
 * @param {string} password - Contraseña principal
 * @param {string} confirmPassword - Contraseña de confirmación (opcional)
 * @returns {boolean} true si la validación pasa, false si hay errores
 */
function validateAndShowPasswordErrors(password, confirmPassword = null) {
    const validation = validatePasswordComplete(password, confirmPassword);
    
    if (!validation.isValid && validation.errors.length > 0) {
        // Mostrar el primer error encontrado
        showPasswordError(validation.errors[0]);
        return false;
    }
    
    return true;
}

// Exportar funciones para uso global
if (typeof window !== 'undefined') {
    window.PASSWORD_CONFIG = PASSWORD_CONFIG;
    window.PASSWORD_MESSAGES = PASSWORD_MESSAGES;
    window.validatePassword = validatePassword;
    window.validatePasswordMatch = validatePasswordMatch;
    window.validatePasswordComplete = validatePasswordComplete;
    window.showPasswordError = showPasswordError;
    window.validateAndShowPasswordErrors = validateAndShowPasswordErrors;
}