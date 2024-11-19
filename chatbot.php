<?php
// Set your OpenAI API key here (you will need to have your own API key from OpenAI)
define('OPENAI_API_KEY', 'your_openai_api_key');

// Handle chat message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    // Get the message from the form input
    $message = $_POST['message'];

    // Send the message to OpenAI's API and get a response
    $chatbotResponse = getChatbotResponse($message);

    // Return the chatbot response as a JSON object
    echo json_encode(['response' => $chatbotResponse]);
    exit();
}

// Function to call OpenAI API and get a response
function getChatbotResponse($message)
{
    // Custom predefined responses
    if (strtolower($message) == "how can i contact customer support?") {
        return "You can contact customer support by calling this mobile number: 09765635267.";
    } elseif (strtolower($message) == "what payment methods are accepted?") {
        return "You can pay through GCASH and Cash on Delivery (COD).";
    }

    // Fallback to OpenAI API for other messages
    $url = 'https://api.openai.com/v1/completions';

    $data = [
        'model' => 'text-davinci-003',  // You can use any GPT-3 or GPT-4 model
        'prompt' => $message,
        'max_tokens' => 150,
        'temperature' => 0.7,
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer " . OPENAI_API_KEY . "\r\n",
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);

    // Get the response text
    if (isset($response['choices'][0]['text'])) {
        return trim($response['choices'][0]['text']);
    } else {
        return "Sorry, I couldn't process your request.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot | Mabsi Soy</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ensure chat window is hidden by default */
        #chatWindow {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Chatbot Button in the Bottom-Right -->
    <button id="chatButton" class="fixed bottom-4 right-4 p-4 shadow-lg transition duration-300 w-24 rounded-full">
         <img src="./img/chat.png" alt="">
    </button>

    <!-- Chat Window -->
    <div id="chatWindow" class="fixed bottom-16 right-4 w-96 bg-white p-6 rounded-lg shadow-lg z-30">
        <!-- Close Button (X) -->
        <button id="closeChat" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Chat with Us</h1>

        <div id="chatbox" class="h-80 overflow-y-auto bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
            <!-- Messages will appear here -->
        </div>

        <!-- Predefined Questions -->
        <div id="predefinedQuestions" class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Popular Questions</h2>
            <div class="flex flex-wrap gap-2">
                <button class="questionBtn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">How to order?</button>
                <button class="questionBtn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">How to register?</button>
                <button class="questionBtn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">What payment methods are accepted?</button>
                <button class="questionBtn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">How can I contact customer support?</button>
            </div>
        </div>

        <!-- User Input Form -->
        <form id="chatForm" class="flex flex-col">
            <input type="text" id="messageInput" class="w-full p-4 border border-gray-300 rounded-md mb-4" placeholder="Type your message..." required>
            <button type="submit" class="w-full py-3 bg-orange-500 text-white font-semibold rounded-md hover:bg-orange-600 transition duration-500">Send</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Show/hide chat window when button is clicked
            $('#chatButton').click(function() {
                $('#chatWindow').toggle();  // Toggle visibility of the chat window
            });

            // Close chat window when "X" button is clicked
            $('#closeChat').click(function() {
                $('#chatWindow').hide();  // Hide chat window
            });

            // Send the predefined question to the server when a question button is clicked
            $('.questionBtn').click(function() {
                const message = $(this).text();
                appendMessage('user', message);

                // Send the message to PHP and get the chatbot response
                $.ajax({
                    type: 'POST',
                    url: 'chatbot.php',
                    data: { message: message },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.response) {
                            appendMessage('chatbot', response.response);
                        }
                    },
                    error: function() {
                        appendMessage('chatbot', 'Sorry, there was an error. Please try again later.');
                    }
                });
            });

            // Send the message to the server when the form is submitted
            $('#chatForm').submit(function(e) {
                e.preventDefault();

                const message = $('#messageInput').val().trim();
                if (message) {
                    appendMessage('user', message);
                    $('#messageInput').val('');  // Clear input field

                    // Send the message to PHP and get the chatbot response
                    $.ajax({
                        type: 'POST',
                        url: 'chatbot.php',
                        data: { message: message },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.response) {
                                appendMessage('chatbot', response.response);
                            }
                        },
                        error: function() {
                            appendMessage('chatbot', 'Sorry, there was an error. Please try again later.');
                        }
                    });
                }
            });

            // Function to append message to the chatbox
            function appendMessage(sender, message) {
                const messageClass = sender === 'user' ? 'bg-green-500 text-white self-end' : 'bg-blue-500 text-white self-start';
                const messageHtml = `<div class="my-2 p-3 rounded-lg ${messageClass} max-w-[80%]">${message}</div>`;
                $('#chatbox').append(messageHtml);
                $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);  // Scroll to the bottom
            }
        });
    </script>

</body>
</html>
