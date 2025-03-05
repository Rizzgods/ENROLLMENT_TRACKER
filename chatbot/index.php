<?php
// Start a session
session_start();

// If the user is already verified, go to chat interface
if (isset($_SESSION['verified_email']) && isset($_SESSION['verified_user'])) {
    header('Location: chat.php');
    exit;
}

// Otherwise, redirect to email verification
header('Location: verify_email.php');
exit;
?>
