<?php
// Start the session to access session data
session_start();

// Log the logout event if the logger is available
if (class_exists('ChatbotLogger')) {
    require_once('config.php');
    
    $userId = isset($_SESSION['verified_user']['IDNO']) ? $_SESSION['verified_user']['IDNO'] : 'unknown';
    $userEmail = isset($_SESSION['verified_email']) ? $_SESSION['verified_email'] : 'unknown';
    
    ChatbotLogger::info("User logged out", [
        'user_id' => $userId,
        'email' => $userEmail
    ]);
}

// Clear all session variables
$_SESSION = array();

// If a session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the verification page
header("Location: verify_email.php");
exit;
?>
