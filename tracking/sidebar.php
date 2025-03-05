<!-- Sidebar Toggle Button (outside sidebar) -->
<button id="sidebarToggle" class="fixed top-4 left-4 z-40 p-2 bg-blue-600 rounded-md text-white lg:hidden">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-16 bg-gradient-to-b from-blue-600 to-blue-800 text-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 z-50">
    <div class="flex flex-col h-full">
        <!-- Logo Section -->
        <div class="p-3 border-b border-blue-500">
            <a href="profile.php" class="flex justify-center text-white hover:text-blue-200 transition duration-150">
                <img src="assets/logo" alt="Logo" class="h-10 w-10">
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 py-6">
            <ul class="space-y-6">
                <li>
                    <a href="profile.php?page=profile" class="flex justify-center text-white hover:text-blue-200 transition duration-150 group relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="absolute left-full ml-4 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200">Profile</span>
                    </a>
                </li>
                <!-- New Documents and Information Link -->
                <li id="documents-info">
                    <a href="profile.php?page=requirements" class="flex justify-center text-white hover:text-blue-200 transition duration-150 group relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="absolute left-full ml-4 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200">Requirements</span>
                    </a>
                </li>

              
            </ul>
        </nav>

        <!-- User Section -->
        <div class="p-3 border-t border-blue-500">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="space-y-6">
                    <a href="profile.php" class="flex justify-center text-white hover:text-blue-200 transition duration-150 group relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="absolute left-full ml-4 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </a>
                    <a href="logout.php" class="flex justify-center text-white hover:text-blue-200 transition duration-150 group relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="absolute left-full ml-4 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200">Logout</span>
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" class="flex justify-center text-white hover:text-blue-200 transition duration-150 group relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span class="absolute left-full ml-4 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200">Login</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</aside>

<!-- Overlay for mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-50 z-45 hidden lg:hidden"></div>