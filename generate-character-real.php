<?php
/**
 * Real AI Character Generation - Complete Flow
 * Step 1: Generate character + world description with OpenAI
 * Step 2: Player approves/regenerates
 * Step 3: Save to PB and send email with descriptions
 * Step 4: Generate image prompt
 * Step 5: Generate image with Freepik
 * Step 6: Save image to PB and email to player
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Load API keys
define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';

// Set debug mode if not defined
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}

// Get the posted data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

$step = $input['step'] ?? 'generate_description'; // generate_description | generate_image
$playerName = $input['playerName'] ?? '';
$language = $input['language'] ?? 'nl';

// Validate based on step
if ($step === 'generate_description') {
    // Need answers for description generation
    if (!isset($input['answers']) || !$playerName) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields for description generation']);
        exit;
    }
    $answers = $input['answers'];
} elseif ($step === 'generate_image') {
    // Need descriptions for image generation
    if (!isset($input['character_description']) || !isset($input['world_description'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields for image generation']);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid step parameter']);
    exit;
}

// OpenAI API configuration from api-keys.php
$apiKey = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '';

if (empty($apiKey) || $apiKey === 'YOUR_OPENAI_API_KEY_HERE') {
    // For development: return mock data
    error_log('WARNING: OpenAI API key not configured, returning mock data');
    echo json_encode([
        'success' => true,
        'character_description' => generateMockDescription($playerName, $language, 'character'),
        'world_description' => generateMockDescription($playerName, $language, 'world')
    ]);
    exit;
}

try {
    if ($step === 'generate_description') {
        // STEP 1: Generate Character + World Description
        $descriptions = generateDescriptions($apiKey, $answers, $playerName, $language);
        
        echo json_encode([
            'success' => true,
            'character_description' => $descriptions['character'],
            'world_description' => $descriptions['world']
        ]);
        
    } elseif ($step === 'generate_image') {
        // STEP 4 & 5: Generate image prompt and create image
        $characterDesc = $input['character_description'] ?? '';
        $worldDesc = $input['world_description'] ?? '';
        
        error_log('=== IMAGE GENERATION START ===');
        error_log('Character desc length: ' . strlen($characterDesc));
        error_log('World desc length: ' . strlen($worldDesc));
        
        if (empty($characterDesc) || empty($worldDesc)) {
            throw new Exception('Missing character or world description for image generation');
        }
        
        // Generate image prompt
        error_log('Generating image prompt with OpenAI...');
        $imagePrompt = generateImagePrompt($apiKey, $characterDesc, $worldDesc, $language);
        error_log('Image prompt generated: ' . substr($imagePrompt, 0, 100) . '...');
        
        // Generate image using Freepik
        error_log('Loading Freepik API...');
        require_once 'freepik-api.php';
        $freepik = new FreepikAPI();
        
        error_log('Calling Freepik to generate image...');
        $imageResult = $freepik->generateCharacterImage($imagePrompt);
        
        error_log('Freepik response received, success: ' . ($imageResult['success'] ? 'YES' : 'NO'));
        
        if (!$imageResult['success']) {
            error_log('❌ Image generation failed: ' . $imageResult['error']);
            throw new Exception('Image generation failed: ' . $imageResult['error']);
        }
        
        // Check if we have image data
        if (empty($imageResult['image_data']) && empty($imageResult['image_binary']) && empty($imageResult['image_url'])) {
            error_log('❌ No image data returned from Freepik');
            error_log('Full Freepik result: ' . print_r($imageResult, true));
            throw new Exception('Freepik returned no image data. Check API response format.');
        }
        
        // Return the base64 image data for client-side upload to PocketBase
        error_log('✅ Image generated successfully, returning base64 data');
        error_log('Has image_data: ' . (isset($imageResult['image_data']) ? 'YES' : 'NO'));
        error_log('Has image_binary: ' . (isset($imageResult['image_binary']) ? 'YES' : 'NO'));
        error_log('Has image_url: ' . (isset($imageResult['image_url']) ? 'YES' : 'NO'));
        
        echo json_encode([
            'success' => true,
            'image_data' => $imageResult['image_data'] ?? null, // Base64 encoded
            'image_binary' => base64_encode($imageResult['image_binary'] ?? ''), // Double encoded for JSON
            'image_url' => $imageResult['image_url'] ?? null, // May be null if using base64
            'image_prompt' => $imagePrompt
        ]);
        
    } else {
        throw new Exception('Invalid step parameter');
    }
    
} catch (Exception $e) {
    error_log('Character generation error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Generation failed',
        'message' => $e->getMessage(),
        'details' => DEBUG_MODE ? $e->getTraceAsString() : null
    ]);
}

/**
 * Generate character and world descriptions using OpenAI
 */
function generateDescriptions($apiKey, $answers, $playerName, $language) {
    $answerText = '';
    foreach ($answers as $question => $answer) {
        $answerText .= "Q: $question\nA: $answer\n\n";
    }
    
    $prompt = buildDescriptionPrompt($answerText, $playerName, $language);
    
    $response = callOpenAI($apiKey, $prompt, 0.9, 600);
    
    // Parse response to extract character and world descriptions
    return parseDescriptions($response, $language);
}

/**
 * Build prompt for character + world description
 */
function buildDescriptionPrompt($answerText, $playerName, $language) {
    if ($language === 'nl') {
        return "Je bent een creatieve schrijver die karakterprofielen maakt voor een mysterieuze gameshow genaamd 'The Masked Employee'.

Op basis van de volgende antwoorden, creëer een VOLLEDIG karakter met TWEE delen:

$answerText

Genereer een JSON response met EXACT deze structuur:

{
  \"character_description\": \"[150-200 woorden] Een levendige, mysterieuze beschrijving van dit fantasie karakter. Geef het karakter een unieke, creatieve naam (bijv. 'De Midnight Gardener', 'De Stille Wolf'). Beschrijf uiterlijk, persoonlijkheid, kenmerkende eigenschappen. Maak het visueel en intrigerend. GEEN echte identiteit.\",
  \"world_description\": \"[100-150 woorden] Beschrijf de kenmerkende wereld/omgeving waar dit karakter leeft. Inclusief atmosfeer, kleuren, geuren, geluiden, belichting. Maak het sensorisch en levendig. Dit is waar het karakter zich thuis voelt.\"
}

BELANGRIJK:
- Output MOET valid JSON zijn
- Gebruik creatieve, mysterieuze taal
- Maak het visueel en cinematografisch
- Geen PII (persoonlijk identificeerbare informatie)
- Gebruik levendige metaforen en beeldende taal";
    } else {
        return "You are a creative writer creating character profiles for a mysterious game show called 'The Masked Employee'.

Based on the following answers, create a COMPLETE character with TWO parts:

$answerText

Generate a JSON response with EXACTLY this structure:

{
  \"character_description\": \"[150-200 words] A vivid, mysterious description of this fantasy character. Give the character a unique, creative name (e.g., 'The Midnight Gardener', 'The Silent Wolf'). Describe appearance, personality, defining traits. Make it visual and intriguing. NO real identity.\",
  \"world_description\": \"[100-150 words] Describe the signature world/environment where this character lives. Include atmosphere, colors, scents, sounds, lighting. Make it sensory and vivid. This is where the character feels at home.\"
}

IMPORTANT:
- Output MUST be valid JSON
- Use creative, mysterious language
- Make it visual and cinematic
- No PII (personally identifiable information)
- Use vivid metaphors and descriptive language";
    }
}

/**
 * Parse OpenAI response to extract descriptions
 */
function parseDescriptions($response, $language) {
    // Try to parse as JSON first
    $decoded = json_decode($response, true);
    
    if ($decoded && isset($decoded['character_description']) && isset($decoded['world_description'])) {
        return [
            'character' => $decoded['character_description'],
            'world' => $decoded['world_description']
        ];
    }
    
    // Fallback: try to extract from text
    $characterMatch = preg_match('/character_description["\']?\s*:\s*["\'](.+?)["\']/', $response, $charMatches);
    $worldMatch = preg_match('/world_description["\']?\s*:\s*["\'](.+?)["\']/', $response, $worldMatches);
    
    if ($characterMatch && $worldMatch) {
        return [
            'character' => $charMatches[1],
            'world' => $worldMatches[1]
        ];
    }
    
    // Last resort: return mock
    return [
        'character' => generateMockDescription('Player', $language, 'character'),
        'world' => generateMockDescription('Player', $language, 'world')
    ];
}

/**
 * Generate image prompt from character + world descriptions
 */
function generateImagePrompt($apiKey, $characterDesc, $worldDesc, $language) {
    $prompt = "Based on these descriptions, create a concise image generation prompt:

CHARACTER: $characterDesc

WORLD: $worldDesc

Create a single paragraph prompt (max 150 words) for an AI image generator. Focus on:
- Visual appearance of the character
- Environment and setting
- Lighting and atmosphere
- Colors and mood
- Composition

Make it cinematic, dramatic, and visually striking. Professional quality, 4K, detailed.";

    $response = callOpenAI($apiKey, $prompt, 0.7, 200);
    
    return trim($response);
}

/**
 * Call OpenAI API
 */
function callOpenAI($apiKey, $prompt, $temperature = 0.8, $maxTokens = 500) {
    $url = 'https://api.openai.com/v1/chat/completions';
    
    $data = [
        'model' => 'gpt-4',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a creative character profiler and world builder for entertainment shows.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => $temperature,
        'max_tokens' => $maxTokens
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception("OpenAI API returned status code $httpCode: $response");
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response from OpenAI API');
    }
    
    return $result['choices'][0]['message']['content'];
}

/**
 * Generate mock descriptions for testing
 */
function generateMockDescription($playerName, $language, $type) {
    if ($type === 'character') {
        if ($language === 'nl') {
            return "🎭 De Stille Wolf\n\nJe bent een fantastisch enthousiaste wolf die rondloopt in de saaie gangen van een kantoor in Birmingham. Overdag ben je gecamoufleerd in corporate grijs, maar je wilde geest huilt onder de oppervlakte. Met scherpe, intelligente ogen die alles observeren en een verzameling vintage instrumenten, transformeer je alledaagse momenten in buitengewone verhalen. Je draagt een donkere cape met subtiele lichtgevende patronen die alleen zichtbaar zijn in het donker. Bekend om zachte woorden die grote impact hebben, en om oplossingen te vinden waar anderen alleen problemen zien. Je leeft voor late-nacht creatieve uitbarstingen en droomt stiekem van vrij rondrennen door maanverlichte bossen.";
        } else {
            return "🎭 The Silent Wolf\n\nYou are a fantastically enthusiastic wolf prowling the dull corridors of a Birmingham office. By day, you're camouflaged in corporate grey, but your wild spirit howls beneath the surface. With sharp, intelligent eyes that observe everything and a collection of vintage instruments, you transform everyday moments into extraordinary stories. You wear a dark cape with subtle luminescent patterns only visible in darkness. Known for quiet words that carry great impact, and for finding solutions where others see only problems. You live for late-night creative bursts and secretly dream of running free through moonlit forests.";
        }
    } else { // world
        if ($language === 'nl') {
            return "🌍 Een verlicht atelier op zolder waar creativiteit en technologie samenkomen. Houten balken kruisen het plafond, met tussen de balken slingers van vintage lampen die een warm, gouden licht werpen. Door de grote daklichten stroomt maanlicht naar binnen, terwijl zachte jazz-muziek op de achtergrond speelt. De geur van vers gezette koffie en oude boeken hangt in de lucht, met hier en daar een vleugje van avontuur en mogelijkheden. Overal liggen notitieboeken met schetsen en ideeën, naast moderne tablets. De atmosfeer is zowel rustgevend als energiserend - een plek waar dag en nacht samenkomen.";
        } else {
            return "🌍 An illuminated attic studio where creativity and technology converge. Wooden beams cross the ceiling, with strings of vintage lamps between them casting warm, golden light. Moonlight streams through large skylights, while soft jazz music plays in the background. The scent of freshly brewed coffee and old books hangs in the air, with hints of adventure and possibilities. Notebooks with sketches and ideas lie everywhere, next to modern tablets. The atmosphere is both calming and energizing - a place where day and night come together.";
        }
    }
}
?>
