<?php
/**
 * Utilidad centralizada para validación de contraseñas en PHP
 * PNK Inmobiliaria - Validación unificada de contraseñas (servidor)
 */

class PasswordValidator {
    
    // Configuración unificada de requisitos de contraseña
    const MIN_LENGTH = 8;
    const SPECIAL_CHARS = '!@#$%^&*';
    const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/';
    
    // Mensajes de error unificados
    const ERROR_TITLE = 'Contraseña inválida';
    const ERROR_REQUIREMENTS = 'La contraseña debe cumplir con los siguientes requisitos:<br>' .
                              '- Mínimo 8 caracteres<br>' .
                              '- Al menos una letra mayúscula (A-Z)<br>' .
                              '- Al menos una letra minúscula (a-z)<br>' .
                              '- Al menos un número (0-9)<br>' .
                              '- Al menos un carácter especial (!@#$%^&*)';
    
    const MISMATCH_TITLE = 'Contraseñas no coinciden';
    const MISMATCH_MESSAGE = 'Las contraseñas ingresadas no coinciden. Por favor verifíquelas.';
    
    /**
     * Valida si una contraseña cumple con todos los requisitos
     * @param string $password La contraseña a validar
     * @return bool true si la contraseña es válida, false en caso contrario
     */
    public static function validatePassword($password) {
        if (empty($password) || !is_string($password)) {
            return false;
        }
        return preg_match(self::PASSWORD_REGEX, $password) === 1;
    }
    
    /**
     * Valida si dos contraseñas son iguales
     * @param string $password Primera contraseña
     * @param string $confirmPassword Segunda contraseña (confirmación)
     * @return bool true si las contraseñas coinciden, false en caso contrario
     */
    public static function validatePasswordMatch($password, $confirmPassword) {
        return $password === $confirmPassword;
    }
    
    /**
     * Realiza validación completa de contraseña incluyendo confirmación
     * @param string $password Contraseña principal
     * @param string $confirmPassword Contraseña de confirmación (opcional)
     * @return array Array con resultado de validación
     */
    public static function validatePasswordComplete($password, $confirmPassword = null) {
        $result = [
            'isValid' => true,
            'errors' => []
        ];
        
        // Validar formato de contraseña
        if (!self::validatePassword($password)) {
            $result['isValid'] = false;
            $result['errors'][] = [
                'type' => 'format',
                'title' => self::ERROR_TITLE,
                'message' => self::ERROR_REQUIREMENTS
            ];
        }
        
        // Validar coincidencia si se proporciona confirmación
        if ($confirmPassword !== null && !self::validatePasswordMatch($password, $confirmPassword)) {
            $result['isValid'] = false;
            $result['errors'][] = [
                'type' => 'mismatch',
                'title' => self::MISMATCH_TITLE,
                'message' => self::MISMATCH_MESSAGE
            ];
        }
        
        return $result;
    }
    
    /**
     * Obtiene los requisitos de contraseña como array
     * @return array Array con los requisitos
     */
    public static function getPasswordRequirements() {
        return [
            'minLength' => self::MIN_LENGTH,
            'requireUppercase' => true,
            'requireLowercase' => true,
            'requireNumber' => true,
            'requireSpecialChar' => true,
            'specialChars' => self::SPECIAL_CHARS,
            'regex' => self::PASSWORD_REGEX
        ];
    }
    
    /**
     * Obtiene los mensajes de error como array
     * @return array Array con los mensajes
     */
    public static function getErrorMessages() {
        return [
            'title' => self::ERROR_TITLE,
            'requirements' => self::ERROR_REQUIREMENTS,
            'mismatch' => [
                'title' => self::MISMATCH_TITLE,
                'text' => self::MISMATCH_MESSAGE
            ]
        ];
    }
    
    /**
     * Valida contraseña y devuelve error para redirección si no es válida
     * @param string $password Contraseña a validar
     * @param string $confirmPassword Contraseña de confirmación (opcional)
     * @return string|null Devuelve string de error para URL o null si es válida
     */
    public static function validateAndGetError($password, $confirmPassword = null) {
        $validation = self::validatePasswordComplete($password, $confirmPassword);
        
        if (!$validation['isValid'] && !empty($validation['errors'])) {
            $firstError = $validation['errors'][0];
            return $firstError['type'] === 'format' ? 'password_format' : 'password_mismatch';
        }
        
        return null;
    }
}
?>