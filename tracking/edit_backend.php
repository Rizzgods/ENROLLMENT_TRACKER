<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/edit_errors.log');
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Updated database connection with correct credentials for production server
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection with the updated credentials
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed in edit_backend.php: " . $conn->connect_error);
    exit("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idno = $_SESSION['user_id'];
    $fname = $_POST['FNAME'];
    $lname = $_POST['LNAME'];
    $mname = $_POST['MNAME'];
    $sex = $_POST['SEX'];
    $bday = $_POST['BDAY'];
    $bplace = $_POST['BPLACE'];
    $nationality = $_POST['NATIONALITY'];
    $religion = $_POST['RELIGION'];
    $contact_no = $_POST['CONTACT_NO'];
    $home_add = $_POST['HOME_ADD'];
    $semester = $_POST['SEMESTER'];
    $email = $_POST['EMAIL'];
    $guardian = $_POST['GUARDIAN'];
    $guardian_address = $_POST['GUARDIAN_ADDRESS'];
    $gcontact = $_POST['GCONTACT'];

    // Fetch existing blob data
    $stmt = $conn->prepare("SELECT form_138, form_137, good_moral, psa_birthCert, Brgy_clearance, tor, honor_dismissal FROM tblstudent WHERE IDNO = ?");
    $stmt->bind_param("i", $idno);
    $stmt->execute();
    $stmt->bind_result($existing_form_138, $existing_form_137, $existing_good_moral, $existing_psa_birthCert, $existing_Brgy_clearance, $existing_tor, $existing_honor_dismissal);
    $stmt->fetch();
    $stmt->close();

    // Handle file uploads
    $form_138 = !empty($_FILES['form_138']['tmp_name']) ? file_get_contents($_FILES['form_138']['tmp_name']) : $existing_form_138;
    $form_137 = !empty($_FILES['form_137']['tmp_name']) ? file_get_contents($_FILES['form_137']['tmp_name']) : $existing_form_137;
    $good_moral = !empty($_FILES['good_moral']['tmp_name']) ? file_get_contents($_FILES['good_moral']['tmp_name']) : $existing_good_moral;
    $psa_birthCert = !empty($_FILES['psa_birthCert']['tmp_name']) ? file_get_contents($_FILES['psa_birthCert']['tmp_name']) : $existing_psa_birthCert;
    $Brgy_clearance = !empty($_FILES['Brgy_clearance']['tmp_name']) ? file_get_contents($_FILES['Brgy_clearance']['tmp_name']) : $existing_Brgy_clearance;
    $tor = !empty($_FILES['tor']['tmp_name']) ? file_get_contents($_FILES['tor']['tmp_name']) : $existing_tor;
    $honor_dismissal = !empty($_FILES['honor_dismissal']['tmp_name']) ? file_get_contents($_FILES['honor_dismissal']['tmp_name']) : $existing_honor_dismissal;

    // Update tblstudent
    $stmt = $conn->prepare("UPDATE tblstudent SET FNAME=?, LNAME=?, MNAME=?, SEX=?, BDAY=?, BPLACE=?, NATIONALITY=?, RELIGION=?, CONTACT_NO=?, HOME_ADD=?, SEMESTER=?, EMAIL=?, form_138=?, form_137=?, good_moral=?, psa_birthCert=?, Brgy_clearance=?, tor=?, honor_dismissal=? WHERE IDNO=?");
    $stmt->bind_param("sssssssssssssssssssi", $fname, $lname, $mname, $sex, $bday, $bplace, $nationality, $religion, $contact_no, $home_add, $semester, $email, $form_138, $form_137, $good_moral, $psa_birthCert, $Brgy_clearance, $tor, $honor_dismissal, $idno);

    if ($stmt->execute()) {
        error_log("Student record updated successfully for IDNO: $idno");
    } else {
        error_log("Error updating student record: " . $stmt->error);
    }

    // Update tblstuddetails
    $stmt = $conn->prepare("UPDATE tblstuddetails SET GUARDIAN=?, GUARDIAN_ADDRESS=?, GCONTACT=? WHERE IDNO=?");
    $stmt->bind_param("sssi", $guardian, $guardian_address, $gcontact, $idno);

    if ($stmt->execute()) {
        error_log("Guardian record updated successfully for IDNO: $idno");
    } else {
        error_log("Error updating guardian record: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    header("Location: profile.php?page=profile");
    exit();
}
?>