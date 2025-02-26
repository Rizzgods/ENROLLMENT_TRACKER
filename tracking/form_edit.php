<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'dbgreenvalley');

// Check connection
if ($conn->connect_error) {
    exit("Connection failed: " . $conn->connect_error);
}

// Fetch the logged-in student's information
$stmt = $conn->prepare("SELECT * FROM tblstudent WHERE IDNO = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fetch the guardian's information
$stmt = $conn->prepare("SELECT * FROM tblstuddetails WHERE IDNO = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$guardian = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="script_js/forms.js" defer></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Profile</h2>
        <form action="edit_backend.php" method="POST" enctype="multipart/form-data" id="editForm" class="bg-white p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="FNAME" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="FNAME" id="FNAME" value="<?php echo htmlspecialchars($student['FNAME']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="LNAME" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="LNAME" id="LNAME" value="<?php echo htmlspecialchars($student['LNAME']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="MNAME" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" name="MNAME" id="MNAME" value="<?php echo htmlspecialchars($student['MNAME']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="SEX" class="block text-sm font-medium text-gray-700">Sex</label>
                    <select name="SEX" id="SEX" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="Male" <?php echo $student['SEX'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $student['SEX'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div>
                    <label for="BDAY" class="block text-sm font-medium text-gray-700">Birthday</label>
                    <input type="date" name="BDAY" id="BDAY" value="<?php echo htmlspecialchars($student['BDAY']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="BPLACE" class="block text-sm font-medium text-gray-700">Birthplace</label>
                    <input type="text" name="BPLACE" id="BPLACE" value="<?php echo htmlspecialchars($student['BPLACE']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="STATUS" class="block text-sm font-medium text-gray-700">Status</label>
                    <input type="text" name="STATUS" id="STATUS" value="<?php echo htmlspecialchars($student['STATUS']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="NATIONALITY" class="block text-sm font-medium text-gray-700">Nationality</label>
                    <input type="text" name="NATIONALITY" id="NATIONALITY" value="<?php echo htmlspecialchars($student['NATIONALITY']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="RELIGION" class="block text-sm font-medium text-gray-700">Religion</label>
                    <input type="text" name="RELIGION" id="RELIGION" value="<?php echo htmlspecialchars($student['RELIGION']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="CONTACT_NO" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="CONTACT_NO" id="CONTACT_NO" value="<?php echo htmlspecialchars($student['CONTACT_NO']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="HOME_ADD" class="block text-sm font-medium text-gray-700">Home Address</label>
                    <input type="text" name="HOME_ADD" id="HOME_ADD" value="<?php echo htmlspecialchars($student['HOME_ADD']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="SEMESTER" class="block text-sm font-medium text-gray-700">Semester</label>
                    <input type="text" name="SEMESTER" id="SEMESTER" value="<?php echo htmlspecialchars($student['SEMESTER']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="EMAIL" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="EMAIL" id="EMAIL" value="<?php echo htmlspecialchars($student['EMAIL']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="GUARDIAN" class="block text-sm font-medium text-gray-700">Guardian</label>
                    <input type="text" name="GUARDIAN" id="GUARDIAN" value="<?php echo htmlspecialchars($guardian['GUARDIAN']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="GUARDIAN_ADDRESS" class="block text-sm font-medium text-gray-700">Guardian Address</label>
                    <input type="text" name="GUARDIAN_ADDRESS" id="GUARDIAN_ADDRESS" value="<?php echo htmlspecialchars($guardian['GUARDIAN_ADDRESS']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="GCONTACT" class="block text-sm font-medium text-gray-700">Guardian Contact</label>
                    <input type="text" name="GCONTACT" id="GCONTACT" value="<?php echo htmlspecialchars($guardian['GCONTACT']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="form_138" class="block text-sm font-medium text-gray-700">Form 138</label>
                    <input type="file" name="form_138" id="form_138" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="form_137" class="block text-sm font-medium text-gray-700">Form 137</label>
                    <input type="file" name="form_137" id="form_137" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="good_moral" class="block text-sm font-medium text-gray-700">Good Moral</label>
                    <input type="file" name="good_moral" id="good_moral" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="psa_birthCert" class="block text-sm font-medium text-gray-700">PSA Birth Certificate</label>
                    <input type="file" name="psa_birthCert" id="psa_birthCert" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="Brgy_clearance" class="block text-sm font-medium text-gray-700">Barangay Clearance</label>
                    <input type="file" name="Brgy_clearance" id="Brgy_clearance" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="tor" class="block text-sm font-medium text-gray-700">Transcript of Records</label>
                    <input type="file" name="tor" id="tor" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="honor_dismissal" class="block text-sm font-medium text-gray-700">Honorable Dismissal</label>
                    <input type="file" name="honor_dismissal" id="honor_dismissal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="mt-4 flex justify-between">
                <a href="profile.php?page=profile" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">Back</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>