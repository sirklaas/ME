<?php
// Test if we can reach Freepik API at all
header('Content-Type: application/json');

define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';

$ch = curl_init(FREEPIK_API_URL);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'prompt' => 'test image',
        'num_images' => 1
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-freepik-api-key: ' . FREEPIK_API_KEY
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10, // Short timeout for test
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo json_encode([
    'success' => !$curlError && $httpCode === 200,
    'http_code' => $httpCode,
    'curl_error' => $curlError,
    'response_length' => strlen($response),
    'response_preview' => substr($response, 0, 200)
]);
