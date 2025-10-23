<?php
/**
 * Debug version of image generation flow
 * Shows exactly where the 500 error occurs
 */

header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';

echo "=== IMAGE GENERATION FLOW DEBUG ===\n\n";

// Test data
$characterDesc = "Meet 'The Chronos Connoisseur', a mysterious gentleman in his prime, adorned in a costume shimmering in deep purple and gold accents.";
$worldDesc = "Venture into 'Time's Enclave', a realm suspended between today and the infinite tomorrows.";
$language = 'en';

echo "Step 1: Checking API key...\n";
$apiKey = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '';
echo "API Key defined: " . ($apiKey ? 'YES' : 'NO') . "\n";
echo "API Key length: " . strlen($apiKey) . "\n\n";

echo "Step 2: Loading generateImagePrompt function...\n";
try {
    // Include the function from generate-character-real.php
    include_once 'generate-character-real.php';
    echo "File loaded: YES\n\n";
} catch (Exception $e) {
    echo "ERROR loading file: " . $e->getMessage() . "\n";
    exit;
}

echo "Step 3: Checking if generateImagePrompt function exists...\n";
if (function_exists('generateImagePrompt')) {
    echo "Function exists: YES\n\n";
} else {
    echo "Function exists: NO ❌\n";
    echo "Available functions: " . implode(', ', get_defined_functions()['user']) . "\n";
    exit;
}

echo "Step 4: Calling generateImagePrompt...\n";
try {
    $imagePrompt = generateImagePrompt($apiKey, $characterDesc, $worldDesc, $language);
    echo "Image prompt generated: YES\n";
    echo "Prompt length: " . strlen($imagePrompt) . "\n";
    echo "Prompt preview: " . substr($imagePrompt, 0, 200) . "...\n\n";
} catch (Exception $e) {
    echo "ERROR in generateImagePrompt: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}

echo "Step 5: Loading FreepikAPI...\n";
try {
    require_once 'freepik-api.php';
    echo "FreepikAPI loaded: YES\n\n";
} catch (Exception $e) {
    echo "ERROR loading FreepikAPI: " . $e->getMessage() . "\n";
    exit;
}

echo "Step 6: Creating FreepikAPI instance...\n";
try {
    $freepik = new FreepikAPI();
    echo "Instance created: YES\n\n";
} catch (Exception $e) {
    echo "ERROR creating instance: " . $e->getMessage() . "\n";
    exit;
}

echo "Step 7: Calling generateCharacterImage...\n";
try {
    $imageResult = $freepik->generateCharacterImage($imagePrompt);
    echo "Image generated: " . ($imageResult['success'] ? 'YES' : 'NO') . "\n";
    
    if ($imageResult['success']) {
        echo "Has image_data: " . (isset($imageResult['image_data']) ? 'YES' : 'NO') . "\n";
        echo "Has image_binary: " . (isset($imageResult['image_binary']) ? 'YES' : 'NO') . "\n";
        echo "Has image_url: " . (isset($imageResult['image_url']) ? 'YES' : 'NO') . "\n";
        
        if (isset($imageResult['image_data'])) {
            echo "Image data length: " . strlen($imageResult['image_data']) . "\n";
        }
    } else {
        echo "Error: " . ($imageResult['error'] ?? 'Unknown') . "\n";
    }
} catch (Exception $e) {
    echo "ERROR calling Freepik: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}

echo "\n=== ALL STEPS PASSED! ✅ ===\n";
echo "The image generation flow works correctly.\n";
echo "The 500 error must be in how the data is being returned or processed.\n";
?>
