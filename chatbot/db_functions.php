<?php
// Function to connect to the database
function connectToDatabase() {
    global $dbConfig;
    
    $conn = new mysqli($dbConfig['server'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname']);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
    }
    
    return $conn;
}

// Function to get student information
function getStudentInfo($studentId) {
    $conn = connectToDatabase();
    if (!$conn) return null;
    
    $sql = "SELECT s.*, sa.* FROM tblstudent s 
            LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id 
            WHERE s.IDNO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $studentInfo = $result->fetch_assoc();
        
        // Get course information
        $courseId = isset($studentInfo['COURSE_ID']) ? $studentInfo['COURSE_ID'] : null;
        if ($courseId) {
            $courseSql = "SELECT * FROM course WHERE course_id = ?";
            $courseStmt = $conn->prepare($courseSql);
            if ($courseStmt) {
                $courseStmt->bind_param("i", $courseId);
                $courseStmt->execute();
                $courseResult = $courseStmt->get_result();
                
                if ($courseResult && $courseResult->num_rows > 0) {
                    $courseInfo = $courseResult->fetch_assoc();
                    $studentInfo['course_name'] = isset($courseInfo['coursename']) ? 
                        $courseInfo['coursename'] : 
                        (isset($courseInfo['course_name']) ? $courseInfo['course_name'] : 'Not specified');
                }
                $courseStmt->close();
            }
        }
        
        $conn->close();
        return $studentInfo;
    }
    
    $conn->close();
    return null;
}

// Function to get available courses
function getAvailableCourses() {
    $conn = connectToDatabase();
    if (!$conn) return [];
    
    $sql = "SELECT * FROM course LIMIT 20";
    $result = $conn->query($sql);
    
    $courses = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if (isset($row['coursename']) && !empty($row['coursename'])) {
                $courses[] = $row['coursename'];
            } else if (isset($row['course_name']) && !empty($row['course_name'])) {
                $courses[] = $row['course_name'];
            }
        }
    }
    
    $conn->close();
    return $courses;
}

// Function to get student by name
function getStudentByName($name) {
    $conn = connectToDatabase();
    if (!$conn) return null;
    
    // Parse the name parts
    $nameParts = explode(' ', trim($name));
    $firstName = '';
    $middleName = '';
    $lastName = '';
    
    // Try to extract name parts based on common patterns
    if (count($nameParts) >= 3) {
        // Assume Format: First Middle Last
        $firstName = $nameParts[0];
        $lastName = $nameParts[count($nameParts) - 1];
        // Middle could be everything in between, possibly with an initial
        $middleParts = array_slice($nameParts, 1, count($nameParts) - 2);
        $middleName = implode(' ', $middleParts);
        
        // Remove any trailing periods from middle name/initial
        $middleName = rtrim($middleName, '.');
    } elseif (count($nameParts) == 2) {
        // Assume Format: First Last
        $firstName = $nameParts[0];
        $lastName = $nameParts[1];
    } else {
        // Just one name, assume it's a last name or first name
        $lastName = $nameParts[0];
    }
    
    // Prepare the search query - use LIKE for fuzzy matching
    // Try different combinations to increase chances of finding the student
    $sql = "SELECT s.*, sa.* FROM tblstudent s 
            LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id 
            WHERE (s.FNAME LIKE ? OR s.LNAME LIKE ?) 
            ORDER BY s.LNAME, s.FNAME LIMIT 5";
    
    $stmt = $conn->prepare($sql);
    
    // Use wildcards for more flexible matching
    $firstNameSearch = "%$firstName%";
    $lastNameSearch = "%$lastName%";
    
    $stmt->bind_param("ss", $firstNameSearch, $lastNameSearch);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    $conn->close();
    return $students;
}

// Function to get student schedule
function getStudentSchedule($studentId) {
    $conn = connectToDatabase();
    if (!$conn) return null;
    
    // First check if schedule exists in studentaccount
    $sql = "SELECT SCHEDULE, STATUS FROM studentaccount WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $schedule = isset($row['SCHEDULE']) ? $row['SCHEDULE'] : null;
        $status = isset($row['STATUS']) ? $row['STATUS'] : null;
        
        if (!empty($schedule)) {
            $conn->close();
            return ['type' => 'schedule', 'data' => $schedule];
        } else {
            // No schedule found, check enrollment status
            if ($status == 'Enrolled' || $status == 'Regular' || $status == 'Irregular') {
                // Student is enrolled but no schedule yet
                $conn->close();
                return ['type' => 'pending', 'data' => 'schedule_pending'];
            } else {
                // Student application is still being processed
                $conn->close();
                return ['type' => 'pending', 'data' => 'application_pending'];
            }
        }
    }
    
    $conn->close();
    // Return pending status as default when no data is found
    return ['type' => 'pending', 'data' => 'application_pending'];
}
?>
