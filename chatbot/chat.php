<?php
// Initialize session if you need to track conversations
session_start();

// Include configuration and helper functions
require_once('config.php');
require_once('db_functions.php');
require_once('chat_functions.php');

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
    
    // Initialize conversation history if it doesn't exist
    if (!isset($_SESSION['conversation'])) {
        $_SESSION['conversation'] = [
            [
                'role' => 'system', 
                'content' => 'You are a helpful enrollment assistant for Bestlink College of the Philippines. Your goal is to assist students with enrollment questions, course information, class schedules, and academic procedures. When students ask about their information, always ask for their student ID number. Keep responses concise, friendly, and educational. Always refer to the institution as "Bestlink College of the Philippines" - never mention any other college or university. If asked about topics outside of the academic context, politely redirect the conversation to enrollment and college-related matters.'
            ]
        ];
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
        // Check for student ID patterns or specific database queries
        if (preg_match('/\b\d{5,10}\b/', $userMessage) || 
            stripos($userMessage, 'schedule') !== false ||
            stripos($userMessage, 'class') !== false ||
            stripos($userMessage, 'status') !== false || 
            stripos($userMessage, 'record') !== false || 
            stripos($userMessage, 'enrollment') !== false ||
            stripos($userMessage, 'information') !== false ||
            stripos($userMessage, 'details') !== false) {
            
            $dbInfoRequested = true;
            $dbResponse = getFallbackResponse($userMessage);
        }
        
        // If database information requested, skip API and use fallback
        if ($dbInfoRequested && $dbResponse) {
            $botMessage = $dbResponse;
            $useApi = false;
        } else {
            // Get response from AI API
            $botMessage = getAIResponse($_SESSION['conversation']);
        }
    } catch (Exception $e) {
        // Log the error with more details
        error_log('Chat processing error: ' . $e->getMessage());
        
        // Fall back to database-based response
        $useApi = false;
        
        // Try to provide a meaningful fallback response
        if (stripos($userMessage, 'schedule') !== false || 
            (stripos($userMessage, 'class') !== false && !stripos($userMessage, 'classroom'))) {
            // If it's a schedule request, try to handle that specifically
            $botMessage = "I can help you find a class schedule! Please provide a student ID number, for example: 'Show me the schedule for student ID 1000000252'.";
        } else {
            // General fallback
            $botMessage = getFallbackResponse($userMessage);
        }
        
        // Add a note to the error log about using fallback
        error_log('Using fallback response system for query: ' . $userMessage);
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