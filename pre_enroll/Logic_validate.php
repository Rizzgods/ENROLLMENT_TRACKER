<?php
// Start session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// Update database credentials for the production server
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection - using standard parameter order for compatibility
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

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

    // Get the absolute URL for the logo
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $logo_url = 'https://admission.bcpsms4.com/pre_enroll/assets/logo.png';

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

        // Add these lines to fix SSL certificate verification issue
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('taranavalvista@gmail.com', 'Bestlink Enrollment Team');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code - Bestlink Enrollment';
        
        // Improved HTML email template with the OTP code
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
                <!-- Header with Logo and Title on same row -->
                <div style='background-color: #1a56db; padding: 15px 20px; text-align: left; border-radius: 8px 8px 0 0; display: flex; align-items: center;'>
                    <img src='{$logo_url}' alt='Bestlink Logo' style='max-height: 60px; margin-right: 15px;'>
                    <h1 style='color: white; margin: 0; font-size: 20px;'>BESTLINK ENROLLMENT SYSTEM</h1>
                </div>
                
                <!-- Email Content -->
                <div style='background-color: #f9fafb; border-radius: 0 0 8px 8px; padding: 20px; border: 1px solid #e5e7eb; border-top: none;'>
                    <h3 style='color: #4A5568; margin-top: 0;'>Email Verification</h3>
                    <p>Thank you for starting the enrollment process at Bestlink College of the Philippines.</p>
                    
                    <div style='background-color: #ebf5ff; border-radius: 8px; padding: 20px; margin: 25px 0; text-align: center; border: 1px solid #bfdbfe;'>
                        <p style='margin: 0; font-size: 16px;'>Your verification code is:</p>
                        <div style='font-size: 32px; font-weight: bold; letter-spacing: 5px; margin: 15px 0; color: #1e40af;'>$otp</div>
                        <p style='margin: 0; font-size: 14px; color: #6B7280;'>This code will expire in 10 minutes</p>
                    </div>
                    
                    <div style='background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                        <p style='margin: 0; font-size: 14px;'><strong>Security Notice:</strong> If you didn't request this code, please ignore this email or contact support if you have concerns.</p>
                    </div>
                    
                    <p>Once verified, you'll be able to continue with your enrollment application.</p>
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
        
        // Plain text alternative for email clients that don't support HTML
        $mail->AltBody = "Your verification code is: $otp. This code will expire in 10 minutes. If you did not request this code, please ignore this email.";

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