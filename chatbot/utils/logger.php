<?php
/**
 * Logging utility for chatbot system
 */
class ChatbotLogger {
    // Log levels
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    
    // Log file paths - using relative paths that will work on Windows
    private static $logDir = null;
    private static $logFile = null;
    private static $errorLogFile = null;
    
    /**
     * Initialize logger - create log directory if it doesn't exist
     */
    public static function init() {
        // Create absolute paths based on __DIR__ which is reliable
        self::$logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs';
        self::$logFile = self::$logDir . DIRECTORY_SEPARATOR . 'chatbot.log';
        self::$errorLogFile = self::$logDir . DIRECTORY_SEPARATOR . 'errors.log';
        
        // Ensure log directory exists with proper permissions
        if (!file_exists(self::$logDir)) {
            try {
                // Create directory with recursive option
                if (!mkdir(self::$logDir, 0755, true)) {
                    error_log("Failed to create log directory: " . self::$logDir);
                }
            } catch (Exception $e) {
                error_log("Exception creating log directory: " . $e->getMessage());
            }
        }
        
        // Test if we can write to the directory
        if (!is_writable(self::$logDir)) {
            error_log("Log directory is not writable: " . self::$logDir);
        }
    }
    
    /**
     * Log a message with specified level
     * 
     * @param string $message The log message
     * @param string $level Log level (ERROR, WARNING, INFO, DEBUG)
     * @param array $context Additional context data
     */
    public static function log($message, $level = self::INFO, $context = []) {
        try {
            // Format the log entry
            $timestamp = date('Y-m-d H:i:s');
            $sessionId = session_id() ?: 'no-session';
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            // Format context data if any
            $contextData = '';
            if (!empty($context)) {
                $contextData = ' - ' . json_encode($context, JSON_UNESCAPED_SLASHES);
            }
            
            $logEntry = "[$timestamp] [$level] [$sessionId] [$ipAddress] - $message$contextData" . PHP_EOL;
            
            // Write to log file - with full error handling
            try {
                if (!file_exists(self::$logDir)) {
                    // Try one more time to create the directory if it doesn't exist
                    mkdir(self::$logDir, 0755, true);
                }
                
                file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
                
                // Also write errors to a separate error log file
                if ($level === self::ERROR) {
                    file_put_contents(self::$errorLogFile, $logEntry, FILE_APPEND);
                }
            } catch (Exception $e) {
                // Last resort: write to PHP's error log
                error_log("Failed to write to custom log: " . $e->getMessage());
                error_log($logEntry);
            }
        } catch (Exception $e) {
            // If anything goes wrong in our logging, use PHP's built-in error_log as fallback
            error_log("Logging system failure: " . $e->getMessage());
            error_log($message);
        }
    }
    
    /**
     * Log an error message
     * 
     * @param string $message Error message
     * @param mixed $exception Exception object or additional context
     */
    public static function error($message, $exception = null) {
        $context = [];
        
        if ($exception instanceof Exception || $exception instanceof Error) {
            $context = [
                'exception_type' => get_class($exception),
                'exception_message' => $exception->getMessage(),
                'exception_code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        } elseif ($exception !== null) {
            $context = ['data' => $exception];
        }
        
        self::log($message, self::ERROR, $context);
    }
    
    /**
     * Log a warning message
     * 
     * @param string $message Warning message
     * @param array $context Additional context data
     */
    public static function warning($message, $context = []) {
        self::log($message, self::WARNING, $context);
    }
    
    /**
     * Log an info message
     * 
     * @param string $message Info message
     * @param array $context Additional context data
     */
    public static function info($message, $context = []) {
        self::log($message, self::INFO, $context);
    }
    
    /**
     * Log a debug message
     * 
     * @param string $message Debug message
     * @param array $context Additional context data
     */
    public static function debug($message, $context = []) {
        self::log($message, self::DEBUG, $context);
    }
    
    /**
     * Log database query for debugging purposes
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     */
    public static function logQuery($query, $params = []) {
        if (defined('LOG_QUERIES') && LOG_QUERIES) {
            self::debug("Database Query", [
                'query' => $query,
                'params' => $params
            ]);
        }
    }
}

// Initialize logger on file include
ChatbotLogger::init();
?>
