<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/tracking_errors.log');


$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection
$conn = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == "login") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Updated SQL query to join studentaccount with tblstudent using the correct fields
    $stmt = $conn->prepare("
        SELECT 
            sa.user_id, 
            sa.username, 
            sa.password, 
            sa.SCHEDULE, 
            sa.STATUS, 
            sa.PAYMENT,
            ts.FNAME,
            ts.LNAME,
            ts.MNAME,
            ts.SEX,
            ts.BDAY,
            ts.AGE,
            ts.CONTACT_NO,
            ts.HOME_ADD,
            ts.student_status,
            ts.YEARLEVEL,
            ts.STUDSECTION,
            ts.COURSE_ID,
            ts.STUDPHOTO,
            ts.SEMESTER,
            ts.SYEAR,
            ts.EMAIL,
            ts.stud_type,
            ts.id_pic
        FROM studentaccount sa
        LEFT JOIN tblstudent ts ON ts.IDNO = sa.user_id
        WHERE sa.username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        session_regenerate_id(true); // Prevent session fixation
        
        // Basic account info
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['schedule'] = $user['SCHEDULE'];
        $_SESSION['status'] = $user['STATUS'];
        $_SESSION['payment'] = $user['PAYMENT'];

        // Student personal information
        $_SESSION['student_name'] = trim($user['FNAME'] . ' ' . $user['MNAME'] . ' ' . $user['LNAME']);
        $_SESSION['student_email'] = $user['EMAIL'];
        $_SESSION['student_contact'] = $user['CONTACT_NO'];
        $_SESSION['student_address'] = $user['HOME_ADD'];
        $_SESSION['student_sex'] = $user['SEX'];
        $_SESSION['student_age'] = $user['AGE'];

        // Academic information
        $_SESSION['student_year'] = $user['YEARLEVEL'];
        $_SESSION['student_section'] = $user['STUDSECTION'];
        $_SESSION['student_course'] = $user['COURSE_ID'];
        $_SESSION['student_semester'] = $user['SEMESTER'];
        $_SESSION['student_sy'] = $user['SYEAR'];
        $_SESSION['student_type'] = $user['stud_type'];
        $_SESSION['student_status'] = $user['student_status'];
        
        // Photo
        $_SESSION['student_photo'] = $user['STUDPHOTO'];
        $_SESSION['id_pic'] = $user['id_pic']; // Add this line to store the photo
        
        // Log the login activity
        $logStmt = $conn->prepare("INSERT INTO tbllogs (USERID, LOGDATETIME, LOGROLE, LOGMODE) VALUES (?, NOW(), 'Student', 'Login')");
        $logStmt->bind_param("i", $user['user_id']);
        $logStmt->execute();
        $logStmt->close();
        
        echo "success";
        exit();
    } else {
        echo "error";
        exit();
    }
}
?>


