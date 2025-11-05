<?php
/**
 * Download Character Image
 * Proxy script to download images from PocketBase with proper filename
 */

// Get image URL from query parameter
$imageUrl = $_GET['url'] ?? '';
$characterName = $_GET['name'] ?? 'Character';

if (empty($imageUrl)) {
    http_response_code(400);
    die('No image URL provided');
}

// Validate URL is from PocketBase
if (strpos($imageUrl, 'pockethost.io') === false && strpos($imageUrl, 'pocketbase') === false) {
    http_response_code(403);
    die('Invalid image source');
}

// Fetch the image
$imageData = @file_get_contents($imageUrl);

if ($imageData === false) {
    http_response_code(404);
    die('Image not found');
}

// Get file extension from URL
$extension = 'jpg';
if (preg_match('/\.(jpg|jpeg|png|gif|webp)(\?|$)/i', $imageUrl, $matches)) {
    $extension = strtolower($matches[1]);
}

// Create safe filename
$safeCharacterName = preg_replace('/[^a-zA-Z0-9-_]/', '', str_replace(' ', '-', $characterName));
$filename = $safeCharacterName . '-MaskedEmployee.' . $extension;

// Send headers for download
header('Content-Type: image/' . $extension);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($imageData));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Output image data
echo $imageData;
exit;
?>
