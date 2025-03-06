<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to client
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/enrollment_check_errors.log');

require_once __DIR__ . "/../include/database.php";

// Get student ID from request
$studentId = $_GET['id'] ?? '';

if (empty($studentId)) {
    echo json_encode(['status' => 'error', 'message' => 'Student ID is required']);
    exit;
}

try {
    global $mydb;
    $mydb->setQuery("SELECT * FROM tblstudent WHERE IDNO = ?");
    $mydb->executeQuery([$studentId]);
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo json_encode([
            'status' => 'success', 
            'found' => true,
            'student' => [
                'id' => $result->IDNO,
                'name' => $result->FNAME . ' ' . $result->LNAME,
                'course' => $result->COURSE_ID,
                'status' => $result->student_status
            ]
        ]);
    } else {
        echo json_encode(['status' => 'success', 'found' => false]);
    }
} catch (Exception $e) {
    error_log("Error checking enrollment: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>
