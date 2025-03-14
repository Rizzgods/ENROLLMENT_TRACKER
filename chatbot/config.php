<?php
// IMPORTANT: Session settings must be set before session_start()
// Security settings
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    // Configure session settings before starting the session
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    ini_set('session.cookie_httponly', 1);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    // Now it's safe to start the session if needed
    // session_start(); // Uncomment if you need to start session here
}

// Database configuration
$dbConfig = [
    'server' => 'localhost',
    'username' => 'root',
    'password' => '', // Set your database password here
    'dbname' => 'schooldb' // Set your database name here
];

// Add PDF handling configuration
define('ENABLE_PDF_EXTRACTION', true);
define('PDF_MAX_PAGES', 3); // Maximum pages to process from a PDF

// API configuration for AI service
$apiKey = 'sk-2048931a5e4543338b01664399691300'; // Your DeepSeek API key
$apiEndpoint = 'https://api.deepseek.com/v1/chat/completions'; // Updated API endpoint with correct path

// Add a flag indicating if this is a local/test environment without true API access
define('LOCAL_DEV_MODE', false); // Set to false in production when API is properly configured

// Add a fallback mode setting to determine what to do when API fails
define('API_FALLBACK_MODE', true); // Keep fallback as an option if API fails

// Set to true during development to avoid sending actual emails
define('DEVELOPMENT_MODE', false);

// Email settings
define('EMAIL_FROM', 'taranavalvista@gmail.com');
define('EMAIL_REPLY_TO', 'support@bestlink.edu.ph');
define('EMAIL_PASSWORD', 'kdiq oeqm cuyr yhuz');

// Logging settings
define('LOG_LEVEL', 'DEBUG'); // Options: ERROR, WARNING, INFO, DEBUG
define('LOG_QUERIES', true); // Whether to log database queries

// Add document validation configuration
define('DOCUMENT_VALIDATION_STRICTNESS', 'medium'); // options: low, medium, high
define('MAX_DOCUMENT_VALIDATION_ATTEMPTS', 3);

// Add OCR configuration for better text extraction
define('OCR_STRICT_TEXT_ONLY', true); // Ensures DeepSeek returns only text, not explanations
define('OCR_MAX_RETRIES', 2); // Number of retry attempts for failed OCR

// Include the logger if it exists
$loggerPath = __DIR__ . '/utils/logger.php';
if (file_exists($loggerPath)) {
    require_once($loggerPath);
} else {
    // Define simple fallback logging function if logger doesn't exist
    if (!class_exists('ChatbotLogger')) {
        class ChatbotLogger {
            public static function init() {}
            public static function log($message, $level = 'INFO', $context = []) {
                error_log("[$level] $message " . (!empty($context) ? json_encode($context) : ''));
            }
            public static function error($message, $exception = null) {
                self::log($message, 'ERROR', $exception ? ['exception' => $exception] : []);
            }
            public static function warning($message, $context = []) { self::log($message, 'WARNING', $context); }
            public static function info($message, $context = []) { self::log($message, 'INFO', $context); }
            public static function debug($message, $context = []) { self::log($message, 'DEBUG', $context); }
            public static function logQuery($query, $params = []) {
                if (defined('LOG_QUERIES') && LOG_QUERIES) {
                    self::debug("Database Query", ['query' => $query, 'params' => $params]);
                }
            }
        }
        ChatbotLogger::init();
    }
}
?>