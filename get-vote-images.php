<?php
/**
 * Get Vote Images - Returns the 4 voting images
 * Looks for files starting with 1_, 2_, 3_, 4_ in vote_images folder
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $imageDir = __DIR__ . '/vote_images/';
    
    if (!file_exists($imageDir)) {
        throw new Exception('vote_images directory not found');
    }
    
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
              . "://" . $_SERVER['HTTP_HOST'];
    
    $images = [];
    
    // Look for images starting with 1_, 2_, 3_, 4_
    for ($i = 1; $i <= 4; $i++) {
        $pattern = $imageDir . $i . '_*';
        $files = glob($pattern);
        
        if (!empty($files)) {
            // Get the first matching file
            $filename = basename($files[0]);
            $images[] = [
                'id' => $i,
                'url' => $baseUrl . '/ME/vote_images/' . $filename,
                'title' => 'Option ' . $i // Default title
            ];
        } else {
            // No file found, use placeholder
            $images[] = [
                'id' => $i,
                'url' => 'https://picsum.photos/seed/' . $i . '/800/600',
                'title' => 'Option ' . $i
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'images' => $images
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
