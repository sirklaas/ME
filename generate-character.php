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
 * Query PocketBase to get character usage counts
 */
function getCharacterUsageCounts($characterType) {
    // Load PocketBase configuration
    require_once __DIR__ . '/api-keys.php';
    $pbUrl = defined('POCKETBASE_URL') ? POCKETBASE_URL : 'https://pb.masked-employee.com';
    
    $usageCounts = [];
    
    try {
        // Query all records for this character type
        $url = $pbUrl . '/api/collections/ME_questions/records?filter=(character_type="' . urlencode($characterType) . '")&perPage=500';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            
            if (isset($data['items'])) {
                // Count occurrences of each character name
                foreach ($data['items'] as $record) {
                    if (isset($record['character_name'])) {
                        // Extract just the character type from "De Leeuw genaamd Luna" -> "leeuw"
                        $fullName = $record['character_name'];
                        
                        // Try to extract the character type (e.g., "De Leeuw genaamd X" -> "leeuw")
                        if (preg_match('/De\s+(\w+)\s+genaamd/i', $fullName, $matches)) {
                            $characterName = strtolower($matches[1]);
                        } else {
                            // Fallback: use the full name
                            $characterName = strtolower($fullName);
                        }
                        
                        if (!isset($usageCounts[$characterName])) {
                            $usageCounts[$characterName] = 0;
                        }
                        $usageCounts[$characterName]++;
                    }
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error querying PocketBase for usage counts: " . $e->getMessage());
    }
    
    return $usageCounts;
}

/**
 * Sort character options by usage count (least used first)
 */
function sortByUsageCount($options, $usageCounts) {
    // Create array with usage counts
    $optionsWithCounts = [];
    foreach ($options as $option) {
        $optionLower = strtolower($option);
        $count = isset($usageCounts[$optionLower]) ? $usageCounts[$optionLower] : 0;
        $optionsWithCounts[] = [
            'name' => $option,
            'count' => $count
        ];
    }
    
    // Sort by count (ascending - least used first)
    usort($optionsWithCounts, function($a, $b) {
        if ($a['count'] === $b['count']) {
            // If same count, randomize order
            return rand(-1, 1);
        }
        return $a['count'] - $b['count'];
    });
    
    // Extract just the names
    return array_map(function($item) {
        return $item['name'];
    }, $optionsWithCounts);
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
 * Generate image prompt using Claude AI (much better than PHP string manipulation!)
 */
function generateImagePromptWithClaude($apiKey, $characterName, $aiSummary, $characterType, $isRegenerate = false) {
    // Extract specific character type (e.g., "Panda", "Tomaat")
    $specificCharacter = extractSpecificCharacter($aiSummary, $characterType);
    
    // Extract gender from Dutch text (hij = male, zij = female, het = neutral)
    $gender = "neutral";
    if (preg_match('/\b(Hij|hij)\b/', $aiSummary)) {
        $gender = "male";
    } elseif (preg_match('/\b(Zij|zij|Ze|ze)\b/', $aiSummary)) {
        $gender = "female";
    }
    error_log("👤 Detected gender: $gender for character: $characterName");
    
    // Create a focused prompt for Claude to generate the image description
    $systemPrompt = "You are an expert at creating stunning image generation prompts for AI image generators like Leonardo.ai. Create concise, visual, cinematic English-language prompts that produce professional, high-quality images.";
    
    // Load type-specific requirements from JSON file
    $requirementsFile = __DIR__ . '/image-prompt-requirements.json';
    $requirementsConfig = json_decode(file_get_contents($requirementsFile), true);
    
    // Get requirements for this character type
    $typeConfig = $requirementsConfig[$characterType] ?? $requirementsConfig['animals'];
    $generalQuality = $requirementsConfig['general_quality'];
    
    // Build type-specific requirements string
    $typeSpecificRequirements = "";
    foreach ($typeConfig['requirements'] as $requirement) {
        // Replace {character} placeholder with actual character name
        $requirement = str_replace('{character}', $specificCharacter, $requirement);
        $typeSpecificRequirements .= "- " . $requirement . "\n";
    }
    
    error_log("🎨 Using style: " . $typeConfig['style'] . " for character type: $characterType");
    
    $userPrompt = "Based on this Dutch character description, create a STUNNING English image generation prompt (MAX 250 characters) for Leonardo.ai.\n\n";
    $userPrompt .= "CHARACTER DESCRIPTION:\n$aiSummary\n\n";
    $userPrompt .= "⚠️ CRITICAL GENDER: The character is " . strtoupper($gender) . ". ";
    if ($gender === "male") {
        $userPrompt .= "The image MUST show a MALE person/character (man, boy, masculine features).\n";
    } elseif ($gender === "female") {
        $userPrompt .= "The image MUST show a FEMALE person/character (woman, girl, feminine features).\n";
    } else {
        $userPrompt .= "The image can be gender-neutral.\n";
    }
    $userPrompt .= "\nTYPE-SPECIFIC REQUIREMENTS:\n";
    $userPrompt .= $typeSpecificRequirements;
    $userPrompt .= "\nGENERAL REQUIREMENTS:\n";
    $userPrompt .= "- Include: The specific clothing/costume details from description\n";
    $userPrompt .= "- Include: The environment/setting mentioned\n";
    $userPrompt .= "- Character MUST be looking directly at camera with eyes visible\n";
    $userPrompt .= "- Technical quality: " . $generalQuality['technical'] . "\n";
    $userPrompt .= "- Camera: " . $generalQuality['camera'] . "\n";
    $userPrompt .= "- Style: " . $generalQuality['style'] . "\n";
    $userPrompt .= "- MAX 300 characters total (increased for quality details)!\n\n";
    $userPrompt .= "OUTPUT FORMAT (plain text, no quotes):\n";
    $userPrompt .= "$specificCharacter mascot named $characterName, [clothing], [pose], [environment], looking at camera. Shot with Sony a7V, Sigma 85mm, hyper-realistic, cinema still, 4K, 16:9, full body.\n\n";
    $userPrompt .= "Write ONLY the image prompt in English. Be concise, visual, and cinematic. Include professional camera/photography details. Create a prompt that will generate a STUNNING hyper-realistic professional image.";
    
    // Call Claude with shorter max tokens since we only need a short prompt
    $imagePrompt = callClaudeHaiku($apiKey, $systemPrompt, $userPrompt, 150, $isRegenerate);
    
    // Clean up the response (remove quotes if Claude added them)
    $imagePrompt = trim($imagePrompt);
    $imagePrompt = trim($imagePrompt, '"\'');
    
    error_log("🎨 Claude-generated image prompt: $imagePrompt");
    error_log("📏 Image prompt length: " . strlen($imagePrompt) . " characters");
    
    return $imagePrompt;
}

/**
 * OLD FUNCTION - Keep as fallback but not used anymore
 * Generate realistic image prompt for Leonardo.ai
 */
function generateImagePrompt_OLD($characterName, $aiSummary, $characterType) {
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
    
    // Build ULTRA-SPECIFIC prompt - mascot costume style
    $prompt = "$specificCharacter mascot costume character named $characterName. ";
    
    // Add type-specific details - mascot costume style (SHORTENED)
    $prompt .= "Mascot suit standing upright on two legs, wearing clothes, large friendly eyes, fabric/foam materials. ";
    
    // Add character description with clothing details (SHORTENED to 150 chars)
    if (!empty($karakterText)) {
        $prompt .= substr($karakterText, 0, 150) . " ";
    }
    
    // Add environment (SHORTENED to 60 chars)
    if (!empty($environmentText)) {
        $prompt .= substr($environmentText, 0, 60) . ". ";
    }
    
    // Add SHORT technical requirements
    $prompt .= "Theme park mascot, full body, vibrant colors, 16:9.";
    
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
    
    // Get all options for the selected character type
    $allOptions = [];
    switch ($characterType) {
        case 'animals':
            $allOptions = $characterOptions['animals_80'];
            break;
        case 'fruits_vegetables':
            $allOptions = $characterOptions['fruits_vegetables_80'];
            break;
        case 'fantasy_heroes':
            $allOptions = $characterOptions['fantasy_heroes_80'];
            break;
        case 'pixar_disney':
            $allOptions = $characterOptions['pixar_disney_80'];
            break;
        case 'fairy_tales':
            $allOptions = $characterOptions['fairy_tales_80'];
            break;
        default:
            $allOptions = $characterOptions['animals_80'];
    }
    
    // Query PocketBase to get usage counts for this character type
    $usageCounts = getCharacterUsageCounts($characterType);
    
    // Sort options by usage count (least used first)
    $sortedOptions = sortByUsageCount($allOptions, $usageCounts);
    
    // Use sorted options (least-used characters will be at the front)
    $animals = ($characterType === 'animals') ? $sortedOptions : $characterOptions['animals_80'];
    $fruitsVeggies = ($characterType === 'fruits_vegetables') ? $sortedOptions : $characterOptions['fruits_vegetables_80'];
    $fantasyHeroes = ($characterType === 'fantasy_heroes') ? $sortedOptions : $characterOptions['fantasy_heroes_80'];
    $pixarDisney = ($characterType === 'pixar_disney') ? $sortedOptions : $characterOptions['pixar_disney_80'];
    $fairyTales = ($characterType === 'fairy_tales') ? $sortedOptions : $characterOptions['fairy_tales_80'];
    
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
    
    // Add special instructions for ALL character types
    $specialInstructions = "";
    if ($characterType === 'animals') {
        $specialInstructions = "🦁 EXTRA BELANGRIJK VOOR DIEREN:\n" .
            "- Het dier MOET een realistisch dierenhoofd hebben op een MENSELIJK lichaam\n" .
            "- Staand op TWEE benen, NOOIT op vier poten\n" .
            "- Draagt VOLLEDIGE menselijke kleding (shirt, broek, schoenen)\n" .
            "- ⚠️ BELANGRIJK: Gebruik VARIATIE - kies verschillende dieren uit de lijst!\n" .
            "- Denk aan: leeuw, tijger, beer, olifant, giraffe, panda, wolf, vos, uil, etc.\n\n";
    } elseif ($characterType === 'fruits_vegetables') {
        $specialInstructions = "🥕 EXTRA BELANGRIJK VOOR GROENTE/FRUIT:\n" .
            "- Het fruit/groente MOET gehumaniseerd zijn met:\n" .
            "  * Expressieve ogen (groot, levendig, met emotie)\n" .
            "  * Een mond (kan glimlachen, praten, emoties tonen)\n" .
            "  * Armen (kunnen dingen vasthouden, gebaren maken)\n" .
            "  * Benen (kunnen lopen, dansen, bewegen)\n" .
            "  * Handen en voeten (met vingers/tenen of handschoenen/schoenen)\n" .
            "- Denk aan Pixar-stijl: levendig, expressief, vol persoonlijkheid!\n" .
            "- ⚠️ BELANGRIJK: Gebruik VARIATIE - NIET altijd tomaat!\n" .
            "- Denk aan: banaan, wortel, broccoli, paprika, komkommer, aardbei, etc.\n\n";
    } elseif ($characterType === 'fantasy_heroes') {
        $specialInstructions = "⚔️ EXTRA BELANGRIJK VOOR FANTASY HELDEN:\n" .
            "- Het karakter MOET een MENSELIJK persoon zijn in een kostuum/uitrusting\n" .
            "- GEEN dieren, GEEN eenhoorns, GEEN mythische wezens\n" .
            "- Denk aan: ridder, tovenaar, elf (menselijk), krijger, magiër, boogschutter, etc.\n" .
            "- ⚠️ BELANGRIJK: Gebruik VARIATIE - NIET altijd eenhoorn!\n" .
            "- Het moet een PERSOON zijn die een fantasy rol speelt\n\n";
    } elseif ($characterType === 'pixar_disney') {
        $specialInstructions = "🎬 EXTRA BELANGRIJK VOOR PIXAR/DISNEY:\n" .
            "- Het karakter MOET een menselijk Pixar/Disney-stijl karakter zijn\n" .
            "- Geanimeerde stijl met expressief gezicht\n" .
            "- Draagt moderne, trendy kleding\n" .
            "- ⚠️ BELANGRIJK: Gebruik VARIATIE uit de lijst!\n\n";
    } elseif ($characterType === 'fairy_tales') {
        $specialInstructions = "🧚 EXTRA BELANGRIJK VOOR SPROOKJES:\n" .
            "- Het karakter MOET een MENSELIJK persoon zijn in sprookjeskostuum\n" .
            "- Denk aan: prins, prinses, heks, fee, kabouter (menselijk), etc.\n" .
            "- Professioneel theaterkostuum\n" .
            "- ⚠️ BELANGRIJK: Gebruik VARIATIE uit de lijst!\n\n";
    }
    
    $userPrompt1 = "⚠️ BELANGRIJK: Het karakter MOET een echt dier/fruit/fantasy wezen zijn - GEEN persoon met masker!\n\n" .
        "CHARACTER TYPE: $characterType\n\n" .
        "$typeExample\n\n" .
        $specialInstructions .
        "Creëer een karakter beschrijving in het Nederlands met deze 3 secties:\n\n" .
        "1. KARAKTER (100-150 woorden):\n" .
        "- Begin met: 'De [DIER/FRUIT/HELD NAAM] genaamd [Creatieve Naam]'\n" .
        ($characterType === 'animals' ? 
            "- Bijvoorbeeld: 'De Leeuw genaamd Leo' of 'De Olifant genaamd Ella' of 'De Uil genaamd Oscar'\n" :
            ($characterType === 'fruits_vegetables' ?
                "- Bijvoorbeeld: 'De Banaan genaamd Benny' of 'De Wortel genaamd Wally' of 'De Aardbei genaamd Anna'\n" :
                ($characterType === 'fantasy_heroes' ?
                    "- Bijvoorbeeld: 'De Ridder genaamd Roland' of 'De Tovenaar genaamd Merlin' of 'De Krijger genaamd Kara'\n" :
                    ($characterType === 'pixar_disney' ?
                        "- Bijvoorbeeld: 'De Uitvinder genaamd Edison' of 'De Ontdekkingsreiziger genaamd Dora'\n" :
                        "- Bijvoorbeeld: 'De Prins genaamd Philip' of 'De Heks genaamd Helga' of 'De Fee genaamd Flora'\n")))) .
        "- ⚠️ KIES EEN SPECIFIEK karakter uit de lijst hierboven - gebruik VARIATIE!\n" .
        ($characterType === 'fantasy_heroes' ? "- ⚠️ GEEN eenhoorn, GEEN dieren - alleen MENSELIJKE fantasy karakters!\n" : "") .
        ($characterType === 'animals' ? "- ⚠️ Kies VERSCHILLENDE dieren - niet altijd dezelfde!\n" : "") .
        ($characterType === 'fruits_vegetables' ? "- ⚠️ Kies VERSCHILLENDE groenten/fruit - niet altijd tomaat!\n" : "") .
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
    
    // CALL 2: Ask Claude to generate a professional image prompt
    $imagePrompt = generateImagePromptWithClaude($apiKey, $characterName, $aiSummary, $characterType, $isRegenerate);
    
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
        'image_prompt' => $imagePrompt, // Renamed from image_generation_prompt for consistency
        'api_calls_used' => 2, // 2 Claude API calls: character generation + image prompt
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
