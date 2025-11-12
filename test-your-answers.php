<?php
/**
 * Test with Your Actual Answers
 */

define('MASKED_EMPLOYEE_APP', true);
require_once 'api-keys.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=================================================\n";
echo "🧪 TESTING WITH YOUR ANSWERS\n";
echo "=================================================\n\n";

// Your actual answers
$testAnswers = [
    // Chapter 2: Masked Identity (6-10)
    6 => "Een kameleon - ik pas me makkelijk aan aan verschillende situaties, observeer graag en verander mijn kleuren om beter te verbinden met mijn omgeving",
    7 => "Diep nachtblauw, bijna zwart - geheimzinnig en krachtig, maar ook rust en diepgang uitstralend",
    8 => "Water - kalm, flexibel en altijd in beweging. Kan zacht en meegaand zijn, maar ook krachtig en onstuitbaar",
    9 => "Een combinatie van maan en sterren, met glinsterende zilveren patronen - symboliseert mijn droomwereld en het mysterie dat ik met me meedraag",
    10 => "Een mix van jazz en elektronische muziek, mysterieus en verrassend - swingende ritmes gecombineerd met moderne beats",
    
    // Chapter 3: Persoonlijke Eigenschappen (11-16)
    11 => "Tijd kunnen manipuleren - om momenten te verlengen die ik wil koesteren en fouten terug te draaien. Tijd is de ultieme vrijheid",
    12 => "Dat ik per ongeluk in een wereld terechtkom waar mensen alleen in stilte communiceren en ik mijn stem kwijt ben",
    13 => "Leonardo da Vinci - zijn nieuwsgierigheid, creativiteit en veelzijdigheid fascineren me",
    14 => "De toekomst van technologie en de rol van kunst in een steeds meer digitale samenleving",
    15 => "Schaduwlopen - over de constante zoektocht naar balans tussen zichtbaarheid en anonimiteit. Pay-off: Waar licht en donker elkaar ontmoeten",
    16 => "Een nachtelijke duik nemen in een ijskoud meer midden in de winter, puur uit nieuwsgierigheid en de drang naar avontuur",
    
    // Chapter 4: Verborgen Talenten (17-21)
    17 => "Ik kan met mijn handen ingewikkelde origamikunstwerken maken",
    18 => "Ik heb een ongekend goed oor voor het nadoen van vogelgeluiden",
    19 => "Het verzamelen van oude, vergeten ansichtkaarten uit de jaren '20 en '30",
    20 => "Theremin bespelen - muziek maken zonder het instrument aan te raken past perfect bij mijn mysterieuze kant",
    21 => "Schermen - een sport die niet goed past bij mijn huidige rustige imago, maar die toen mijn adrenaline liet stromen",
    
    // Chapter 5: Jeugd & Verleden (22-26)
    22 => "Het schrijven van korte fantasyverhalen die ik zelf illustreer",
    23 => "Astronaut - de gedachte om de aarde vanuit de ruimte te zien en het onbekende te verkennen vulde me met verwondering",
    24 => "Geschiedenis en tekenen - ze namen me mee op reizen door de tijd en stimuleerden mijn creativiteit",
    25 => "Ik viel per ongeluk tijdens de schoolvoorstelling terwijl ik de hoofdrol speelde - nu kan ik erom lachen",
    26 => "De opkomst van cassettebandjes en het delen van mixtapes - zo persoonlijk en handgemaakt",
    
    // Chapter 6: Fantasie & Dromen (27-31)
    27 => "Meer durven falen zonder bang te zijn - daar leer je uiteindelijk het meest van",
    28 => "De Gouden Kompas - een magische wereld vol avontuur en mysterie die de grenzen van wetenschap en fantasie vervaagt",
    29 => "Direct een ruimtevaartreis boeken om zelf de nieuwe frontiers van ons universum te verkennen",
    30 => "De Renaissance - een tijd van enorme artistieke en wetenschappelijke explosie, waarin de menselijkheid centraal stond",
    31 => "Een fusionrestaurant genaamd Horizonten - waar culturen samenkomen in onverwachte gerechten",
    
    // Chapter 7: Eigenaardigheden (32-36)
    32 => "Ik moet altijd eerst met mijn pink aan mijn oorlel friemelen voordat ik iets belangrijks zeg",
    33 => "Ik mag nooit met mijn linkervoet mijn huis uit stappen als ik die dag iets belangrijks moet doen",
    34 => "Pindakaas met augurk - het zoetzure en het romige samen is onweerstaanbaar",
    35 => "Vroege ochtend (5-8 uur) - wanneer de wereld nog stil is en mijn gedachten helder als glas",
    36 => "Ik dans soms onverwachts salsa in mijn woonkamer - een mix van vrolijkheid en energie die meteen oplucht",
    
    // Chapter 8: Onverwachte Voorkeuren (37-40)
    37 => "Klassieke Indiase raag muziek - iets wat niemand van me verwacht maar me diep ontspant en inspireert",
    38 => "Oude, overdreven soapseries met eindeloze drama's en onwaarschijnlijke plotwendingen",
    39 => "Een fijne gouden veer op mijn pols - symbool voor lichtheid, vrijheid en altijd kunnen opstijgen",
    40 => "Het fictieve eiland Cirquesia - een magische plek vol onontdekte natuur en oude legendes"
];

$playerName = "Kameleon Gebruiker";

echo "👤 Player: $playerName\n";
echo "📝 Total Answers: " . count($testAnswers) . "\n\n";

// Format answers for AI
$formattedAnswers = "";
foreach ($testAnswers as $qId => $answer) {
    $formattedAnswers .= "Vraag $qId: $answer\n\n";
}

echo "📤 Generating Character with OpenAI...\n\n";

// Create comprehensive prompt
$systemPrompt = "Je bent een creatieve character designer voor de gameshow 'The Masked Employee'. Creëer unieke gemaskerde karakters gebaseerd op de antwoorden van spelers. Wees creatief, mysterieus en verrassend.";

$userPrompt = "Speler: $playerName\n\nAntwoorden:\n$formattedAnswers\n\nCreëer een compleet karakter:\n\n1. CHARACTER NAME: Een mysterieuze, creatieve naam (Nederlands)\n2. CHARACTER TYPE: Kies uit: kameleon, water-wezen, of maan-figuur\n3. PERSONALITY TRAITS: 3-4 zinnen over de persoonlijkheid\n4. AI SUMMARY: Een samenvatting van 4-5 zinnen\n5. STORY PROMPT 1 (subtiel): Een mysterieuze scene voor video 1\n6. STORY PROMPT 2 (meer hints): Een scene met meer aanwijzingen voor video 2\n7. STORY PROMPT 3 (reveal): De finale onthullende scene voor video 3\n8. IMAGE PROMPT: Een gedetailleerde beschrijving voor AI image generation (in het Engels)";

$data = [
    'model' => OPENAI_MODEL,
    'messages' => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => $userPrompt]
    ],
    'max_tokens' => 1000,
    'temperature' => 0.85
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
curl_close($ch);

echo "HTTP Response: $httpCode\n\n";

if ($httpCode === 200) {
    $result = json_decode($response, true);
    $aiResponse = $result['choices'][0]['message']['content'];
    
    echo "=================================================\n";
    echo "✅ JOUW KARAKTER IS GEGENEREERD!\n";
    echo "=================================================\n\n";
    echo $aiResponse . "\n\n";
    echo "=================================================\n\n";
    
    echo "📊 Token Usage:\n";
    echo "- Input: " . $result['usage']['prompt_tokens'] . " tokens\n";
    echo "- Output: " . $result['usage']['completion_tokens'] . " tokens\n";
    echo "- Total: " . $result['usage']['total_tokens'] . " tokens\n";
    echo "- Cost: ~$" . number_format(($result['usage']['total_tokens'] / 1000) * 0.03, 4) . "\n\n";
    
    echo "✅ Character generation SUCCESS!\n";
    echo "✅ Ready for PocketBase!\n";
    
} else {
    echo "❌ OpenAI API Error\n\n";
    $errorResult = json_decode($response, true);
    echo json_encode($errorResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
