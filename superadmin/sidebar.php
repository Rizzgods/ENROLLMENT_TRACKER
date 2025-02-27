<!-- Sidebar -->
<aside class="bg-gradient-to-r from-blue-600 to-blue-800 text-white h-screen p-4 md:p-6 fixed top-0 left-0 w-32">
    <div class="container mx-auto max-w-[1920px] px-4 h-full flex flex-col justify-between">
        <div class="flex flex-col items-center space-y-4">
            <a href="#" class="text-white hover:text-blue-200 transition duration-150">
                <img src="assets/logo" alt="Logo" class="h-10 w-10">
            </a>
        </div>
        <div class="flex flex-col space-y-4 mt-8 flex-grow">
            <!-- Home Button -->
            <a href="#" id="home-btn" class="text-blue-700 hover:text-blue-200 transition duration-150 text-center p-2 bg-white rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                
            </a>
            <!-- Create Account Button -->
            <a href="#" id="account-btn" class="text-blue-700 hover:text-blue-200 transition duration-150 text-center p-2 bg-white rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 mx-auto">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            
            </a>
        </div>
        <div class="flex flex-col space-y-4">
            <?php if (isset($_SESSION['USERNAME'])): ?>
                <!-- Show Welcome Message & Logout -->
                <a href="logout.php" class="text-white hover:text-blue-200 transition duration-150 text-center p-2 bg-red-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-xs mt-1">Logout</span>
                </a>
            <?php else: ?>
                <!-- Add login link or other options if needed -->
            <?php endif; ?>
        </div>
    </div>
</aside>