<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgreenvalley";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'] ?? null; // Ensure token is set
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$token) {
        echo json_encode(["status" => "error", "message" => "Token is missing."]);
        exit();
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit();
    }

    // Encrypt password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    date_default_timezone_set('Asia/Manila');

    // Find user linked to the token in tblstudent
    $stmt = $conn->prepare("SELECT IDNO FROM tblstudent WHERE reset_token = ? AND (reset_expiry IS NULL OR reset_expiry > NOW())");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Query error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid or expired token."]);
        exit();
    }

    $stmt->bind_result($idno);
    $stmt->fetch();
    $stmt->close();

    // Update password in studentaccount using user_id (linked to IDNO)
    $updateStmt = $conn->prepare("UPDATE studentaccount SET password = ? WHERE user_id = ?");
    if (!$updateStmt) {
        echo json_encode(["status" => "error", "message" => "Query error: " . $conn->error]);
        exit();
    }

    $updateStmt->bind_param("si", $hashed_password, $idno);

    if ($updateStmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password has been successfully reset."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update password."]);
    }
    exit();
}
?>