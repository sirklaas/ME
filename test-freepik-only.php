<?php
/**
 * Test Freepik Image Generation Only
 * Skip OpenAI text generation to test Freepik API
 */

define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';
require_once __DIR__ . '/freepik-api.php';

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║   FREEPIK API TEST - Image Generation Only             ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

echo "Testing Freepik API (your premium account)...\n\n";

$freepik = new FreepikAPI();

// Test 1: Character Image
echo "Test 1: Generating character image...\n";
echo str_repeat("-", 60) . "\n";

$characterPrompt = "Professional character portrait for TV gameshow. " .
                   "A mysterious masked figure embodying a wolf - loyal yet independent. " .
                   "Costume in deep midnight blue with silver accents. " .
                   "Mask featuring constellation patterns with wolf silhouettes and a half moon. " .
                   "Dramatic studio lighting, centered composition, cinematic quality, 4K.";

echo "Prompt: " . substr($characterPrompt, 0, 100) . "...\n\n";

$result1 = $freepik->generateCharacterImage($characterPrompt);

if ($result1['success']) {
    echo "✓ SUCCESS!\n";
    echo "Image URL: " . $result1['image_url'] . "\n\n";
    
    // Download and save locally
    echo "Downloading and saving locally...\n";
    $localUrl = $freepik->downloadAndSaveImage($result1['image_url'], 'test_character.jpg');
    
    if ($localUrl) {
        echo "✓ Saved to: " . $localUrl . "\n";
        echo "Local file: generated-images/test_character.jpg\n\n";
    }
} else {
    echo "✗ FAILED: " . $result1['error'] . "\n\n";
}

// Test 2: Environment Image
echo "\nTest 2: Generating environment image...\n";
echo str_repeat("-", 60) . "\n";

$envPrompt = "Cinematic environment scene inspired by Avatar: The Last Airbender. " .
             "A mystical studio blending fire element energy with spiritual atmosphere. " .
             "Epic landscape with dramatic lighting, northern lights effect, volcanic elements. " .
             "Professional photography, wide angle, atmospheric, 4K quality, TV production standard.";

echo "Prompt: " . substr($envPrompt, 0, 100) . "...\n\n";

$result2 = $freepik->generateEnvironmentImage($envPrompt);

if ($result2['success']) {
    echo "✓ SUCCESS!\n";
    echo "Image URL: " . $result2['image_url'] . "\n\n";
    
    // Download and save locally
    echo "Downloading and saving locally...\n";
    $localUrl = $freepik->downloadAndSaveImage($result2['image_url'], 'test_environment.jpg');
    
    if ($localUrl) {
        echo "✓ Saved to: " . $localUrl . "\n";
        echo "Local file: generated-images/test_environment.jpg\n\n";
    }
} else {
    echo "✗ FAILED: " . $result2['error'] . "\n\n";
}

echo "═══════════════════════════════════════════════════════════\n";
echo "FREEPIK TEST COMPLETE\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if ($result1['success'] || $result2['success']) {
    echo "✓ Freepik API is working!\n";
    echo "✓ Your premium account has credits available\n";
    echo "✓ Images saved to: /Users/mac/GitHubLocal/ME/generated-images/\n\n";
    echo "Check the generated images:\n";
    if ($result1['success']) echo "- test_character.jpg\n";
    if ($result2['success']) echo "- test_environment.jpg\n";
    echo "\nOnce OpenAI billing activates, run: php test-ai-auto.php\n";
} else {
    echo "⚠️  Both tests failed. Check your Freepik API key.\n";
}

echo "\nEstimated cost for this test: ~$0.01 (from Freepik credits)\n";

?>
