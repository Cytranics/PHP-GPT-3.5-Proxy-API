<?php
// To use Call the URL with parameters /chatgpt.php?role=user&content=Hello
// Edit the below Initial Prompt to create a message.json file which is used to store the conversation history
// Set the max number of conversations to store in the message.json file. 20 is a good number.
// Both user messages and bot responses are stored in the message.json file
// The bot will remember prior context.



$endpointUrl        = 'https://api.openai.com/v1/chat/completions';
$apiKey             = 'YOURAPI';
$initialMessage     = "Hello, I'm your AI assistant. How can I help you today?";
$jsonFile           = 'messages.json';
$maxConversations   = 30;

$jsonArray = [];

// Check if the JSON file exists. If not create a default JSON object
if (file_exists($jsonFile)) {
    // Load the existing JSON data from the file
    $jsonData = file_get_contents($jsonFile);
    $jsonArray = json_decode($jsonData, true);
} else {
    // Create a new JSON object with the 'messages' node
    $jsonArray = [
        "model" => "gpt-3.5-turbo",
        "temperature" => 1.3,
        "frequency_penalty" => 1.3,
        "messages" => [
            [
                "role" => "system",
                "content" => $initialMessage
            ]
        ]
    ];
}

// Add the new message to the 'messages' array
$newMessage = [
    "role" => $_GET['role'],
    "content" => $_GET['content']
];
array_push($jsonArray['messages'], $newMessage);

// Limit the 'messages' array to the last $maxConversations nodes with 'role' set to 'user' or 'assistant'
$userAssistantMessages = array_filter($jsonArray['messages'], function($message) {
    return ($message['role'] === 'user' || $message['role'] === 'assistant');
});
$systemMessages = array_filter($jsonArray['messages'], function($message) {
    return $message['role'] === 'system';
});
$userAssistantMessages = array_slice($userAssistantMessages, -$maxConversations);

// Merge the filtered 'messages' arrays and update the 'messages' node
$jsonArray['messages'] = array_merge($systemMessages, $userAssistantMessages);

// Encode the updated JSON data and save it back to the file
$jsonUpdated = json_encode($jsonArray);
file_put_contents($jsonFile, $jsonUpdated);

// Send the JSON data to the OpenAI API
$ch = curl_init($endpointUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $jsonUpdated,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]
]);
$responseData = curl_exec($ch);

// Add the bot response data to the 'messages' array
$jsonArray = json_decode($jsonUpdated, true);
$responseArray = json_decode($responseData, true);
$choicesNode = $responseArray['choices'][0]['message'];
array_push($jsonArray['messages'], $choicesNode);

// Encode the updated JSON data and save it back to the file
$jsonUpdated = json_encode($jsonArray);
file_put_contents($jsonFile, $jsonUpdated);

// Return the response data to the client
header("Content-Type: application/json");
echo $responseData;

?>
