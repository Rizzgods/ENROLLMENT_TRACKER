<?php
ob_start(); // Start output buffering

$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['USERNAME']);
    $password = trim($_POST['PASSWORD']);

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT id, USERNAME, PASSWORD FROM superadmin WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['PASSWORD'])) {
        session_start();
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['id'] = $user['id'];
        $_SESSION['USERNAME'] = $user['USERNAME'];

        header("Location: index.php");
        exit(); // Ensure no further code runs
    } else {
        header("Location: login.php?error=" . urlencode("Invalid username or password."));
        exit();
    }
}

ob_end_flush(); // End output buffering
?>
