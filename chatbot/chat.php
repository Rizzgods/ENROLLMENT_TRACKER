<?php
// First include config for session settings
require_once('config.php');
// Then start the session
session_start();

// Check if user is verified
if (!isset($_SESSION['verified_email']) || !isset($_SESSION['verified_user'])) {
    // Redirect to verification page
    header('Location: verify_email.php');
    exit;
}

// Include configuration and helper functions
require_once('db_functions.php');
require_once('chat_functions.php');

// Log access to the chat page
ChatbotLogger::info("Chat page accessed", [
    'user_id' => $_SESSION['verified_user']['IDNO'],
    'email' => $_SESSION['verified_email']
]);

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'error' => ''
];

// Handle the incoming chat request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the message from the request
    $data = json_decode(file_get_contents('php://input'), true);
    $userMessage = isset($data['message']) ? trim($data['message']) : '';
    
    // Check if message is empty
    if (empty($userMessage)) {
        $response['error'] = 'Please provide a message';
        echo json_encode($response);
        exit;
    }
    
    ChatbotLogger::info("Chat message received", [
        'user_id' => $_SESSION['verified_user']['IDNO'],
        'message' => $userMessage
    ]);
    
    // Initialize conversation history if it doesn't exist
    if (!isset($_SESSION['conversation'])) {
        // Get user's first name for personalization
        $firstName = $_SESSION['verified_user']['FNAME'];
        $studentId = $_SESSION['verified_user']['IDNO'];
        
        // Customize the system prompt to include student ID and always use their verified information
        $_SESSION['conversation'] = [
            [
                'role' => 'system', 
                'content' => "You are a helpful enrollment assistant for Bestlink College of the Philippines. You are currently helping {$firstName} (Student ID: {$studentId}). Your goal is to assist with enrollment questions, course information, schedules, and academic procedures. Keep responses concise, friendly, and educational. Always refer to the institution as \"Bestlink College of the Philippines\". For student-specific information like schedules or records, remind them that you'll use their verified account information - they don't need to provide their ID again. If asked about topics outside of the academic context, politely redirect the conversation to enrollment and college-related matters."
            ]
        ];
        
        ChatbotLogger::debug("New conversation initialized for user", [
            'user_id' => $studentId
        ]);
    }
    
    // Add user message to conversation
    $_SESSION['conversation'][] = [
        'role' => 'user',
        'content' => $userMessage
    ];
    
    // Check if user is asking for specific database information
    $dbInfoRequested = false;
    $dbResponse = null;
    $useApi = true;
    
    try {
        // Only use database responses for specific personal information or sensitive data
        // This is a more focused list than before
        if (stripos($userMessage, 'my schedule') !== false ||
            stripos($userMessage, 'my status') !== false || 
            stripos($userMessage, 'my record') !== false || 
            stripos($userMessage, 'my enrollment') !== false ||
            stripos($userMessage, 'my account') !== false || 
            stripos($userMessage, 'my profile') !== false ||
            stripos($userMessage, 'my balance') !== false || 
            stripos($userMessage, 'who am i') !== false) {
            
            $dbInfoRequested = true;
            // Always use the verified student ID from session
            $dbResponse = getFallbackResponse($userMessage, $_SESSION['verified_user']['IDNO']);
            
            // Add any database responses to the AI conversation history too,
            // so it can maintain context even when DB answers questions
            if ($dbResponse) {
                $_SESSION['conversation'][] = [
                    'role' => 'assistant',
                    'content' => $dbResponse
                ];
            }
            
            ChatbotLogger::info("Personal information requested", [
                'request_type' => 'personal_info',
                'user_id' => $_SESSION['verified_user']['IDNO']
            ]);
        } else {
            // For general questions about the college, courses, etc., use the AI
            $dbInfoRequested = false;
        }
        
        // If database information requested and successfully retrieved, use it
        if ($dbInfoRequested && $dbResponse) {
            $botMessage = $dbResponse;
            $useApi = false;
            
            // Check if the response contains HTML (for logout links)
            if (strpos($botMessage, '<a href') !== false) {
                $response['html_content'] = true;
            }
            
            ChatbotLogger::info("Using database response", [
                'response_length' => strlen($botMessage)
            ]);
        } else {
            // Check if API fallback mode is enabled before trying API
            try {
                // Skip API call if in local dev mode without API access
                if (defined('LOCAL_DEV_MODE') && LOCAL_DEV_MODE) {
                    // Simulate API error to use fallback
                    throw new Exception('Development mode - using fallback responses');
                }
                
                // For everything else, try to use AI API
                $botMessage = getAIResponse($_SESSION['conversation']);
                
                ChatbotLogger::info("Using API response", [
                    'response_length' => strlen($botMessage)
                ]);
            } catch (Exception $apiEx) {
                ChatbotLogger::info("API not available - using rule-based responses", [
                    'reason' => $apiEx->getMessage()
                ]);
                
                // Use our regular expression system as fallback
                $fallbackMessage = getFallbackResponse($userMessage, $_SESSION['verified_user']['IDNO']);
                
                if ($fallbackMessage !== null) {
                    $botMessage = $fallbackMessage;
                    ChatbotLogger::info("Using fallback rule-based response", [
                        'response_length' => strlen($botMessage)
                    ]);
                } else {
                    // If even the fallback system returns null, give a generic response
                    $botMessage = "I understand you're asking about Bestlink College of the Philippines. " .
                                 "For this specific question, please contact our admissions office at info@bestlink.edu.ph " .
                                 "or call (083) 228-9722 for the most accurate information.";
                }
            }
        }
    } catch (Exception $e) {
        // Log the error with more details
        ChatbotLogger::error("Chat processing error", $e);
        
        // Fall back to database-based response
        $useApi = false;
        
        // Try to provide a meaningful fallback response
        try {
            if (stripos($userMessage, 'schedule') !== false || 
                (stripos($userMessage, 'class') !== false && !stripos($userMessage, 'classroom'))) {
                // If it's a schedule request, provide personalized response using verified ID
                $botMessage = getFallbackResponse($userMessage, $_SESSION['verified_user']['IDNO']);
            } else {
                // General fallback
                $botMessage = getFallbackResponse($userMessage, $_SESSION['verified_user']['IDNO']);
            }
            
            // Add a note to the error log about using fallback
            error_log('Using fallback response system for query: ' . $userMessage);
        } catch (Exception $fallbackException) {
            ChatbotLogger::error("Fallback response error", $fallbackException);
            $botMessage = "Sorry, we are currently experiencing technical difficulties. Please try again later.";
        }
    }
    
    // Add bot response to conversation history
    $_SESSION['conversation'][] = [
        'role' => 'assistant',
        'content' => $botMessage
    ];
    
    // Keep conversation history manageable (limit to last 10 messages)
    if (count($_SESSION['conversation']) > 11) { // 1 system message + 10 conversation messages
        array_splice($_SESSION['conversation'], 1, count($_SESSION['conversation']) - 11);
    }
    
    // Set successful response
    $response['success'] = true;
    $response['message'] = $botMessage;
    if (!$useApi || $dbInfoRequested) {
        $response['source'] = 'database';
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If not a POST request, display the chat interface
include('chat_interface.php');
?>