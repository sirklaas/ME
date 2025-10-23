<?php
/**
 * Test API Connections
 * Run this to verify OpenAI and Freepik APIs are working
 */

define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';

echo "=== API CONNECTION TEST ===\n\n";

// Test 1: Check if keys are loaded
echo "1. Checking API Keys...\n";
echo "   OpenAI Key: " . (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY) ? '✅ SET' : '❌ NOT SET') . "\n";
echo "   Freepik Key: " . (defined('FREEPIK_API_KEY') && !empty(FREEPIK_API_KEY) ? '✅ SET' : '❌ NOT SET') . "\n";

if (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY)) {
    echo "   OpenAI Key starts with: " . substr(OPENAI_API_KEY, 0, 7) . "...\n";
}

echo "\n";

// Test 2: Test OpenAI API
echo "2. Testing OpenAI API...\n";
if (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY)) {
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => defined('OPENAI_MODEL') ? OPENAI_MODEL : 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => 'Reply with exactly: "API works!"']
            ],
            'max_tokens' => 10
        ])
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    echo "   HTTP Code: $httpCode\n";
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        $message = $result['choices'][0]['message']['content'] ?? 'No response';
        echo "   Response: $message\n";
        echo "   ✅ OpenAI API is WORKING!\n";
    } else if ($curlError) {
        echo "   ❌ cURL Error: $curlError\n";
    } else {
        echo "   ❌ HTTP Error: $httpCode\n";
        $error = json_decode($response, true);
        echo "   Error: " . ($error['error']['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "   ⚠️ OpenAI key not configured, skipping test\n";
}

echo "\n";

// Test 3: Check Freepik API key format
echo "3. Checking Freepik Configuration...\n";
if (defined('FREEPIK_API_KEY') && !empty(FREEPIK_API_KEY) && FREEPIK_API_KEY !== 'YOUR_FREEPIK_KEY_HERE') {
    echo "   ✅ Freepik API key is configured\n";
    echo "   Model: " . (defined('FREEPIK_AI_MODEL') ? FREEPIK_AI_MODEL : 'Not set') . "\n";
    echo "   Image Size: " . (defined('FREEPIK_IMAGE_SIZE') ? FREEPIK_IMAGE_SIZE : 'Not set') . "\n";
    echo "   Style: " . (defined('FREEPIK_STYLE') ? FREEPIK_STYLE : 'Not set') . "\n";
} else {
    echo "   ⚠️ Freepik key not configured\n";
}

echo "\n";

// Test 4: Check image storage
echo "4. Checking Image Storage...\n";
$imagePath = defined('IMAGE_STORAGE_PATH') ? IMAGE_STORAGE_PATH : './generated-images/';
echo "   Path: $imagePath\n";

if (!file_exists($imagePath)) {
    echo "   ⚠️ Directory does NOT exist - will be created when needed\n";
} else {
    echo "   ✅ Directory exists\n";
    echo "   Writable: " . (is_writable($imagePath) ? '✅ YES' : '❌ NO - chmod 755 needed') . "\n";
}

echo "\n";

// Summary
echo "=== SUMMARY ===\n";
$openaiOk = defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY);
$freepikOk = defined('FREEPIK_API_KEY') && !empty(FREEPIK_API_KEY) && FREEPIK_API_KEY !== 'YOUR_FREEPIK_KEY_HERE';

if ($openaiOk && $freepikOk) {
    echo "✅ ALL APIs CONFIGURED - Ready to test!\n";
    echo "\nNext Steps:\n";
    echo "1. Upload all files to production server\n";
    echo "2. Go to questions.html\n";
    echo "3. Click 'TEST MODE' button\n";
    echo "4. Wait for REAL AI character generation\n";
    echo "5. Test regenerate button\n";
    echo "6. Accept character and test image generation\n";
} else {
    echo "⚠️ Some APIs not configured:\n";
    if (!$openaiOk) echo "   - OpenAI API key needed\n";
    if (!$freepikOk) echo "   - Freepik API key needed\n";
}

echo "\n";
?>
