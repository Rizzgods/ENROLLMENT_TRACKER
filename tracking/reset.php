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

        <!-- Error Alert (Hidden by default) -->
        <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 hidden">
            <span class="block sm:inline" id="error-message"></span>
        </div>
        
        <!-- Password Requirements Info -->
        <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded">
            <h3 class="font-bold mb-2">Password Requirements:</h3>
            <ul class="list-disc pl-5 text-sm">
                <li id="length-check" class="text-gray-500">At least 8 characters (12+ recommended)</li>
                <li id="uppercase-check" class="text-gray-500">At least one uppercase letter (A-Z)</li>
                <li id="lowercase-check" class="text-gray-500">At least one lowercase letter (a-z)</li>
                <li id="number-check" class="text-gray-500">At least one number (0-9)</li>
                <li id="special-check" class="text-gray-500">At least one special character (@, #, $, etc.)</li>
                <li id="common-check" class="text-gray-500">No common passwords (password, 123456, admin)</li>
                <li id="sequential-check" class="text-gray-500">No sequential patterns (1234, abcd)</li>
                <li id="repeated-check" class="text-gray-500">No repeated characters (aaaa, 1111)</li>
            </ul>
        </div>
        
        <form id="resetPasswordForm" action="" class="mt-4">
            
            <input type="hidden" name="token" id="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                <input type="password" name="new_password" id="new_password" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
                <div id="password-strength" class="mt-2 h-2 rounded-full bg-gray-200"></div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
                <p id="password-match" class="mt-1 text-sm hidden"></p>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700" disabled>Reset Password</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('new_password');
    const confirmInput = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');
    const passwordMatch = document.getElementById('password-match');
    const passwordStrength = document.getElementById('password-strength');
    const errorAlert = document.getElementById('error-alert');
    const errorMessage = document.getElementById('error-message');
    
    // Elements for password requirement checks
    const lengthCheck = document.getElementById('length-check');
    const uppercaseCheck = document.getElementById('uppercase-check');
    const lowercaseCheck = document.getElementById('lowercase-check');
    const numberCheck = document.getElementById('number-check');
    const specialCheck = document.getElementById('special-check');
    const commonCheck = document.getElementById('common-check');
    const sequentialCheck = document.getElementById('sequential-check');
    const repeatedCheck = document.getElementById('repeated-check');
    
    // Common passwords to avoid
    const commonPasswords = ['password', 'admin', '123456', 'qwerty', 'welcome', 'abc123', 'football', 
                             'letmein', 'monkey', '111111', '12345678', 'iloveyou', '1234567', 'dragon'];
    
    // Check for sequential characters like "1234" or "abcd"
    function hasSequentialChars(password) {
        // Check numeric sequences
        for (let i = 0; i < password.length - 3; i++) {
            if (
                parseInt(password[i]) + 1 === parseInt(password[i + 1]) &&
                parseInt(password[i + 1]) + 1 === parseInt(password[i + 2]) &&
                parseInt(password[i + 2]) + 1 === parseInt(password[i + 3])
            ) {
                return true;
            }
        }
        
        // Check alphabetic sequences
        const lowerPassword = password.toLowerCase();
        for (let i = 0; i < lowerPassword.length - 3; i++) {
            if (
                lowerPassword.charCodeAt(i) + 1 === lowerPassword.charCodeAt(i + 1) &&
                lowerPassword.charCodeAt(i + 1) + 1 === lowerPassword.charCodeAt(i + 2) &&
                lowerPassword.charCodeAt(i + 2) + 1 === lowerPassword.charCodeAt(i + 3)
            ) {
                return true;
            }
        }
        
        return false;
    }
    
    // Check for repeated characters (e.g., "aaaa", "1111")
    function hasRepeatedChars(password) {
        for (let i = 0; i < password.length - 3; i++) {
            if (
                password[i] === password[i + 1] &&
                password[i + 1] === password[i + 2] &&
                password[i + 2] === password[i + 3]
            ) {
                return true;
            }
        }
        return false;
    }
    
    // Validate password on input
    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        let score = 0;
        let isValid = true;
        
        // Check length (min 8, ideally 12+)
        const hasMinLength = password.length >= 8;
        if (hasMinLength) {
            lengthCheck.classList.remove('text-gray-500');
            lengthCheck.classList.add('text-green-500');
            score += (password.length >= 12) ? 25 : 15;
        } else {
            lengthCheck.classList.remove('text-green-500');
            lengthCheck.classList.add('text-gray-500');
            isValid = false;
        }
        
        // Check for uppercase
        const hasUppercase = /[A-Z]/.test(password);
        if (hasUppercase) {
            uppercaseCheck.classList.remove('text-gray-500');
            uppercaseCheck.classList.add('text-green-500');
            score += 20;
        } else {
            uppercaseCheck.classList.remove('text-green-500');
            uppercaseCheck.classList.add('text-gray-500');
            isValid = false;
        }
        
        // Check for lowercase
        const hasLowercase = /[a-z]/.test(password);
        if (hasLowercase) {
            lowercaseCheck.classList.remove('text-gray-500');
            lowercaseCheck.classList.add('text-green-500');
            score += 20;
        } else {
            lowercaseCheck.classList.remove('text-green-500');
            lowercaseCheck.classList.add('text-gray-500');
            isValid = false;
        }
        
        // Check for numbers
        const hasNumbers = /[0-9]/.test(password);
        if (hasNumbers) {
            numberCheck.classList.remove('text-gray-500');
            numberCheck.classList.add('text-green-500');
            score += 20;
        } else {
            numberCheck.classList.remove('text-green-500');
            numberCheck.classList.add('text-gray-500');
            isValid = false;
        }
        
        // Check for special characters
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        if (hasSpecial) {
            specialCheck.classList.remove('text-gray-500');
            specialCheck.classList.add('text-green-500');
            score += 20;
        } else {
            specialCheck.classList.remove('text-green-500');
            specialCheck.classList.add('text-gray-500');
            isValid = false;
        }
        
        // Check for common passwords
        const isCommonPassword = commonPasswords.includes(password.toLowerCase());
        if (!isCommonPassword && password.length > 0) {
            commonCheck.classList.remove('text-gray-500');
            commonCheck.classList.add('text-green-500');
        } else {
            commonCheck.classList.remove('text-green-500');
            commonCheck.classList.add('text-gray-500');
            if (isCommonPassword) isValid = false;
        }
        
        // Check for sequential characters
        const hasSequential = hasSequentialChars(password);
        if (!hasSequential && password.length > 0) {
            sequentialCheck.classList.remove('text-gray-500');
            sequentialCheck.classList.add('text-green-500');
        } else {
            sequentialCheck.classList.remove('text-green-500');
            sequentialCheck.classList.add('text-gray-500');
            if (hasSequential) isValid = false;
        }
        
        // Check for repeated characters
        const hasRepeated = hasRepeatedChars(password);
        if (!hasRepeated && password.length > 0) {
            repeatedCheck.classList.remove('text-gray-500');
            repeatedCheck.classList.add('text-green-500');
        } else {
            repeatedCheck.classList.remove('text-green-500');
            repeatedCheck.classList.add('text-gray-500');
            if (hasRepeated) isValid = false;
        }
        
        // Update password strength indicator
        if (score >= 80) {
            passwordStrength.style.width = '100%';
            passwordStrength.classList.remove('bg-gray-200', 'bg-red-500', 'bg-yellow-500', 'bg-blue-500');
            passwordStrength.classList.add('bg-green-500');
        } else if (score >= 60) {
            passwordStrength.style.width = '75%';
            passwordStrength.classList.remove('bg-gray-200', 'bg-red-500', 'bg-yellow-500', 'bg-green-500');
            passwordStrength.classList.add('bg-blue-500');
        } else if (score >= 40) {
            passwordStrength.style.width = '50%';
            passwordStrength.classList.remove('bg-gray-200', 'bg-red-500', 'bg-green-500', 'bg-blue-500');
            passwordStrength.classList.add('bg-yellow-500');
        } else if (score > 0) {
            passwordStrength.style.width = '25%';
            passwordStrength.classList.remove('bg-gray-200', 'bg-yellow-500', 'bg-green-500', 'bg-blue-500');
            passwordStrength.classList.add('bg-red-500');
        } else {
            passwordStrength.style.width = '0%';
            passwordStrength.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500', 'bg-blue-500');
            passwordStrength.classList.add('bg-gray-200');
        }
        
        // Check if both passwords match
        checkPasswordsMatch();
        
        // Enable or disable submit button based on validity
        submitBtn.disabled = !isValid || password !== confirmInput.value || password.length === 0;
    });
    
    // Check if passwords match
    confirmInput.addEventListener('input', checkPasswordsMatch);
    
    function checkPasswordsMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmInput.value;
        
        if (confirmPassword.length > 0) {
            passwordMatch.classList.remove('hidden');
            
            if (password === confirmPassword) {
                passwordMatch.textContent = 'Passwords match';
                passwordMatch.classList.remove('text-red-500');
                passwordMatch.classList.add('text-green-500');
            } else {
                passwordMatch.textContent = 'Passwords do not match';
                passwordMatch.classList.remove('text-green-500');
                passwordMatch.classList.add('text-red-500');
                submitBtn.disabled = true;
            }
        } else {
            passwordMatch.classList.add('hidden');
        }
    }
    
    // Form submission with AJAX
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        const password = passwordInput.value;
        const confirmPassword = confirmInput.value;
        const token = document.getElementById('token').value;
        
        // Hide any previous error messages
        errorAlert.classList.add('hidden');
        
        // Final validation check before submission
        if (password !== confirmPassword) {
            showError('Passwords do not match.');
            return false;
        }
        
        const hasMinLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumbers = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        const isCommonPassword = commonPasswords.includes(password.toLowerCase());
        const hasSequential = hasSequentialChars(password);
        const hasRepeated = hasRepeatedChars(password);
        
        if (!hasMinLength || !hasUppercase || !hasLowercase || !hasNumbers || !hasSpecial || 
            isCommonPassword || hasSequential || hasRepeated) {
            showError('Password does not meet security requirements.');
            return false;
        }
        
        // Disable the submit button to prevent multiple submissions
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
        
        // Create form data
        const formData = new FormData();
        formData.append('token', token);
        formData.append('new_password', password);
        formData.append('confirm_password', confirmPassword);
        
        // Send the request
        fetch('Logic_reset.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success modal
                document.getElementById('successModal').classList.remove('hidden');
                
                // Redirect after 3 seconds
                setTimeout(redirectToLogin, 3000);
            } else {
                // Show error message
                showError(data.message || 'An error occurred while processing your request.');
                
                // Re-enable the button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Reset Password';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An unexpected error occurred. Please try again later.');
            
            // Re-enable the button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Reset Password';
        });
    });
    
    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('hidden');
    }
    
    // Function to redirect to login page
    window.redirectToLogin = function() {
        window.location.href = 'student_login.php';
    };
});
</script>

</body>