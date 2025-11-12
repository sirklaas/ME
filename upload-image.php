<?php
/**
 * Image Upload Script for Voting App
 * Saves uploaded images to vote_images folder
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Create vote_images directory if it doesn't exist
$uploadDir = __DIR__ . '/vote_images/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    // Get the base64 image data from POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['image']) || !isset($input['filename'])) {
        throw new Exception('Missing image data or filename');
    }
    
    $imageData = $input['image'];
    $filename = $input['filename'];
    
    // Remove data:image/...;base64, prefix if present
    if (strpos($imageData, 'data:image') === 0) {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
    }
    
    // Decode base64
    $decodedImage = base64_decode($imageData);
    
    if ($decodedImage === false) {
        throw new Exception('Failed to decode image');
    }
    
    // Generate unique filename
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = 'jpg'; // Default extension
    }
    
    $uniqueFilename = uniqid('vote_img_') . '.' . $extension;
    $filepath = $uploadDir . $uniqueFilename;
    
    // Save the file
    if (file_put_contents($filepath, $decodedImage) === false) {
        throw new Exception('Failed to save image');
    }
    
    // Return the URL
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
              . "://" . $_SERVER['HTTP_HOST'];
    $imageUrl = $baseUrl . '/vote_images/' . $uniqueFilename;
    
    echo json_encode([
        'success' => true,
        'url' => $imageUrl,
        'filename' => $uniqueFilename
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
