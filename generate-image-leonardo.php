<?php
/**
 * Generate image using Leonardo.ai API
 * Called after character generation to create the character image
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Load Leonardo API
define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/leonardo-api.php';

// Leonardo API Key
define('LEONARDO_API_KEY', '3f57f74f-48e5-4c29-a525-cf279eee861a');

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

try {
    // Initialize Leonardo API
    $leonardo = new LeonardoAPI(LEONARDO_API_KEY);
    
    // Generate image with optimized settings for 16:9
    $result = $leonardo->generateImage($prompt, [
        'width' => 1472,  // 16:9 ratio
        'height' => 832,
        'guidance_scale' => 7,
        'num_inference_steps' => 30,
        'presetStyle' => 'CINEMATIC'
    ]);
    
    if (!$result['success']) {
        throw new Exception($result['error'] ?? 'Image generation failed');
    }
    
    // Return result
    echo json_encode([
        'success' => true,
        'image_data' => $result['image_data'] ?? null,
        'image_url' => $result['image_url'] ?? null,
        'message' => 'Image generated successfully with Leonardo.ai'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
