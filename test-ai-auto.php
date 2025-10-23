<?php
/**
 * Auto-Run Test Script for AI Character Generation
 * No user interaction required
 */

require_once __DIR__ . '/generate-character.php';

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║   MASKED EMPLOYEE - AI CHARACTER GENERATION TEST        ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Sample test data
$testData = [
    'nameplayer' => 'Test User',
    'gamename' => 'Masked Employee - Test Run',
    
    'chapter01' => [
        '1' => 0, '2' => 1, '3' => 1, '4' => 1, '5' => 2
    ],
    
    'chapter02' => [
        '6' => 'Een wolf, omdat ik loyaal ben maar ook zelfstandig kan opereren.',
        '7' => 'Diep middernachtblauw met zilveren accenten',
        '8' => 0,
        '9' => 'Een masker met sterrenbeelden patronen en wolven silhouetten',
        '10' => 'Epische orkestmuziek gemengd met elektronische beats'
    ],
    
    'chapter03' => [
        '11' => 'Teleportatie, omdat ik overal kan zijn voor familie en vrienden',
        '12' => 'Ik ben bang voor watjes - die vreemde textuur geeft me kippenvel',
        '13' => 'Nikola Tesla, vanwege zijn geniale geest',
        '14' => 'Het concept van tijd en parallelle universums',
        '15' => 'The Procrastinator\'s Guide to Success',
        '16' => 'Stiekem een tattoo laten zetten zonder het mijn ouders te vertellen'
    ],
    
    'chapter04' => [
        '17' => 'Ik kan beatboxen op professioneel niveau',
        '18' => 'Ik verzamel vintage typemachines, heb er nu 15',
        '19' => 'Ik kan saxofoon spelen',
        '20' => 'Ik was vroeger competitief balletdanser',
        '21' => 'Aquarelleren bij zonsondergang'
    ],
    
    'chapter05' => [
        '22' => 'Astronaut! Ik wilde naar de maan',
        '23' => 'Natuurkunde en scheikunde',
        '24' => 'Tijdens schooltoneel mijn tekst vergeten en in rijm improviseren',
        '25' => 'Pokémon kaarten verzamelen',
        '26' => 'Volg je passie en wees jezelf'
    ],
    
    'chapter06' => [
        '27' => 'Avatar: The Last Airbender wereld',
        '28' => 'Een eigen muziekstudio met alle instrumenten',
        '29' => 'De Renaissance periode',
        '30' => 'Fusion restaurant "Elements"',
        '31' => 'IJsland - noorderlichten en vulkanen'
    ],
    
    'chapter07' => [
        '32' => 'Linkervoet als eerste uit bed',
        '33' => 'Drie keer op de deur tikken voor geluk',
        '34' => 'Pindakaas met komkommer op toast',
        '35' => 3,
        '36' => 'Schreeuwen in lege parkeergarage'
    ],
    
    'chapter08' => [
        '37' => 'K-pop! Niemand verwacht dat van mij',
        '38' => 'The Bachelor guilty pleasure',
        '39' => 'Klein kompas op linkerpols',
        '40' => 'Diep paars - combineert passie en kalmte'
    ]
];

echo "Test Data Loaded ✓\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Check API configuration
echo "API Configuration:\n";
echo "- Freepik API: " . (defined('FREEPIK_API_KEY') && !empty(FREEPIK_API_KEY) ? "✓ Ready" : "✗ Missing") . "\n";
echo "- OpenAI API: " . (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY) ? "✓ Ready" : "✗ Missing") . "\n\n";

if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY)) {
    echo "ERROR: OpenAI API key not configured!\n";
    exit(1);
}

echo "Starting AI generation (estimated cost: ~$0.25)...\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Run generation
$generator = new CharacterGenerator();
$result = $generator->generateCharacterProfile($testData);

echo "\n═══════════════════════════════════════════════════════════\n";
echo "RESULTS:\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if ($result['success']) {
    echo "✓ SUCCESS!\n\n";
    
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
        echo "🎭 PROPS:\n";
        echo str_repeat("-", 60) . "\n";
        echo wordwrap($result['props_list'], 60) . "\n\n";
    }
    
    if (!empty($result['video_stories'])) {
        echo "🎬 VIDEO STORIES (" . count($result['video_stories']) . "):\n";
        echo str_repeat("-", 60) . "\n";
        foreach ($result['video_stories'] as $story) {
            echo "Level " . $story['level'] . ": " . $story['title'] . "\n";
            echo wordwrap($story['prompt'], 60) . "\n\n";
        }
    }
    
    if ($result['character_image_url']) {
        echo "🖼️  CHARACTER IMAGE: " . $result['character_image_url'] . "\n\n";
    }
    
    if ($result['environment_image_url']) {
        echo "🖼️  ENVIRONMENT IMAGE: " . $result['environment_image_url'] . "\n\n";
    }
    
} else {
    echo "✗ FAILED\n\n";
}

if (!empty($result['errors'])) {
    echo "⚠️  ERRORS:\n";
    foreach ($result['errors'] as $error) {
        echo "- " . $error . "\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════\n";
echo "TEST COMPLETE\n";
echo "═══════════════════════════════════════════════════════════\n";

?>
