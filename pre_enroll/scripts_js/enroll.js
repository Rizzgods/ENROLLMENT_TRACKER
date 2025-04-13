document.addEventListener('DOMContentLoaded', function() {
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const submitBtn = document.getElementById('submitBtn'); // Updated ID to match the new button ID
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
    
    // Define the form submit handler as a named function so it can be removed
    window.formSubmitHandler = function(e) {
        if (!validateStep(currentStep)) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        } else {
            loadingScreen.classList.remove('hidden');
            loadingScreen.classList.add('flex');
        }
    };
    
    // Attach the form submit handler
    form.addEventListener('submit', window.formSubmitHandler);
    
    // Initialize the form
    showStep(currentStep);
});