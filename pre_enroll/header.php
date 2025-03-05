<!-- Navbar -->
<header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 md:p-6">
    <div class="container mx-auto max-w-[1920px] px-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-0">
            <div class="flex items-center space-x-4">
                <a href="home.php" class="text-white hover:text-blue-200 transition duration-150">
                    <img src="assets/logo" alt="Logo" class="h-10 w-10">
                </a>
                <h1 class="text-2xl font-bold tracking-tight"></h1>
            </div>
            <div class="space-x-4 sm:space-x-6">
                <a href="#about" class="text-white hover:text-blue-200 transition duration-150 scroll-smooth">About</a>
                <a href="#" class="text-white hover:text-blue-200 transition duration-150">Department</a>
                <a href="#" class="text-white hover:text-blue-200 transition duration-150">Contact</a>

                <?php if (isset($_SESSION['username'])): ?>
                    <!-- Show Welcome Message & Logout -->
                    <a href="profile.php" class="text-white hover:text-blue-200 transition duration-150">Profile</a>
                    <span class="text-white">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
                    <a href="logout.php" class="text-white hover:text-blue-200 transition duration-150">Logout</a>
                <?php else: ?>
                    <!-- Show Login Link if Not Authenticated -->
                    <a href="../tracking/student_login.php" class="text-white hover:text-blue-200 transition duration-150">Login</a>    <?php endif; ?>
            </div>
        </div>
    </div>
</header>