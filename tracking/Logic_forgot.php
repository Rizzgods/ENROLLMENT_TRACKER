<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// Database Connection
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

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

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

        // Get the current host (e.g., localhost or your domain)
        $host = $_SERVER['HTTP_HOST'];
        
        // Construct the absolute URL for the reset link
        $reset_link = $protocol . $host . '/tracking/passwordreset.php?token=' . $token;
        
        // Construct the absolute URL for the logo
        $logo_url = $protocol . $host . 'https://admission.bcpsms4.com/pre_enroll/assets/logo.png';
        
        $mail->isHTML(true);
        $mail->Subject = "Bestlink Account Password Reset";
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
            <!-- Header with Logo and Title on same row -->
            <div style='background-color: #1a56db; padding: 15px 20px; text-align: left; border-radius: 8px 8px 0 0; display: flex; align-items: center;'>
                <img src='{$logo_url}' alt='Bestlink Logo' style='max-height: 60px; margin-right: 15px;'>
                <h1 style='color: white; margin: 0; font-size: 20px;'>BESTLINK ENROLLMENT SYSTEM</h1>
            </div>
            
            <!-- Email Content -->
            <div style='background-color: #f9fafb; border-radius: 0 0 8px 8px; padding: 20px; border: 1px solid #e5e7eb; border-top: none;'>
                <h3 style='color: #4A5568; margin-top: 0;'>Password Reset Request</h3>
                <p>Hello <b>{$fname} {$lname}</b>,</p>
                <p>We received a request to reset your password for your Bestlink College student account.</p>
                
                <div style='background-color: #ebf5ff; border-radius: 8px; padding: 20px; margin: 25px 0; text-align: center; border: 1px solid #bfdbfe;'>
                    <p style='margin: 0 0 15px 0; font-size: 16px;'>Click the button below to reset your password:</p>
                    <a href='{$reset_link}' style='background-color: #1a56db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;'>Reset Password</a>
                    <p style='margin: 15px 0 0 0; font-size: 12px; color: #6B7280;'>This link will expire in 1 hour</p>
                </div>
                
                <div style='background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                    <p style='margin: 0; font-size: 14px;'><strong>Security Notice:</strong> If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
                </div>
                
                <p>If the button above doesn't work, you can copy and paste the following link into your browser:</p>
                <p style='background-color: #f3f4f6; padding: 10px; border-radius: 4px; word-break: break-all; font-size: 13px;'><a href='{$reset_link}' style='color: #3182CE;'>{$reset_link}</a></p>
                
                <p>If you have any questions, please contact our admissions office:</p>
                <p style='margin-bottom: 5px;'><b>Email:</b> <a href='mailto:admissions@bestlink.edu.ph' style='color: #3182CE;'>admissions@bestlink.edu.ph</a></p>
                <p><b>Phone:</b> (02) 8-123-4567</p>
                
                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 14px;'>
                    <p style='margin-bottom: 5px;'><b>Thank you!</b></p>
                    <p style='margin-top: 0; color: #6B7280;'>Bestlink Enrollment Team</p>
                </div>
            </div>
        </div>
        ";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Password reset link has been sent to your email."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Email sending failed: {$mail->ErrorInfo}"]);
    }

    exit();
}
?>