$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        // Remove any existing error messages
        $('.error-message').remove();
        
        // Show spinner, disable button
        $('#buttonText').text('Logging in...');
        $('#spinner').removeClass('hidden');
        $('#loginButton').prop('disabled', true);

        // Submit form
        $.ajax({
            type: 'POST',
            url: 'Logic_login.php',
            data: $(this).serialize(),
            success: function(response) {
                if (response.includes('success')) {
                    window.location.href = 'profile.php';
                } else {
                    // Show error message
                    $('<div>')
                        .addClass('error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4')
                        .html(`
                            <span class="block sm:inline">Invalid username or password</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-red-500 cursor-pointer close-error" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <title>Close</title>
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </span>
                        `)
                        .insertBefore('#loginForm');
                    
                    // Auto dismiss after 5 seconds
                    setTimeout(function() {
                        $('.error-message').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 5000);
                    
                    // Reset button
                    $('#buttonText').text('Log in');
                    $('#spinner').addClass('hidden');
                    $('#loginButton').prop('disabled', false);
                }
            },
            error: function() {
                // Show error message
                $('<div>')
                    .addClass('error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4')
                    .html(`
                        <span class="block sm:inline">An error occurred. Please try again.</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-red-500 cursor-pointer close-error" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Close</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    `)
                    .insertBefore('#loginForm');
                
                // Auto dismiss after 5 seconds
                setTimeout(function() {
                    $('.error-message').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 5000);
                
                // Reset button
                $('#buttonText').text('Log in');
                $('#spinner').addClass('hidden');
                $('#loginButton').prop('disabled', false);
            }
        });
    });

    // Handle click on close button for error messages
    $(document).on('click', '.close-error', function() {
        $(this).closest('.error-message').fadeOut('slow', function() {
            $(this).remove();
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const forgotPasswordLink = document.getElementById("forgotPasswordLink");
    const forgotPasswordModal = document.getElementById("forgotPasswordModal");
    const closeModal = document.getElementById("closeModal");

    // Show modal when "Forgot Password?" is clicked
    forgotPasswordLink.addEventListener("click", function(event) {
        event.preventDefault();
        forgotPasswordModal.classList.remove("hidden");
    });

    // Hide modal when "Close" is clicked
    closeModal.addEventListener("click", function() {
        forgotPasswordModal.classList.add("hidden");
    });
});

$(document).ready(function () {
    $("#forgotPasswordForm").submit(function (event) {
        event.preventDefault(); // Prevent default form submission

        // Show the loading modal
        $("#loadingModal").removeClass("hidden");

        $.ajax({
            url: "Logic_forgot.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                $("#loadingModal").addClass("hidden"); // Hide loading modal when done

                if (response.status === "success") {
                    $("#forgotPasswordModal").addClass("hidden"); // Hide forgot password modal
                    $("#successModal").removeClass("hidden"); // Show success modal
                } else {
                    alert(response.message); // Show error message if email is not found
                }
            },
            error: function () {
                $("#loadingModal").addClass("hidden"); // Hide loading modal on error
                alert("Something went wrong. Please try again.");
            }
        });
    });

    // Close Success Modal
    $("#closeSuccessModal").click(function () {
        $("#successModal").addClass("hidden");
    });

    // Close Forgot Password Modal
    $("#closeModal").click(function () {
        $("#forgotPasswordModal").addClass("hidden");
    });
});