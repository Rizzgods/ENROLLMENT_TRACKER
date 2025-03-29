let currentStep = 1;
const totalSteps = 5; // Update from 4 to 5
let errorTimeout;

// Initialize progress bar when page loads
document.addEventListener('DOMContentLoaded', () => {
    updateProgress(currentStep);
});

document.getElementById("next").addEventListener("click", () => {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            // Hide current step
            document.querySelector(`#step${currentStep}`).classList.add('hidden');
            currentStep++;
            // Show next step
            document.querySelector(`#step${currentStep}`).classList.remove('hidden');
            
            // Update UI
            updateUI();
        }
    } else {
        showErrorBubble(true);
    }
});

document.getElementById("prev").addEventListener("click", () => {
    if (currentStep > 1) {
        // Hide current step
        document.querySelector(`#step${currentStep}`).classList.add('hidden');
        currentStep--;
        // Show previous step
        document.querySelector(`#step${currentStep}`).classList.remove('hidden');
        
        // Update UI
        updateUI();
    }
});

function validateStep(step) {
    const currentStepEl = document.querySelector(`#step${step}`);
    const requiredFields = currentStepEl.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        // Skip validation for file inputs
        if (field.type === 'file') {
            return;
        }

        // For select elements, check if value is empty string
        if ((field.tagName === 'SELECT' && field.value === '') || 
            (field.tagName !== 'SELECT' && !field.value)) {
            isValid = false;
            field.classList.add('border-red-500');
        } else {
            field.classList.remove('border-red-500');
        }
    });

    return isValid;
}

function updateRequiredFields() {
    document.querySelectorAll('.step').forEach(step => {
        if (step.classList.contains('hidden')) {
            step.querySelectorAll('[required]').forEach(field => {
                // Skip file inputs
                if (field.type !== 'file') {
                    field.removeAttribute('required');
                }
            });
        } else {
            step.querySelectorAll('input, select').forEach(field => {
                // Skip file inputs
                if (field.type !== 'file' && !field.hasAttribute('required')) {
                    field.setAttribute('required', 'true');
                }
            });
        }
    });
}

function showErrorBubble(show) {
    const nextButton = document.getElementById("next");
    let errorBubble = document.getElementById("error-bubble");

    if (show) {
        if (!errorBubble) {
            errorBubble = document.createElement("div");
            errorBubble.id = "error-bubble";
            errorBubble.textContent = "Please fill in all fields before proceeding.";
            errorBubble.className = "absolute bg-red-500 text-white text-sm px-3 py-1 rounded-md bottom-full mb-2 shadow-md";
            nextButton.parentNode.classList.add("relative");
            nextButton.parentNode.appendChild(errorBubble);
        }

        // Clear any existing timeout before setting a new one
        clearTimeout(errorTimeout);
        errorTimeout = setTimeout(() => {
            errorBubble?.remove();
        }, 5000); // Hide error after 5 seconds
    } else {
        if (errorBubble) {
            errorBubble.remove();
        }
    }
}

function updateButtons() {
    document.getElementById("prev").classList.toggle("hidden", currentStep === 1);
    document.getElementById("next").classList.toggle("hidden", currentStep === totalSteps);
    document.getElementById("submit").classList.toggle("hidden", currentStep !== totalSteps);
}
function updateUI() {
    updateProgress(currentStep);
    document.getElementById('stepCounter').textContent = currentStep;
    updateButtons();
    updateRequiredFields();
}

function updateProgress(step) {
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
        const progress = (step / totalSteps) * 100;
        progressBar.style.width = `${progress}%`;
        
        // Optional: Add transition class if not present
        if (!progressBar.classList.contains('transition-all')) {
            progressBar.classList.add('transition-all', 'duration-300', 'ease-in-out');
        }
    }
}

// Add these functions at the end of the file

function showLoadingScreen() {
    const loadingScreen = document.getElementById('loadingScreen');
    loadingScreen.classList.remove('hidden');
    loadingScreen.classList.add('flex');
}

function hideLoadingScreen() {
    const loadingScreen = document.getElementById('loadingScreen');
    loadingScreen.classList.add('hidden');
    loadingScreen.classList.remove('flex');
}

// Update the countdown and redirection logic
function showSuccessPopup(message = null) {
    const successPopup = document.getElementById('successPopup');
    
    // Update message if provided
    if (message) {
        const messageElement = successPopup.querySelector('p.text-gray-600');
        if (messageElement) {
            messageElement.textContent = message;
        }
    }
    
    successPopup.classList.remove('hidden');
    successPopup.classList.add('flex');
    
    let countdown = 5; // Increased from 3 to 5 seconds to give more time to read
    const countdownElement = document.getElementById('countdownTimer');
    
    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = 'home.php';
        }
    }, 1000);
}

// Update the form submit handler to better handle different response types
document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (!validateStep(currentStep)) {
        showErrorBubble(true);
        return;
    }
    
    try {
        showLoadingScreen();
        const form = e.target;
        const formData = new FormData(form);
        formData.append('regsubmit', 'true');
        
        const response = await fetch('Logic_enroll.php', {
            method: 'POST',
            body: formData
        });

        // Check if we got a JSON response or HTML (like the redirect script)
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            // Handle JSON response
            const result = await response.json();
            console.log('Server response:', result);
            
            if (result.status === 'success') {
                hideLoadingScreen();
                
                // Build custom success message including verification info
                let message = 'Your enrollment was successful!';
                
                if (result.hasOwnProperty('docsVerified') && !result.docsVerified) {
                    message += ' However, some of your documents need verification. ' +
                              'Please bring the original documents during campus visit.';
                }
                
                if (result.hasOwnProperty('emailSent') && !result.emailSent) {
                    message += ' Your confirmation email could not be sent.';
                }
                
                showSuccessPopup(message);
            } else if (result.status === 'warning') {
                hideLoadingScreen();
                showSuccessPopup(result.message || 'Your enrollment was processed with some warnings.');
            } else {
                throw new Error(result.message || 'Submission failed with unknown error');
            }
        } else {
            // Handle HTML/text response - could be a redirect script
            const responseText = await response.text();
            console.log('Non-JSON response:', responseText);
            
            // Check if it's a redirect script
            if (responseText.includes('window.location') && responseText.includes('home.php')) {
                hideLoadingScreen();
                showSuccessPopup('Your enrollment was processed. You will be redirected to the home page.');
            } else if (responseText.includes('Enrollment successful') || responseText.includes('success')) {
                hideLoadingScreen();
                showSuccessPopup('Your enrollment appears to be successful.');
            } else {
                throw new Error('Unexpected server response');
            }
        }
    } catch (error) {
        console.error('Form submission error:', error);
        hideLoadingScreen();
        alert('An error occurred during submission: ' + error.message);
    }
});