<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/enrollment_errors.log');

require_once __DIR__ .  "/../include/autonumbers.php";
require_once __DIR__ .  "/../include/students.php";
require_once __DIR__ .  "/../include/session.php";
require_once __DIR__ .  "/../include/function.php";

// Database credentials
$servername = "localhost";
$username = "root";
$password = "OH3nb3jPdGnCM8gK";
$dbname = "dbgreenvalley";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tbl_bcpdepts";
$result = $conn->query($sql);

$count_stud = "SELECT COUNT(*) FROM tblstudent";
$total = $conn->query($count_stud);

$count_course = "SELECT COUNT(*) FROM course";
$total_course = $conn->query($count_course);
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__. '/../vendor/autoload.php'; // Ensure PHPMailer is included

// Add this function at the top of your file
function handleFileUpload($file) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        return file_get_contents($file['tmp_name']);
    }
    return null;
}

if (isset($_POST['regsubmit'])) {
    require_once "../include/database.php"; 

    // Generate new IDNO using Autonumber class
    $studAuto = new Autonumber();
    $autonum = $studAuto->stud_autonumber();
    $IDNO = $autonum->AUTO;

    // Update the auto-number immediately to prevent duplicates
    $studAuto->studauto_update();

    // Get POST data
    $FNAME        = $_POST['FNAME'];
    $LNAME        = $_POST['LNAME'];
    $MI           = $_POST['MI'];
    $PADDRESS     = $_POST['PADDRESS'];
    $SEX          = $_POST['optionsRadios'];
    $BIRTHDATE    = date_format(date_create($_POST['BIRTHDATE']), 'Y-m-d'); 
    $NATIONALITY  = $_POST['NATIONALITY'];
    $BIRTHPLACE   = $_POST['BIRTHPLACE'];
    $RELIGION     = $_POST['RELIGION'];
    $CONTACT      = $_POST['CONTACT'];
    $CIVILSTATUS  = $_POST['CIVILSTATUS'];
    $GUARDIAN     = $_POST['GUARDIAN'];
    $GCONTACT     = $_POST['GCONTACT'];
    $COURSEID     = $_POST['COURSE'];
    $EMAIL        = $_POST['EMAIL']; 
    $SEMESTER     = $_POST['SEMESTER']; 
    $stud_type    = $_POST['stud_type'];
    
    // Set default values
    $student_status = "New";  // Set student_status to "New"
    $YEARLEVEL = "1st";      // Optionally set YEARLEVEL for new students
    $NewEnrollees = 1;    // Set NewEnrollees flag to 1
    
    // Add logging after we have the variables
    error_log("Generated IDNO: " . $IDNO);
    error_log("Processing enrollment for: " . $EMAIL . " (Status: " . $student_status . ")");

    // Then in your form processing
    try {
        // Handle file uploads
        $form_138 = handleFileUpload($_FILES['form_138'] ?? null);
        $good_moral = handleFileUpload($_FILES['good_moral'] ?? null);
        $psa_birthCert = handleFileUpload($_FILES['psa_birthCert'] ?? null);
        $id_pic = handleFileUpload($_FILES['id_pic'] ?? null);
        $Brgy_clearance = handleFileUpload($_FILES['Brgy_clearance'] ?? null);
        $tor = handleFileUpload($_FILES['tor'] ?? null);
        $honor_dismissal = handleFileUpload($_FILES['honor_dismissal'] ?? null);

        // Check if student already exists
        $student = new Student();
        $res = $student->find_all_student($LNAME, $FNAME, $MI);

        if ($res) {
            message("Student already exists.", "error");
            redirect("pre_enroll/home.php");
            exit();
        }

        // Validate course and semester selection
        if ($COURSEID == 'Select' || $SEMESTER == 'Select') {
            message("Select course and semester correctly.", "error");
            redirect("pre_enroll/home.php");
            exit();
        }

        // Validate age
        $age = date_diff(date_create($BIRTHDATE), date_create('today'))->y;
        if ($age < 15) {
            message("Cannot proceed. Must be 15 years old and above to enroll.", "error");
            redirect("pre_enroll/home.php");
            exit();
        }

        // Generate username and password
        // Generate username and password
        // Fix the username generation to remove spaces
        $username = strtolower(str_replace(' ', '', $FNAME) . $LNAME); // firstnamelastname (spaces removed)
        error_log("Generated username: " . $username);

        // Generate a secure password meeting the criteria
        function generateSecurePassword($length = 12) {
            $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $lowercase = 'abcdefghijklmnopqrstuvwxyz';
            $numbers = '0123456789';
            $special = '@#$%^&*()_-+=<>?';
            
            // Ensure we have at least one of each character type
            $password = [
                $uppercase[rand(0, strlen($uppercase) - 1)],
                $lowercase[rand(0, strlen($lowercase) - 1)],
                $numbers[rand(0, strlen($numbers) - 1)],
                $special[rand(0, strlen($special) - 1)]
            ];
            
            // Fill the rest of the password
            $allChars = $uppercase . $lowercase . $numbers . $special;
            for ($i = 4; $i < $length; $i++) {
                $password[] = $allChars[rand(0, strlen($allChars) - 1)];
            }
            
            // Shuffle to avoid predictable pattern (first uppercase, then lowercase, etc.)
            shuffle($password);
            
            return implode('', $password);
        }
        
        $password = generateSecurePassword();
        error_log("Generated secure password: [REDACTED FOR SECURITY]");

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Add more detailed logging
        error_log("Student details - Name: $FNAME $MI $LNAME, Course: $COURSEID, Semester: $SEMESTER");

        try {
            // First, check if IDNO already exists
            $check_sql = "SELECT COUNT(*) as count FROM tblstudent WHERE IDNO = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $IDNO);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                throw new Exception("Duplicate Student ID generated. Please try again.");
            }

            // Proceed with insertion if no duplicate found
            $sql = "INSERT INTO tblstudent (IDNO, FNAME, LNAME, MNAME, SEX, BDAY, BPLACE, STATUS, NATIONALITY, RELIGION, CONTACT_NO, HOME_ADD, COURSE_ID, SEMESTER, EMAIL, student_status, YEARLEVEL, NewEnrollees, stud_type, form_138, good_moral, psa_birthCert, id_pic, Brgy_clearance, tor, honor_dismissal) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssssssssssssssssss", $IDNO, $FNAME, $LNAME, $MI, $SEX, $BIRTHDATE, $BIRTHPLACE, $CIVILSTATUS, $NATIONALITY, $RELIGION, $CONTACT, $PADDRESS, $COURSEID, $SEMESTER, $EMAIL, $student_status, $YEARLEVEL, $NewEnrollees, $stud_type, $form_138, $good_moral, $psa_birthCert, $id_pic, $Brgy_clearance, $tor, $honor_dismissal);

            if ($stmt->execute()) {
                // Insert into studentaccount with generated credentials
                $sql = "INSERT INTO studentaccount (user_id, username, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $IDNO, $username, $hashed_password);
                $stmt->execute();
            
                if ($stmt->affected_rows > 0) {
                    // Insert guardian details
                    $sql = "INSERT INTO tblstuddetails (IDNO, GUARDIAN, GCONTACT) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $IDNO, $GUARDIAN, $GCONTACT);
                    $stmt->execute();

                    // ✅ Send Confirmation Email
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'taranavalvista@gmail.com'; // Your email
                        $mail->Password   = 'kdiq oeqm cuyr yhuz'; // Your email password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Recipients
                        $mail->setFrom('taranavalvista@gmail.com', 'Enrollment Team');
                        $mail->addAddress($EMAIL, $FNAME . ' ' . $LNAME);

                        // Get the absolute URL for the logo
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                        $host = $_SERVER['HTTP_HOST'];
                        $logo_url = $protocol . $host . '/onlineenrolmentsystem/assets/logo.png';

                        // Email content
                        $mail->isHTML(true);
                        $mail->Subject = "Enrollment Confirmation";
                        $mail->Body    = "
                            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
                                <!-- Header with Logo -->
                                <div style='background-color: #1a56db; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
                                    <img src='{$logo_url}' alt='Bestlink Logo' style='max-height: 80px; margin-bottom: 10px;'>
                                    <h1 style='color: white; margin: 0; font-size: 24px;'>BESTLINK ENROLLMENT SYSTEM</h1>
                                </div>
                                
                                <!-- Email Content -->
                                <div style='background-color: #f9fafb; border-radius: 0 0 8px 8px; padding: 20px; border: 1px solid #e5e7eb; border-top: none;'>
                                    <h3 style='color: #4A5568; margin-top: 0;'>Hello $FNAME,</h3>
                                    <p>Your enrollment has been successfully processed.</p>
                                    
                                    <div style='background-color: white; border-radius: 8px; padding: 15px; margin: 20px 0; border: 1px solid #e5e7eb;'>
                                        <p style='font-weight: bold; color: #4A5568; margin-top: 0;'>Enrollment Details:</p>
                                        <table style='width: 100%; border-collapse: collapse;'>
                                            <tr>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb; width: 40%;'><b>Student ID:</b></td>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$IDNO</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'><b>Full Name:</b></td>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$FNAME $MI $LNAME</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'><b>Course:</b></td>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$COURSEID</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'><b>Semester:</b></td>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$SEMESTER</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'><b>Student Type:</b></td>
                                                <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$stud_type</td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <div style='background-color: #ebf5ff; border-left: 4px solid #3182ce; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                                        <p style='margin-top: 0; font-weight: bold;'>Login Credentials</p>
                                        <p><b>Username:</b> $username</p>
                                        <p style='margin-bottom: 0;'><b>Password:</b> $password</p>
                                        <p>Use these credentials to check on your progress at <a href='http://localhost/onlineenrolmentsystem/tracking/student_login.php' style='color: #3182CE;'>http://localhost/onlineenrolmentsystem/tracking/student_login.php</a></p>
                                    </div>
                                    
                                    <p>Your documents have been received and are being processed.</p>
                                    <p>If you have any questions, please contact us at <a href='mailto:support@yourdomain.com' style='color: #3182CE;'>support@yourdomain.com</a>.</p>
                                    
                                    <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 14px;'>
                                        <p style='margin-bottom: 5px;'><b>Thank you for enrolling!</b></p>
                                        <p style='margin-top: 0; color: #6B7280;'>Bestlink Enrollment Team</p>
                                    </div>
                                </div>
                            </div>
                        ";

                        $mail->send();
                        
                        // Return JSON response instead of redirect
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'message' => 'Enrollment successful']);
                        exit;
                    } catch (Exception $e) {
                        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => 'Email sending failed']);
                        exit;
                    }
                }
            } else {
                throw new Exception("Failed to create student record: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            message("Error: " . $e->getMessage(), "error");
            redirect("pre_enroll/home.php");
            exit();
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// School Year Calculation
$currentyear = date('Y');
$nextyear = date('Y') + 1;
$sy = $currentyear . '-' . $nextyear;

$studAuto = new Autonumber();
$autonum = $studAuto->stud_autonumber();
?>