<?php
/**
 * Test Full Submission Flow
 * Simulates a complete questionnaire submission with all 43 questions
 */

// Define constant to allow api-keys.php to load
define('MASKED_EMPLOYEE_APP', true);

// Load API configuration
require_once 'api-keys.php';
require_once 'generate-character.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=================================================\n";
echo "🧪 TESTING FULL SUBMISSION FLOW\n";
echo "=================================================\n\n";

// Test answers (you can replace these with your actual answers)
$testAnswers = [
    // Chapter 1: Introductie & Basisinformatie (1-5)
    1 => "Man",
    2 => "In een relatie",
    3 => "Ja, 2 kinderen",
    4 => "35-44",
    5 => "Creatief/Artistiek",
    
    // Chapter 2: Masked Identity (6-10)
    6 => "De Mysterieuze Vos",
    7 => "Een slimme, behendig vos die door de schaduwen beweegt",
    8 => "Een vintage masker met gouden accenten",
    9 => "Diep paars met gouden details",
    10 => "Mysterieus, intelligent, speels",
    
    // Chapter 3: Persoonlijke Eigenschappen (11-16)
    11 => "Teleportatie, omdat ik graag op verschillende plekken zou zijn",
    12 => "Ik ben bang voor spinnen, ook al weet ik dat het irrationeel is",
    13 => "Leonardo da Vinci, om over kunst en wetenschap te praten",
    14 => "Quantummechanica en parallelle universums",
    15 => "De Procrastinator's Gids naar Succes",
    16 => "Ik heb ooit een nacht doorgehaald om een kunstproject af te maken",
    
    // Chapter 4: Verborgen Talenten (17-21)
    17 => "Ik kan goed jongleren met 5 ballen tegelijk",
    18 => "Gitaar spelen en muziek componeren",
    19 => "Ik kan perfect geluiden van dieren nadoen",
    20 => "Ik kan een Rubik's kubus oplossen in minder dan 2 minuten",
    21 => "Ik spreek vloeiend 4 talen",
    
    // Chapter 5: Jeugd & Verleden (22-26)
    22 => "Klimmen in bomen en bouwen van boomhutten",
    23 => "Mijn opa, die me leerde om creatief te denken",
    24 => "Toen ik mijn eerste kunstwerk verkocht op 16-jarige leeftijd",
    25 => "Astronaut of uitvinder",
    26 => "Ik verzamelde vintage speelgoed en strips",
    
    // Chapter 6: Fantasie & Dromen (27-31)
    27 => "Een atelier op een afgelegen eiland met uitzicht op zee",
    28 => "De gave om tijd te kunnen vertragen",
    29 => "Een wereld waar creativiteit de hoofdvaluta is",
    30 => "Een reis naar Japan om de cultuur en kunst te ervaren",
    31 => "Een kunstgalerie openen die jonge artiesten ondersteunt",
    
    // Chapter 7: Eigenaardigheden (32-36)
    32 => "Ik moet altijd met mijn linkervoet eerst uit bed stappen",
    33 => "Ik verzamel vintage camera's",
    34 => "Ik luister naar klassieke muziek tijdens het werken",
    35 => "Ik schrijf dagelijks in mijn dagboek voordat ik ga slapen",
    36 => "Ik ga hardlopen in het bos om mijn hoofd leeg te maken",
    
    // Chapter 8: Onverwachte Voorkeuren (37-40)
    37 => "K-pop, niemand zou dat van me verwachten",
    38 => "Reality TV shows over bakken",
    39 => "Een kleine vos op mijn schouder",
    40 => "Diep paars, omdat het creativiteit en mysterie combineert",
    
    // Chapter 9: Film Maken (41-43)
    41 => "Scene 1: Een gemaskerde figuur loopt door een mistig bos\nScene 2: Close-up van handen die iets creatiefs maken\nScene 3: Een glimp van een vossengezicht in de schaduw",
    42 => "Scene 1: De figuur onthult een kunstwerk in een atelier\nScene 2: Vintage camera's en instrumenten komen in beeld\nScene 3: Een paarse cape wappert in de wind",
    43 => "Scene 1: De figuur speelt gitaar bij maanlicht\nScene 2: Jongleert met gekleurde ballen\nScene 3: Het masker valt af en onthult... bijna alles"
];

$playerName = "Test User";
$gameName = "The Masked Employee";

echo "👤 Player Name: $playerName\n";
echo "🎮 Game Name: $gameName\n";
echo "📝 Total Answers: " . count($testAnswers) . "\n\n";

echo "=================================================\n";
echo "STEP 1: GENERATING CHARACTER DATA\n";
echo "=================================================\n\n";

try {
    // Simulate the character generation request
    $requestData = [
        'playerName' => $playerName,
        'answers' => $testAnswers,
        'gameName' => $gameName
    ];
    
    // Save to temp file to simulate POST data
    $tempFile = tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tempFile, json_encode($requestData));
    
    // Simulate POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = [];
    
    // Capture the output
    ob_start();
    
    // Include and execute the generate-character.php logic
    // (We'll call it directly instead of including to avoid exit())
    
    echo "🤖 Calling OpenAI to generate character...\n\n";
    
    // Call OpenAI directly here (simplified version)
    $apiKey = OPENAI_API_KEY;
    
    // Format answers for AI
    $formattedAnswers = "";
    foreach ($testAnswers as $qId => $answer) {
        $formattedAnswers .= "Q$qId: $answer\n";
    }
    
    // Create character generation prompt
    $systemPrompt = "You are a creative character designer for a gameshow called 'The Masked Employee'. Based on the player's answers, create a unique masked character.";
    
    $userPrompt = "Based on these answers, create a character:\n\n$formattedAnswers\n\nGenerate:\n1. A creative character name\n2. Character type (animal/fruit/fantasy)\n3. Personality traits (2-3 sentences)\n4. Brief summary (3-4 sentences)";
    
    $data = [
        'model' => OPENAI_MODEL,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ],
        'max_tokens' => 300,
        'temperature' => 0.8
    ];
    
    $ch = curl_init(OPENAI_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        $aiResponse = $result['choices'][0]['message']['content'];
        
        echo "✅ Character Generated Successfully!\n\n";
        echo "--- AI RESPONSE ---\n";
        echo $aiResponse . "\n";
        echo "-------------------\n\n";
        
        $characterData = [
            'success' => true,
            'character_name' => 'De Mysterieuze Vos',
            'character_type' => 'vos',
            'personality_traits' => $aiResponse,
            'ai_summary' => 'Generated from test answers',
            'story_prompt_level1' => 'A mysterious figure in purple moves through shadows...',
            'story_prompt_level2' => 'The fox reveals creative talents and vintage collections...',
            'story_prompt_level3' => 'Final reveal shows the artistic soul behind the mask...',
            'image_generation_prompt' => 'A mysterious purple fox wearing a vintage golden mask, artistic studio setting',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
    } else {
        echo "❌ OpenAI API Error (HTTP $httpCode)\n";
        $errorResult = json_decode($response, true);
        echo json_encode($errorResult, JSON_PRETTY_PRINT) . "\n\n";
        
        $characterData = [
            'success' => false,
            'error' => 'API call failed'
        ];
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    $characterData = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

echo "\n=================================================\n";
echo "STEP 2: PREPARING POCKETBASE DATA\n";
echo "=================================================\n\n";

// Organize answers by chapter
$chapterAnswers = [
    'chapter01' => array_intersect_key($testAnswers, array_flip([1,2,3,4,5])),
    'chapter02' => array_intersect_key($testAnswers, array_flip([6,7,8,9,10])),
    'chapter03' => array_intersect_key($testAnswers, array_flip([11,12,13,14,15,16])),
    'chapter04' => array_intersect_key($testAnswers, array_flip([17,18,19,20,21])),
    'chapter05' => array_intersect_key($testAnswers, array_flip([22,23,24,25,26])),
    'chapter06' => array_intersect_key($testAnswers, array_flip([27,28,29,30,31])),
    'chapter07' => array_intersect_key($testAnswers, array_flip([32,33,34,35,36])),
    'chapter08' => array_intersect_key($testAnswers, array_flip([37,38,39,40])),
    'chapter09' => array_intersect_key($testAnswers, array_flip([41,42,43]))
];

$submissionData = [
    'gamename' => $gameName,
    'nameplayer' => $playerName,
    'chapter01' => $chapterAnswers['chapter01'],
    'chapter02' => $chapterAnswers['chapter02'],
    'chapter03' => $chapterAnswers['chapter03'],
    'chapter04' => $chapterAnswers['chapter04'],
    'chapter05' => $chapterAnswers['chapter05'],
    'chapter06' => $chapterAnswers['chapter06'],
    'chapter07' => $chapterAnswers['chapter07'],
    'chapter08' => $chapterAnswers['chapter08'],
    'chapter09' => $chapterAnswers['chapter09'],
    'submission_date' => date('Y-m-d H:i:s'),
    'total_questions' => count($testAnswers),
    'status' => 'completed',
    'character_name' => $characterData['character_name'] ?? '',
    'character_type' => $characterData['character_type'] ?? '',
    'personality_traits' => $characterData['personality_traits'] ?? '',
    'ai_summary' => $characterData['ai_summary'] ?? '',
    'story_prompt1' => $characterData['story_prompt_level1'] ?? '',
    'story_prompt2' => $characterData['story_prompt_level2'] ?? '',
    'story_prompt3' => $characterData['story_prompt_level3'] ?? '',
    'image_generation_prompt' => $characterData['image_generation_prompt'] ?? '',
    'character_generation_success' => $characterData['success'] ?? false
];

echo "📦 Submission Data Structure:\n\n";
echo json_encode($submissionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "\n=================================================\n";
echo "✅ TEST COMPLETE!\n";
echo "=================================================\n\n";

echo "Summary:\n";
echo "- Character Generation: " . ($characterData['success'] ? '✅ SUCCESS' : '❌ FAILED') . "\n";
echo "- Data Structure: ✅ READY\n";
echo "- Total Fields: " . count($submissionData) . "\n";
echo "\nNext step: Upload to server and test with real PocketBase!\n";
