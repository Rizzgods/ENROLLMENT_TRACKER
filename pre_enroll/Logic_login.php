<?php
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection
$conn = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT user_id, username, password, SCHEDULE, STATUS FROM studentaccount WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['schedule'] = $user['SCHEDULE'];
        $_SESSION['status'] = $user['STATUS'];
        
        header("Location: profile.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>



