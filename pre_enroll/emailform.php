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

            <form id="otpForm" class="space-y-6" method="POST" action="Logic_validate.php">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address:</label>
                <input type="email" name="email" id="email" required 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                
                <button type="submit" id="sendOTP" name="sendOTP" 
                        class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
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
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2" id="successTitle">Success</h3>
                <p class="text-sm text-gray-500" id="successMessage"></p>
                <div class="mt-5">
                    <button type="button" onclick="closeModal('successModal')" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Error</h3>
                <p class="text-sm text-gray-500" id="errorMessage"></p>
                <div class="mt-5">
                    <button type="button" onclick="closeModal('errorModal')" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mr-4"></div>
            <p class="text-gray-700">Processing...</p>
        </div>
    </div>

    <!-- Invalid Email Modal -->
    <div id="invalidEmailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Invalid Email</h3>
                <p class="text-sm text-gray-500 mb-4">Please enter a valid email address.</p>
                <button type="button" onclick="closeModal('invalidEmailModal')" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    Try Again
                </button>
            </div>
        </div>
    </div>

    <!-- OTP Sent Successfully Modal -->
    <div id="otpSentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">OTP Sent!</h3>
                <p class="text-sm text-gray-500 mb-4">Please check your email for the verification code.</p>
                <button type="button" onclick="closeModal('otpSentModal')" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Server Error Modal -->
    <div id="serverErrorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <!-- Server Error Modal -->
    <div id="serverErrorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Server Error</h3>
                <p class="text-sm text-gray-500 mb-4">Something went wrong. Please try again later.</p>
                <button type="button" onclick="closeModal('serverErrorModal')" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- OTP Error Modal -->
    <div id="otpErrorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">OTP Error</h3>
                <p class="text-sm text-gray-500 mb-4">Failed to send OTP. Please try again.</p>
                <button type="button" onclick="closeModal('otpErrorModal')" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Try Again
                </button>
            </div>
        </div>
    </div>
</body>