<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detect environment

    define("server", "localhost"); // Check if this should be a different host
    define("user", "admi_greenvalley"); // Check if this is correct
    define("pass", "xr9%kxu%*my^+kf2"); // Check if password is correct
    define("database_name", "admi_dbgreenvalley");
}
// Test database connection
try {
    $test_connection = mysqli_connect(
        constant('server'), 
        constant('user'), 
        constant('pass'), 
        constant('database_name')
    );

    if (!$test_connection) {
        $error_msg = "Failed to connect to MySQL: " . mysqli_connect_error();
        error_log($error_msg);
        die($error_msg); // Show actual error
    }

    mysqli_close($test_connection);
} catch (Exception $e) {
    error_log("Exception during database connection test: " . $e->getMessage());
    die("An error occurred: " . $e->getMessage());
}



// Path configurations
$this_file = str_replace('\\', '/', __FILE__); // Fixed: _File_ to __FILE__ (double underscore)
$doc_root = $_SERVER['DOCUMENT_ROOT'];

$web_root = str_replace(array($doc_root, "include/config.php"), '', $this_file);
$server_root = str_replace('config/config.php', '', $this_file);

define('web_root', $web_root);
define('server_root', $server_root);

// Add helpful constants for your application
define('SITE_TITLE', 'Online Enrollment System');

// Error reporting - change to 0 for production
define('SHOW_ERROR_DETAIL', 0);
?>