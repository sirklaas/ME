<?php
define('MASKED_EMPLOYEE_APP', true);
require_once 'api-keys.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo json_encode([
    'step' => 'Starting test',
    'time' => date('Y-m-d H:i:s')
]) . "\n";

// Test OpenAI call for image prompt
echo json_encode([
    'step' => 'Testing OpenAI image prompt generation',
    'api_key_length' => strlen(OPENAI_API_KEY)
]) . "\n";

$prompt = "Generate a detailed image prompt for a character portrait";

$ch = curl_init(OPENAI_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => OPENAI_MODEL,
    'messages' => [
        ['role' => 'user', 'content' => $prompt]
    ],
    'temperature' => 0.7,
    'max_tokens' => 200
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo json_encode([
    'step' => 'OpenAI response',
    'http_code' => $httpCode,
    'error' => $error ?: null,
    'response_length' => strlen($response),
    'response_preview' => substr($response, 0, 200)
]) . "\n";

if ($httpCode != 200) {
    echo json_encode([
        'error' => 'OpenAI API failed',
        'full_response' => $response
    ]) . "\n";
    exit;
}

echo json_encode(['step' => 'OpenAI test PASSED ✅']) . "\n";

// Test Freepik call
echo json_encode([
    'step' => 'Testing Freepik image generation',
    'api_key_length' => strlen(FREEPIK_API_KEY)
]) . "\n";

$imagePrompt = "Professional portrait of a mysterious character";

$ch = curl_init(FREEPIK_ENDPOINT);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-freepik-api-key: ' . FREEPIK_API_KEY
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'prompt' => $imagePrompt,
    'num_images' => 1,
    'image' => ['size' => '1024x1024']
]));
curl_setopt($ch, CURLOPT_TIMEOUT, 90); // Long timeout for image gen

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo json_encode([
    'step' => 'Freepik response',
    'http_code' => $httpCode,
    'error' => $error ?: null,
    'response_length' => strlen($response),
    'response_preview' => substr($response, 0, 200)
]) . "\n";

if ($httpCode != 200) {
    echo json_encode([
        'error' => 'Freepik API failed',
        'full_response' => $response
    ]) . "\n";
    exit;
}

echo json_encode(['step' => 'Freepik test PASSED ✅']) . "\n";
echo json_encode(['result' => 'ALL TESTS PASSED! ✅✅✅']) . "\n";
?>
