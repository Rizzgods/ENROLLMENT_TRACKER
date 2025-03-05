<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
<div class="container mx-auto">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
        <h2 class="text-2xl font-bold text-center text-gray-800">Reset Password</h2>

        <!-- Success Modal (Hidden by default) -->
        <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-xl font-bold text-green-600 mb-4">Password Reset Successful!</h2>
                <p class="text-gray-700">You will be redirected to the login page shortly.</p>
                <button onclick="redirectToLogin()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                    Go to Login
                </button>
            </div>
        </div>

        <?php if (isset($error)): ?>
                <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
                <?php endif; ?>
        <form id="resetPasswordForm" method="POST" action="" class="mt-4">
            
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                <input type="password" name="new_password" id="new_password" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">Reset Password</button>
        </form>
    </div>
</div>

</body>


