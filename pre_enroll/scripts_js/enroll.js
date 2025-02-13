let currentStep = 1;
const totalSteps = 4;
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
        if (!field.value) {
            isValid = false;
            field.classList.add('border-red-500');
        } else {
            field.classList.remove('border-red-500');
        }
    });

    return isValid;
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
    // Update progress bar
    updateProgress(currentStep);
    
    // Update step counter
    document.getElementById('stepCounter').textContent = currentStep;
    
    // Update buttons visibility
    updateButtons();
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
function showSuccessPopup() {
    const successPopup = document.getElementById('successPopup');
    successPopup.classList.remove('hidden');
    successPopup.classList.add('flex');
    
    let countdown = 3;
    const countdownElement = document.getElementById('countdownTimer');
    
    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = 'home.php'; // Changed to redirect to home.php in the same directory
        }
    }, 1000);
}

// Update the form submit handler
document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Validate all steps before submission
    for (let step = 1; step <= totalSteps; step++) {
        if (!validateStep(step)) {
            alert('Please fill in all required fields');
            return;
        }
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

        if (response.ok) {
            hideLoadingScreen();
            showSuccessPopup();
        } else {
            const result = await response.text();
            throw new Error(`Submission failed: ${result}`);
        }
    } catch (error) {
        console.error('Form submission error:', error);
        hideLoadingScreen();
        alert('An error occurred. Please try again.');
    }
});
