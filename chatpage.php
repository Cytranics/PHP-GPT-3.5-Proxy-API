<!DOCTYPE html>
<html>
<head>
    <title>Chat with GPT-3</title>
</head>
<body>
    <h1>Chat with GPT-3</h1>

    <ul id="messages">
        <li><strong>Bot:</strong> Hello, I'm your AI assistant. How can I help you today?</li>
    </ul>

    <form id="message-form">
        <input type="text" id="message-input" placeholder="Type your message here...">
        <button type="submit">Send</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            var chatUrl = 'chatgpt.php';

            // Submit the message when the form is submitted
            $('#message-form').on('submit', function(event) {
                event.preventDefault();

                // Get the message content from the input field
                var message = $('#message-input').val();

                // Send the chat message to the PHP script
                $.ajax({
                    url: chatUrl,
                    data: {
                        role: 'user',
                        content: message
                    },
                    dataType: 'json',
                    success: function(data) {
                        // Retrieve the bot's response as a JSON object
                        var botResponse = data.choices[0].message;

                        // Display the bot response in the chat window
                        var botMessage = botResponse.content;
                        $('#messages').append('<li><strong>Bot:</strong> ' + botMessage + '</li>');

                        // Clear the message input field
                        $('#message-input').val('');
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors that occur during the AJAX request
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>
</body>
</html>
