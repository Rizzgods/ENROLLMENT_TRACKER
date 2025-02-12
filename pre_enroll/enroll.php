<?php
require_once __DIR__ .  "/../include/autonumbers.php";
require_once __DIR__ .  "/../include/students.php";
require_once __DIR__ .  "/../include/session.php";
require_once __DIR__ .  "/../include/function.php";

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgreenvalley";

// Create connection
$conn = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tbl_bcpdepts";
$result = $conn->query(query: $sql);

$count_stud = "SELECT COUNT(*) FROM tblstudent";
$total = $conn->query(query: $count_stud);

$count_course = "SELECT COUNT(*) FROM course";
$total_course = $conn->query(query: $count_course);
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__. '/../vendor/autoload.php'; // Ensure PHPMailer is included

if (isset($_POST['regsubmit'])) {
    require_once "../include/database.php"; 

    $IDNO         = $_POST['IDNO'];
    $FNAME        = $_POST['FNAME'];
    $LNAME        = $_POST['LNAME'];
    $MI           = $_POST['MI'];
    $PADDRESS     = $_POST['PADDRESS'];
    $SEX          = $_POST['optionsRadios'];
    $BIRTHDATE    = date_format(object: date_create(datetime: $_POST['BIRTHDATE']), format: 'Y-m-d'); 
    $NATIONALITY  = $_POST['NATIONALITY'];
    $BIRTHPLACE   = $_POST['BIRTHPLACE'];
    $RELIGION     = $_POST['RELIGION'];
    $CONTACT      = $_POST['CONTACT'];
    $CIVILSTATUS  = $_POST['CIVILSTATUS'];
    $GUARDIAN     = $_POST['GUARDIAN'];
    $GCONTACT     = $_POST['GCONTACT'];
    $COURSEID     = $_POST['COURSE'];
    $USER_NAME    = $_POST['USER_NAME']; 
    $PASS         = sha1(string: $_POST['PASS']); // Encrypt password
    $EMAIL        = $_POST['EMAIL']; 
    $SEMESTER     = $_POST['SEMESTER']; 

    // Check if student already exists
    $student = new Student();
    $res = $student->find_all_student(lname: $LNAME, fname: $FNAME, mname: $MI);

    if ($res) {
        message(msg: "Student already exists.", msgtype: "error");
        redirect(location: "pre_enroll/home.php");
        exit();
    }

    // Check if username is already taken
    $sql = "SELECT * FROM tblstudent WHERE ACC_USERNAME=?";
    $stmt = $mydb->conn->prepare(query: $sql);
    $stmt->bind_param("s", $_SESSION['USER_NAME']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userStud = $result->fetch_assoc();
    
    if ($userStud) {
        message(msg: "Username is already taken.", msgtype: "error");
        redirect(location: "pre_enroll/home.php");
        exit();
    }

    // Validate course and semester selection
    if ($COURSEID == 'Select' || $SEMESTER == 'Select') {
        message(msg: "Select course and semester correctly.", msgtype: "error");
        redirect(location: "pre_enroll/home.php");
        exit();
    }

    // Validate age
    $age = date_diff(baseObject: date_create(datetime: $BIRTHDATE), targetObject: date_create('today'))->y;
    if ($age < 15) {
        message(msg: "Cannot proceed. Must be 15 years old and above to enroll.", msgtype: "error");
        redirect(location: "pre_enroll/home.php");
        exit();
    }

    // Insert student data
    $student = new Student();
    $student->IDNO          = $IDNO;
    $student->FNAME         = $FNAME;
    $student->LNAME         = $LNAME;
    $student->MNAME         = $MI;
    $student->SEX           = $SEX;
    $student->BDAY          = $BIRTHDATE;
    $student->BPLACE        = $BIRTHPLACE;
    $student->STATUS        = $CIVILSTATUS;
    $student->NATIONALITY   = $NATIONALITY;
    $student->RELIGION      = $RELIGION;
    $student->CONTACT_NO    = $CONTACT;
    $student->HOME_ADD      = $PADDRESS;
    $student->ACC_USERNAME  = $USER_NAME;
    $student->ACC_PASSWORD  = $PASS;
    $student->COURSE_ID     = $COURSEID;
    $student->SEMESTER      = $SEMESTER;
    $student->EMAIL         = $EMAIL;
    $student->student_status = 'New';
    $student->YEARLEVEL     = 1;
    $student->NewEnrollees  = 1;
    $student->create();

    // Insert guardian details
    $studentdetails = new StudentDetails();
    $studentdetails->IDNO     = $IDNO;
    $studentdetails->GUARDIAN = $GUARDIAN;
    $studentdetails->GCONTACT = $GCONTACT;
    $studentdetails->create();

    // Update student auto-number
    $studAuto = new Autonumber();
    $studAuto->studauto_update();

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

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Enrollment Confirmation";
        $mail->Body    = "
            <h3>Hello $FNAME,</h3>
            <p>Your enrollment has been successfully processed.</p>
            <p><strong>Enrollment Details:</strong></p>
            <ul>
                <li><b>Student ID:</b> $IDNO</li>
                <li><b>Full Name:</b> $FNAME $MI $LNAME</li>
                <li><b>Course:</b> $COURSEID</li>
                <li><b>Semester:</b> $SEMESTER</li>
            </ul>
            <p>Thank you for enrolling. If you have any questions, contact us at support@yourdomain.com.</p>
            <p><b>- Enrollment Team</b></p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log(message: "Email could not be sent. Error: {$mail->ErrorInfo}");
    }

    redirect(location: "home.php");
}

// School Year Calculation
$currentyear = date(format: 'Y');
$nextyear = date(format: 'Y') + 1;
$sy = $currentyear . '-' . $nextyear;

$studAuto = new Autonumber();
$autonum = $studAuto->stud_autonumber();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Swiper CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css_files/cards.css" />
</head>

<body class="overflow-x-hidden"> <!-- Prevent horizontal scrolling -->
        

        <!-- Carousel -->
        <?php 
        
        include "header.php";
        include "enrollform.php";
        ?>
    </div>

    <!-- Swiper JS CDN -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="scripts_js/script.js"></script>
</body>
</html>