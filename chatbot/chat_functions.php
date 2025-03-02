<?php
// Function to get AI response from DeepSeek API
function getAIResponse($conversation) {
    global $apiKey, $apiEndpoint;
    
    // Prepare data for API request
    $postData = [
        'model' => 'deepseek-chat',
        'messages' => $conversation,
        'temperature' => 0.7,
        'max_tokens' => 150,
        'top_p' => 1.0
    ];
    
    // Set up cURL request
    $ch = curl_init($apiEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    // Execute the request
    $apiResponse = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }
    
    // Get HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        // Log full response for debugging
        error_log('DeepSeek API error response: ' . $apiResponse);
        throw new Exception('API returned HTTP status ' . $httpCode . ': ' . $apiResponse);
    }
    
    curl_close($ch);
    
    // Decode the response
    $responseData = json_decode($apiResponse, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON decode error: ' . json_last_error_msg());
    }
    
    // DeepSeek response format might be different, check and adjust as needed
    if (!isset($responseData['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response format from API. Response: ' . $apiResponse);
    }
    
    // Get the bot's response
    return $responseData['choices'][0]['message']['content'];
}

// Fallback function for database-driven responses
function getFallbackResponse($userMessage) {
    $conn = connectToDatabase();
    
    if (!$conn) {
        return "I'm having trouble connecting to the database. Please try again later or contact technical support.";
    }
    
    $userMessage = strtolower($userMessage);
    
    // Check if message contains both a name and schedule request
    if (strpos($userMessage, 'schedule') !== false || 
        (strpos($userMessage, 'class') !== false && !strpos($userMessage, 'classroom'))) {
        
        // First try to extract a student ID from the message
        $studentId = null;
        preg_match('/\b\d{5,10}\b/', $userMessage, $matches);
        if (!empty($matches)) {
            $studentId = $matches[0];
            $studentInfo = getStudentInfo($studentId);
            
            if ($studentInfo) {
                $fullName = $studentInfo['FNAME'] . ' ' . 
                           ($studentInfo['MNAME'] ? $studentInfo['MNAME'] . ' ' : '') . 
                           $studentInfo['LNAME'];
                
                $scheduleResult = getStudentSchedule($studentId);
                
                if ($scheduleResult['type'] == 'schedule') {
                    return "Here is the class schedule for $fullName (ID: $studentId):\n\n" . $scheduleResult['data'];
                } else if ($scheduleResult['type'] == 'pending') {
                    // Different messages based on what's pending
                    if ($scheduleResult['data'] == 'schedule_pending') {
                        $email = isset($studentInfo['email']) ? $studentInfo['email'] : 'your registered email';
                        return "Hello $fullName (ID: $studentId),\n\nYour enrollment has been processed, but your class schedule is still being finalized. Please check back later or monitor $email for updates. If you have any questions, please contact the Registrar's Office.";
                    } else {
                        $email = isset($studentInfo['email']) ? $studentInfo['email'] : 'your registered email';
                        return "Hello $fullName (ID: $studentId),\n\nYour application is still being processed. You will receive your class schedule once your enrollment is complete. Please check $email regularly for updates or contact the Admissions Office at 123-456-7890 for more information.";
                    }
                }
            }
        }
        
        // If no student ID is found, try to extract a name
        // Common name patterns in messages like "Show me Hazardous Ceniza's schedule"
        $possibleNamePatterns = [
            '/schedule\s+(?:for|of)\s+([A-Za-z\s\.]+)(?:\s+with|$)/', // "schedule for John Doe"
            '/([A-Za-z\s\.]+)(?:\'s)?\s+schedule/', // "John Doe's schedule" or "John Doe schedule"
            '/([A-Za-z\s\.]+)(?:\'s)?\s+class/', // "John Doe's classes" 
            '/class\s+(?:for|of)\s+([A-Za-z\s\.]+)/', // "classes for John Doe"
        ];
        
        $extractedName = null;
        foreach ($possibleNamePatterns as $pattern) {
            if (preg_match($pattern, $userMessage, $matches)) {
                $extractedName = trim($matches[1]);
                break;
            }
        }
        
        if ($extractedName) {
            // Attempt to find student by name
            $students = getStudentByName($extractedName);
            
            if (!empty($students)) {
                // If only one student found, show their schedule
                if (count($students) === 1) {
                    $student = $students[0];
                    $studentId = $student['IDNO'];
                    $fullName = $student['FNAME'] . ' ' . 
                               ($student['MNAME'] ? $student['MNAME'] . ' ' : '') . 
                               $student['LNAME'];
                    
                    $schedule = getStudentSchedule($studentId);
                    
                    if ($schedule) {
                        return "Here is the class schedule for $fullName (ID: $studentId):\n\n$schedule";
                    } else {
                        return "I couldn't find any scheduled classes for $fullName (ID: $studentId). Please contact the Registrar's Office for the complete schedule or check your student portal.";
                    }
                } else {
                    // Multiple students with similar names, ask for clarification
                    $response = "I found multiple students with that name. Please specify which one by providing their student ID:\n\n";
                    foreach ($students as $student) {
                        $fullName = $student['FNAME'] . ' ' . 
                                   ($student['MNAME'] ? $student['MNAME'] . ' ' : '') . 
                                   $student['LNAME'];
                        $response .= "- $fullName (ID: " . $student['IDNO'] . ")\n";
                    }
                    return $response;
                }
            } else {
                return "I couldn't find a student named '$extractedName' in our database. Please check the spelling or provide the student ID for more accurate results.";
            }
        }
        
        // If no name or ID was successfully extracted
        return "To check a class schedule, please provide a student ID or full name. For example: 'Show me the schedule for student ID 1000000252' or 'What is Hazardous Ceniza's schedule?'";
    }
    
    // Check for enrollment status or progress questions
    if ((strpos($userMessage, 'status') !== false || strpos($userMessage, 'progress') !== false || 
         strpos($userMessage, 'application') !== false || strpos($userMessage, 'enrollment status') !== false ||
         strpos($userMessage, 'my enrollment') !== false || strpos($userMessage, 'track') !== false) && 
        (strpos($userMessage, 'check') !== false || strpos($userMessage, 'know') !== false || 
         strpos($userMessage, 'tell') !== false || strpos($userMessage, 'what is') !== false || 
         strpos($userMessage, 'what\'s') !== false || strpos($userMessage, 'how is') !== false || 
         strpos($userMessage, 'track') !== false)) {
        
        // Extract student ID if provided
        $studentId = null;
        
        // Simple pattern match for student ID - looking for numbers
        preg_match('/\b\d{5,10}\b/', $userMessage, $matches);
        if (!empty($matches)) {
            $studentId = $matches[0];
        }
        
        if ($studentId) {
            $studentInfo = getStudentInfo($studentId);
            
            if ($studentInfo) {
                // Get student information based on actual column names
                $fullName = $studentInfo['FNAME'] . ' ' . 
                           ($studentInfo['MNAME'] ? $studentInfo['MNAME'] . ' ' : '') . 
                           $studentInfo['LNAME'];
                           
                $course = isset($studentInfo['course_name']) ? $studentInfo['course_name'] : 'Not specified';
                $yearLevel = isset($studentInfo['YEARLEVEL']) ? $studentInfo['YEARLEVEL'] : 'Not specified';
                
                // Check payment status from studentaccount
                $paymentStatus = isset($studentInfo['PAYMENT']) ? $studentInfo['PAYMENT'] : 'Not available';
                $enrollmentStatus = isset($studentInfo['STATUS']) ? $studentInfo['STATUS'] : 'Pending';
                
                // Student type and semester
                $studentType = isset($studentInfo['stud_type']) ? $studentInfo['stud_type'] : 'Not specified';
                $semester = isset($studentInfo['SEMESTER']) ? $studentInfo['SEMESTER'] : 'Not specified';
                
                // Get schedule information
                $scheduleResult = getStudentSchedule($studentId);
                $scheduleInfo = "";
                
                if ($scheduleResult['type'] == 'schedule') {
                    $scheduleInfo = "\n\nSchedule Summary: You have scheduled classes. Type 'show my schedule' to view your complete timetable.";
                } else if ($scheduleResult['type'] == 'pending') {
                    if ($scheduleResult['data'] == 'schedule_pending') {
                        $email = isset($studentInfo['email']) ? $studentInfo['email'] : 'your registered email';
                        $scheduleInfo = "\n\nYour class schedule is still being finalized. Please check back later or monitor $email for updates.";
                    } else {
                        $email = isset($studentInfo['email']) ? $studentInfo['email'] : 'your registered email';
                        $scheduleInfo = "\n\nYour application is still being processed. You will receive your class schedule once your enrollment is complete. Please check $email regularly for updates.";
                    }
                }
                
                // Create comprehensive response with the actual data structure
                $response = "Here's the enrollment information for student ID $studentId ($fullName):\n\n";
                $response .= "Student Type: $studentType\n";
                $response .= "Program/Course: $course\n";
                $response .= "Year Level: $yearLevel\n";
                $response .= "Semester: $semester\n";
                $response .= "Enrollment Status: $enrollmentStatus\n";
                $response .= "Payment Status: $paymentStatus";
                $response .= $scheduleInfo;
                $response .= "\n\nFor more detailed information about your enrollment at Bestlink College of the Philippines, please visit the Registrar's Office or Finance Office.";
                
                return $response;
            } else {
                return "I couldn't find a student with ID $studentId in our Bestlink College of the Philippines database. Please make sure you've entered the correct student ID or contact the Registrar's Office for assistance.";
            }
        } else {
            return "To check your enrollment status at Bestlink College of the Philippines, please provide your student ID number. Type: 'Check my enrollment status with student ID 1000000***'. I need your ID to access your specific enrollment records from our database.";
        }
    }
    
    // Check for enrollment-related questions
    if (strpos($userMessage, 'enroll') !== false || strpos($userMessage, 'register') !== false || strpos($userMessage, 'sign up') !== false) {
        return "To enroll at Bestlink College of the Philippines, follow these steps: 1) Complete the online application form, 2) Submit required documents, 3) Pay the enrollment fee, and 4) Select your courses. Would you like more details about any of these steps?";
    }
    
    // Check for course-related questions
    if (strpos($userMessage, 'course') !== false || strpos($userMessage, 'program') !== false || strpos($userMessage, 'degree') !== false) {
        $courses = getAvailableCourses();
        
        if (count($courses) > 0) {
            return "Bestlink College of the Philippines offers the following programs: " . implode(", ", $courses) . ". Which program are you interested in?";
        }
        
        // Default response if we can't get specific program info
        return "Bestlink College of the Philippines offers various undergraduate and graduate programs in Business, Information Technology, Education, Engineering, and Health Sciences. Please visit our website or contact the admissions office for a complete list of programs.";
    }
    
    // Check for tuition-related questions
    if (strpos($userMessage, 'fee') !== false || strpos($userMessage, 'cost') !== false || strpos($userMessage, 'tuition') !== false || strpos($userMessage, 'payment') !== false) {
        return "At Bestlink College of the Philippines, tuition fees vary by program. For specific information about your tuition and other fees, please check your student portal or visit our Finance Office. We offer various payment schemes and financial assistance programs to help students manage their educational expenses.";
    }
    
    // Check for admission requirements
    if (strpos($userMessage, 'require') !== false || strpos($userMessage, 'document') !== false || strpos($userMessage, 'admission') !== false) {
        return "For admission to Bestlink College of the Philippines, you'll need to submit: 1) Accomplished application form, 2) Original copy of Form 138/transcript of records, 3) Certificate of Good Moral Character, 4) Birth Certificate, 5) 2x2 ID photos, and 6) Application fee. Additional requirements may apply for specific programs or transferees.";
    }
    
    // Check for enrollment statistics
    if (strpos($userMessage, 'how many') !== false && (strpos($userMessage, 'student') !== false || strpos($userMessage, 'enroll') !== false)) {
        // Query enrollment statistics
        $sql = "SELECT COUNT(*) as total FROM tblstudent";
        $result = $conn->query($sql);
        
        if ($result && $row = $result->fetch_assoc()) {
            return "Currently, Bestlink College of the Philippines has approximately " . $row["total"] . " students enrolled in our system.";
        }
        
        return "Bestlink College of the Philippines has thousands of enrolled students across various programs. For exact figures, please contact the Registrar's Office.";
    }
    
    // Check for balance inquiries
    if ((strpos($userMessage, 'balance') !== false || strpos($userMessage, 'payment') !== false || 
         strpos($userMessage, 'owe') !== false || strpos($userMessage, 'paid') !== false || 
         strpos($userMessage, 'due') !== false) &&
        (strpos($userMessage, 'my') !== false || strpos($userMessage, 'check') !== false || 
         strpos($userMessage, 'how much') !== false)) {
        
        return "To check your balance at Bestlink College of the Philippines, please provide your student ID. For example: 'Check my balance with student ID 12345'.";
    }
    
    // Check for contact information
    if (strpos($userMessage, 'contact') !== false || strpos($userMessage, 'phone') !== false || strpos($userMessage, 'email') !== false || strpos($userMessage, 'address') !== false) {
        return "You can contact Bestlink College of the Philippines through: Email: info@bestlink.edu.ph, Phone: (02) 8XXX-XXXX, or visit our campus at [College Address]. Our office hours are Monday to Friday, 8:00 AM to 5:00 PM.";
    }
    
    // Handle basic greetings
    if (strpos($userMessage, 'hello') !== false || strpos($userMessage, 'hi') !== false || strpos($userMessage, 'hey') !== false) {
        return "Hello! Welcome to Bestlink College of the Philippines. How can I assist you with your enrollment or academic inquiries today?";
    }
    
    // Handle thanks
    if (strpos($userMessage, 'thank') !== false) {
        return "You're welcome! If you have any more questions about Bestlink College of the Philippines, feel free to ask. We're here to help you succeed in your educational journey.";
    }
    
    // Check for basic name or ID inquiries
    if (strpos($userMessage, 'my name') !== false || 
        strpos($userMessage, 'my id') !== false ||
        strpos($userMessage, 'who am i') !== false) {
        
        return "To look up your information in our system, I'll need your student ID number. Could you please provide your student ID? For example, you can say 'My student ID is 1000000252'.";
    }
    
    // Default response for unknown queries - now proactively asking for ID
    if (!preg_match('/\b\d{5,10}\b/', $userMessage)) {
        return "Thank you for your interest in Bestlink College of the Philippines. To provide you with specific information related to your enrollment, courses, or schedule, I'll need your student ID number. Could you please share your student ID with me?";
    }
    
    // Default response for unknown queries
    return "Thank you for your interest in Bestlink College of the Philippines. I don't have specific information about your query. For more detailed information, please contact our Admissions Office at info@bestlink.edu.ph or call (02) 8XXX-XXXX.";
    
    $conn->close();
}
?>
