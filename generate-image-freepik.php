<?php
/**
 * Generate image using Freepik API
 * Called after character generation to create the character image
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Load Freepik API
try {
    define('MASKED_EMPLOYEE_APP', true);
    require_once __DIR__ . '/freepik-api.php';
} catch (Exception $e) {
    error_log("Failed to load freepik-api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load API: ' . $e->getMessage()]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$prompt = $data['prompt'] ?? '';
$playerName = $data['playerName'] ?? 'Unknown';

// Validate prompt
if (empty($prompt)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No prompt provided']);
    exit;
}

// DIRECT TEST: Call Freepik API directly without the class
$ch = curl_init(FREEPIK_API_URL);

// Prepend aspect ratio instruction to prompt
$finalPrompt = "ASPECT RATIO: 16:9 WIDESCREEN HORIZONTAL (1920x1080 or 1216x832) - MANDATORY. " . $prompt;
$finalPrompt = substr($finalPrompt, 0, 1000); // Truncate to 1000 chars

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'prompt' => $finalPrompt,
        'num_images' => 1,
        'aspect_ratio' => '16:9' // Try aspect_ratio parameter
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-freepik-api-key: ' . FREEPIK_API_KEY
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo json_encode(['success' => false, 'error' => 'cURL error: ' . $curlError]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode(['success' => false, 'error' => 'HTTP ' . $httpCode, 'response' => substr($response, 0, 200)]);
    exit;
}

$result = json_decode($response, true);

if (isset($result['data'][0]['base64'])) {
    echo json_encode([
        'success' => true,
        'image_data' => $result['data'][0]['base64'],
        'message' => 'Image generated successfully'
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'No image data in response']);
exit;

// OLD CODE BELOW (not reached)
try {
    // Initialize Freepik API
    $freepik = new FreepikAPI();
    
    // Generate image
    $result = $freepik->generateCharacterImage($prompt);
    
    if (!$result['success']) {
        throw new Exception($result['error'] ?? 'Image generation failed');
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'image_data' => $result['image_data'] ?? null,
        'image_binary' => null, // Don't send binary, too large
        'image_url' => $result['image_url'] ?? null,
        'message' => 'Image generated successfully'
    ];
    
    // Return result
    echo json_encode($response);
    
    // Force flush output
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
    flush();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
