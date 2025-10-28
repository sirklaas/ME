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

// Get Claude API key from api-keys.php
$apiKey = defined('CLAUDE_API_KEY') ? CLAUDE_API_KEY : '';
if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'Claude API key not configured in api-keys.php']);
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
 * Extract specific character (animal/fruit/hero) from AI summary
 */
function extractSpecificCharacter($aiSummary, $characterType) {
    // Try to extract the specific animal/fruit/character
    // Pattern: "De [SPECIFIC] genaamd"
    if (preg_match('/De\s+([A-Z][a-zëéèêïöü]+)\s+genaamd/i', $aiSummary, $matches)) {
        return $matches[1]; // e.g., "Kameleon", "Tomaat", "Vos"
    }
    
    // Fallback: return generic type
    $fallbacks = [
        'animals' => 'dier',
        'fruits_vegetables' => 'groente',
        'fantasy_heroes' => 'held',
        'pixar_disney' => 'figuur',
        'fairy_tales' => 'sprookjesfiguur'
    ];
    
    return $fallbacks[$characterType] ?? 'karakter';
}

/**
 * Generate realistic image prompt for Leonardo.ai
 */
function generateImagePrompt($characterName, $aiSummary, $characterType) {
    // Extract specific character (e.g., "Kameleon", "Tomaat", "Vos")
    $specificCharacter = extractSpecificCharacter($aiSummary, $characterType);
    error_log("🎯 Extracted specific character: $specificCharacter");
    // Extract the KARAKTER section (most important details)
    $karakterText = "";
    if (preg_match('/1\.\s*KARAKTER[:\s]+(.+?)(?=2\.\s*OMGEVING|$)/is', $aiSummary, $matches)) {
        $karakterText = trim($matches[1]);
        // Remove line breaks and extra spaces
        $karakterText = preg_replace('/\s+/', ' ', $karakterText);
        // Limit to first 150 chars for character description (reduced from 300)
        $karakterText = substr($karakterText, 0, 150);
    }
    
    // Extract environment from AI summary (OMGEVING section)
    $environmentText = "";
    if (preg_match('/2\.\s*OMGEVING[:\s]+(.+?)$/is', $aiSummary, $matches)) {
        $environmentText = trim($matches[1]);
        $environmentText = preg_replace('/\s+/', ' ', $environmentText);
        // Limit to first 80 chars for environment (reduced from 200)
        $environmentText = substr($environmentText, 0, 80);
    }
    
    // Build ULTRA-SPECIFIC prompt - REPEAT character type 3 times for emphasis
    $prompt = "CRITICAL: This MUST be a $specificCharacter (NOT any other animal/fruit/character). ";
    $prompt .= "Full body portrait of $specificCharacter named $characterName. ";
    $prompt .= "The character is a $specificCharacter. ";
    
    // Add type-specific details
    if ($characterType === 'fruits_vegetables') {
        $prompt .= "Anthropomorphic $specificCharacter with cartoon face, expressive eyes, smiling mouth, stick arms, stick legs, wearing clothes. Pixar/Disney style. ";
    } elseif ($characterType === 'animals') {
        $prompt .= "Anthropomorphic $specificCharacter with clothes and personality. ";
    } elseif ($characterType === 'fantasy_heroes') {
        $prompt .= "Fantasy $specificCharacter with detailed costume. ";
    } elseif ($characterType === 'pixar_disney') {
        $prompt .= "Pixar-style 3D animated $specificCharacter. ";
    } elseif ($characterType === 'fairy_tales') {
        $prompt .= "Fairy tale $specificCharacter. ";
    }
    
    // Add character description (shortened to 100 chars)
    if (!empty($karakterText)) {
        $prompt .= substr($karakterText, 0, 100) . " ";
    }
    
    // Add environment (shortened to 60 chars)
    if (!empty($environmentText)) {
        $prompt .= "Setting: " . substr($environmentText, 0, 60) . " ";
    }
    
    // Add SHORT technical requirements
    $prompt .= "16:9, 4K, cinematic, full body, rule of thirds.";
    
    // Log prompt length for debugging
    error_log("Image prompt length: " . strlen($prompt) . " characters");
    
    return $prompt;
}

/**
 * Call Claude Haiku API (Anthropic)
 */
function callClaudeHaiku($apiKey, $systemPrompt, $userPrompt, $maxTokens = 1024, $isRegenerate = false) {
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    
    // Increase temperature for regeneration to get more variation
    $temperature = $isRegenerate ? 1.0 : 0.8;
    
    $payload = [
        'model' => 'claude-3-haiku-20240307',
        'max_tokens' => $maxTokens,
        'temperature' => $temperature,
        'system' => $systemPrompt,
        'messages' => [
            [
                'role' => 'user',
                'content' => $userPrompt
            ]
        ]
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01'
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Log the full response for debugging
    error_log("Claude API Response - HTTP $httpCode: $response");
    
    if ($curlError) {
        error_log("Claude cURL error: $curlError");
        throw new Exception("Claude API connection error: $curlError");
    }
    
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['error']['message'] ?? 'Unknown error';
        error_log("Claude API error: HTTP $httpCode - Message: $errorMsg - Full response: $response");
        throw new Exception("Claude API error: $errorMsg");
    }
    
    $result = json_decode($response, true);
    if (!isset($result['content'][0]['text'])) {
        error_log("Invalid Claude API response structure: " . json_encode($result));
        throw new Exception("Invalid API response from Claude");
    }
    
    return trim($result['content'][0]['text']);
}

try {
    // Check if this is a regeneration request
    $isRegenerate = isset($data['regenerate']) && $data['regenerate'] === true;
    
    // Get user-selected character type (from Question 7) or fallback to AI determination
    if (isset($data['characterType']) && !empty($data['characterType'])) {
        $characterType = $data['characterType'];
        error_log("✅ Using user-selected character type: $characterType");
    } else {
        // Fallback: Analyze personality and determine character type (old method)
        $personalityTraits = analyzePersonality($data['answers']);
        $characterType = determineCharacterType($personalityTraits);
        error_log("⚠️ No character type selected, using AI determination: $characterType");
    }
    
    // Still analyze personality for character traits
    $personalityTraits = analyzePersonality($data['answers']);
    
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
        "2. OMGEVING (30-50 woorden):\n" .
        "- Waar hangt dit karakter rond?\n" .
        "- Beschrijf EEN SPECIFIEKE LOCATIE (bijv: 'een zonnige tuin', 'een moderne keuken', 'een druk marktplein')\n" .
        "- HOUD HET SIMPEL EN CONCREET - geen abstracte concepten\n\n" .
        "⚠️ NOGMAALS: Kies EEN specifiek item uit de lijst hierboven. GEEN gemaskeerde personen!\n" .
        "⚠️ VERBODEN WOORDEN: Gebruik NOOIT de woorden 'masker', 'mask', 'gemaskeerd', 'masked' in je beschrijving!\n\n" .
        "Antwoorden van de speler:\n" .
        $formattedAnswers;
    
    $aiSummary = callClaudeHaiku($apiKey, $systemPrompt1, $userPrompt1, 1024, $isRegenerate);
    
    // Extract character name from the summary (try multiple patterns)
    $characterName = 'De Gemaskeerde Medewerker'; // Default fallback
    
    // Pattern 1: Look for "genaamd Name" (handles multi-word names like "Paarse Nebula")
    if (preg_match('/genaamd\s+([A-Z][a-zA-Z\s]+?)(?:\s+is|\s+draagt|\s+heeft|\.|,)/i', $aiSummary, $matches)) {
        $characterName = trim($matches[1]);
    }
    // Pattern 2: Look for 'Name' in quotes
    elseif (preg_match("/'([^']+)'/", $aiSummary, $matches)) {
        $characterName = $matches[1];
    }
    // Pattern 3: Look for "De [Type] genaamd Name"
    elseif (preg_match('/De\s+\w+\s+genaamd\s+([A-Z][a-zA-Z\s]+)/i', $aiSummary, $matches)) {
        $characterName = trim($matches[1]);
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
