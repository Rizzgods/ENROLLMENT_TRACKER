document.getElementById("resetPasswordForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission

    let formData = new FormData(this);

    fetch("Logic_reset.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Expecting JSON response
    .then(data => {
        console.log(data); // Debugging: Log response to console

        if (data.status === "success") {
            // Show the modal
            document.getElementById("successModal").classList.remove("hidden");
            
            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = "student_login.php";
            }, 3000);
        } else {
            alert(data.message); // Show error if something goes wrong
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Something went wrong. Please try again.");
    });
});

function redirectToLogin() {
    window.location.href = "student_login.php";
}
