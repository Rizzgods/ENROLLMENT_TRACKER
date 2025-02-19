
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
    <div class="container mx-auto">
        <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <h2 class="text-2xl font-bold text-center text-gray-800">Student Profile</h2>

            <div class="mt-4">
                <p><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
                <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                <p><strong>Schedule:</strong> <?php echo $_SESSION['schedule']; ?></p>
                <p><strong>Status:</strong> <?php echo $_SESSION['status']; ?></p>
            </div>

            <div class="mt-6 text-center">
                <a href="logout.php" class="text-white bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700">Logout</a>
            </div>
        </div>
    </div>
</body>