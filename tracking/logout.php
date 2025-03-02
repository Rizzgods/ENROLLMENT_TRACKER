<?php
session_start();

// Get the user ID before unsetting session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Log the logout activity if user_id exists
if ($user_id) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbgreenvalley";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if (!$conn->connect_error) {
        $logStmt = $conn->prepare("INSERT INTO tbllogs (USERID, LOGDATETIME, LOGROLE, LOGMODE) VALUES (?, NOW(), 'Student', 'Logout')");
        $logStmt->bind_param("i", $user_id);
        $logStmt->execute();
        $logStmt->close();
        $conn->close();
    }
}

session_unset();  // Unset all session variables
session_destroy(); // Destroy the session
header("Location: ../pre_enroll/home.php"); // Redirect to login page
exit();
?>