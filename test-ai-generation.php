<?php
/**
 * Test Script for AI Character Generation
 * Run this to test the complete AI pipeline
 */

require_once __DIR__ . '/generate-character.php';

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║   MASKED EMPLOYEE - AI CHARACTER GENERATION TEST        ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Sample test data based on your Questions.JSON structure
$testData = [
    'nameplayer' => 'Test User',
    'gamename' => 'Masked Employee - Test Run',
    
    // Chapter 1: Demographics (multiple choice - indices)
    'chapter01' => [
        '1' => 0,  // Man
        '2' => 1,  // In een relatie
        '3' => 1,  // 1 kind
        '4' => 1,  // 30-40 jaar
        '5' => 2   // 3-5 jaar bij bedrijf
    ],
    
    // Chapter 2: Masked Identity
    'chapter02' => [
        '6' => 'Een wolf, omdat ik loyaal ben maar ook zelfstandig kan opereren. Ik werk graag in een team maar heb ook mijn eigen jachtgebied.',
        '7' => 'Diep middernachtblauw met zilveren accenten, omdat het wijsheid en mysterie uitstraalt maar ook kracht en betrouwbaarheid',
        '8' => 0,  // Vuur - passievol en energiek
        '9' => 'Een masker met sterrenbeelden patronen, met wolven silhouetten en een halve maan, symboliserend de nachtelijke jager',
        '10' => 'Epische orkestmuziek gemengd met elektronische beats - krachtig en mysterieus'
    ],
    
    // Chapter 3: Personality
    'chapter03' => [
        '11' => 'Teleportatie, omdat ik dan overal ter wereld kan zijn voor familie en vrienden, en nooit meer in de file hoef te staan!',
        '12' => 'Ik ben bang voor watjes - echt waar! Die vreemde textuur geeft me kippenvel.',
        '13' => 'Nikola Tesla, omdat ik gefascineerd ben door zijn geniale geest en visie op de toekomst',
        '14' => 'Over het concept van tijd en parallelle universums - fascinerende natuurkunde',
        '15' => 'The Procrastinator\'s Guide to Success',
        '16' => 'Stiekem een tattoo laten zetten zonder het mijn ouders te vertellen toen ik 18 was'
    ],
    
    // Chapter 4: Hidden Talents
    'chapter04' => [
        '17' => 'Ik kan beatboxen op professioneel niveau - geleerd tijdens studie maar doe het nooit op werk',
        '18' => 'Ik verzamel vintage typemachines, heb er nu 15 stuks',
        '19' => 'Ik kan saxofoon spelen, wat niemand verwacht van een IT-consultant',
        '20' => 'Ik was vroeger competitief balletdanser, wat totaal niet bij mijn huidige stoere imago past',
        '21' => 'Aquarelleren bij zonsondergang - super zen en creatief'
    ],
    
    // Chapter 5: Childhood
    'chapter05' => [
        '22' => 'Astronaut! Ik wilde naar de maan en andere planeten ontdekken',
        '23' => 'Natuurkunde en scheikunde - alles wat met experimenten te maken had was geweldig',
        '24' => 'Tijdens schooltoneel mijn tekst vergeten en begonnen te improviseren in rijm - publiek vond het geweldig',
        '25' => 'Pokémon kaarten verzamelen en ruilen op het schoolplein - die nostalgie!',
        '26' => 'Maak je niet druk over wat anderen denken, volg je passie en wees jezelf'
    ],
    
    // Chapter 6: Fantasy & Dreams
    'chapter06' => [
        '27' => 'De wereld van Avatar: The Last Airbender - een perfecte mix van spiritualiteit, avontuur en magie',
        '28' => 'Een eigen muziekstudio met alle instrumenten en opnameapparatuur die ik maar wil',
        '29' => 'De Renaissance periode - zo\'n explosie van kunst, wetenschap en creativiteit',
        '30' => 'Een fusion restaurant genaamd "Elements" waar elk gerecht een element representeert en smaakexplosies combineert',
        '31' => 'IJsland - de noorderlichten, vulkanen, en pure natuur trekken me enorm aan'
    ],
    
    // Chapter 7: Quirks
    'chapter07' => [
        '32' => 'Ik moet altijd met mijn linkervoet als eerste uit bed stappen, anders voelt de dag "verkeerd"',
        '33' => 'Ik tik drie keer op de deur voordat ik binnenkom, voor "geluk"',
        '34' => 'Pindakaas met komkommer op toast - iedereen vindt het raar maar het is heerlijk!',
        '35' => 3,  // Avond/nacht (17+ uur)
        '36' => 'Ik ga naar een lege parkeergarage en schreeuw daar - de echo is therapeutisch'
    ],
    
    // Chapter 8: Unexpected Preferences
    'chapter08' => [
        '37' => 'K-pop! Niemand verwacht dat van mij maar de energie is verslavend',
        '38' => 'The Bachelor - guilty pleasure reality TV voor als ik mijn brein op nul wil',
        '39' => 'Een klein kompas op mijn linkerpols - symboliseert dat ik altijd mijn eigen richting bepaal',
        '40' => 'Diep paars - het combineert de passie van rood met de kalmte van blauw, dat ben ik helemaal'
    ]
];

echo "Test Data Loaded:\n";
echo "- Player: " . $testData['nameplayer'] . "\n";
echo "- Chapters: 8\n";
echo "- Total Answers: " . array_sum(array_map('count', array_filter($testData, 'is_array'))) . "\n\n";

// Check API configuration
echo "Checking API Configuration...\n";
echo "- Freepik API Key: " . (defined('FREEPIK_API_KEY') && !empty(FREEPIK_API_KEY) ? "✓ Configured" : "✗ Missing") . "\n";
echo "- OpenAI API Key: " . (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY) ? "✓ Configured" : "✗ Missing (add to api-keys.php)") . "\n\n";

if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY)) {
    echo "⚠️  WARNING: OpenAI API key not configured!\n";
    echo "   Text generation will fail. Add your OpenAI key to api-keys.php\n";
    echo "   Get one from: https://platform.openai.com/api-keys\n\n";
    echo "   For now, we'll test Freepik image generation only.\n\n";
}

echo "═══════════════════════════════════════════════════════════\n\n";

// Ask user to confirm
echo "Ready to test AI generation? This will:\n";
echo "1. Generate character description (OpenAI GPT-4)\n";
echo "2. Generate environment description (OpenAI GPT-4)\n";
echo "3. Generate props list (OpenAI GPT-4)\n";
echo "4. Generate 3 video story prompts (OpenAI GPT-4)\n";
echo "5. Generate character image (Freepik API)\n";
echo "6. Generate environment image (Freepik API)\n\n";
echo "Estimated cost: ~$0.25 for this test\n\n";
echo "Continue? (y/n): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) != 'y' && trim($line) != 'yes') {
    echo "Test cancelled.\n";
    exit;
}
fclose($handle);

echo "\n═══════════════════════════════════════════════════════════\n";
echo "STARTING AI GENERATION...\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Run the generation
$generator = new CharacterGenerator();
$result = $generator->generateCharacterProfile($testData);

echo "\n═══════════════════════════════════════════════════════════\n";
echo "RESULTS:\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if ($result['success']) {
    echo "✓ SUCCESS! Character generated\n\n";
    
    if ($result['character_description']) {
        echo "📝 CHARACTER DESCRIPTION:\n";
        echo str_repeat("-", 60) . "\n";
        echo wordwrap($result['character_description'], 60) . "\n\n";
    }
    
    if ($result['environment_description']) {
        echo "🌍 ENVIRONMENT:\n";
        echo str_repeat("-", 60) . "\n";
        echo wordwrap($result['environment_description'], 60) . "\n\n";
    }
    
    if ($result['props_list']) {
        echo "🎭 SIGNATURE PROPS:\n";
        echo str_repeat("-", 60) . "\n";
        echo wordwrap($result['props_list'], 60) . "\n\n";
    }
    
    if (!empty($result['video_stories'])) {
        echo "🎬 VIDEO STORY PROMPTS:\n";
        echo str_repeat("-", 60) . "\n";
        foreach ($result['video_stories'] as $story) {
            echo "Level " . $story['level'] . ": " . $story['title'] . "\n";
            echo "Duration: " . $story['duration'] . "\n";
            echo wordwrap($story['prompt'], 60) . "\n\n";
        }
    }
    
    if ($result['character_image_url']) {
        echo "🖼️  CHARACTER IMAGE:\n";
        echo str_repeat("-", 60) . "\n";
        echo $result['character_image_url'] . "\n\n";
    }
    
    if ($result['environment_image_url']) {
        echo "🖼️  ENVIRONMENT IMAGE:\n";
        echo str_repeat("-", 60) . "\n";
        echo $result['environment_image_url'] . "\n\n";
    }
    
} else {
    echo "✗ GENERATION FAILED\n\n";
}

if (!empty($result['errors'])) {
    echo "⚠️  ERRORS ENCOUNTERED:\n";
    echo str_repeat("-", 60) . "\n";
    foreach ($result['errors'] as $error) {
        echo "- " . $error . "\n";
    }
    echo "\n";
}

echo "═══════════════════════════════════════════════════════════\n";
echo "TEST COMPLETE\n";
echo "═══════════════════════════════════════════════════════════\n";

?>
