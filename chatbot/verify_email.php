<?php
// Import PHPMailer classes at the top of the file (must come before any output)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// First include config for session settings
require_once('config.php');
// Then start the session
session_start();
require_once('db_functions.php');

// Include the PHPMailer libraries
require_once '../vendor/phpmailer/phpmailer/src/Exception.php';
require_once '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';

// Initialize message variables
$message = '';
$error = '';

ChatbotLogger::info("Email verification page accessed", [
    'page' => 'verify_email.php',
    'ip' => $_SERVER['REMOTE_ADDR']
]);

// Check if already verified
if (isset($_SESSION['verified_email']) && !empty($_SESSION['verified_email'])) {
    // User already verified, redirect to chat interface
    ChatbotLogger::info("User already verified, redirecting to chat", [
        'email' => $_SESSION['verified_email']
    ]);
    header('Location: chat.php');
    exit;
}

// Process email submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    ChatbotLogger::info("Email verification attempt", [
        'email' => $email
    ]);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email exists in database
        $conn = connectToDatabase();
        
        if (!$conn) {
            $error = "Database connection error. Please try again later.";
            ChatbotLogger::error("Database connection failed during email verification");
        } else {
            // Check in student table for email
            try {
                $student = getStudentByEmail($email);
                
                if ($student) {
                    // Generate verification code
                    $verificationCode = mt_rand(100000, 999999);
                    
                    // Store in session
                    $_SESSION['temp_email'] = $email;
                    $_SESSION['verification_code'] = $verificationCode;
                    $_SESSION['student_data'] = $student;
                    $_SESSION['verification_time'] = time();
                    
                    ChatbotLogger::info("Verification code generated", [
                        'email' => $email,
                        'code' => $verificationCode,
                        'studentId' => $student['IDNO']
                    ]);
                    
                    // Send email with verification code
                    if (sendVerificationEmail($email, $verificationCode, $student)) {
                        header('Location: verify_code.php');
                        exit;
                    } else {
                        $error = "Failed to send verification email. Please try again.";
                        ChatbotLogger::error("Failed to send verification email", [
                            'email' => $email
                        ]);
                    }
                } else {
                    $error = "This email address is not registered in our system. Please use the email associated with your student account.";
                    ChatbotLogger::warning("Email not found in database", [
                        'email' => $email
                    ]);
                }
            } catch (Exception $e) {
                $error = "An error occurred while verifying your email. Please try again.";
                ChatbotLogger::error("Exception during email verification", $e);
            }
            
            if ($conn) $conn->close();
        }
    } else {
        $error = "Please enter a valid email address.";
        ChatbotLogger::warning("Invalid email format submitted", [
            'email' => $email
        ]);
    }
}

/**
 * Send verification email with code
 * 
 * @param string $email Recipient email
 * @param string $code Verification code
 * @param array $student Student information
 * @return bool Whether email was sent successfully
 */
function sendVerificationEmail($email, $code, $student) {
    $fullName = $student['FNAME'] . ' ' . 
              (!empty($student['MNAME']) ? $student['MNAME'] . ' ' : '') . 
              $student['LNAME'];
    
    // For development/testing, you might want to output instead of sending
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
        // Log instead of send in development mode
        ChatbotLogger::info("Development mode: Email not sent, logging instead", [
            'email' => $email,
            'code' => $code,
            'student' => $fullName
        ]);
        
        // Even in development mode, let's still try to send the email
        // But return true even if it fails
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_FROM; // Using the constant from config
        $mail->Password = EMAIL_PASSWORD; // Using the constant from config
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom(EMAIL_FROM, 'Bestlink College Enrollment System');
        $mail->addAddress($email, $fullName);
        $mail->addReplyTo(EMAIL_REPLY_TO, 'Support Team');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verification Code - Bestlink College Chatbot';
        
        // HTML Email content
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 5px; padding: 20px; color: #333;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h2 style='color: #1d4ed8;'>Bestlink College of the Philippines</h2>
                <p style='font-size: 18px; font-weight: bold;'>Verification Code</p>
            </div>
            
            <div style='margin-bottom: 25px;'>
                <p>Hello {$fullName},</p>
                <p>You recently requested access to the Enrollment Assistant Chatbot. Use the verification code below to complete your sign-in:</p>
            </div>
            
            <div style='background-color: #f3f4f6; padding: 15px; text-align: center; border-radius: 4px; margin: 20px 0;'>
                <span style='font-size: 24px; font-weight: bold; letter-spacing: 5px;'>{$code}</span>
            </div>
            
            <div>
                <p>This code will expire in 15 minutes.</p>
                <p>If you didn't request this code, you can safely ignore this email.</p>
            </div>
            
            <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; font-size: 12px; color: #666; text-align: center;'>
                <p>This is an automated message, please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " Bestlink College of the Philippines</p>
            </div>
        </div>
        ";
        
        // Plain text version for email clients that don't support HTML
        $mail->AltBody = "Hello {$fullName},\n\nYour verification code for Bestlink College Chatbot is: {$code}\n\nThis code will expire in 15 minutes.\n\nIf you didn't request this code, you can safely ignore this email.";
        
        $mail->send();
        ChatbotLogger::info("Verification email sent successfully", ['email' => $email]);
        return true;
    } catch (Exception $e) {
        ChatbotLogger::error("Mail sending failed", [
            'error' => $mail->ErrorInfo,
            'email' => $email
        ]);
        
        // In development mode, return true even if email fails
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            return true;
        }
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Bestlink College Chatbot</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6">
        <div class="text-center">
            <!-- Replace with actual college logo -->
            <div class="mx-auto h-24 w-24 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">Enrollment Assistant</h1>
            <h2 class="text-lg font-medium text-blue-600 mb-6">Bestlink College of the Philippines</h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-gray-700 mb-6">
            <p class="mb-4">To access your student information through our chatbot, please verify your student email address.</p>
            <p>We'll send a verification code to confirm your identity.</p>
        </div>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">Student Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="youremail@example.com"
                    required
                >
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-colors"
            >
                Send Verification Code
            </button>
        </form>
        
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Need help? Contact the IT Department at support@bestlink.edu.ph</p>
        </div>
    </div>
</body>
</html>
