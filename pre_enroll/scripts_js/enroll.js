document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded - initializing enrollment form');
    
    // UI Elements - Form navigation
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const submitBtn = document.getElementById('submit');
    const stepCounter = document.getElementById('stepCounter');
    const progressBar = document.getElementById('progressBar');
    const steps = document.querySelectorAll('.step');
    
    // UI Elements - Containers
    const dataPrivacyContainer = document.getElementById('data_privacy_container');
    const parentConsentContainer = document.getElementById('parent_consent_container');
    const registrationContainer = document.getElementById('registration_container');
    const loadingScreen = document.getElementById('loadingScreen');
    const successPopup = document.getElementById('successPopup');
    const verificationBanner = document.getElementById('verificationBanner');
    
    // UI Elements - Buttons
    const proceedFromPrivacyBtn = document.getElementById('proceedFromPrivacy');
    const backToPrivacyBtn = document.getElementById('backToPrivacy');
    const proceedToRegistrationBtn = document.getElementById('proceedToRegistration');
    
    // Form reference
    const form = document.querySelector('form');
    
    // Debugging - Log elements to verify
    console.log('Important elements:');
    console.log('- Form:', form);
    console.log('- Success Popup:', successPopup);
    console.log('- Loading Screen:', loadingScreen);
    console.log('- Verification Banner:', verificationBanner);
    
    // Variables
    let currentStep = 0;
    const totalSteps = steps.length;
    let parentGuardianName = '';
    
    // Function to show the current step
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('hidden', index !== stepIndex);
        });
        
        // Update step counter
        stepCounter.textContent = stepIndex + 1;
        
        // Update progress bar
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
            // For selects with empty value
            else if (field.tagName === 'SELECT' && field.value === '') {
                isValid = false;
                field.classList.add('border-red-500');
            }
            // For regular input fields
            else if (field.tagName === 'INPUT' && field.value.trim() === '') {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    }

    // File upload handling
    function updateFileName(input) {
        const fileName = input.files[0]?.name || 'Choose file...';
        const fileNameElement = input.parentElement.querySelector('.file-name');
        if (fileNameElement) {
            fileNameElement.textContent = fileName;
        }
        
        // Update border color based on validation
        const container = input.parentElement.querySelector('.border');
        if (input.files.length > 0) {
            container.classList.remove('border-red-500');
            container.classList.add('border-green-500');
        } else {
            container.classList.remove('border-green-500');
            container.classList.add('border-red-500');
        }
    }
    
    // Input validation functions
    window.validateLettersOnly = function(input) {
        // Replace any non-letter characters (except spaces)
        input.value = input.value.replace(/[^A-Za-z\s]/g, '');
        
        // Add visual feedback
        if (input.value.trim() === '') {
            input.classList.add('border-red-500');
        } else if (/^[A-Za-z\s]+$/.test(input.value)) {
            input.classList.remove('border-red-500');
            input.classList.add('border-green-500');
        } else {
            input.classList.add('border-red-500');
            input.classList.remove('border-green-500');
        }
    };
    
    window.validateNumbersOnly = function(input) {
        // Replace any non-numeric characters
        input.value = input.value.replace(/[^0-9]/g, '');
        
        // Add visual feedback
        if (input.value.trim() === '') {
            input.classList.add('border-red-500');
        } else if (/^[0-9]+$/.test(input.value)) {
            input.classList.remove('border-red-500');
            input.classList.add('border-green-500');
        } else {
            input.classList.add('border-red-500');
            input.classList.remove('border-green-500');
        }
    };

    // ======= EVENT LISTENERS =======
    
    // Next button click handler
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
                
                // If we're moving from Step 3 to Step 4, auto-fill guardian name
                if (currentStep === 3) {
                    setTimeout(function() {
                        const guardianField = document.getElementById('GUARDIAN');
                        if (guardianField && guardianField.value === '' && parentGuardianName) {
                            guardianField.value = parentGuardianName;
                        }
                    }, 100);
                }
            } else {
                // Show validation error message
                alert('Please fill in all required fields.');
            }
        });
    }
    
    // Previous button click handler
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentStep--;
            showStep(currentStep);
        });
    }
    
    // Proceed from privacy notice button handler
    if (proceedFromPrivacyBtn) {
        proceedFromPrivacyBtn.addEventListener('click', function() {
            // Validate privacy agreements
            const privacyAgreement = document.getElementById('privacy_agreement');
            const termsAgreement = document.getElementById('terms_agreement');
            const ageVerification = document.querySelector('input[name="age_verification"]:checked');
            
            let isValid = true;
            
            // Validate privacy agreement
            if (!privacyAgreement.checked) {
                privacyAgreement.classList.add('border-red-500');
                isValid = false;
            } else {
                privacyAgreement.classList.remove('border-red-500');
            }
            
            // Validate terms agreement
            if (!termsAgreement.checked) {
                termsAgreement.classList.add('border-red-500');
                isValid = false;
            } else {
                termsAgreement.classList.remove('border-red-500');
            }
            
            // Validate age verification
            if (!ageVerification) {
                document.querySelectorAll('input[name="age_verification"]').forEach(radio => {
                    radio.closest('label').classList.add('text-red-500');
                });
                isValid = false;
            } else {
                document.querySelectorAll('input[name="age_verification"]').forEach(radio => {
                    radio.closest('label').classList.remove('text-red-500');
                });
            }
            
            if (!isValid) {
                alert('Please complete all required fields before proceeding.');
                return;
            }
            
            // Store values in hidden fields
            document.getElementById('hidden_privacy_agreement').value = privacyAgreement.checked ? '1' : '0';
            document.getElementById('hidden_age_verification').value = ageVerification.value;
            document.getElementById('hidden_terms_agreement').value = termsAgreement.checked ? '1' : '0';
            
            // Determine next step based on age
            if (ageVerification.value === 'under18') {
                // Show parent consent container
                dataPrivacyContainer.classList.add('hidden');
                parentConsentContainer.classList.remove('hidden');
            } else {
                // Skip to registration form
                dataPrivacyContainer.classList.add('hidden');
                registrationContainer.classList.remove('hidden');
            }
        });
    }
    
    // Go back from parent consent to privacy
    if (backToPrivacyBtn) {
        backToPrivacyBtn.addEventListener('click', function() {
            parentConsentContainer.classList.add('hidden');
            dataPrivacyContainer.classList.remove('hidden');
        });
    }
    
    // Proceed from parent consent to registration
    if (proceedToRegistrationBtn) {
        proceedToRegistrationBtn.addEventListener('click', function() {
            // Validate parent consent form
            const parentName = document.getElementById('parent_name');
            const parentRelation = document.getElementById('parent_relation');
            const parentAgreement = document.getElementById('parent_agreement');
            
            let isValid = true;
            
            if (parentName.value.trim() === '') {
                parentName.classList.add('border-red-500');
                isValid = false;
            } else {
                parentName.classList.remove('border-red-500');
            }
            
            if (parentRelation.value.trim() === '') {
                parentRelation.classList.add('border-red-500');
                isValid = false;
            } else {
                parentRelation.classList.remove('border-red-500');
            }
            
            if (!parentAgreement.checked) {
                parentAgreement.classList.add('border-red-500');
                isValid = false;
            } else {
                parentAgreement.classList.remove('border-red-500');
            }
            
            if (!isValid) {
                alert('Please complete all required parent/guardian consent fields before proceeding.');
                return;
            }
            
            // Store parent/guardian info
            parentGuardianName = parentName.value;
            document.getElementById('hidden_parent_name').value = parentGuardianName;
            document.getElementById('hidden_parent_relation').value = parentRelation.value;
            document.getElementById('hidden_parent_agreement').value = parentAgreement.checked ? '1' : '0';
            
            // Proceed to registration form
            parentConsentContainer.classList.add('hidden');
            registrationContainer.classList.remove('hidden');
        });
    }
    
    // Add validation handlers to parent name field
    const parentNameField = document.getElementById('parent_name');
    if (parentNameField) {
        parentNameField.setAttribute('pattern', '[A-Za-z\\s]+');
        parentNameField.setAttribute('title', 'Only letters are allowed');
        parentNameField.addEventListener('input', function() {
            validateLettersOnly(this);
        });
    }
    
    // Add drag and drop functionality to file uploaders
    document.querySelectorAll('.relative').forEach(dropZone => {
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.querySelector('.border').classList.add('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.querySelector('.border').classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            const input = dropZone.querySelector('input[type="file"]');
            const dt = e.dataTransfer;
            input.files = dt.files;
            updateFileName(input);
            dropZone.querySelector('.border').classList.remove('border-blue-500', 'bg-blue-50');
        });
    });
    
    // Autofill email from localStorage
    const verifiedEmail = localStorage.getItem('verifiedEmail');
    if (verifiedEmail) {
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.value = verifiedEmail;
            emailInput.setAttribute('readonly', true);
        }
    }
    
    // Form submit handler with AJAX
    if (form) {
        console.log('Attaching submit handler to form:', form);
        form.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            e.preventDefault(); // Prevent the default form submission
            
            if (!validateStep(currentStep)) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Show loading screen
            console.log('Showing loading screen');
            if (loadingScreen) {
                loadingScreen.classList.remove('hidden');
                loadingScreen.classList.add('flex');
            }
            
            // Create form data object
            const formData = new FormData(form);
            formData.append('regsubmit', 'true'); // Ensure the form submission is identified correctly
            
            console.log('Sending AJAX request to Logic_enroll.php');
            
            // Send AJAX request
            fetch('Logic_enroll.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response);
                console.log('Response status:', response.status);
                
                // Get the raw text first
                return response.text();
            })
            .then(text => {
                console.log('Raw response text:', text);
                
                try {
                    // Try to parse the text as JSON
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Raw response that failed to parse:', text);
                    
                    // Display raw text in console for debugging
                    document.body.innerHTML = '<pre>' + text + '</pre>';
                    
                    throw new Error('Failed to parse server response as JSON');
                }
            })
            .then(data => {
                console.log('Parsed JSON data:', data);
                
                // Hide loading screen
                if (loadingScreen) {
                    loadingScreen.classList.add('hidden');
                    loadingScreen.classList.remove('flex');
                }
                
                // Process the response
                if (data && data.status === 'success') {
                    console.log('Submission was successful!');
                    
                    // Show verification warning if needed
                    if (verificationBanner && data.docsVerified === false && data.verificationWarning) {
                        verificationBanner.textContent = data.verificationWarning;
                        verificationBanner.classList.remove('hidden');
                    }
                    
                    // Show success popup
                    if (successPopup) {
                        console.log('Showing success popup');
                        successPopup.classList.remove('hidden');
                        successPopup.classList.add('flex');
                        
                        // Start countdown for redirect
                        let countdown = 5;
                        const countdownTimer = document.getElementById('countdownTimer');
                        
                        if (countdownTimer) {
                            const timer = setInterval(() => {
                                countdown--;
                                countdownTimer.textContent = countdown;
                                
                                if (countdown <= 0) {
                                    clearInterval(timer);
                                    window.location.href = '../index.php';
                                }
                            }, 1000);
                        }
                    } else {
                        console.error('Success popup element not found!');
                        alert('Form submitted successfully! Redirecting...');
                        setTimeout(() => {
                            window.location.href = '../index.php';
                        }, 2000);
                    }
                } else {
                    // Show error message
                    console.error('Submission error:', data ? data.message : 'Unknown error');
                    alert('Error: ' + (data && data.message ? data.message : 'An unknown error occurred. Please try again.'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                
                // Hide loading screen
                if (loadingScreen) {
                    loadingScreen.classList.add('hidden');
                    loadingScreen.classList.remove('flex');
                }
                
                alert('An unexpected error occurred. Please try again. ' + error.message);
            });
        });
    } else {
        console.error('Form element not found!');
    }
    
    // Make file input elements accessible globally for the updateFileName function
    window.updateFileName = updateFileName;
    
    // Initialize the form by showing the first step
    showStep(currentStep);
    
    console.log('Enrollment form initialization complete');
});