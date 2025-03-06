<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/email_test_errors.log');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__. '/../vendor/autoload.php';

// Start output buffering
ob_start();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Email Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .result { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        pre { background: #f8f9fa; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>PHPMailer Test</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? 'Email Test';
    $message = $_POST['message'] ?? 'This is a test email from the enrollment system.';
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Enable verbose debug output
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        
        // Capture debug output
        $mail->Debugoutput = function($str, $level) {
            echo "<pre>$str</pre>";
        };
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'taranavalvista@gmail.com'; // Your email
        $mail->Password   = 'kdiq oeqm cuyr yhuz'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('taranavalvista@gmail.com', 'Enrollment Team');
        $mail->addAddress($recipient);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        // Send the email
        if($mail->send()) {
            echo "<div class='result success'>
                    <h3>✓ Email sent successfully!</h3>
                    <p>Email was sent to: {$recipient}</p>
                  </div>";
        } else {
            throw new Exception("Email could not be sent");
        }
        
    } catch (Exception $e) {
        echo "<div class='result error'>
                <h3>✗ Email sending failed</h3>
                <p>Error: {$mail->ErrorInfo}</p>
              </div>";
    }
}

echo "
        <form method='post' action=''>
            <div style='margin-bottom: 15px;'>
                <label for='email' style='display: block; margin-bottom: 5px;'>Recipient Email:</label>
                <input type='email' id='email' name='email' required style='width: 100%; padding: 8px;'>
            </div>
            
            <div style='margin-bottom: 15px;'>
                <label for='subject' style='display: block; margin-bottom: 5px;'>Subject:</label>
                <input type='text' id='subject' name='subject' value='Email Test' style='width: 100%; padding: 8px;'>
            </div>
            
            <div style='margin-bottom: 15px;'>
                <label for='message' style='display: block; margin-bottom: 5px;'>Message:</label>
                <textarea id='message' name='message' rows='5' style='width: 100%; padding: 8px;'>This is a test email from the enrollment system.</textarea>
            </div>
            
            <div>
                <button type='submit' style='padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer;'>Send Test Email</button>
            </div>
        </form>
    </div>
</body>
</html>";
?>
