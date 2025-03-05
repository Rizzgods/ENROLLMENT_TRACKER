<?php
session_start();
include 'Logic_login.php';
include 'Logic_forgot.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Student Login | Bestlink Enrollment Tracker</title>
</head>
<body class="bg-[url('assets/bestlink.jpg')] bg-cover relative">

    <!-- Grey Overlay -->
    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>

    <!-- Main Container -->
    <div class="container mx-auto flex justify-center items-center min-h-screen p-4 relative z-10">

    <div id="forgotPasswordModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Forgot Password</h2>
        <form id="forgotPasswordForm" method="POST" action="Logic_forgot.php">
        <input type="hidden" name="form_type" value="forgot_password">
            <input type="email" 
                   name="email" 
                   class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none mb-4" 
                   placeholder="Enter your email"
                   required>
            <button type="submit" 
                    name="resetsubmit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-3">
                Send Reset Link
            </button>
        </form>
        <button id="closeModal" class="mt-4 text-sm text-gray-600 hover:underline">Close</button>
    </div>
</div>       


<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
        <h2 class="text-xl font-bold mb-4 text-green-600">Success!</h2>
        <p class="text-gray-700">An email with a reset link has been sent to your email.</p>
        <button id="closeSuccessModal" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-3">
            OK
        </button>
    </div>
</div>

        <!-- Login Container -->
        <div class="bg-white rounded-2xl shadow-lg flex flex-col md:flex-row w-full max-w-4xl">

            <!-- Left Box -->
            <div class="md:w-1/2 bg-[#103cbe] rounded-l-2xl p-8 flex flex-col justify-center items-center">
                <div class="mb-6">
                    <img src="assets/logo.png" class="w-64" alt="Logo">
                </div>
                <h2 class="text-white text-center text-2xl font-bold  mb-2">
                    Bestlink College of the Philippines
                </h2>
                <p class="text-white text-center w-72 ">
                    Enrollment Tracker System
                </p>
            </div>

            <!-- Right Box -->
            <div class="md:w-1/2 p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-2">Hello, Again</h2>
                    <p class="text-gray-600">We are happy to have you back.</p>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($error)): ?>
                <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="" class="space-y-4">
                <input type="hidden" name="form_type" value="login">
                    <input type="text" 
                           name="username"
                           class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none" 
                           placeholder="Username"
                           required>

                    <input type="password" 
                           name="password"
                           class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none" 
                           placeholder="Password"
                           required>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 border-gray-300 rounded">
                            <label for="remember" class="ml-2 text-sm text-gray-600">Remember Me</label>
                        </div>
                        <a href="#" id="forgotPasswordLink" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
                    </div>

                    <button type="submit" 
                            id="loginButton"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-3 text-center flex items-center justify-center">
                        <span id="buttonText">Log in</span>
                        <span id="spinner" class="hidden ml-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
        
    <script src = "script_js/login.js"></script>
</body>
</html>