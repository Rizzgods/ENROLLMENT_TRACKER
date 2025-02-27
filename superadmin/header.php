<!-- Navbar -->
<header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 md:p-6">
    <div class="container mx-auto max-w-[1920px] px-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-0">
            <div class="flex items-center space-x-4">
                <a href="home.php" class="text-white hover:text-blue-200 transition duration-150">
                    <img src="assets/logo" alt="Logo" class="h-10 w-10"> 
                </a>
                <h1 class="text-xl tracking-tight">Bestlink College of The Philippines</h1>
            </div>
            <div class="space-x-4 sm:space-x-6">

                <?php if (isset($_SESSION['USERNAME'])): ?>
                    <!-- Show Welcome Message & Logout -->
                    <a  class="text-white hover:text-blue-200 transition duration-150">
                    <span class="text-white">Welcome, <strong><?php echo htmlspecialchars($_SESSION['USERNAME']); ?></strong>!</span></a>
                    <a href="logout.php" class="text-white hover:text-blue-200 transition duration-150">Logout</a>
                <?php else: ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>