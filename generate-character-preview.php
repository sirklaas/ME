<?php
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

// Get the posted data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['answers']) || !isset($input['playerName'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$answers = $input['answers'];
$playerName = $input['playerName'];
$language = $input['language'] ?? 'nl';

// OpenAI API configuration
$apiKey = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_API_KEY_HERE';

if ($apiKey === 'YOUR_OPENAI_API_KEY_HERE') {
    // For development: return mock data
    error_log('WARNING: OpenAI API key not configured, returning mock preview');
    echo json_encode([
        'success' => true,
        'preview' => generateMockPreview($playerName, $answers, $language)
    ]);
    exit;
}

// Prepare the prompt for OpenAI
$prompt = buildPreviewPrompt($answers, $playerName, $language);

try {
    // Call OpenAI API
    $response = callOpenAI($apiKey, $prompt);
    
    echo json_encode([
        'success' => true,
        'preview' => $response
    ]);
    
} catch (Exception $e) {
    error_log('OpenAI API Error: ' . $e->getMessage());
    // Fallback to mock on error
    echo json_encode([
        'success' => true,
        'preview' => generateMockPreview($playerName, $answers, $language)
    ]);
}

function buildPreviewPrompt($answers, $playerName, $language) {
    $answerText = '';
    foreach ($answers as $question => $answer) {
        $answerText .= "Q: $question\nA: $answer\n\n";
    }
    
    if ($language === 'nl') {
        return "Je bent een creatieve schrijver die karakterprofielen maakt voor een mysterieuze gameshow.

Op basis van de volgende antwoorden, creëer een KORTE, PAKKENDE character preview (100-150 woorden).

$answerText

Schrijf in HTML format met deze structuur:

<div class=\"character-preview\">
<h3>🎭 [Mysterieuze Character Naam]</h3>
<p>[2-3 zinnen die het karakter introduceren. Gebruik levendige taal, metaforen, en maak het mysterieus. Beschrijf wie/wat ze zijn, hun essentie, en één opvallend kenmerk. Maak het visueel en intrigerend.]</p>
</div>

BELANGRIJK:
- Houd het kort en pakkend
- Gebruik een mysterieuze, creatieve naam
- Maak het visueel en levendig
- Focus op essentie, niet details
- Geen PII (persoonlijk identificeerbare informatie)";
    } else {
        return "You are a creative writer creating character profiles for a mysterious game show.

Based on the following answers, create a SHORT, COMPELLING character preview (100-150 words).

$answerText

Write in HTML format with this structure:

<div class=\"character-preview\">
<h3>🎭 [Mysterious Character Name]</h3>
<p>[2-3 sentences introducing the character. Use vivid language, metaphors, and make it mysterious. Describe who/what they are, their essence, and one striking feature. Make it visual and intriguing.]</p>
</div>

IMPORTANT:
- Keep it short and compelling
- Use a mysterious, creative name
- Make it visual and vivid
- Focus on essence, not details
- No PII (personally identifiable information)";
    }
}

function callOpenAI($apiKey, $prompt) {
    $url = 'https://api.openai.com/v1/chat/completions';
    
    $data = [
        'model' => 'gpt-4',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a creative character profiler for entertainment shows.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.9,
        'max_tokens' => 300
    ];
    
    $ch = curl_init($url);
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
    
    if ($httpCode !== 200) {
        throw new Exception("OpenAI API returned status code $httpCode: $response");
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response from OpenAI API');
    }
    
    return $result['choices'][0]['message']['content'];
}

function generateMockPreview($playerName, $answers, $language) {
    // Generate different random previews for variety
    $variations = [
        'wolf' => [
            'nl' => '<div class="character-preview">
<h3>🎭 De Stille Wolf</h3>
<p>Je bent een fantastisch enthousiaste wolf die rondloopt in de saaie gangen van een kantoor in Birmingham. Overdag ben je gecamoufleerd in corporate grijs, maar je wilde geest huilt onder de oppervlakte. Je leeft voor late-nacht creatieve uitbarstingen en droomt stiekem van vrij rondrennen door maanverlichte bossen.</p>
</div>',
            'en' => '<div class="character-preview">
<h3>🎭 The Silent Wolf</h3>
<p>You are a fantastically enthusiastic wolf prowling the dull corridors of a Birmingham office. By day, you\'re camouflaged in corporate grey, but your wild spirit howls beneath the surface. You live for late-night creative bursts and secretly dream of running free through moonlit forests.</p>
</div>'
        ],
        'phoenix' => [
            'nl' => '<div class="character-preview">
<h3>🎭 De Phoenix van Afdeling 3</h3>
<p>Als een verborgen feniks schitter je tussen de grijze werkplekken. Elke dag hergeboren in een nieuw idee, elk project een transformatie van as naar goud. Jouw vlammen branden stil maar fel, voedend op creativiteit waar anderen alleen routine zien.</p>
</div>',
            'en' => '<div class="character-preview">
<h3>🎭 The Phoenix of Department 3</h3>
<p>Like a hidden phoenix, you shine among the grey workspaces. Every day reborn in a new idea, each project a transformation from ash to gold. Your flames burn quietly but fiercely, feeding on creativity where others see only routine.</p>
</div>'
        ],
        'architect' => [
            'nl' => '<div class="character-preview">
<h3>🎭 De Midnight Architect</h3>
<p>In de stilte van de nacht bouw je werelden waar anderen slechts gebouwen zien. Met een koffiekop als kompas en dromen als blauwdrukken, transformeer je het gewone in het extraordinaire. Jouw kantoor is een portal, jouw laptop een toverstaf.</p>
</div>',
            'en' => '<div class="character-preview">
<h3>🎭 The Midnight Architect</h3>
<p>In the silence of the night, you build worlds where others see only buildings. With a coffee cup as compass and dreams as blueprints, you transform the ordinary into the extraordinary. Your office is a portal, your laptop a magic wand.</p>
</div>'
        ]
    ];
    
    // Randomly select a variation
    $types = array_keys($variations);
    $selectedType = $types[array_rand($types)];
    
    return $variations[$selectedType][$language];
}
?>
