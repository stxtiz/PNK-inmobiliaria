<?php
/**
 * Centralized Error Handler for PNK Inmobiliaria
 * Provides secure error logging and user-friendly messaging
 */

class ErrorHandler {
    
    // Error levels
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_CRITICAL = 'CRITICAL';
    
    // Common error codes for user messages
    const ERROR_DB_CONNECTION = 'db_connection';
    const ERROR_DB_QUERY = 'db_error';
    const ERROR_VALIDATION = 'validation_error';
    const ERROR_FILE_UPLOAD = 'file_error';
    const ERROR_PERMISSION = 'permission_error';
    const ERROR_NOT_FOUND = 'not_found';
    const ERROR_EMAIL_EXISTS = 'email_exists';
    const ERROR_INVALID_FILE = 'invalid_file';
    
    /**
     * Log error with detailed information for developers
     * @param string $message Error message
     * @param string $level Error level
     * @param string $file Source file where error occurred
     * @param array $context Additional context data
     */
    public static function logError($message, $level = self::LEVEL_ERROR, $file = '', $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $fileStr = !empty($file) ? " | File: $file" : '';
        
        $logMessage = "[$timestamp] [$level]$fileStr $message$contextStr";
        error_log($logMessage);
    }
    
    /**
     * Handle database errors securely
     * @param string $operation Operation being performed
     * @param string $mysqli_error MySQL error message
     * @param string $file Source file
     * @param string $sql SQL query (optional, will be sanitized)
     * @return array Error response for JSON or redirect
     */
    public static function handleDatabaseError($operation, $mysqli_error, $file = '', $sql = '') {
        // Log detailed error for developers
        $context = [
            'operation' => $operation,
            'mysql_error' => $mysqli_error
        ];
        
        if (!empty($sql)) {
            // Sanitize SQL for logging (remove sensitive data)
            $context['sql'] = self::sanitizeSqlForLogging($sql);
        }
        
        self::logError("Database error during $operation: $mysqli_error", self::LEVEL_ERROR, $file, $context);
        
        // Return user-friendly error
        return [
            'success' => false,
            'error_code' => self::ERROR_DB_QUERY,
            'user_message' => 'Error en la base de datos. Por favor, intente nuevamente.',
            'developer_message' => $mysqli_error // Only for debugging, should be removed in production
        ];
    }
    
    /**
     * Handle validation errors
     * @param string $field Field name
     * @param string $message Validation message
     * @param string $file Source file
     * @return array Error response
     */
    public static function handleValidationError($field, $message, $file = '') {
        self::logError("Validation error in field '$field': $message", self::LEVEL_WARNING, $file);
        
        return [
            'success' => false,
            'error_code' => self::ERROR_VALIDATION,
            'user_message' => "Error de validación: $message",
            'field' => $field
        ];
    }
    
    /**
     * Handle file upload errors
     * @param string $filename Original filename
     * @param int $error_code PHP upload error code
     * @param string $file Source file
     * @return array Error response
     */
    public static function handleFileError($filename, $error_code, $file = '') {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo es demasiado grande',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño permitido',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Error del servidor: directorio temporal no encontrado',
            UPLOAD_ERR_CANT_WRITE => 'Error del servidor: no se puede escribir el archivo',
            UPLOAD_ERR_EXTENSION => 'Extensión de archivo no permitida'
        ];
        
        $user_message = isset($error_messages[$error_code]) ? 
                       $error_messages[$error_code] : 
                       'Error desconocido al subir el archivo';
        
        self::logError("File upload error for '$filename': Code $error_code", self::LEVEL_ERROR, $file, ['filename' => $filename, 'error_code' => $error_code]);
        
        return [
            'success' => false,
            'error_code' => self::ERROR_FILE_UPLOAD,
            'user_message' => $user_message
        ];
    }
    
    /**
     * Handle general application errors
     * @param string $message Error message
     * @param string $code Error code
     * @param string $file Source file
     * @param array $context Additional context
     * @return array Error response
     */
    public static function handleError($message, $code = 'general_error', $file = '', $context = []) {
        self::logError($message, self::LEVEL_ERROR, $file, $context);
        
        // Map technical error codes to user-friendly messages
        $user_messages = [
            self::ERROR_EMAIL_EXISTS => 'Este correo electrónico ya está registrado',
            self::ERROR_INVALID_FILE => 'Tipo de archivo no válido. Solo se permiten archivos PDF',
            self::ERROR_NOT_FOUND => 'El registro solicitado no fue encontrado',
            self::ERROR_PERMISSION => 'No tiene permisos para realizar esta acción',
            'general_error' => 'Ha ocurrido un error. Por favor, intente nuevamente'
        ];
        
        $user_message = isset($user_messages[$code]) ? $user_messages[$code] : $user_messages['general_error'];
        
        return [
            'success' => false,
            'error_code' => $code,
            'user_message' => $user_message
        ];
    }
    
    /**
     * Generate redirect URL with error parameters
     * @param string $base_url Base URL to redirect to
     * @param string $error_code Error code
     * @param array $additional_params Additional parameters
     * @return string Complete redirect URL
     */
    public static function generateErrorRedirect($base_url, $error_code, $additional_params = []) {
        $params = array_merge(['error' => $error_code], $additional_params);
        $query_string = http_build_query($params);
        
        return $base_url . '?' . $query_string;
    }
    
    /**
     * Sanitize SQL query for logging (remove sensitive information)
     * @param string $sql SQL query
     * @return string Sanitized SQL
     */
    private static function sanitizeSqlForLogging($sql) {
        // Remove potential passwords and sensitive data
        $sql = preg_replace("/password\s*=\s*'[^']*'/i", "password='***'", $sql);
        $sql = preg_replace("/clave\s*=\s*'[^']*'/i", "clave='***'", $sql);
        
        // Limit query length for logging
        if (strlen($sql) > 500) {
            $sql = substr($sql, 0, 500) . '... [truncated]';
        }
        
        return $sql;
    }
    
    /**
     * Log successful operations for audit trail
     * @param string $operation Operation performed
     * @param string $file Source file
     * @param array $context Additional context
     */
    public static function logSuccess($operation, $file = '', $context = []) {
        self::logError("Success: $operation", self::LEVEL_INFO, $file, $context);
    }
    
    /**
     * Create JSON response for AJAX requests
     * @param array $error_response Error response from handle methods
     * @return string JSON response
     */
    public static function jsonResponse($error_response) {
        // Remove developer_message in production
        if (isset($error_response['developer_message']) && !defined('DEBUG_MODE')) {
            unset($error_response['developer_message']);
        }
        
        return json_encode($error_response);
    }
}