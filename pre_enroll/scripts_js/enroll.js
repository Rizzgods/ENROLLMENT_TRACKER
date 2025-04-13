document.addEventListener('DOMContentLoaded', function() {
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const submitBtn = document.getElementById('submit');
    const stepCounter = document.getElementById('stepCounter');
    const progressBar = document.getElementById('progressBar');
    const steps = document.querySelectorAll('.step');
    const loadingScreen = document.getElementById('loadingScreen');
    const form = document.querySelector('form');
    
    let currentStep = 0;
    const totalSteps = steps.length;

    // Show the current step
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('hidden', index !== stepIndex);
        });
        
        // Update step counter
        stepCounter.textContent = stepIndex + 1;
        
        // Update progress bar (add 1 to stepIndex since we're 0-indexed)
        const progressPercentage = ((stepIndex + 1) / totalSteps) * 100;
        progressBar.style.width = `${progressPercentage}%`;
        
        // Show/hide prev button
        if (stepIndex === 0) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }
        
        // Show/hide next and submit buttons
        if (stepIndex === totalSteps - 1) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    // Validate current step fields
    function validateStep(stepIndex) {
        const currentStepElement = steps[stepIndex];
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            // For checkboxes and radio buttons
            if ((field.type === 'checkbox' || field.type === 'radio') && !field.checked) {
                isValid = false;
                field.classList.add('border-red-500');
            } 
            // For regular input fields
            else if (field.value.trim() === '') {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    }

    // Next button click handler
    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        } else {
            // Show validation error message
            alert('Please fill in all required fields.');
        }
    });
    
    // Previous button click handler
    prevBtn.addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });
    
    // Form submit handler - MODIFIED TO USE AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        if (!validateStep(currentStep)) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // Show loading screen
        loadingScreen.classList.remove('hidden');
        loadingScreen.classList.add('flex');
        
        // Create form data object
        const formData = new FormData(form);
        
        // Log to console for debugging
        console.log('Submitting form...');
        
        // Send AJAX request
        fetch('Logic_enroll.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response received:', response);
            console.log('Response status:', response.status);
            
            return response.text().then(text => {
                console.log('Response text:', text);
                
                // Try to parse as JSON
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Raw response:', text);
                    throw new Error('Failed to parse response as JSON');
                }
            });
        })
        .then(data => {
            console.log('Parsed data:', data);
            
            // Hide loading screen
            loadingScreen.classList.add('hidden');
            loadingScreen.classList.remove('flex');
            
            // Process the response
            if (data && data.status === 'success') {
                console.log('Success response detected');
                
                // Show verification warning if needed
                const verificationBanner = document.getElementById('verificationBanner');
                if (data.docsVerified === false && data.verificationWarning) {
                    verificationBanner.textContent = data.verificationWarning;
                    verificationBanner.classList.remove('hidden');
                }
                
                // Show success popup
                const successPopup = document.getElementById('successPopup');
                console.log('Success popup element:', successPopup);
                successPopup.classList.remove('hidden');
                successPopup.classList.add('flex');
                
                // Start countdown
                let countdown = 5;
                const countdownTimer = document.getElementById('countdownTimer');
                console.log('Countdown timer element:', countdownTimer);
                
                const timer = setInterval(() => {
                    countdown--;
                    countdownTimer.textContent = countdown;
                    console.log('Countdown:', countdown);
                    
                    if (countdown <= 0) {
                        clearInterval(timer);
                        window.location.href = '../index.php'; // Redirect to home
                    }
                }, 1000);
            } else {
                // Show error message
                console.error('Error in response:', data.message || 'Unknown error');
                alert(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            // Hide loading screen
            loadingScreen.classList.add('hidden');
            loadingScreen.classList.remove('flex');
            
            console.error('Fetch error:', error);
            alert('An unexpected error occurred. Please try again.');
        });
    });
    
    // Initialize the form
    showStep(currentStep);
});