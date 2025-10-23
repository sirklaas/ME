<?php
/**
 * AI Character Generation Script
 * Generates character using simplified prompts (4 API calls instead of 6)
 * 
 * Usage: POST to this endpoint with player data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get OpenAI API key from environment
$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured']);
    exit;
}

// Get request data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['answers']) || !isset($data['playerName'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit;
}

/**
 * Calculate answer weight based on length and creativity
 */
function calculateAnswerWeight($answer) {
    if (is_numeric($answer)) return 1; // Multiple choice
    
    $wordCount = str_word_count($answer);
    $weight = 1;
    
    // Length-based weight
    if ($wordCount > 50) $weight = 3;
    elseif ($wordCount > 20) $weight = 2;
    
    // Creativity keywords bonus
    $creativityKeywords = ['unique', 'unusual', 'creative', 'different', 'special', 'weird', 'quirky', 'strange', 'bizarre', 'extraordinary'];
    foreach ($creativityKeywords as $keyword) {
        if (stripos($answer, $keyword) !== false) {
            $weight *= 1.5;
            break;
        }
    }
    
    return $weight;
}

/**
 * Analyze personality traits from answers
 */
function analyzePersonality($answers) {
    $traits = [
        'adventurous' => 0,
        'practical' => 0,
        'playful' => 0,
        'wise' => 0,
        'creative' => 0
    ];
    
    $keywords = [
        'adventurous' => ['adventure', 'explore', 'travel', 'risk', 'new', 'exciting', 'bold'],
        'practical' => ['organize', 'fix', 'solve', 'plan', 'efficient', 'practical', 'logical'],
        'playful' => ['fun', 'play', 'joke', 'laugh', 'silly', 'humor', 'funny'],
        'wise' => ['learn', 'teach', 'wisdom', 'knowledge', 'understand', 'mentor', 'guide'],
        'creative' => ['create', 'imagine', 'design', 'art', 'invent', 'innovative', 'original']
    ];
    
    foreach ($answers as $answer) {
        if (!is_string($answer)) continue;
        
        $answerLower = strtolower($answer);
        $weight = calculateAnswerWeight($answer);
        
        foreach ($keywords as $trait => $words) {
            foreach ($words as $word) {
                if (stripos($answerLower, $word) !== false) {
                    $traits[$trait] += $weight;
                }
            }
        }
    }
    
    return $traits;
}

/**
 * Determine character type based on personality analysis
 */
function determineCharacterType($traits) {
    arsort($traits);
    $topTrait = key($traits);
    
    $mapping = [
        'creative' => ['pixar_disney', 'fantasy_heroes'],
        'practical' => ['animals', 'fruits_vegetables'],
        'adventurous' => ['fantasy_heroes', 'fairy_tales'],
        'playful' => ['pixar_disney', 'fruits_vegetables'],
        'wise' => ['animals', 'fairy_tales']
    ];
    
    $types = $mapping[$topTrait] ?? ['animals'];
    return $types[array_rand($types)];
}

/**
 * Format answers into readable text for AI
 */
function formatAnswersForAI($answers, $chapters) {
    $formatted = "PLAYER QUESTIONNAIRE ANSWERS:\n\n";
    $totalWeight = 0;
    
    // Load chapter files to get questions
    $chapterFiles = [
        'chapter1-introductie.json',
        'chapter2-masked-identity.json',
        'chapter3-persoonlijke-eigenschappen.json',
        'chapter4-verborgen-talenten.json',
        'chapter5-jeugd-verleden.json',
        'chapter6-fantasie-dromen.json',
        'chapter7-eigenaardigheden.json',
        'chapter8-onverwachte-voorkeuren.json'
    ];
    
    foreach ($chapterFiles as $index => $file) {
        $chapterData = json_decode(file_get_contents($file), true);
        if (!$chapterData) continue;
        
        $formatted .= "=== " . $chapterData['title'] . " ===\n";
        
        foreach ($chapterData['questions'] as $question) {
            $questionId = $question['id'];
            if (!isset($answers[$questionId])) continue;
            
            $answer = $answers[$questionId];
            $weight = calculateAnswerWeight($answer);
            $totalWeight += $weight;
            
            $formatted .= "Q: " . $question['question'] . "\n";
            
            if ($question['type'] === 'multiple-choice' && isset($question['options'][$answer])) {
                $formatted .= "A: " . $question['options'][$answer] . " [weight: $weight]\n";
            } else {
                $formatted .= "A: " . $answer . " [weight: $weight]\n";
            }
            $formatted .= "\n";
        }
        $formatted .= "\n";
    }
    
    $formatted .= "\n=== ANALYSIS ===\n";
    $formatted .= "Total Answer Weight: $totalWeight\n";
    
    return $formatted;
}

/**
 * Generate realistic image prompt for Freepik/Flux
 */
function generateImagePrompt($characterName, $aiSummary, $characterType) {
    // Extract key details from summary
    $prompt = "STUDIO QUALITY PHOTO, 16:9 aspect ratio, hyper-realistic: ";
    
    // Add character type context
    $typeDescriptions = [
        'animals' => 'anthropomorphic animal character',
        'fruits_vegetables' => 'anthropomorphic fruit/vegetable character',
        'fantasy_heroes' => 'fantasy hero character',
        'pixar_disney' => 'Pixar-style 3D character',
        'fairy_tales' => 'fairy tale character'
    ];
    
    $prompt .= $typeDescriptions[$characterType] ?? 'character';
    $prompt .= " named '$characterName'. ";
    
    // Extract clothing and appearance from summary
    if (preg_match('/draagt ([^.]+)/i', $aiSummary, $matches)) {
        $prompt .= "Wearing " . trim($matches[1]) . ". ";
    }
    
    $prompt .= "\n\nSTYLE: Hyper-realistic, professional studio photography";
    $prompt .= "\nLIGHTING: Professional studio lighting, soft shadows, dramatic highlights";
    $prompt .= "\nQUALITY: 8K resolution, sharp focus, photorealistic textures";
    $prompt .= "\nCOMPOSITION: Cinematic, 16:9 widescreen format for video";
    $prompt .= "\nIMPORTANT: NO MASK on character, photorealistic rendering (not cartoon or drawing)";
    $prompt .= "\nCamera: Professional DSLR, 85mm lens, f/2.8, studio lighting setup";
    
    return $prompt;
}

/**
 * Call OpenAI API
 */
function callOpenAI($apiKey, $systemPrompt, $userPrompt, $maxTokens = 500) {
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    
    $payload = [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ],
        'max_tokens' => $maxTokens,
        'temperature' => 0.8
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception("OpenAI API error: HTTP $httpCode");
    }
    
    $result = json_decode($response, true);
    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception("Invalid API response");
    }
    
    return trim($result['choices'][0]['message']['content']);
}

try {
    // Analyze personality and determine character type
    $personalityTraits = analyzePersonality($data['answers']);
    $characterType = determineCharacterType($personalityTraits);
    
    // Format answers for AI
    $formattedAnswers = formatAnswersForAI($data['answers'], $data['chapters'] ?? []);
    
    // Add personality analysis to formatted answers
    $formattedAnswers .= "\n=== PERSONALITY ANALYSIS ===\n";
    foreach ($personalityTraits as $trait => $score) {
        $formattedAnswers .= ucfirst($trait) . ": $score\n";
    }
    $formattedAnswers .= "\nSUGGESTED CHARACTER TYPE: $characterType\n\n";
    
    // CALL 1: Generate combined character summary (character + environment + props)
    $systemPrompt1 = "You are creating diverse, creative character descriptions for a workplace game show. Characters should be VARIED - animals, fruits/vegetables, fantasy heroes, Pixar-style figures, or fairy tale characters. NO MASKS NEEDED. Each character should wear clothes and have distinct personality. Keep language simple and fun. Write in Dutch.";
    
    // Load 80 options per character type
    $characterOptions = json_decode(file_get_contents('character-options-80.json'), true);
    
    $characterTypeExamples = [
        'animals' => 'Kies uit deze 80 dieren: ' . implode(', ', array_slice($characterOptions['animals_80'], 0, 40)) . ', en 40 meer! Wees creatief!',
        'fruits_vegetables' => 'Kies uit deze 80 groenten/fruit: ' . implode(', ', array_slice($characterOptions['fruits_vegetables_80'], 0, 40)) . ', en 40 meer! Wees creatief!',
        'fantasy_heroes' => 'Kies uit deze 80 fantasy karakters: ' . implode(', ', array_slice($characterOptions['fantasy_heroes_80'], 0, 40)) . ', en 40 meer! Mix klassiek met modern!',
        'pixar_disney' => 'Kies uit deze 80 Pixar/Disney karakters: ' . implode(', ', array_slice($characterOptions['pixar_disney_80'], 0, 40)) . ', en 40 meer! Denk aan Pixar!',
        'fairy_tales' => 'Kies uit deze 80 sprookjesfiguren: ' . implode(', ', array_slice($characterOptions['fairy_tales_80'], 0, 40)) . ', en 40 meer! Maak het modern!'
    ];
    
    $typeExample = $characterTypeExamples[$characterType] ?? $characterTypeExamples['animals'];
    
    $userPrompt1 = "SUGGESTED CHARACTER TYPE: $characterType\n\n" .
        "Based on the personality analysis and questionnaire answers, create a character description in Dutch with these 3 sections:\n\n" .
        "1. KARAKTER (100-150 woorden):\n" .
        "- Kies een specifiek dier, fruit/groente, fantasy held, Pixar-figuur, of sprookjesfiguur\n" .
        "- GEEN MASKER NODIG\n" .
        "- Beschrijf hun kleding (ze dragen altijd kleding!)\n" .
        "- Beschrijf hun persoonlijkheid in simpele termen\n" .
        "- Maak het mysterieus maar makkelijk te begrijpen\n" .
        "- Wees creatief en divers!\n\n" .
        "Voorbeelden voor $characterType: $typeExample\n\n" .
        "2. OMGEVING (50-75 woorden):\n" .
        "- Waar hangt dit karakter rond?\n" .
        "- Hoe ziet het er daar uit?\n" .
        "- Houd het simpel en visueel\n\n" .
        "3. PROPS (3-5 items):\n" .
        "- Lijst 3-5 objecten die dit karakter altijd bij zich heeft\n" .
        "- Formaat: '- Item naam: waarom het belangrijk is'\n\n" .
        "Houd alles casual en leuk. Geen ingewikkelde woorden!\n" .
        "WEES CREATIEF EN DIVERS - niet alle karakters moeten hetzelfde type zijn!\n\n" .
        $formattedAnswers;
    
    $aiSummary = callOpenAI($apiKey, $systemPrompt1, $userPrompt1, 600);
    
    // Extract character name from the summary
    preg_match("/'([^']+)'/", $aiSummary, $matches);
    $characterName = $matches[1] ?? 'De Gemaskeerde Medewerker';
    
    // CALL 2: Story Level 1 (Surface - work achievement)
    $systemPrompt2 = "You create simple video prompts for a game show. Keep it casual and easy to answer. Write in Dutch.";
    
    $userPrompt2 = "Based on this character and their answers, create a simple video prompt in Dutch about a work achievement.\n\n" .
        "Character: $characterName\n\n" .
        "Format: 'Als [CHARACTER_NAME], vertel over een moment waarop je...' (30-60 seconden)\n\n" .
        "Keep it simple - like talking to a friend.\n\n" .
        $formattedAnswers;
    
    $storyLevel1 = callOpenAI($apiKey, $systemPrompt2, $userPrompt2, 200);
    
    // CALL 3: Story Level 2 (Hidden talent)
    $userPrompt3 = "Based on this character and their answers, create a fun prompt in Dutch about a hidden talent or surprising skill.\n\n" .
        "Character: $characterName\n\n" .
        "Format: 'Als [CHARACTER_NAME], deel iets verrassends over jezelf...' (60-90 seconden)\n\n" .
        "Make it interesting!\n\n" .
        $formattedAnswers;
    
    $storyLevel2 = callOpenAI($apiKey, $systemPrompt2, $userPrompt3, 200);
    
    // CALL 4: Story Level 3 (Deep/personal growth)
    $userPrompt4 = "Based on this character and their answers, create a deeper prompt in Dutch about personal growth or a meaningful moment.\n\n" .
        "Character: $characterName\n\n" .
        "Format: 'Als [CHARACTER_NAME], deel een moment dat je veranderde...' (90-120 seconden)\n\n" .
        "Be real and honest, but not too heavy.\n\n" .
        $formattedAnswers;
    
    $storyLevel3 = callOpenAI($apiKey, $systemPrompt2, $userPrompt4, 250);
    
    // Generate image prompt for realistic studio photo
    $imagePrompt = generateImagePrompt($characterName, $aiSummary, $characterType);
    
    // Return all generated content
    $result = [
        'success' => true,
        'character_name' => $characterName,
        'character_type' => $characterType,
        'personality_traits' => $personalityTraits,
        'ai_summary' => $aiSummary,
        'story_prompt_level1' => $storyLevel1,
        'story_prompt_level2' => $storyLevel2,
        'story_prompt_level3' => $storyLevel3,
        'image_generation_prompt' => $imagePrompt,
        'api_calls_used' => 4,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
