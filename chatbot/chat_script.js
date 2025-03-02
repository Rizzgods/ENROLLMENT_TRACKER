document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.querySelector('#chat-form button[type="submit"]');
    const chatMessages = document.getElementById('chat-messages');
    
    // Flag to track if a request is in progress
    let isRequestInProgress = false;

    chatForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // If a request is already in progress, do nothing
        if (isRequestInProgress) {
            return;
        }
        
        const message = messageInput.value.trim();
        if (message === '') return;
        
        // Set request in progress flag
        isRequestInProgress = true;
        
        // Disable input and button
        messageInput.disabled = true;
        sendButton.disabled = true;
        messageInput.classList.add('bg-gray-100');
        sendButton.classList.add('opacity-50');
        
        // Add user message to chat
        addMessage(message, 'user');
        
        // Clear input
        messageInput.value = '';
        
        // Show typing indicator
        showTypingIndicator();
        
        // Send message to server
        fetch('chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            // Hide typing indicator
            hideTypingIndicator();
            
            if (data.success) {
                // Add bot response
                addMessage(data.message);
            } else {
                // Show error
                addMessage('Sorry, I encountered an error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            // Hide typing indicator
            hideTypingIndicator();
            
            // Show error message
            addMessage('Sorry, there was a problem connecting to the server. Please try again later.');
            console.error('Error:', error);
        })
        .finally(() => {
            // Reset request in progress flag
            isRequestInProgress = false;
            
            // Re-enable input and button
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.classList.remove('bg-gray-100');
            sendButton.classList.remove('opacity-50');
            
            // Focus the input field for next message
            messageInput.focus();
        });
    });

    function addMessage(content, sender = 'bot') {
        const messageBubble = document.createElement('div');
        messageBubble.classList.add('message-bubble', sender === 'user' ? 'user-message' : 'bot-message', 'p-3', 'mb-4');
        
        // Create paragraph element for the message content with line breaks
        const formattedContent = content.replace(/\n/g, '<br>');
        messageBubble.innerHTML = `<p>${formattedContent}</p>`;
        
        const messageContainer = document.createElement('div');
        messageContainer.classList.add('flex', sender === 'user' ? 'justify-end' : 'justify-start');
        messageContainer.appendChild(messageBubble);
        
        chatMessages.appendChild(messageContainer);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTypingIndicator() {
        const typingIndicator = document.createElement('div');
        typingIndicator.id = 'typing-indicator';
        typingIndicator.classList.add('message-bubble', 'bot-message', 'p-3', 'mb-4');
        typingIndicator.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
        
        const typingContainer = document.createElement('div');
        typingContainer.classList.add('flex', 'justify-start');
        typingContainer.appendChild(typingIndicator);
        
        chatMessages.appendChild(typingContainer);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.parentElement.remove();
        }
    }
});
