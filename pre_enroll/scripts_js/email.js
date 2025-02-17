document.addEventListener("DOMContentLoaded", function () {
    const sendOTPButton = document.getElementById("sendOTP");
    const otpSection = document.getElementById("otpSection");
    const emailInput = document.querySelector("input[name='email']");

    sendOTPButton.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default form submission

        const email = emailInput.value.trim();

        if (email === "") {
            return;
        }

        // Show OTP input field
        otpSection.classList.remove("hidden");

        // Send OTP via AJAX request to PHP
        fetch("Logic_validate.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `sendOTP=1&email=${encodeURIComponent(email)}`,
        })
        .then(response => response.text())
        .then(data => {
        })
        .catch(error => console.error("Error:", error));
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const verifyOTPButton = document.getElementById("verifyOTP");
    const otpInput = document.getElementById("otp");

    verifyOTPButton.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default form submission

        const otp = otpInput.value.trim();

        if (otp === "") {
            return;
        }

        // Send OTP for verification
        fetch("Logic_validate.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                verifyOTP: "1",
                otp: otp
            }),
        })
        .then(response => response.text()) 
        .then(data => {
            if (data.trim() === "success") {
                window.location.href = "enroll.php";
            } else {
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
