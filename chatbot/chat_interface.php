<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestlink College - Enrollment Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: 500px;
        }
        
        .message-bubble {
            max-width: 80%;
            word-wrap: break-word;
        }
        
        .user-message {
            background-color: #1d4ed8;
            color: white;
            border-radius: 18px 18px 0 18px;
        }
        
        .bot-message {
            background-color: #f3f4f6;
            color: #1f2937;
            border-radius: 18px 18px 18px 0;
        }
        
        .typing-indicator {
            display: inline-block;
        }
        
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #1d4ed8;
            border-radius: 50%;
            margin-right: 5px;
            animation: typing 1.4s infinite ease-in-out both;
        }
        
        .typing-indicator span:nth-child(1) {
            animation-delay: 0s;
        }
        
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Enrollment Assistant</h1>
                            <p class="text-sm text-blue-100">Bestlink College of the Philippines</p>
                        </div>
                    </div>
                    
                    <!-- User info display and logout button -->
                    <div class="flex items-center">
                        <div class="text-right mr-2">
                            <p class="font-medium">
                                <?php 
                                    echo htmlspecialchars($_SESSION['verified_user']['FNAME']) . ' ' . 
                                         htmlspecialchars($_SESSION['verified_user']['LNAME']); 
                                ?>
                            </p>
                            <p class="text-xs text-blue-100">
                                ID: <?php echo htmlspecialchars($_SESSION['verified_user']['IDNO']); ?>
                            </p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-blue-300 flex items-center justify-center text-blue-800 font-bold">
                            <?php echo strtoupper(substr($_SESSION['verified_user']['FNAME'], 0, 1)); ?>
                        </div>
                        <a href="logout.php" class="ml-3 px-3 py-1 bg-blue-800 hover:bg-red-600 rounded text-xs text-white transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Chat area -->
            <div id="chat-messages" class="chat-container p-4 overflow-y-auto">
                <!-- Welcome message - updated to be more human-like -->
                <div class="flex mb-4">
                    <div class="message-bubble bot-message p-3 ml-2">
                        <p>Hi <?php echo htmlspecialchars($_SESSION['verified_user']['FNAME']); ?>! 👋 I'm Emily, your enrollment assistant at Bestlink College. Great to meet you! How can I help with your questions about enrollment, courses, or anything else related to your studies today?</p>
                    </div>
                </div>
                
                <!-- Messages will be appended here by JavaScript -->
            </div>
            
            <!-- Input area -->
            <div class="border-t border-gray-200 p-4">
                <form id="chat-form" class="flex">
                    <input 
                        id="message-input" 
                        type="text" 
                        placeholder="Type your question here..." 
                        class="flex-grow px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-300 transition-colors"
                        required
                    >
                    <button 
                        type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-opacity"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
                <!-- Status indicator -->
                <div id="status-message" class="text-xs text-center mt-2 text-gray-500 h-4"></div>
            </div>
            
            <!-- Information footer -->
            <div class="bg-gray-50 p-3 text-center text-xs text-gray-500 border-t border-gray-200">
                <p>This assistant provides information about Bestlink College of the Philippines programs and enrollment.</p>
                <p class="mt-1">For official inquiries, please contact the Registrar's Office.</p>
            </div>
        </div>
    </div>

    <script src="chat_script.js"></script>
</body>
</html>