<?php


// Add debugging
error_log('Session id_pic exists: ' . isset($_SESSION['id_pic']));
if(isset($_SESSION['id_pic'])) {
    error_log('Session id_pic length: ' . strlen($_SESSION['id_pic']));
}

// Debug payment status specifically
error_log('Payment status in session: ' . ($_SESSION['payment'] ?? 'Not set'));

// Add this debug line temporarily to check the session data
error_log(print_r($_SESSION, true));

// Helper function to safely display session data
function displaySessionData($key, $default = 'N/A') {
    return htmlspecialchars($_SESSION[$key] ?? $default);
}
?>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
    <div class="container mx-auto">
        <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center overflow-hidden">
                        <?php if (isset($_SESSION['id_pic']) && !empty($_SESSION['id_pic'])): ?>
                            <?php error_log('Attempting to display image with length: ' . strlen($_SESSION['id_pic'])); ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($_SESSION['id_pic']); ?>" 
                                 alt="Student Photo" 
                                 class="w-full h-full object-cover"
                                 onerror="this.onerror=null; this.src='assets/default-avatar.png';">
                        <?php else: ?>
                            <?php error_log('No image data found in session'); ?>
                            <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">No photo</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold"><?php echo displaySessionData('username'); ?></h2>
                        <p class="text-blue-100">Student ID: <?php echo displaySessionData('user_id'); ?></p>
                        <p class="text-blue-100">Name: <?php echo displaySessionData('student_name'); ?></p>
                        <p class="text-blue-100">Email: <?php echo displaySessionData('student_email'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="p-8">
                <div class="grid gap-6">
                    <!-- Schedule -->
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-600">Schedule</p>
                            <p class="font-semibold text-gray-800"><?php echo displaySessionData('schedule'); ?></p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="font-semibold text-gray-800"><?php echo displaySessionData('status'); ?></p>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <?php 
                    // Make sure we use case-insensitive comparison since database might return 'Paid' or 'PAID'
                    $payment = strtoupper($_SESSION['payment'] ?? 'UNPAID'); 
                    ?>
                    
                    <!-- PAID Status Element -->
                    <div id="paid-status" class="flex items-center p-4 bg-green-50 rounded-lg" style="display: none;">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-600">Payment Status</p>
                            <p class="font-semibold text-green-800">PAID</p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Verified
                            </span>
                        </div>
                    </div>
                    
                    <!-- UNPAID Status Element -->
                    <div id="unpaid-status" class="flex items-center p-4 bg-red-50 rounded-lg" style="display: none;">
                        <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-600">Payment Status</p>
                            <p class="font-semibold text-red-800">UNPAID</p>
                        </div>
                    </div>
                </div>

                <!-- Logout Button -->
                <div class="mt-8 text-center">
                    <a href="logout.php" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to toggle payment status visibility with debugging -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentStatus = "<?php echo htmlspecialchars($payment); ?>";
            const paidElement = document.getElementById('paid-status');
            const unpaidElement = document.getElementById('unpaid-status');
            
            console.log('Payment status:', paymentStatus);
            
            // Use case-insensitive comparison
            if (paymentStatus.toUpperCase() === 'PAID') {
                paidElement.style.display = 'flex';
                unpaidElement.style.display = 'none';
                console.log('Showing PAID status');
            } else {
                paidElement.style.display = 'none';
                unpaidElement.style.display = 'flex';
                console.log('Showing UNPAID status');
            }
        });
    </script>
</body>