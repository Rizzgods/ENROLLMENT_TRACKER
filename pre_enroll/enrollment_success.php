<?php
// Start the session
session_start();

// Check if user came from a successful enrollment
if (!isset($_SESSION['enrollment_success']) || $_SESSION['enrollment_success'] !== true) {
    // Redirect to home if they try to access this page directly
    header("Location: home.php");
    exit;
}

// Get the student ID and success message
$studentId = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : 'Your enrollment has been submitted successfully!';

// Clear the session variables
unset($_SESSION['enrollment_success']);
unset($_SESSION['student_id']);
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="flex items-center justify-center mb-4">
            <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Enrollment Successful!</h3>
        <p class="text-gray-600 text-center mb-4"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php if (!empty($studentId)): ?>
        <p class="text-gray-600 text-center mb-4">Your Student ID: <strong><?php echo htmlspecialchars($studentId); ?></strong></p>
        <?php endif; ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <p class="text-yellow-800">
                <strong>Note:</strong> Some documents require verification. Please bring original documents during your campus visit.
            </p>
        </div>
        <p class="text-gray-500 text-center text-sm">Redirecting to Home in <span id="countdownTimer">5</span>s</p>
        
        <!-- Redirect back to home automatically -->
        <script>
            // Set up countdown timer for redirect
            let countdown = 5;
            const timerElement = document.getElementById('countdownTimer');
            
            const countdownInterval = setInterval(function() {
                countdown--;
                timerElement.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = 'home.php';
                }
            }, 1000);
        </script>
    </div>
</body>
</html>
