<?php
/**
 * Simulate the ACTUAL image generation request
 * This mimics what the JavaScript sends
 */

header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== SIMULATING ACTUAL IMAGE GENERATION REQUEST ===\n\n";

// Prepare test data (same as what JavaScript sends)
$testData = [
    'step' => 'generate_image',
    'playerName' => 'Test Player',
    'language' => 'en',
    'character_description' => "Meet 'The Chronos Connoisseur', a mysterious gentleman in his prime, adorned in a costume shimmering in deep purple and gold accents. His countenance radiates confidence.",
    'world_description' => "Venture into 'Time's Enclave', a realm suspended between today and the infinite tomorrows. Flush with deep purple and gold hues."
];

echo "Step 1: Preparing POST request...\n";
echo "Data prepared: YES\n";
echo "Character desc length: " . strlen($testData['character_description']) . "\n";
echo "World desc length: " . strlen($testData['world_description']) . "\n\n";

echo "Step 2: Sending POST request to generate-character-real.php...\n";

// Use cURL to make actual POST request (with www to avoid redirect)
$ch = curl_init('https://www.pinkmilk.eu/ME/generate-character-real.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_TIMEOUT, 90); // Long timeout for image generation

echo "Calling API...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "cURL Error: " . ($curlError ?: 'None') . "\n";
echo "Response length: " . strlen($response) . "\n\n";

echo "Step 3: Analyzing response...\n";

if ($httpCode === 500) {
    echo "❌ 500 ERROR REPRODUCED!\n\n";
    echo "Response body:\n";
    echo "----------------------------------------\n";
    echo $response;
    echo "\n----------------------------------------\n\n";
    
    // Try to decode as JSON
    $json = json_decode($response, true);
    if ($json) {
        echo "Parsed JSON:\n";
        echo "Success: " . ($json['success'] ?? 'N/A') . "\n";
        echo "Error: " . ($json['error'] ?? 'N/A') . "\n";
        echo "Message: " . ($json['message'] ?? 'N/A') . "\n";
        if (isset($json['details'])) {
            echo "Details:\n" . $json['details'] . "\n";
        }
    } else {
        echo "Response is not valid JSON\n";
        echo "Raw response:\n" . $response . "\n";
    }
    
} elseif ($httpCode === 200) {
    echo "✅ 200 SUCCESS!\n\n";
    
    $json = json_decode($response, true);
    if ($json && $json['success']) {
        echo "Image generation succeeded!\n";
        echo "Has image_data: " . (isset($json['image_data']) ? 'YES' : 'NO') . "\n";
        echo "Has image_binary: " . (isset($json['image_binary']) ? 'YES' : 'NO') . "\n";
        echo "Has image_url: " . (isset($json['image_url']) ? 'YES' : 'NO') . "\n";
        echo "Has image_prompt: " . (isset($json['image_prompt']) ? 'YES' : 'NO') . "\n";
        
        if (isset($json['image_data'])) {
            echo "Image data length: " . strlen($json['image_data']) . " bytes\n";
        }
        if (isset($json['image_prompt'])) {
            echo "Image prompt: " . substr($json['image_prompt'], 0, 100) . "...\n";
        }
    } else {
        echo "Response structure:\n";
        print_r($json);
    }
    
} else {
    echo "❌ Unexpected HTTP code: " . $httpCode . "\n";
    echo "Response:\n" . $response . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
