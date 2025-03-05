<?php
// IMPORTANT: Session settings must be set before session_start()
// Security settings
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_httponly', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

// Database configuration
$dbConfig = [
    'server' => 'localhost',
    'username' => 'root',
    'password' => '', // Set your database password here
    'dbname' => 'schooldb' // Set your database name here
];

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
