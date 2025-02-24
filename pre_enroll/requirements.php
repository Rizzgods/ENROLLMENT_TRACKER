<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Requirements</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-gray-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="flex-grow flex items-center justify-center p-6">
        <div class="container mx-auto">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Enrollment Requirements</h2>
                <p class="text-gray-700 mb-4">Please prepare the picture of the following requirements for enrollment:</p>
                
                <h3 class="text-2xl font-bold text-gray-800 mb-2">College New/Freshmen Requirements:</h3>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2"></ul>
                    <li>Form 138 (Report Card)</li>
                    <li>Form 137</li>
                    <li>Certificate of Good Moral</li>
                    <li>PSA Authenticated Birth Certificate</li>
                    <li>Passport Size ID Picture (White Background, Formal Attire) - 2pcs.</li>
                    <li>Barangay Clearance</li>
                </ul>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">College Transferee Requirements:</h3>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2"></ul>
                    <li>Transcript of Records from Previous School</li>
                    <li>Honorable Dismissal</li>
                    <li>Certificate of Good Moral</li>
                    <li>PSA Authenticated Birth Certificate</li>
                    <li>Passport Size ID Picture (White Background, Formal Attire) - 2pcs.</li>
                    <li>Barangay Clearance</li>
                </ul>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">Senior High School Requirements:</h3>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2"></ul>
                    <li>Form 138 (Report Card)</li>
                    <li>Form 137</li>
                    <li>Certificate of Good Moral</li>
                    <li>2"x2" ID Picture (White Background) - 2pcs.</li>
                    <li>Photocopy of NCAE Result</li>
                    <li>ESC Certificate, if a graduate of a private Junior High School</li>
                    <li>PSA Authenticated Birth Certificate</li>
                    <li>Barangay Clearance</li>
                    <li>Photocopy of Diploma</li>
                </ul>

                <button onclick="proceedToEnrollment()" class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">Proceed to Enrollment Form</button>
            </div>
        </div>
    </div>

    <script>
        function proceedToEnrollment() {
            localStorage.setItem('requirementsSeen', 'true');
            window.location.href = 'emailvalidate.php';
        }
    </script>
</body>
</html>