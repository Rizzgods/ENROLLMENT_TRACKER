<?php
// Function to get AI response from DeepSeek API
function getAIResponse($conversation) {
    global $apiKey, $apiEndpoint;
    
    try {
        // Check if API key is properly configured
        if (empty($apiKey)) {
            ChatbotLogger::warning("API key not configured", [
                'key_status' => 'empty'
            ]);
            throw new Exception('API key not configured');
        }
        
        // Add general info about the college as context if not already included
        $hasSystemMessage = false;
        foreach ($conversation as $message) {
            if ($message['role'] === 'system') {
                $hasSystemMessage = true;
                break;
            }
        }
        
        if (!$hasSystemMessage) {
            // Add system message with college details and conversational instruction
            array_unshift($conversation, [
                'role' => 'system',
                'content' => "You are Emily, a friendly and helpful enrollment assistant for Bestlink College of the Philippines. 
                
                About the college:
                - Bestlink College is located in Novaliches, Quezon City, Metro Manila
                - Contact: (083) 228-9722, Email: info@bestlink.edu.ph
                - Programs include Information Technology, Business Administration, Education, Nursing
                - Current semester is 1st Semester, Academic Year 2023-2024
                
                Your personality:
                - You're warm, friendly, and speak naturally like a human assistant
                - You use conversational tone, occasional friendly emojis, and natural speech patterns
                - You care about students and their educational journey
                - You avoid robotic responses and don't just look for keywords
                - You ask clarifying questions when needed
                - You maintain a professional but warm demeanor
                
                Additional capabilities:
                - You can also assist in teaching students direction on how to commute from their place to Bestlink College of the Philippines
                
                For student-specific data, remind them you can access their records since they're logged in already.
                If you don't know something specific, politely suggest they contact the appropriate office."
            ]);
        }
        
        // Prepare data for API request with optimized parameters for human-like conversation
        $postData = [
            'model' => 'deepseek-chat',
            'messages' => $conversation,
            'temperature' => 0.8,  // More creative and human-like
            'max_tokens' => 500,   // Allow for more detailed responses
            'top_p' => 0.9,        // More diverse vocabulary
            'presence_penalty' => 0.3, // Slightly discourage repetition
            'frequency_penalty' => 0.3 // Slightly discourage repetitive language
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 second timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 second connection timeout
        
        // Disable SSL verification for testing
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Execute the request
        $apiResponse = curl_exec($ch);
        
        // Check for errors
        if (curl_errno($ch)) {
            $curlError = curl_error($ch);
            ChatbotLogger::error("cURL error in API request", [
                'errno' => curl_errno($ch),
                'error' => $curlError
            ]);
            throw new Exception('cURL error: ' . $curlError);
        }
        
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            ChatbotLogger::error("API HTTP error", [
                'http_code' => $httpCode,
                'response' => $apiResponse
            ]);
            throw new Exception('API returned HTTP status ' . $httpCode . ': ' . $apiResponse);
        }
        
        curl_close($ch);
        
        // Decode the response
        $responseData = json_decode($apiResponse, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ChatbotLogger::error("JSON decode error", [
                'error' => json_last_error_msg(),
                'response' => $apiResponse
            ]);
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }
        
        // Check response format
        if (!isset($responseData['choices'][0]['message']['content'])) {
            ChatbotLogger::error("Invalid API response format", [
                'response' => $apiResponse
            ]);
            throw new Exception('Invalid response format from API');
        }
        
        // Get the bot's response
        $botResponse = $responseData['choices'][0]['message']['content'];
        
        ChatbotLogger::info("API response received successfully", [
            'response_length' => strlen($botResponse)
        ]);
        
        return $botResponse;
        
    } catch (Exception $e) {
        ChatbotLogger::error("Exception in getAIResponse", $e);
        throw $e; // Re-throw for higher level handling
    }
}

// Fallback function for database-driven responses
function getFallbackResponse($userMessage, $studentId = null) {
    try {
        $conn = connectToDatabase();
        
        if (!$conn) {
            ChatbotLogger::error("Database connection failed in getFallbackResponse");
            return "I'm having trouble connecting to the database. Please try again later or contact technical support.";
        }
        
        $userMessage = strtolower($userMessage);
        
        // Always use the verified user's ID from the session for security
        // This ensures the user can only access their own information
        if (isset($_SESSION['verified_user']) && isset($_SESSION['verified_user']['IDNO'])) {
            $studentId = $_SESSION['verified_user']['IDNO'];
            
            ChatbotLogger::debug("Using verified user's ID", [
                'student_id' => $studentId
            ]);
        } else {
            ChatbotLogger::warning("No verified user found in session");
            return "Please verify your email first to access your student information.";
        }
        
        // Extract any student ID mentioned in the message for comparison
        preg_match('/\b\d{5,10}\b/', $userMessage, $matches);
        if (!empty($matches)) {
            $mentionedId = $matches[0];
            
            // If user is trying to access someone else's information, block it
            if ($mentionedId != $studentId) {
                ChatbotLogger::warning("User attempted to access another student's information", [
                    'user_id' => $studentId,
                    'requested_id' => $mentionedId
                ]);
                
                return "For security and privacy reasons, I can only provide information about your own student record. I'm showing information for your verified account only.";
            }
        }
        
        // Check if message contains schedule request
        if (strpos($userMessage, 'schedule') !== false || 
            (strpos($userMessage, 'class') !== false && !strpos($userMessage, 'classroom'))) {
            
            $studentInfo = getStudentInfo($studentId);
                
            if ($studentInfo) {
                $fullName = $studentInfo['FNAME'] . ' ' . 
                           ($studentInfo['MNAME'] ? $studentInfo['MNAME'] . ' ' : '') . 
                           $studentInfo['LNAME'];
                
                $scheduleResult = getStudentSchedule($studentId);
                
                if ($scheduleResult['type'] == 'schedule') {
                    return "Hello $fullName,\n\nHere is your enrollment schedule:\n\n" . 
                           $scheduleResult['data'] . 
                           "\n\nIMPORTANT REMINDER: Please proceed to the Registrar's Office at your scheduled time with the following:\n" .
                           "1. Required documents (Form 138, Good Moral Certificate, Birth Certificate, etc.)\n" .
                           "2. Enrollment down payment\n" .
                           "3. Student ID or valid identification\n\n" .
                           "Missing your scheduled time may result in delays. If you have any questions, please contact the Registrar's Office.";
                } else if ($scheduleResult['type'] == 'pending') {
                    // Different messages based on what's pending
                    if ($scheduleResult['data'] == 'schedule_pending') {
                        $email = isset($studentInfo['email']) ? $studentInfo['email'] : 'your registered email';
                        return "Hello $fullName,\n\nYour enrollment has been processed, but your enrollment schedule is still being finalized. You will receive an email notification at $email when your schedule is ready. Please ensure to prepare your required documents and downpayment in advance. If you have any questions, please contact the Registrar's Office.";
                    } else {
                        $email = isset($studentInfo['email']) ? $studentInfo['email'] : 'your registered email';
                        return "Hello $fullName,\n\nYour application is still being processed. You will receive your enrollment schedule at $email once your application is approved. Please prepare the required documents and downpayment for enrollment. For more information, contact the Admissions Office.";
                    }
                } else {
                    return "I couldn't retrieve your enrollment schedule information. Please visit or contact the Registrar's Office for assistance with your enrollment schedule.";
                }
            } else {
                return "I couldn't find your student information in our system. Please contact the Registrar's Office for assistance with your enrollment schedule.";
            }
        }
        
        // Check for enrollment status or progress questions
        if (strpos($userMessage, 'status') !== false || 
            strpos($userMessage, 'progress') !== false || 
            strpos($userMessage, 'application') !== false || 
            strpos($userMessage, 'enrollment') !== false ||
            strpos($userMessage, 'my account') !== false || 
            strpos($userMessage, 'profile') !== false ||
            strpos($userMessage, 'my info') !== false) {
            
            $studentInfo = getStudentInfo($studentId);
            
            if ($studentInfo) {
                // Get student information based on actual column names
                $fullName = $studentInfo['FNAME'] . ' ' . 
                           ($studentInfo['MNAME'] ? $studentInfo['MNAME'] . ' ' : '') . 
                           $studentInfo['LNAME'];
                           
                // Improve course information display with name and description
                $courseName = isset($studentInfo['course_name']) ? $studentInfo['course_name'] : 'Not specified';
                $courseDesc = isset($studentInfo['COURSE_DESC']) ? $studentInfo['COURSE_DESC'] : '';
                
                // Format course display with description if available
                $courseDisplay = $courseName;
                if (!empty($courseDesc)) {
                    $courseDisplay .= " ($courseDesc)";
                }
                
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
                    $scheduleInfo = "\n\nEnrollment Schedule: You have a scheduled enrollment appointment. Type 'show my schedule' to view your enrollment appointment details.";
                } else if ($scheduleResult['type'] == 'pending') {
                    if ($scheduleResult['data'] == 'schedule_pending') {
                        $email = isset($studentInfo['EMAIL']) ? $studentInfo['EMAIL'] : 'your registered email';
                        $scheduleInfo = "\n\nYour enrollment schedule is still being finalized. You will be notified at $email when your appointment is set.";
                    } else {
                        $email = isset($studentInfo['EMAIL']) ? $studentInfo['EMAIL'] : 'your registered email';
                        $scheduleInfo = "\n\nYour application is still being processed. You will receive your enrollment schedule once your application is approved.";
                    }
                }
                
                // Create comprehensive response with the actual data structure
                $response = "Hello $fullName, here's your enrollment information:\n\n";
                $response .= "Student ID: $studentId\n";
                $response .= "Student Type: $studentType\n";
                $response .= "Program/Course: $courseDisplay\n";
                $response .= "Year Level: $yearLevel\n";
                $response .= "Semester: $semester\n";
                $response .= "Enrollment Status: $enrollmentStatus\n";
                $response .= "Payment Status: $paymentStatus";
                $response .= $scheduleInfo;
                $response .= "\n\nFor more detailed information about your enrollment at Bestlink College of the Philippines, please visit the Registrar's Office or Finance Office.";
                
                return $response;
            } else {
                return "I couldn't find your student information in our system. Please contact the Registrar's Office for assistance.";
            }
        }
        
        // Check for logout/sign out requests
        if (strpos($userMessage, 'logout') !== false || 
            strpos($userMessage, 'log out') !== false || 
            strpos($userMessage, 'sign out') !== false ||
            strpos($userMessage, 'end session') !== false ||
            strpos($userMessage, 'terminate session') !== false) {
            
            return "To log out from your session, please click the 'Logout' button in the top right corner of the screen, or <a href='logout.php' class='text-blue-600 underline'>click here</a>.";
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
            // Get personalized fee information if available
            $studentInfo = getStudentInfo($studentId);
            
            if ($studentInfo && isset($studentInfo['PAYMENT'])) {
                $paymentStatus = $studentInfo['PAYMENT'] ?: 'Not available';
                $course = isset($studentInfo['course_name']) ? $studentInfo['course_name'] : 'your course';
                
                return "According to our records, your current payment status is: $paymentStatus. Tuition fees for $course vary based on your specific program and enrolled units. For a detailed breakdown of your fees, please visit the Finance Office or check your student portal. We offer various payment schemes and financial assistance programs to help students manage their educational expenses.";
            } else {
                // Generic response if no specific payment info is available
                return "At Bestlink College of the Philippines, tuition fees vary by program. For specific information about your tuition and other fees, please check your student portal or visit our Finance Office. We offer various payment schemes and financial assistance programs to help students manage their educational expenses.";
            }
        }
        
        // Check for admission requirements
        if (strpos($userMessage, 'require') !== false || strpos($userMessage, 'document') !== false || strpos($userMessage, 'admission') !== false) {
            return "For admission to Bestlink College of the Philippines, you'll need to submit: 1) Accomplished application form, 2) Original copy of Form 138/transcript of records, 3) Certificate of Good Moral Character, 4) Birth Certificate, 5) 2x2 ID photos, and 6) Application fee. Additional requirements may apply for specific programs or transferees.";
        }
        
        // Check for balance inquiries
        if ((strpos($userMessage, 'balance') !== false || strpos($userMessage, 'payment') !== false || 
             strpos($userMessage, 'owe') !== false || strpos($userMessage, 'paid') !== false || 
             strpos($userMessage, 'due') !== false) &&
            (strpos($userMessage, 'my') !== false || strpos($userMessage, 'check') !== false || 
             strpos($userMessage, 'how much') !== false)) {
            
            // Get personalized balance information
            $studentInfo = getStudentInfo($studentId);
            
            if ($studentInfo && isset($studentInfo['PAYMENT'])) {
                $paymentStatus = $studentInfo['PAYMENT'] ?: 'Not available';
                return "According to our records, your current payment status is: $paymentStatus. For the exact balance and detailed breakdown of your account, please visit the Finance Office or check your student portal.";
            } else {
                return "I don't have access to your detailed balance information. For the exact amount and breakdown of your fees, please visit the Finance Office or check your student portal.";
            }
        }
        
        // Check for contact information
        if (strpos($userMessage, 'contact') !== false || strpos($userMessage, 'phone') !== false || strpos($userMessage, 'email') !== false || strpos($userMessage, 'address') !== false) {
            return "You can contact Bestlink College of the Philippines through: Email: info@bestlink.edu.ph, Phone: (083) 228-9722, or visit our campus at Kaligayahan, Novaliches, Quezon City, Philippines. Our office hours are Monday to Friday, 8:00 AM to 5:00 PM.";
        }
        
        // Handle basic greetings
        if (strpos($userMessage, 'hello') !== false || strpos($userMessage, 'hi') !== false || strpos($userMessage, 'hey') !== false) {
            $studentInfo = getStudentInfo($studentId);
            $firstName = $studentInfo ? $studentInfo['FNAME'] : "there";
            
            return "Hello $firstName! I'm the Bestlink College enrollment assistant. I can help you with enrollment information, schedules, course details, and more. What would you like to know today?";
        }
        
        // Handle thanks
        if (strpos($userMessage, 'thank') !== false) {
            return "You're welcome! If you have any more questions about Bestlink College of the Philippines, feel free to ask. We're here to help you succeed in your educational journey.";
        }
        
        // Check for ID or personal information queries
        if (strpos($userMessage, 'my name') !== false || 
            strpos($userMessage, 'my id') !== false ||
            strpos($userMessage, 'who am i') !== false) {
            
            $studentInfo = getStudentInfo($studentId);
            
            if ($studentInfo) {
                $fullName = $studentInfo['FNAME'] . ' ' . 
                           ($studentInfo['MNAME'] ? $studentInfo['MNAME'] . ' ' : '') . 
                           $studentInfo['LNAME'];
                           
                return "You are $fullName, student ID: $studentId. Your account is currently verified with email: " . $_SESSION['verified_email'] . ".";
            } else {
                return "You're logged in with student ID: $studentId, but I couldn't find additional details in our system. Please contact the Registrar's Office for assistance.";
            }
        }
        
        // Default response for general queries - no need to ask for ID anymore
        return "Thank you for your query about Bestlink College of the Philippines. I can help you with enrollment information, course details, schedules, and more. If you'd like specific information about your enrollment, you can ask about your 'status', 'schedule', or 'balance'.";
        
        $conn->close();
    } catch (Exception $e) {
        ChatbotLogger::error("Exception in getFallbackResponse", $e);
        return "I'm sorry, but I encountered a technical error while processing your request. Please try again or contact the IT support team if the issue persists.";
    }
}
?>
