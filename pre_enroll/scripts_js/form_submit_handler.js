document.addEventListener('DOMContentLoaded', function() {
    // Get form element
    const registrationForm = document.querySelector('form');
    
    // Find UI elements
    const loadingScreen = document.getElementById('loadingScreen');
    const successPopup = document.getElementById('successPopup');
    const verificationBanner = document.getElementById('verificationBanner');
    const countdownTimer = document.getElementById('countdownTimer');
    
    // Attach submit event handler
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            // Prevent default form submission
            e.preventDefault();
            
            // Check if we're on the last step
            const submitBtn = document.getElementById('submit');
            if (submitBtn.classList.contains('hidden')) {
                // Not on the last step, let the step navigation handle this
                return true;
            }
            
            // Show loading screen
            loadingScreen.classList.remove('hidden');
            loadingScreen.classList.add('flex');
            
            // Create form data for submission
            const formData = new FormData(registrationForm);
            
            // Use fetch for AJAX submission
            fetch('Logic_enroll.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Try to parse as JSON, but handle text fallback
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        try {
                            // Try to parse text as JSON
                            return JSON.parse(text);
                        } catch (e) {
                            // If parsing fails, wrap the text in an object
                            console.error('Response is not valid JSON:', text);
                            throw new Error('Invalid JSON response');
                        }
                    });
                }
            })
            .then(data => {
                // Hide loading screen
                loadingScreen.classList.add('hidden');
                loadingScreen.classList.remove('flex');
                
                // Check response status
                if (data.status === 'success') {
                    // Show verification warning if needed
                    if (data.docsVerified === false && data.verificationWarning) {
                        verificationBanner.textContent = data.verificationWarning;
                        verificationBanner.classList.remove('hidden');
                    }
                    
                    // Show success popup
                    successPopup.classList.remove('hidden');
                    successPopup.classList.add('flex');
                    
                    // Start countdown for redirect
                    let countdown = 5;
                    
                    const timer = setInterval(() => {
                        countdown--;
                        countdownTimer.textContent = countdown;
                        
                        if (countdown <= 0) {
                            clearInterval(timer);
                            window.location.href = '../index.php'; // Redirect to home
                        }
                    }, 1000);
                } else {
                    // Handle error
                    alert('Error: ' + (data.message || 'Something went wrong. Please try again.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingScreen.classList.add('hidden');
                loadingScreen.classList.remove('flex');
                alert('An unexpected error occurred. Please try again.');
            });
        });
    }
});
