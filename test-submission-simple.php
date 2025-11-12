<?php
/**
 * Simple Test - Character Generation Only
 */

define('MASKED_EMPLOYEE_APP', true);
require_once 'api-keys.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=================================================\n";
echo "🧪 TESTING CHARACTER GENERATION\n";
echo "=================================================\n\n";

// Your test answers - REPLACE THESE WITH YOUR ACTUAL ANSWERS
$testAnswers = [
    1 => "Your answer to question 1",
    2 => "Your answer to question 2",
    // ... add all 43 answers here
];

$playerName = "Test User";

echo "Testing with " . count($testAnswers) . " answers...\n\n";

// Format answers for AI
$formattedAnswers = "";
foreach ($testAnswers as $qId => $answer) {
    $formattedAnswers .= "Q$qId: $answer\n";
}

echo "📤 Sending request to OpenAI...\n\n";

// Create prompt
$systemPrompt = "You are a creative character designer for 'The Masked Employee' gameshow. Create unique masked characters based on player answers.";

$userPrompt = "Player: $playerName\n\nAnswers:\n$formattedAnswers\n\nCreate:\n1. Character name (creative, mysterious)\n2. Character type (animal/fruit/fantasy)\n3. Personality (2-3 sentences)\n4. Summary (3-4 sentences)";

$data = [
    'model' => OPENAI_MODEL,
    'messages' => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => $userPrompt]
    ],
    'max_tokens' => 400,
    'temperature' => 0.8
];

$ch = curl_init(OPENAI_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "❌ CURL Error: $curlError\n";
    exit(1);
}

echo "HTTP Response Code: $httpCode\n\n";

if ($httpCode === 200) {
    $result = json_decode($response, true);
    $aiResponse = $result['choices'][0]['message']['content'];
    
    echo "✅ SUCCESS! Character Generated:\n\n";
    echo "=================================================\n";
    echo $aiResponse . "\n";
    echo "=================================================\n\n";
    
    echo "Token Usage:\n";
    echo "- Prompt: " . $result['usage']['prompt_tokens'] . "\n";
    echo "- Completion: " . $result['usage']['completion_tokens'] . "\n";
    echo "- Total: " . $result['usage']['total_tokens'] . "\n\n";
    
    echo "✅ Character generation is working!\n";
    echo "✅ Ready to save to PocketBase!\n";
    
} else {
    echo "❌ OpenAI API Error\n\n";
    $errorResult = json_decode($response, true);
    echo json_encode($errorResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
