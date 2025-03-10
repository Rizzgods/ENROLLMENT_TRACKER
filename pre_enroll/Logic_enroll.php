<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/enrollment_errors.log');

require_once __DIR__ .  "/../include/autonumbers.php";
require_once __DIR__ .  "/../include/students.php";
require_once __DIR__ .  "/../include/session.php";
require_once __DIR__ .  "/../include/function.php";

// Database credentials - Update with correct server credentials
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Rest of your code remains unchanged
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
    // Buffer all output to prevent headers already sent issues
    ob_start();
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
    $SYEAR        = $_POST['SYEAR'];
    
    // Set default values
    $student_status = "New";  // Set student_status to "New"
    $YEARLEVEL = "1";        // Change from "1st" to "1" - this is likely the issue
    $NewEnrollees = 1;       // Set NewEnrollees flag to 1
    
    // Calculate age based on birthdate
    $BIRTHDATE = date_format(date_create($_POST['BIRTHDATE']), 'Y-m-d');
    $birthDate = new DateTime($BIRTHDATE);
    $today = new DateTime('today');
    $AGE = $birthDate->diff($today)->y;  // This extracts just the year difference as an integer

    // Add logging after we have the variables
    error_log("Generated IDNO: " . $IDNO);
    error_log("Processing enrollment for: " . $EMAIL . " (Status: " . $student_status . ")");
    error_log("Calculated age: " . $AGE);

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
            $special = '@#$%^&*()_-+=';  // Removed problematic characters: <>?
            
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
            
            // Shuffle to avoid predictable pattern
            shuffle($password);
            
            // Return the password as a string
            return implode('', $password);
        }
        
        $password = generateSecurePassword();
        error_log("Raw password generated: " . $password);
        error_log("Password length: " . strlen($password));
        error_log("Password character codes: " . implode(',', array_map(function($char) { 
            return ord($char); 
        }, str_split($password))));
        $displayPassword = htmlspecialchars($password); // Ensure special characters are properly encoded for HTML display
        error_log("Generated secure password length: " . strlen($password) . " characters");
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
            $sql = "INSERT INTO tblstudent (IDNO, FNAME, LNAME, MNAME, SEX, BDAY, AGE, BPLACE, STATUS, NATIONALITY, RELIGION, CONTACT_NO, HOME_ADD, COURSE_ID, SEMESTER, EMAIL, student_status, YEARLEVEL, NewEnrollees, stud_type, form_138, good_moral, psa_birthCert, id_pic, Brgy_clearance, tor, honor_dismissal, SYEAR) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssissssssssssissssssssss", $IDNO, $FNAME, $LNAME, $MI, $SEX, $BIRTHDATE, $AGE, $BIRTHPLACE, $CIVILSTATUS, $NATIONALITY, $RELIGION, $CONTACT, $PADDRESS, $COURSEID, $SEMESTER, $EMAIL, $student_status, $YEARLEVEL, $NewEnrollees, $stud_type, $form_138, $good_moral, $psa_birthCert, $id_pic, $Brgy_clearance, $tor, $honor_dismissal, $SYEAR);
            if ($stmt->execute()) {
                // Insert into studentaccount with generated credentials and required fields
                $sql = "INSERT INTO studentaccount (user_id, username, password, STATUS, PAYMENT, SCHEDULE, test, enrollment_date) 
                        VALUES (?, ?, ?, 'pending', 'Unpaid', NULL, '', CURDATE())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $IDNO, $username, $hashed_password);
                
                error_log("Inserting student account for ID: " . $IDNO);
                $accountInserted = $stmt->execute();
                
                // Check if the account was created
                $accountCreated = $stmt->affected_rows > 0;
                error_log("Student account creation result: " . ($accountCreated ? "Success" : "Failed or already exists"));
                
                // Insert guardian details - add empty string for GUARDIAN_ADDRESS field
                $guardian_address = ""; // This field is required but not present in the form
                $sql = "INSERT INTO tblstuddetails (IDNO, GUARDIAN, GUARDIAN_ADDRESS, GCONTACT) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $IDNO, $GUARDIAN, $guardian_address, $GCONTACT);
                
                error_log("Inserting guardian details for ID: " . $IDNO);
                $guardianInsertResult = $stmt->execute();
                
                error_log("Guardian details insertion: " . ($guardianInsertResult ? "Success" : "Failed - " . $stmt->error));
                
                // Continue with the flow even if guardian details insert has issues
                // We have the main student record, which is most important
                
                // ✅ Send Confirmation Email
                $mail = new PHPMailer(true);
                
                // Clean any output buffers to avoid interfering with JSON response
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                try {
                    // Server settings
                    $mail->SMTPDebug = 0; // Disable debug output in the response
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
                    $logo_url = 'https://admission.bcpsms4.com/pre_enroll/assets/logo.png';

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = "Enrollment Confirmation";
                    $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
                            <!-- Header with Logo and Title on same row -->
                            <div style='background-color: #1a56db; padding: 15px 20px; text-align: left; border-radius: 8px 8px 0 0; display: flex; align-items: center;'>
                                <img src='{$logo_url}' alt='Bestlink Logo' style='max-height: 60px; margin-right: 15px;'>
                                <h1 style='color: white; margin: 0; font-size: 20px;'>BESTLINK ENROLLMENT SYSTEM</h1>
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
                                
                                <div style='background-color: #fff8e1; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin-top: 0; font-weight: bold; color: #b45309;'>Payment Information</p>
                                    <p style='margin-bottom: 5px;'>Please bring <b>₱1,000.00</b> as a downpayment when you visit our campus.</p>
                                    <p style='margin-top: 0;'>This downpayment is required to complete your enrollment process.</p>
                                </div>
                                
                                <div style='background-color: #ebf5ff; border-left: 4px solid #3182ce; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin-top: 0; font-weight: bold;'>Login Credentials</p>
                                    <p><b>Username:</b> $username</p>
                                    <p style='margin-bottom: 0;'><b>Password:</b> $displayPassword</p>
                                    <p>Use these credentials to check on your progress at <a href='https://admission.bcpsms4.com/tracking/student_login.php' style='color: #3182CE;'>https://admission.bcpsms4.com/tracking/student_login.php</a></p>
                                </div>
                                
                                <p>Your documents have been received and are being processed.</p>
                                <p>If you have any questions, please contact us at <a href='mailto:bcp-inquiry@bcp.edu.ph' style='color: #3182CE;'>bcp-inquiry@bcp.edu.ph</a>.</p>
                                
                                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 14px;'>
                                    <p style='margin-bottom: 5px;'><b>Thank you for enrolling!</b></p>
                                    <p style='margin-top: 0; color: #6B7280;'>Bestlink Enrollment Team</p>
                                </div>
                            </div>
                        </div>
                    ";
                    
                    // Log email attempt
                    error_log("Attempting to send email to: {$EMAIL}");
                    
                    // Send email and log result
                    $emailSent = false;
                    try {
                        $emailSent = $mail->send();
                        if($emailSent) {
                            error_log("Email successfully sent to: {$EMAIL}");
                        } else {
                            error_log("Email not sent, but continuing: " . $mail->ErrorInfo);
                        }
                    } catch (Exception $emailEx) {
                        error_log("Email exception caught but continuing: " . $emailEx->getMessage());
                    }
                    
                    // Return success regardless of email result since the student was created
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'Enrollment successful',
                        'emailSent' => $emailSent,
                        'studentID' => $IDNO
                    ]);
                    exit;
                    
                } catch (Exception $e) {
                    error_log("Email preparation failed: " . $e->getMessage());
                    // Still return success since student record was created
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'Enrollment successful but email notification failed',
                        'emailSent' => false,
                        'studentID' => $IDNO
                    ]);
                    exit;
                }
            } else {
                throw new Exception("Failed to create student record: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            // Return JSON error response (end execution)
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            exit;
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        // Return JSON error response (end execution)
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

<?php
