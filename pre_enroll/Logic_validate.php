<?php
// Start session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgreenvalley";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sendOTP"])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid email']);
        exit;
    }
    
    // Generate a 6-digit OTP
    $otp = rand(100000, 999999);
    
    // Store OTP in session for verification later
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + (10 * 60);

    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'taranavalvista@gmail.com'; // Your email
        $mail->Password   = 'kdiq oeqm cuyr yhuz'; // Your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('taranavalvista@gmail.com', 'Enrollment OTP');
        $mail->addAddress($email);

        $mail->Subject = 'Your OTP for Enrollment';
        $mail->Body    = "Your OTP is: $otp. Please enter this code to continue. The OTP will expire in 10 minutes";

        $mail->send();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error sending OTP: ' . $mail->ErrorInfo]);
    }
    exit;
}

if (isset($_POST['verifyOTP'])) {
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry'])) {
        echo "expired";
        exit();
    }

    if (time() > $_SESSION['otp_expiry']) {
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expiry']);
        echo "expired";
        exit();
    }

    if ($_POST['otp'] == $_SESSION['otp']) {
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expiry']);
        echo "success";
    } else {
        echo "invalid";
    }
    exit();
}
?>