<?php
// Set up error logging
error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/database_messages.log');


function log_db_activity($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message" . PHP_EOL;
    error_log($log_message, 3, __DIR__ . '/db_activity.log');
}

$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";


log_db_activity("Attempting to connect to database: $dbname");


$conn = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname);


if ($conn->connect_error) {
    log_db_activity("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
} else {
    log_db_activity("Database connection successful");
}
?>