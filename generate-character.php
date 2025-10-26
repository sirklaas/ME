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

// Load API keys
define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';

// Get OpenAI API key
$apiKey = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '';
if (empty($apiKey) || $apiKey === 'YOUR_OPENAI_API_KEY_HERE') {
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
    $prompt = "⚠️ CRITICAL: 16:9 WIDESCREEN ASPECT RATIO (1920x1080 or 1280x720) - MANDATORY\n\n";
    $prompt .= "STUDIO QUALITY PHOTO, hyper-realistic: ";
    
    // Add character type context
    $typeDescriptions = [
        'animals' => 'anthropomorphic animal character (actual animal with clothes)',
        'fruits_vegetables' => 'HUMANIZED fruit/vegetable character with EXPRESSIVE EYES, SMILING MOUTH, ARMS with HANDS, LEGS with FEET (Pixar-style, cartoon-like features on actual fruit/vegetable body)',
        'fantasy_heroes' => 'fantasy hero character (elf, wizard, knight, etc)',
        'pixar_disney' => 'Pixar-style 3D animated character',
        'fairy_tales' => 'fairy tale character (from classic stories)'
    ];
    
    $prompt .= $typeDescriptions[$characterType] ?? 'character';
    $prompt .= " named '$characterName'. ";
    
    // Extract clothing and appearance from summary
    if (preg_match('/draagt ([^.]+)/i', $aiSummary, $matches)) {
        $prompt .= "Wearing " . trim($matches[1]) . ". ";
    }
    
    // Extract environment from AI summary (OMGEVING section)
    $environmentText = "";
    if (preg_match('/2\.\s*OMGEVING[:\s]+(.+?)(?=3\.\s*PROPS|$)/is', $aiSummary, $matches)) {
        $environmentText = trim($matches[1]);
        $environmentText = preg_replace('/\s+/', ' ', $environmentText); // Clean whitespace
    }
    
    $prompt .= "\n\n=== ENVIRONMENT & BACKGROUND ===";
    if (!empty($environmentText)) {
        $prompt .= "\nSETTING: " . $environmentText;
    } else {
        // Fallback if extraction fails
        $prompt .= "\nSETTING: Professional TV gameshow studio with dramatic stage lighting";
    }
    $prompt .= "\nSTYLE: Cinematic, theatrical, vibrant and colorful";
    $prompt .= "\nATMOSPHERE: Exciting gameshow environment with professional lighting";
    
    $prompt .= "\n\n=== TECHNICAL SPECS ===";
    $prompt .= "\nASPECT RATIO: 16:9 widescreen (1216x832) - MANDATORY";
    $prompt .= "\nSTYLE: Hyper-realistic, professional studio photography";
    $prompt .= "\nLIGHTING: Professional studio lighting, soft shadows, dramatic highlights";
    $prompt .= "\nQUALITY: 8K resolution, sharp focus, photorealistic textures";
    $prompt .= "\nCOMPOSITION: Cinematic widescreen, horizontal orientation";
    $prompt .= "\nFRAMING: Full body or 3/4 body shot, centered in 16:9 frame with gameshow studio visible behind";
    $prompt .= "\nIMPORTANT: NO MASK, NO human face - character IS the animal/fruit/fantasy being";
    
    // Add special note for fruits/vegetables
    if ($characterType === 'fruits_vegetables') {
        $prompt .= "\n\n🥕 CRITICAL FOR FRUIT/VEGETABLE:";
        $prompt .= "\n- MUST show cartoon-style EYES (big, expressive, with pupils and emotion)";
        $prompt .= "\n- MUST show MOUTH (smiling, talking, or showing emotion)";
        $prompt .= "\n- MUST show ARMS (thin limbs with hands/gloves that can hold things)";
        $prompt .= "\n- MUST show LEGS (thin limbs with feet/shoes for standing/walking)";
        $prompt .= "\n- Think: Pixar vegetables/fruits like from 'Veggie Tales' or animated produce";
        $prompt .= "\n- Body is the actual fruit/vegetable, but with added cartoon facial features and limbs";
    }
    
    $prompt .= "\nCamera: Professional DSLR, 85mm lens, f/2.8, studio lighting setup";
    $prompt .= "\n\n⚠️ VERIFY: Image MUST be 16:9 widescreen format (horizontal rectangle, NOT square or vertical)";
    
    return $prompt;
}

/**
 * Call OpenAI API
 */
function callOpenAI($apiKey, $systemPrompt, $userPrompt, $maxTokens = 500, $isRegenerate = false) {
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    
    // Increase temperature for regeneration to get more variation
    $temperature = $isRegenerate ? 1.0 : 0.8;
    
    $payload = [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ],
        'max_tokens' => $maxTokens,
        'temperature' => $temperature
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
    // Check if this is a regeneration request
    $isRegenerate = isset($data['regenerate']) && $data['regenerate'] === true;
    
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
    
    // Add regeneration note if applicable
    if ($isRegenerate) {
        $formattedAnswers .= "\n⚠️ REGENERATION REQUEST: Create a DIFFERENT character than before. Use different name, different specific animal/fruit/hero from the list, different clothing style, different personality emphasis. Be creative and varied!\n\n";
    }
    
    // CALL 1: Generate combined character summary (character + environment + props)
    $systemPrompt1 = "You are creating diverse, creative character descriptions for a workplace game show. CRITICAL RULES: 1) Characters MUST be actual animals, fruits/vegetables, fantasy heroes, Pixar-style figures, or fairy tale characters - NOT people with masks. 2) Pick ONE specific option from the provided list. 3) ABSOLUTELY NO MASKS ANYWHERE - the character IS the animal/fruit/etc itself. 4) Characters wear clothes and have personality. 5) NEVER mention masks, maskers, or masked in your description. Write in Dutch.";
    
    // Load 80 options per character type
    $characterOptions = json_decode(file_get_contents('character-options-80.json'), true);
    
    $characterTypeExamples = [
        'animals' => "VERPLICHT: Kies EEN dier uit deze lijst:\n" . implode(', ', $characterOptions['animals_80']),
        'fruits_vegetables' => "VERPLICHT: Kies EEN groente/fruit uit deze lijst:\n" . implode(', ', $characterOptions['fruits_vegetables_80']),
        'fantasy_heroes' => "VERPLICHT: Kies EEN fantasy karakter uit deze lijst:\n" . implode(', ', $characterOptions['fantasy_heroes_80']),
        'pixar_disney' => "VERPLICHT: Kies EEN Pixar/Disney karakter uit deze lijst:\n" . implode(', ', $characterOptions['pixar_disney_80']),
        'fairy_tales' => "VERPLICHT: Kies EEN sprookjesfiguur uit deze lijst:\n" . implode(', ', $characterOptions['fairy_tales_80'])
    ];
    
    $typeExample = $characterTypeExamples[$characterType] ?? $characterTypeExamples['animals'];
    
    // Add special instructions for fruits/vegetables
    $specialInstructions = "";
    if ($characterType === 'fruits_vegetables') {
        $specialInstructions = "🥕 EXTRA BELANGRIJK VOOR GROENTE/FRUIT:\n" .
            "- Het fruit/groente MOET gehumaniseerd zijn met:\n" .
            "  * Expressieve ogen (groot, levendig, met emotie)\n" .
            "  * Een mond (kan glimlachen, praten, emoties tonen)\n" .
            "  * Armen (kunnen dingen vasthouden, gebaren maken)\n" .
            "  * Benen (kunnen lopen, dansen, bewegen)\n" .
            "  * Handen en voeten (met vingers/tenen of handschoenen/schoenen)\n" .
            "- Denk aan Pixar-stijl: levendig, expressief, vol persoonlijkheid!\n" .
            "- Bijvoorbeeld: Een tomaat met grote ronde ogen, brede glimlach, dunne armpjes in een jasje, en kleine beentjes in sneakers\n\n";
    }
    
    $userPrompt1 = "⚠️ BELANGRIJK: Het karakter MOET een echt dier/fruit/fantasy wezen zijn - GEEN persoon met masker!\n\n" .
        "CHARACTER TYPE: $characterType\n\n" .
        "$typeExample\n\n" .
        $specialInstructions .
        "Creëer een karakter beschrijving in het Nederlands met deze 3 secties:\n\n" .
        "1. KARAKTER (100-150 woorden):\n" .
        "- Begin met: 'De [DIER/FRUIT/HELD NAAM] genaamd [Creatieve Naam]'\n" .
        "- Bijvoorbeeld: 'De Vos genaamd Luna' of 'De Tomaat genaamd Rooie Rico'\n" .
        "- GEEN MASKER - het karakter IS het dier/fruit/held zelf\n" .
        ($characterType === 'fruits_vegetables' ? "- VERPLICHT: Beschrijf de ogen, mond, armen en benen van het fruit/groente!\n" : "") .
        "- Beschrijf hun kleding (ze dragen altijd kleding!)\n" .
        "- Beschrijf hun persoonlijkheid\n" .
        "- Maak het levendig en visueel\n\n" .
        "2. OMGEVING (50-75 woorden):\n" .
        "- Waar hangt dit karakter rond?\n" .
        "- Hoe ziet het er daar uit?\n" .
        "- Houd het simpel en visueel\n\n" .
        "3. PROPS (3-5 items):\n" .
        "- Lijst 3-5 objecten die dit karakter altijd bij zich heeft\n" .
        "- Formaat: '- Item naam: waarom het belangrijk is'\n\n" .
        "⚠️ NOGMAALS: Kies EEN specifiek item uit de lijst hierboven. GEEN gemaskeerde personen!\n" .
        "⚠️ VERBODEN WOORDEN: Gebruik NOOIT de woorden 'masker', 'mask', 'gemaskeerd', 'masked' in je beschrijving!\n\n" .
        "Antwoorden van de speler:\n" .
        $formattedAnswers;
    
    $aiSummary = callOpenAI($apiKey, $systemPrompt1, $userPrompt1, 600, $isRegenerate);
    
    // Extract character name from the summary (try multiple patterns)
    $characterName = 'De Gemaskeerde Medewerker'; // Default fallback
    
    // Pattern 1: Look for 'Name' in quotes
    if (preg_match("/'([^']+)'/", $aiSummary, $matches)) {
        $characterName = $matches[1];
    }
    // Pattern 2: Look for "genaamd Name" or "named Name"
    elseif (preg_match('/genaamd\s+([A-Z][a-zA-Z]+)/i', $aiSummary, $matches)) {
        $characterName = $matches[1];
    }
    // Pattern 3: Look for "De [Type] genaamd Name"
    elseif (preg_match('/De\s+\w+\s+genaamd\s+([A-Z][a-zA-Z]+)/i', $aiSummary, $matches)) {
        $characterName = $matches[1];
    }
    
    // Use Chapter 9 answers directly (Questions 41-43 with 3 scenes each)
    // Story 1: Question 41 (3 scenes)
    $storyLevel1 = "";
    if (isset($data['answers']['41_scene1'])) {
        $storyLevel1 .= "Scene 1: " . $data['answers']['41_scene1'] . "\n\n";
        $storyLevel1 .= "Scene 2: " . $data['answers']['41_scene2'] . "\n\n";
        $storyLevel1 .= "Scene 3: " . $data['answers']['41_scene3'];
    } elseif (isset($data['answers'][41])) {
        // Fallback for old format
        $storyLevel1 = $data['answers'][41];
    }
    
    // Story 2: Question 42 (3 scenes)
    $storyLevel2 = "";
    if (isset($data['answers']['42_scene1'])) {
        $storyLevel2 .= "Scene 1: " . $data['answers']['42_scene1'] . "\n\n";
        $storyLevel2 .= "Scene 2: " . $data['answers']['42_scene2'] . "\n\n";
        $storyLevel2 .= "Scene 3: " . $data['answers']['42_scene3'];
    } elseif (isset($data['answers'][42])) {
        // Fallback for old format
        $storyLevel2 = $data['answers'][42];
    }
    
    // Story 3: Question 43 (3 scenes)
    $storyLevel3 = "";
    if (isset($data['answers']['43_scene1'])) {
        $storyLevel3 .= "Scene 1: " . $data['answers']['43_scene1'] . "\n\n";
        $storyLevel3 .= "Scene 2: " . $data['answers']['43_scene2'] . "\n\n";
        $storyLevel3 .= "Scene 3: " . $data['answers']['43_scene3'];
    } elseif (isset($data['answers'][43])) {
        // Fallback for old format
        $storyLevel3 = $data['answers'][43];
    }
    
    // Generate image prompt for realistic studio photo
    $imagePrompt = generateImagePrompt($characterName, $aiSummary, $characterType);
    
    // Format personality traits as string for PocketBase
    $personalityTraitsString = "";
    foreach ($personalityTraits as $trait => $score) {
        $personalityTraitsString .= ucfirst($trait) . ": " . $score . "\n";
    }
    $personalityTraitsString = trim($personalityTraitsString);
    
    // Return all generated content
    $result = [
        'success' => true,
        'character_name' => $characterName,
        'character_type' => $characterType,
        'personality_traits' => $personalityTraitsString,
        'ai_summary' => $aiSummary,
        'story_prompt_level1' => $storyLevel1,
        'story_prompt_level2' => $storyLevel2,
        'story_prompt_level3' => $storyLevel3,
        'image_generation_prompt' => $imagePrompt,
        'api_calls_used' => 1, // Only 1 call now (character generation)
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
