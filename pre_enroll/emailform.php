<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
    <div class="container mx-auto">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Pre-Registration Form</h2>
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <span class="text-sm font-medium text-gray-500">First, we need to validate your email to redirect to the enrollment form.</span>
                </div>
           
            </div>

            <form id="otpForm" method="POST" action="Logic_validate.php">
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address:</label>
    <input type="email" name="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    
    <button type="submit" id="sendOTP" name="sendOTP" class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
        Send OTP
    </button>
    </form>

    <form>
    <div id="otpSection" class="hidden mt-4">
                    <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP:</label>
                    <input type="text" name="otp" id="otp" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    
                    <button type="submit" id="verifyOTP" name="verifyOTP" class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Verify OTP & Proceed
                    </button>
                </div>
       
                </form>
        </div>
    </div>

    <div id="otpErrorMessage" class="text-red-500 mt-2 hidden"></div>


    <script src="scripts_js/email.js"></script>

   