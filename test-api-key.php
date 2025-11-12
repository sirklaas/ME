<?php
/**
 * Test OpenAI API Key
 * Quick test to verify the API key is working
 */

// Define constant to allow api-keys.php to load
define('MASKED_EMPLOYEE_APP', true);

// Load API configuration
require_once 'api-keys.php';

header('Content-Type: application/json');

echo "Testing OpenAI API Key...\n\n";

// Test API call
$apiKey = OPENAI_API_KEY;

if (empty($apiKey) || $apiKey === 'sk-YOUR_OPENAI_KEY_HERE') {
    echo json_encode([
        'success' => false,
        'error' => 'API key not configured. Please update api-keys.php'
    ], JSON_PRETTY_PRINT);
    exit;
}

echo "API Key found: " . substr($apiKey, 0, 10) . "..." . substr($apiKey, -4) . "\n\n";

// Make a simple test call to OpenAI
$data = [
    'model' => OPENAI_MODEL,
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant.'
        ],
        [
            'role' => 'user',
            'content' => 'Say "API key is working!" if you can read this.'
        ]
    ],
    'max_tokens' => 20,
    'temperature' => 0.7
];

$ch = curl_init(OPENAI_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

echo "Making test API call to OpenAI...\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo json_encode([
        'success' => false,
        'error' => 'CURL Error: ' . $curlError
    ], JSON_PRETTY_PRINT);
    exit;
}

echo "HTTP Response Code: $httpCode\n\n";

$result = json_decode($response, true);

if ($httpCode === 200 && isset($result['choices'][0]['message']['content'])) {
    echo json_encode([
        'success' => true,
        'message' => '✅ API Key is working!',
        'response' => $result['choices'][0]['message']['content'],
        'model' => $result['model'],
        'usage' => $result['usage']
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'http_code' => $httpCode,
        'error' => $result['error'] ?? 'Unknown error',
        'full_response' => $result
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
