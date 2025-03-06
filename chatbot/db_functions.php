<?php
// Function to connect to the database
function connectToDatabase() {
    global $dbConfig;
    
    try {
        $servername = "localhost";
        $username = "admi_greenvalley";
        $password = "xr9%kxu%*my^+kf2";
        $dbname = "admi_dbgreenvalley";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            ChatbotLogger::error("Database connection failed", [
                'error' => $conn->connect_error,
                'errno' => $conn->connect_errno
            ]);
            return null;
        }
        
        ChatbotLogger::debug("Database connected successfully");
        return $conn;
    } catch (Exception $e) {
        ChatbotLogger::error("Exception when connecting to database", $e);
        return null;
    }
}

// Function to get student information
function getStudentInfo($studentId) {
    $conn = connectToDatabase();
    if (!$conn) return null;
    
    try {
        $sql = "SELECT s.*, sa.* FROM tblstudent s 
                LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id 
                WHERE s.IDNO = ?";
        ChatbotLogger::logQuery($sql, ['studentId' => $studentId]);
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            ChatbotLogger::error("Prepare failed in getStudentInfo", [
                'error' => $conn->error,
                'studentId' => $studentId
            ]);
            $conn->close();
            return null;
        }
        
        $stmt->bind_param("i", $studentId);
        $execResult = $stmt->execute();
        
        if (!$execResult) {
            ChatbotLogger::error("Execute failed in getStudentInfo", [
                'error' => $stmt->error,
                'studentId' => $studentId
            ]);
            $stmt->close();
            $conn->close();
            return null;
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $studentInfo = $result->fetch_assoc();
            
            // Get course information
            $courseId = isset($studentInfo['COURSE_ID']) ? $studentInfo['COURSE_ID'] : null;
            if ($courseId) {
                $courseSql = "SELECT * FROM course WHERE course_id = ?";
                ChatbotLogger::logQuery($courseSql, ['courseId' => $courseId]);
                
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
                } else {
                    ChatbotLogger::warning("Failed to prepare course query", [
                        'error' => $conn->error,
                        'courseId' => $courseId
                    ]);
                }
            }
            
            ChatbotLogger::info("Student information retrieved", [
                'studentId' => $studentId,
                'found' => true
            ]);
            
            $stmt->close();
            $conn->close();
            return $studentInfo;
        }
        
        ChatbotLogger::info("No student found with ID", [
            'studentId' => $studentId
        ]);
        
        $stmt->close();
        $conn->close();
        return null;
    } catch (Exception $e) {
        ChatbotLogger::error("Exception in getStudentInfo", $e);
        if ($conn) $conn->close();
        return null;
    }
}

// Function to get available courses
function getAvailableCourses() {
    $conn = connectToDatabase();
    if (!$conn) return [];
    
    try {
        $sql = "SELECT * FROM course LIMIT 20";
        ChatbotLogger::logQuery($sql);
        
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
            
            ChatbotLogger::info("Courses retrieved", [
                'count' => count($courses)
            ]);
        } else {
            ChatbotLogger::warning("No courses found in database");
        }
        
        $conn->close();
        return $courses;
    } catch (Exception $e) {
        ChatbotLogger::error("Exception in getAvailableCourses", $e);
        if ($conn) $conn->close();
        return [];
    }
}

// Function to get student by name
function getStudentByName($name) {
    $conn = connectToDatabase();
    if (!$conn) return null;
    
    try {
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
        
        ChatbotLogger::debug("Parsed name parts", [
            'original' => $name,
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName
        ]);
        
        // Prepare the search query - use LIKE for fuzzy matching
        // Try different combinations to increase chances of finding the student
        $sql = "SELECT s.*, sa.* FROM tblstudent s 
                LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id 
                WHERE (s.FNAME LIKE ? OR s.LNAME LIKE ?) 
                ORDER BY s.LNAME, s.FNAME LIMIT 5";
        
        ChatbotLogger::logQuery($sql, [
            'firstName' => "%$firstName%",
            'lastName' => "%$lastName%"
        ]);
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            ChatbotLogger::error("Prepare failed in getStudentByName", [
                'error' => $conn->error,
                'name' => $name
            ]);
            $conn->close();
            return null;
        }
        
        // Use wildcards for more flexible matching
        $firstNameSearch = "%$firstName%";
        $lastNameSearch = "%$lastName%";
        
        $stmt->bind_param("ss", $firstNameSearch, $lastNameSearch);
        $execResult = $stmt->execute();
        
        if (!$execResult) {
            ChatbotLogger::error("Execute failed in getStudentByName", [
                'error' => $stmt->error,
                'name' => $name
            ]);
            $stmt->close();
            $conn->close();
            return null;
        }
        
        $result = $stmt->get_result();
        
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        
        ChatbotLogger::info("Students found by name", [
            'name' => $name,
            'count' => count($students)
        ]);
        
        $stmt->close();
        $conn->close();
        return $students;
    } catch (Exception $e) {
        ChatbotLogger::error("Exception in getStudentByName", $e);
        if ($conn) $conn->close();
        return null;
    }
}

// Function to get student by email
function getStudentByEmail($email) {
    $conn = connectToDatabase();
    if (!$conn) return null;
    
    try {
        $sql = "SELECT * FROM tblstudent WHERE EMAIL = ?";
        ChatbotLogger::logQuery($sql, ['email' => $email]);
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            ChatbotLogger::error("Prepare failed in getStudentByEmail", [
                'error' => $conn->error,
                'email' => $email
            ]);
            $conn->close();
            return null;
        }
        
        $stmt->bind_param("s", $email);
        $execResult = $stmt->execute();
        
        if (!$execResult) {
            ChatbotLogger::error("Execute failed in getStudentByEmail", [
                'error' => $stmt->error,
                'email' => $email
            ]);
            $stmt->close();
            $conn->close();
            return null;
        }
        
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $student = $result->fetch_assoc();
            
            ChatbotLogger::info("Student found by email", [
                'email' => $email,
                'studentId' => $student['IDNO']
            ]);
            
            $stmt->close();
            $conn->close();
            return $student;
        }
        
        ChatbotLogger::info("No student found with email", [
            'email' => $email
        ]);
        
        $stmt->close();
        $conn->close();
        return null;
    } catch (Exception $e) {
        ChatbotLogger::error("Exception in getStudentByEmail", $e);
        if ($conn) $conn->close();
        return null;
    }
}

// Function to get student schedule
function getStudentSchedule($studentId) {
    $conn = connectToDatabase();
    if (!$conn) return null;
    
    try {
        // First check if schedule exists in studentaccount
        $sql = "SELECT SCHEDULE, STATUS FROM studentaccount WHERE user_id = ?";
        ChatbotLogger::logQuery($sql, ['studentId' => $studentId]);
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            ChatbotLogger::error("Prepare failed in getStudentSchedule", [
                'error' => $conn->error,
                'studentId' => $studentId
            ]);
            $conn->close();
            return null;
        }
        
        $stmt->bind_param("i", $studentId);
        $execResult = $stmt->execute();
        
        if (!$execResult) {
            ChatbotLogger::error("Execute failed in getStudentSchedule", [
                'error' => $stmt->error,
                'studentId' => $studentId
            ]);
            $stmt->close();
            $conn->close();
            return null;
        }
        
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $schedule = isset($row['SCHEDULE']) ? $row['SCHEDULE'] : null;
            $status = isset($row['STATUS']) ? $row['STATUS'] : null;
            
            if (!empty($schedule)) {
                ChatbotLogger::info("Schedule found for student", [
                    'studentId' => $studentId,
                    'status' => 'has_schedule'
                ]);
                
                $stmt->close();
                $conn->close();
                
                // Format the schedule to make it clear it's for enrollment
                $formattedSchedule = "Date: " . date("F j, Y") . "\n"; // Current date or you could parse from DB
                $formattedSchedule .= "Time: " . $schedule . "\n";
                $formattedSchedule .= "Location: Registrar's Office, Main Building";
                
                return ['type' => 'schedule', 'data' => $formattedSchedule];
            } else {
                // No schedule found, check enrollment status
                if ($status == 'Enrolled' || $status == 'Regular' || $status == 'Irregular') {
                    // Student is enrolled but no schedule yet
                    ChatbotLogger::info("Student enrolled but no schedule yet", [
                        'studentId' => $studentId,
                        'status' => $status
                    ]);
                    
                    $stmt->close();
                    $conn->close();
                    return ['type' => 'pending', 'data' => 'schedule_pending'];
                } else {
                    // Student application is still being processed
                    ChatbotLogger::info("Student application pending", [
                        'studentId' => $studentId,
                        'status' => $status
                    ]);
                    
                    $stmt->close();
                    $conn->close();
                    return ['type' => 'pending', 'data' => 'application_pending'];
                }
            }
        }
        
        ChatbotLogger::warning("No schedule or account information found", [
            'studentId' => $studentId
        ]);
        
        $stmt->close();
        $conn->close();
        // Return pending status as default when no data is found
        return ['type' => 'pending', 'data' => 'application_pending'];
    } catch (Exception $e) {
        ChatbotLogger::error("Exception in getStudentSchedule", $e);
        if ($conn) $conn->close();
        return ['type' => 'error', 'data' => 'system_error'];
    }
}
?>
