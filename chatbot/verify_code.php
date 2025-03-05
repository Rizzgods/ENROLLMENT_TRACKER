<?php
// First include config for session settings
require_once('config.php');
// Then start the session
session_start();

ChatbotLogger::info("Code verification page accessed", [
    'page' => 'verify_code.php',
    'ip' => $_SERVER['REMOTE_ADDR']
]);

// Check if we have necessary session data
if (!isset($_SESSION['temp_email']) || 
    !isset($_SESSION['verification_code']) || 
    !isset($_SESSION['verification_time'])) {
    // Redirect to email entry page if missing session data
    ChatbotLogger::warning("Missing session data for code verification, redirecting to email verification");
    header('Location: verify_email.php');
    exit;
}

// Initialize message variables
$message = '';
$error = '';

// Check for code expiration (15 minutes)
$expirationTime = $_SESSION['verification_time'] + (15 * 60); // 15 minutes in seconds
if (time() > $expirationTime) {
    // Code expired, clear session
    ChatbotLogger::warning("Verification code expired", [
        'email' => $_SESSION['temp_email'],
        'time_elapsed' => (time() - $_SESSION['verification_time']) . ' seconds'
    ]);
    
    unset($_SESSION['temp_email']);
    unset($_SESSION['verification_code']);
    unset($_SESSION['verification_time']);
    
    $error = "Your verification code has expired. Please request a new one.";
    
    // After 3 seconds, redirect
    header("Refresh: 3; URL=verify_email.php");
}

// Process verification code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $submittedCode = trim($_POST['verification_code']);
    
    ChatbotLogger::info("Code verification attempt", [
        'email' => $_SESSION['temp_email'],
        'submitted_code' => $submittedCode,
        'expected_code' => $_SESSION['verification_code']
    ]);
    
    if (empty($submittedCode)) {
        $error = "Please enter the verification code.";
        ChatbotLogger::warning("Empty verification code submitted");
    } elseif ($submittedCode == $_SESSION['verification_code']) {
        // Success - set verified status
        $_SESSION['verified_email'] = $_SESSION['temp_email'];
        $_SESSION['verified_user'] = $_SESSION['student_data'];
        
        ChatbotLogger::info("Successful verification", [
            'email' => $_SESSION['verified_email'],
            'studentId' => $_SESSION['verified_user']['IDNO']
        ]);
        
        // Clear temporary data
        unset($_SESSION['temp_email']);
        unset($_SESSION['verification_code']);
        unset($_SESSION['verification_time']);
        
        // Redirect to chatbot
        header('Location: chat.php');
        exit;
    } else {
        $error = "Invalid verification code. Please try again.";
        ChatbotLogger::warning("Invalid verification code submitted", [
            'email' => $_SESSION['temp_email'],
            'submitted_code' => $submittedCode,
            'expected_code' => $_SESSION['verification_code'],
            'attempts' => ($_SESSION['code_attempts'] ?? 0) + 1
        ]);
        
        // Track attempts
        $_SESSION['code_attempts'] = ($_SESSION['code_attempts'] ?? 0) + 1;
        
        // If too many failed attempts, ask for new code
        if ($_SESSION['code_attempts'] >= 5) {
            ChatbotLogger::warning("Too many failed verification attempts", [
                'email' => $_SESSION['temp_email'],
                'attempts' => $_SESSION['code_attempts']
            ]);
            
            // Clear verification data
            unset($_SESSION['verification_code']);
            unset($_SESSION['verification_time']);
            unset($_SESSION['code_attempts']);
            
            $error = "Too many failed attempts. Please request a new verification code.";
            header("Refresh: 3; URL=verify_email.php");
        }
    }
}

// Handle resend request
if (isset($_GET['action']) && $_GET['action'] === 'resend') {
    ChatbotLogger::info("Resend verification code requested", [
        'email' => $_SESSION['temp_email']
    ]);
    
    // Reset verification time
    $_SESSION['verification_time'] = time();
    
    // Generate new code
    $newCode = mt_rand(100000, 999999);
    $_SESSION['verification_code'] = $newCode;
    
    // Reset attempts
    $_SESSION['code_attempts'] = 0;
    
    // Resend email
    require_once('verify_email.php'); // Include to access the sendVerificationEmail function
    
    // Call the function from verify_email.php
    if (function_exists('sendVerificationEmail') && sendVerificationEmail($_SESSION['temp_email'], $newCode, $_SESSION['student_data'])) {
        $message = "A new verification code has been sent to your email.";
        ChatbotLogger::info("New verification code sent", [
            'email' => $_SESSION['temp_email'],
            'new_code' => $newCode
        ]);
    } else {
        $error = "Failed to send verification code. Please try again.";
        ChatbotLogger::error("Failed to send new verification code", [
            'email' => $_SESSION['temp_email']
        ]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Verification - Bestlink College Chatbot</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6">
        <div class="text-center">
            <!-- College logo -->
            <div class="mx-auto h-24 w-24 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">Verify Your Email</h1>
            <h2 class="text-lg font-medium text-blue-600 mb-6">Bestlink College of the Philippines</h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-gray-700 mb-6">
            <p class="mb-2">We've sent a 6-digit verification code to:</p>
            <p class="font-medium text-blue-600"><?php echo htmlspecialchars($_SESSION['temp_email']); ?></p>
            <p class="mt-2 text-sm">Please enter the code below to verify your identity.</p>
        </div>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
            <div>
                <label for="verification_code" class="block text-gray-700 font-medium mb-1">Verification Code</label>
                <input 
                    type="text" 
                    id="verification_code" 
                    name="verification_code" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-xl tracking-wider"
                    placeholder="123456"
                    maxlength="6"
                    autocomplete="off"
                    required
                >
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-colors"
            >
                Verify Code
            </button>
        </form>
        
        <div class="mt-4 flex items-center justify-center space-x-1 text-sm">
            <span class="text-gray-500">Didn't receive the code?</span>
            <a href="?action=resend" class="text-blue-600 hover:text-blue-800">Resend Code</a>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Need help? Contact the IT Department at support@bestlink.edu.ph</p>
        </div>
    </div>
</body>
</html>
