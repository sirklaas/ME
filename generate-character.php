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
    if (preg_match('/De\s+([A-Z][a-zëéèêïöü\s]+?)\s+genaamd/i', $aiSummary, $matches)) {
        $dutchCharacter = trim($matches[1]); // e.g., "Kameleon", "Tomaat", "Vos", "Tovenaar"
        
        // Translate to English for Leonardo.ai (it understands English better)
        $englishCharacter = translateCharacterToEnglish($dutchCharacter, $characterType);
        error_log("🌍 Translated '$dutchCharacter' to '$englishCharacter' for Leonardo.ai");
        
        return $englishCharacter;
    }
    
    // Fallback: return generic type in English
    $fallbacks = [
        'animals' => 'animal',
        'fruits_vegetables' => 'fruit or vegetable',
        'fantasy_heroes' => 'fantasy hero',
        'pixar_disney' => 'Pixar character',
        'fairy_tales' => 'fairy tale character'
    ];
    
    return $fallbacks[$characterType] ?? 'character';
}

/**
 * Translate Dutch character names to English using JSON translation file
 */
function translateCharacterToEnglish($dutchName, $characterType) {
    static $translations = null;
    
    // Load translations once
    if ($translations === null) {
        $translationsFile = __DIR__ . '/character-translations.json';
        if (file_exists($translationsFile)) {
            $translations = json_decode(file_get_contents($translationsFile), true);
        } else {
            error_log("⚠️ Translation file not found: $translationsFile");
            $translations = [];
        }
    }
    
    // Try to find translation in the specific category
    if (isset($translations[$characterType][$dutchName])) {
        return $translations[$characterType][$dutchName];
    }
    
    // Try all categories as fallback
    foreach ($translations as $category => $categoryTranslations) {
        if (isset($categoryTranslations[$dutchName])) {
            return $categoryTranslations[$dutchName];
        }
    }
    
    // Return original if no translation found (might already be English)
    error_log("⚠️ No translation found for '$dutchName' in category '$characterType'");
    return $dutchName;
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
        // Extract clothing/costume details (CRITICAL for accurate image)
        $clothingKeywords = ['draagt', 'ruimtepak', 'pak', 'kleding', 'kostuum', 'outfit', 'jasje', 'broek', 'schoenen', 'hoed', 'cape', 'armor', 'robes'];
        $clothingDetails = '';
        foreach ($clothingKeywords as $keyword) {
            if (stripos($karakterText, $keyword) !== false) {
                // Extract sentence containing clothing keyword
                if (preg_match('/[^.]*' . preg_quote($keyword, '/') . '[^.]*/i', $karakterText, $clothingMatch)) {
                    $clothingDetails .= trim($clothingMatch[0]) . '. ';
                }
            }
        }
        // Prioritize clothing details, then rest of description
        if (!empty($clothingDetails)) {
            $karakterText = $clothingDetails . ' ' . $karakterText;
        }
        // Limit to first 200 chars to include important clothing details
        $karakterText = substr($karakterText, 0, 200);
    }
    
    // Extract environment from AI summary (OMGEVING section)
    $environmentText = "";
    if (preg_match('/2\.\s*OMGEVING[:\s]+(.+?)$/is', $aiSummary, $matches)) {
        $environmentText = trim($matches[1]);
        $environmentText = preg_replace('/\s+/', ' ', $environmentText);
        // Limit to first 80 chars for environment (reduced from 200)
        $environmentText = substr($environmentText, 0, 80);
    }
    
    // Build ULTRA-SPECIFIC prompt - REPEAT character type and style 3 times for emphasis
    $prompt = "CRITICAL: This MUST be a CARTOON/ANIMATED $specificCharacter (NOT a realistic photo or real animal). ";
    $prompt .= "Full body portrait of anthropomorphic cartoon $specificCharacter named $characterName. ";
    $prompt .= "The character is a stylized animated $specificCharacter in Pixar/Disney style. ";
    
    // Add type-specific details - ALL types are anthropomorphic/humanized
    if ($characterType === 'fruits_vegetables') {
        $prompt .= "Anthropomorphic $specificCharacter with cartoon face, expressive eyes, smiling mouth, stick arms with hands, stick legs with feet, wearing stylish clothes. Pixar/Disney style, NOT realistic. ";
    } elseif ($characterType === 'animals') {
        $prompt .= "CARTOON anthropomorphic $specificCharacter character standing upright on TWO LEGS like a human, wearing the EXACT clothes described, expressive cartoon face with big eyes and smile, human-like hands with fingers (NOT paws), human posture. 3D animated Pixar/Disney/Zootopia style. ABSOLUTELY NOT a realistic animal photo. MUST be cartoon/animated style. ";
    } elseif ($characterType === 'fantasy_heroes') {
        $prompt .= "Humanoid fantasy $specificCharacter character with detailed costume, armor or robes, standing upright, expressive face. Fantasy RPG style, NOT realistic. ";
    } elseif ($characterType === 'pixar_disney') {
        $prompt .= "Pixar-style 3D animated human $specificCharacter character, expressive face, stylized proportions, wearing modern clothes. Animated movie style, NOT realistic. ";
    } elseif ($characterType === 'fairy_tales') {
        $prompt .= "Storybook fairy tale $specificCharacter character, whimsical style, expressive features, magical costume. Illustrated fairy tale style, NOT realistic. ";
    }
    
    // Add character description with clothing details (up to 200 chars now)
    if (!empty($karakterText)) {
        $prompt .= $karakterText . " ";
    }
    
    // Add environment with more detail (up to 100 chars for better scene)
    if (!empty($environmentText)) {
        $environmentText = substr($environmentText, 0, 100);
        $prompt .= "Background setting: " . $environmentText . ". ";
    }
    
    // Add SHORT technical requirements emphasizing cartoon style
    $prompt .= "Cartoon 3D animation style, vibrant colors, full body shot, 16:9 ratio, professional animation quality.";
    
    // Log prompt length for debugging
    error_log("Image prompt length: " . strlen($prompt) . " characters");
    
    return $prompt;
}

/**
 * Call Claude Haiku API (Anthropic)
 */
function callClaudeHaiku($apiKey, $systemPrompt, $userPrompt, $maxTokens = 1024, $isRegenerate = false) {
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    
    // Higher temperature for regeneration to get more variety
    $temperature = $isRegenerate ? 1.0 : 0.8;
    
    // Add random variation to user prompt for regeneration to prevent identical outputs
    $promptVariation = "";
    if ($isRegenerate) {
        $randomWords = ['unique', 'creative', 'original', 'distinctive', 'unusual', 'fresh', 'innovative'];
        $randomWord = $randomWords[array_rand($randomWords)];
        $promptVariation = "\n🎲 Make this character extra $randomWord and different! Random seed: " . mt_rand(1000, 9999) . "\n";
    }
    
    $payload = [
        'model' => 'claude-3-haiku-20240307',
        'max_tokens' => $maxTokens,
        'temperature' => $temperature,
        'system' => $systemPrompt,
        'messages' => [
            [
                'role' => 'user',
                'content' => $userPrompt . $promptVariation
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
    
    // Get list of used characters to avoid duplicates
    $usedCharacters = isset($data['usedCharacters']) ? $data['usedCharacters'] : [];
    
    // Add regeneration note if applicable
    if ($isRegenerate) {
        $formattedAnswers .= "\n🔄 REGENERATION REQUEST #" . (count($usedCharacters) + 1) . ": Create a COMPLETELY DIFFERENT character than before!\n";
        $formattedAnswers .= "⚠️ CRITICAL: Pick a character from the BEGINNING or MIDDLE of the list above - NOT the same one!\n";
        $formattedAnswers .= "⚠️ Use a different species/type, different name, different clothing style, different personality traits.\n";
        $formattedAnswers .= "⚠️ Be CREATIVE and VARIED - surprise us with something unexpected!\n\n";
        
        // Add list of used characters to avoid
        if (!empty($usedCharacters)) {
            $formattedAnswers .= "⚠️ DO NOT USE THESE CHARACTERS (already used):\n";
            foreach ($usedCharacters as $usedChar) {
                $formattedAnswers .= "- $usedChar\n";
            }
            $formattedAnswers .= "\nPick a COMPLETELY DIFFERENT character from the list!\n\n";
        }
    }
    
    // CALL 1: Generate combined character summary (character + environment + props)
    $systemPrompt1 = "You are creating diverse, creative character descriptions for a workplace game show. CRITICAL RULES: 1) Characters MUST be actual animals, fruits/vegetables, fantasy heroes, Pixar-style figures, or fairy tale characters - NOT people with masks. 2) Pick ONE specific option from the provided list. 3) ABSOLUTELY NO MASKS ANYWHERE - the character IS the animal/fruit/etc itself. 4) Characters wear clothes and have personality. 5) NEVER mention masks, maskers, or masked in your description. Write in Dutch.";
    
    // Load 80 options per character type
    $characterOptions = json_decode(file_get_contents('character-options-80.json'), true);
    
    // RANDOMIZE the order to get more variety (especially important for regeneration)
    $animals = $characterOptions['animals_80'];
    $fruitsVeggies = $characterOptions['fruits_vegetables_80'];
    $fantasyHeroes = $characterOptions['fantasy_heroes_80'];
    $pixarDisney = $characterOptions['pixar_disney_80'];
    $fairyTales = $characterOptions['fairy_tales_80'];
    
    // Shuffle multiple times for better randomization
    for ($i = 0; $i < 3; $i++) {
        shuffle($animals);
        shuffle($fruitsVeggies);
        shuffle($fantasyHeroes);
        shuffle($pixarDisney);
        shuffle($fairyTales);
    }
    
    // For regeneration, move used characters to the END of the list
    if ($isRegenerate && !empty($usedCharacters)) {
        $animals = array_diff($animals, $usedCharacters);
        $animals = array_merge($animals, array_intersect($usedCharacters, $characterOptions['animals_80']));
        
        $fruitsVeggies = array_diff($fruitsVeggies, $usedCharacters);
        $fruitsVeggies = array_merge($fruitsVeggies, array_intersect($usedCharacters, $characterOptions['fruits_vegetables_80']));
        
        $fantasyHeroes = array_diff($fantasyHeroes, $usedCharacters);
        $fantasyHeroes = array_merge($fantasyHeroes, array_intersect($usedCharacters, $characterOptions['fantasy_heroes_80']));
        
        $pixarDisney = array_diff($pixarDisney, $usedCharacters);
        $pixarDisney = array_merge($pixarDisney, array_intersect($usedCharacters, $characterOptions['pixar_disney_80']));
        
        $fairyTales = array_diff($fairyTales, $usedCharacters);
        $fairyTales = array_merge($fairyTales, array_intersect($usedCharacters, $characterOptions['fairy_tales_80']));
    }
    
    $characterTypeExamples = [
        'animals' => "VERPLICHT: Kies EEN dier uit deze lijst:\n" . implode(', ', $animals),
        'fruits_vegetables' => "VERPLICHT: Kies EEN groente/fruit uit deze lijst:\n" . implode(', ', $fruitsVeggies),
        'fantasy_heroes' => "VERPLICHT: Kies EEN fantasy karakter uit deze lijst:\n" . implode(', ', $fantasyHeroes),
        'pixar_disney' => "VERPLICHT: Kies EEN Pixar/Disney karakter uit deze lijst:\n" . implode(', ', $pixarDisney),
        'fairy_tales' => "VERPLICHT: Kies EEN sprookjesfiguur uit deze lijst:\n" . implode(', ', $fairyTales)
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
