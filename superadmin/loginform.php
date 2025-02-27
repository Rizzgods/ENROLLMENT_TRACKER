<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
<div class="container mx-auto">
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
        <h2 class="text-2xl font-bold text-center text-gray-800">Login</h2>

        <?php if (isset($_GET['error'])) { ?>
    <p class="text-red-600 text-sm text-center mt-2"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php } ?>
        <form action="Logic_login.php" method="POST" class="mt-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="USERNAME" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="PASSWORD" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">Login</button>
        </form>
    </div>
    </div>
</body>
</html>