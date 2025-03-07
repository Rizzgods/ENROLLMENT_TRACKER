<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bestlink Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom premium gradient background */
        .premium-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
            background-size: 200% 200%;
            animation: gradientAnimation 15s ease infinite;
        }
        
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Subtle shadow enhancement for card */
        .premium-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="min-h-screen premium-gradient flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-xl premium-shadow p-8">
            <div class="flex justify-center mb-6">
                <img src="../pre_enroll/assets/logo.png" alt="Bestlink Logo" class="h-16">
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Administrator Login</h2>
            
            <?php if (isset($_GET['error'])) { ?>
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo htmlspecialchars($_GET['error']); ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
            
            <form action="Logic_login.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input 
                        type="text" 
                        name="USERNAME"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="Enter your username"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input 
                        type="password" 
                        name="PASSWORD"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="••••••••"
                        required
                    />
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-medium py-2.5 rounded-lg transition-all duration-300 transform hover:scale-[1.01]">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <div class="flex items-center justify-center">
                    <span class="bg-gray-300 h-px flex-grow t-2 relative top-2"></span>
                    <span class="flex-shrink mx-4 text-gray-400">Secure Admin Access</span>
                    <span class="bg-gray-300 h-px flex-grow t-2 relative top-2"></span>
                </div>
                <p class="mt-4">
                    Return to <a href="https://admission.bcpsms4.com/pre_enroll/home.php" class="text-blue-600 hover:text-blue-700 font-medium">Main Site</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>