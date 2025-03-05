<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgreenvalley";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == "forgot_password") { 
    $email = trim($_POST['email']);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT IDNO, FNAME, LNAME FROM tblstudent WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "Email not found"]);
        exit();
    }

    $fname = $user['FNAME'];
    $lname = $user['LNAME'];

    // Generate a unique token
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store the reset token in the database
    $stmt = $conn->prepare("UPDATE tblstudent SET reset_token = ?, reset_expiry = ? WHERE EMAIL = ?");
    $stmt->bind_param("sss", $token, $expiry, $email);
    $stmt->execute();
    $stmt->close();

    // Send reset email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'taranavalvista@gmail.com'; 
        $mail->Password   = 'kdiq oeqm cuyr yhuz'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('taranavalvista@gmail.com', 'Enrollment Team');
        $mail->addAddress($email, "$fname $lname"); 

        $mail->isHTML(true);
        $mail->Subject = "Bestlink Account Password Reset";
        $reset_link = "http://localhost/onlineenrolmentsystem/tracking/passwordreset.php?token=" . $token;
        $mail->Body = "Hello <b>$fname $lname</b>,<br><br>Click the link below to reset your password:<br><a href='$reset_link'>$reset_link</a><br><br>If you didn't request this, please ignore this email.";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Password reset link has been sent to your email."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Email sending failed: {$mail->ErrorInfo}"]);
    }

    exit();
}
?>