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
// IMPORTANT: Store your API key in environment variable or secure config file
$apiKey = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_API_KEY_HERE';

if ($apiKey === 'YOUR_OPENAI_API_KEY_HERE') {
    // For development: return mock data
    error_log('WARNING: OpenAI API key not configured, returning mock data');
    echo json_encode([
        'success' => true,
        'summary' => generateMockSummary($playerName, $answers, $language)
    ]);
    exit;
}

// Prepare the prompt for OpenAI
$prompt = buildPrompt($answers, $playerName, $language);

try {
    // Call OpenAI API
    $response = callOpenAI($apiKey, $prompt);
    
    echo json_encode([
        'success' => true,
        'summary' => $response
    ]);
    
} catch (Exception $e) {
    error_log('OpenAI API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to generate summary',
        'message' => $e->getMessage()
    ]);
}

function buildPrompt($answers, $playerName, $language) {
    $answerText = '';
    foreach ($answers as $question => $answer) {
        $answerText .= "Q: $question\nA: $answer\n\n";
    }
    
    if ($language === 'nl') {
        return "Je bent een creatieve schrijver die karakterprofielen maakt voor een mysterieuze gameshow genaamd 'The Masked Employee'.

Speler naam: $playerName

Op basis van de volgende antwoorden op 40 vragen, genereer een COMPLEET karakter profiel met ALLE onderstaande secties:

$answerText

Creëer een uitgebreid profiel in HTML format met EXACT deze structuur:

<div class=\"character-section\">
<h3>🎭 Karakter Persona</h3>
<p><strong>Karakter Naam:</strong> [Verzin een mysterieuze, creatieve fantasienaam - bijv. \"De Midnight Gardener\", \"The Silent Architect\"]</p>
<p><strong>Beschrijving:</strong> [150-250 woorden] Een levendige, mysterieuze beschrijving van dit fantasie karakter. Vermeld uiterlijk, persoonlijkheid, backstory. Maak het anoniem - geen echte identiteit. Gebruik beeldende taal en mysterie.</p>
</div>

<div class=\"environment-section\">
<h3>🌍 Kenmerkende Omgeving</h3>
<p>[100-150 woorden] Beschrijf de signature locatie waar dit karakter zich bevindt. Inclusief sensorische details (geur, geluid, licht), sfeer, kleuren, mood. Maak het levendig en atmosferisch.</p>
</div>

<div class=\"props-section\">
<h3>✨ Signature Props</h3>
<ul>
<li><strong>[Prop 1]:</strong> [Korte symbolische betekenis]</li>
<li><strong>[Prop 2]:</strong> [Korte symbolische betekenis]</li>
<li><strong>[Prop 3]:</strong> [Korte symbolische betekenis]</li>
<li><strong>[Prop 4]:</strong> [Korte symbolische betekenis]</li>
</ul>
</div>

<div class=\"story-prompts-section\">
<h3>🎬 Video Story Prompts</h3>

<div class=\"story-level-1\">
<h4>Level 1: Oppervlakte Verhaal (30-60 sec)</h4>
<p><strong>Prompt:</strong> \"[Pakkende opening hook]. Vertel het verhaal over [specifiek aspect uit hun antwoorden over prestaties/bekende feiten]. Zorg dat je [belangrijke elementen] behandelt. Sluit af met [emotionele resonantie of les].\"</p>
</div>

<div class=\"story-level-2\">
<h4>Level 2: Verborgen Dieptes (60-90 sec)</h4>
<p><strong>Prompt:</strong> \"[Intrigerende opening]. Onthul [verrassend feit of verborgen talent uit hun antwoorden]. Leg uit hoe [ontdekking/ontwikkeling]. Eindig met [onverwachte twist of inzicht].\"</p>
</div>

<div class=\"story-level-3\">
<h4>Level 3: Diepste Geheimen (90-120 sec)</h4>
<p><strong>Prompt:</strong> \"[Kwetsbare opening]. Deel het verhaal over [transformerende ervaring uit hun antwoorden]. Beschrijf [emotionele reis en impact]. Sluit af met [persoonlijke groei of life lesson].\"</p>
</div>
</div>

BELANGRIJK: 
- Gebruik ALLE HTML tags exact zoals aangegeven
- Vul ALLE secties in
- Maak het creatief, mysterieus en entertainend
- Geen PII (personally identifiable information)
- Gebruik emoji's zoals aangegeven";
    } else {
        return "You are a creative writer creating character profiles for a mysterious game show called 'The Masked Employee'.

Player name: $playerName

Based on the following answers to 40 questions, generate a COMPLETE character profile with ALL sections below:

$answerText

Create a comprehensive profile in HTML format with EXACTLY this structure:

<div class=\"character-section\">
<h3>🎭 Character Persona</h3>
<p><strong>Character Name:</strong> [Create a mysterious, creative fantasy name - e.g., \"The Midnight Gardener\", \"The Silent Architect\"]</p>
<p><strong>Description:</strong> [150-250 words] A vivid, mysterious description of this fantasy character. Include appearance, personality, backstory. Keep it anonymous - no real identity. Use vivid imagery and mystery.</p>
</div>

<div class=\"environment-section\">
<h3>🌍 Signature Environment</h3>
<p>[100-150 words] Describe the signature location where this character is found. Include sensory details (scent, sound, light), atmosphere, colors, mood. Make it vivid and atmospheric.</p>
</div>

<div class=\"props-section\">
<h3>✨ Signature Props</h3>
<ul>
<li><strong>[Prop 1]:</strong> [Brief symbolic meaning]</li>
<li><strong>[Prop 2]:</strong> [Brief symbolic meaning]</li>
<li><strong>[Prop 3]:</strong> [Brief symbolic meaning]</li>
<li><strong>[Prop 4]:</strong> [Brief symbolic meaning]</li>
</ul>
</div>

<div class=\"story-prompts-section\">
<h3>🎬 Video Story Prompts</h3>

<div class=\"story-level-1\">
<h4>Level 1: Surface Story (30-60 sec)</h4>
<p><strong>Prompt:</strong> \"[Compelling opening hook]. Tell the story about [specific aspect from their answers about achievements/known facts]. Make sure to cover [key elements]. End with [emotional resonance or lesson].\"</p>
</div>

<div class=\"story-level-2\">
<h4>Level 2: Hidden Depths (60-90 sec)</h4>
<p><strong>Prompt:</strong> \"[Intriguing opening]. Reveal [surprising fact or hidden talent from their answers]. Explain how [discovery/development]. End with [unexpected twist or insight].\"</p>
</div>

<div class=\"story-level-3\">
<h4>Level 3: Deepest Secrets (90-120 sec)</h4>
<p><strong>Prompt:</strong> \"[Vulnerable opening]. Share the story about [transformative experience from their answers]. Describe [emotional journey and impact]. Close with [personal growth or life lesson].\"</p>
</div>
</div>

IMPORTANT: 
- Use ALL HTML tags exactly as shown
- Fill in ALL sections
- Make it creative, mysterious and entertaining
- No PII (personally identifiable information)
- Use emojis as indicated";
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
        'temperature' => 0.8,
        'max_tokens' => 1000
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

function generateMockSummary($playerName, $answers, $language) {
    if ($language === 'nl') {
        return '
<div class="character-section">
<h3>🎭 Karakter Persona</h3>
<p><strong>Karakter Naam:</strong> De Stille Innovator</p>
<p><strong>Beschrijving:</strong> Een mysterieuze figuur die werkt in de schaduwen van creativiteit, waar anderen slechts oppervlakkige patronen zien. De Stille Innovator draagt een donkere cape met subtiele lichtgevende patronen die alleen zichtbaar zijn in het donker, symboliserend verborgen diepte en onverwachte helderheid. Met een kalme maar indringende blik en een verzameling vintage instrumenten, transformeert dit karakter alledaagse momenten in buitengewone verhalen. Bekend om zachte woorden die grote impact hebben, en om oplossingen te vinden waar anderen alleen problemen zien.</p>
</div>

<div class="environment-section">
<h3>🌍 Kenmerkende Omgeving</h3>
<p>Een verlicht atelier op zolder waar creativiteit en technologie samenkomen. Houten balken kruisen het plafond, met tussen de balken slingers van vintage lampen die een warm, gouden licht werpen. Overal liggen notitieboeken met schetsen en ideeën, naast moderne tablets en apparatuur. Door de grote daklichten stroomt natuurlijk licht naar binnen, terwijl zachte jazz-muziek op de achtergrond speelt. De geur van vers gezette koffie en oude boeken hangt in de lucht, met hier en daar een vleugje van avontuur en mogelijkheden.</p>
</div>

<div class="props-section">
<h3>✨ Signature Props</h3>
<ul>
<li><strong>Vintage Lederen Notitieboek:</strong> Gevuld met hand-getekende mindmaps en dromen die werkelijkheid werden</li>
<li><strong>Bronzen Kompas:</strong> Wijst niet naar het noorden, maar naar nieuwe mogelijkheden en onontdekte paden</li>
<li><strong>Collectie Oude Sleutels:</strong> Elke sleutel representeert een opgelost mysterie of ontgrendelde potentie</li>
<li><strong>Draadloze Koptelefoon met Vintage Aesthetic:</strong> Brug tussen moderne technologie en tijdloze wijsheid</li>
</ul>
</div>

<div class="story-prompts-section">
<h3>🎬 Video Story Prompts</h3>

<div class="story-level-1">
<h4>Level 1: Oppervlakte Verhaal (30-60 sec)</h4>
<p><strong>Prompt:</strong> "Als De Stille Innovator, vertel over het moment dat je een project realiseerde dat niemand voor mogelijk hield. Wat was het idee, welke obstakels overwon je, en wat was de reactie toen het succesvol was? Eindig met wat deze ervaring je leerde over doorzettingsvermogen."</p>
</div>

<div class="story-level-2">
<h4>Level 2: Verborgen Dieptes (60-90 sec)</h4>
<p><strong>Prompt:</strong> "Onthul een verborgen talent of passie die je collega\'s nooit hebben gezien. Hoe ontdekte je dit talent? Waarom houd je het verborgen in je professionele leven? Deel een moment waarop deze verborgen kant van jezelf bijna aan het licht kwam. Eindig met waarom deze dualiteit belangrijk voor je is."</p>
</div>

<div class="story-level-3">
<h4>Level 3: Diepste Geheimen (90-120 sec)</h4>
<p><strong>Prompt:</strong> "Vertel het verhaal van het moment dat je leven fundamenteel veranderde - een moment van verlies, transformatie, of onverwachte helderheid. Beschrijf hoe je je voelde voor dat moment, wat er gebeurde, en wie je daarna werd. Deel de kwetsbaarheid en kracht die je uit die ervaring haalde. Sluit af met de levensles die je nog steeds elke dag gebruikt."</p>
</div>
</div>

<p style="margin-top: 30px;"><em>Deze samenvatting is gegenereerd op basis van je ' . count($answers) . ' antwoorden. Dit is een voorbeeld - met OpenAI API krijg je een volledig gepersonaliseerd profiel.</em></p>';
    } else {
        return '
<div class="character-section">
<h3>🎭 Character Persona</h3>
<p><strong>Character Name:</strong> The Silent Innovator</p>
<p><strong>Description:</strong> A mysterious figure who works in the shadows of creativity, where others see only surface patterns. The Silent Innovator wears a dark cape with subtle luminescent patterns only visible in darkness, symbolizing hidden depth and unexpected clarity. With a calm but penetrating gaze and a collection of vintage instruments, this character transforms everyday moments into extraordinary stories. Known for quiet words that carry great impact, and for finding solutions where others see only problems.</p>
</div>

<div class="environment-section">
<h3>🌍 Signature Environment</h3>
<p>An illuminated attic studio where creativity and technology converge. Wooden beams cross the ceiling, with strings of vintage lamps between them casting warm, golden light. Notebooks with sketches and ideas lie everywhere, next to modern tablets and equipment. Natural light streams through large skylights, while soft jazz music plays in the background. The scent of freshly brewed coffee and old books hangs in the air, with hints of adventure and possibilities.</p>
</div>

<div class="props-section">
<h3>✨ Signature Props</h3>
<ul>
<li><strong>Vintage Leather Notebook:</strong> Filled with hand-drawn mind maps and dreams that became reality</li>
<li><strong>Bronze Compass:</strong> Points not to north, but to new possibilities and undiscovered paths</li>
<li><strong>Collection of Old Keys:</strong> Each key represents a solved mystery or unlocked potential</li>
<li><strong>Wireless Headphones with Vintage Aesthetic:</strong> Bridge between modern technology and timeless wisdom</li>
</ul>
</div>

<div class="story-prompts-section">
<h3>🎬 Video Story Prompts</h3>

<div class="story-level-1">
<h4>Level 1: Surface Story (30-60 sec)</h4>
<p><strong>Prompt:</strong> "As The Silent Innovator, tell about the moment you realized a project that no one thought possible. What was the idea, which obstacles did you overcome, and what was the reaction when it succeeded? End with what this experience taught you about perseverance."</p>
</div>

<div class="story-level-2">
<h4>Level 2: Hidden Depths (60-90 sec)</h4>
<p><strong>Prompt:</strong> "Reveal a hidden talent or passion your colleagues have never seen. How did you discover this talent? Why do you keep it hidden in your professional life? Share a moment when this hidden side of you almost came to light. End with why this duality is important to you."</p>
</div>

<div class="story-level-3">
<h4>Level 3: Deepest Secrets (90-120 sec)</h4>
<p><strong>Prompt:</strong> "Tell the story of the moment your life fundamentally changed - a moment of loss, transformation, or unexpected clarity. Describe how you felt before that moment, what happened, and who you became afterwards. Share the vulnerability and strength you drew from that experience. Close with the life lesson you still use every day."</p>
</div>
</div>

<p style="margin-top: 30px;"><em>This summary was generated based on your ' . count($answers) . ' answers. This is an example - with OpenAI API you\'ll get a fully personalized profile.</em></p>';
    }
}
