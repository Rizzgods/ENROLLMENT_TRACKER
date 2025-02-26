<?php
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : 'N/A';
$password = isset($_GET['password']) ? htmlspecialchars($_GET['password']) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
    <div class="container mx-auto">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <div class="text-center mb-8">
                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Form Successfully Submitted!</h3>
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="text-gray-700 mb-2"><strong>Your Login Credentials:</strong></p>
                    <p class="text-gray-600">Username: <?php echo $username; ?></p>
                    <p class="text-gray-600">Password: <?php echo $password; ?></p>
                    <p class="text-sm text-gray-500 mt-2">Please save these credentials and check your email for more details.</p>
                </div>
                <p class="text-gray-600 text-center mb-4">A confirmation email has been sent to your address.</p>
                <p class="text-gray-500 text-center text-sm">Redirecting to Home in <span id="countdownTimer">3</span>s</p>
            </div>
        </div>
    </div>
    <script>
        let countdown = 3;
        const countdownElement = document.getElementById('countdownTimer');
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'home.php';
            }
        }, 1000);
    </script>
</body>
</html>