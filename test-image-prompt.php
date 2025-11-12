<?php
// Test what prompt is being generated
define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/generate-character.php';

// Test data
$characterName = "Cirquesia";
$characterType = "fantasy_heroes";
$aiSummary = "1. KARAKTER (100-150 woorden):
De magiër genaamd Cirquesia is gehuld in een diep robijnrode mantel met daarop glinsterende zilveren patronen van manen en sterren. Deze zorgvuldige keuze van kleding weerspiegelt haar geheimzinnige en krachtige persoonlijkheid, maar straalt ook een diepe rust en een sterk gevoel van diepgang uit. Ze is een adaptieve observator, die haar kleuren kan veranderen om zich beter te verbinden met haar omgeving. Ze draagt altijd een fijne gouden veer om haar pols, een symbool van lichtheid, vrijheid en de mogelijkheid om altijd op te stijgen.

2. OMGEVING (50-75 woorden):
Cirquesia is vaak te vinden in haar torentje, een plek vol mysterie en magie die de grenzen van wetenschap en fantasie vervaagt. De muren zijn bekleed met oude, vergeten ansichtkaarten uit de jaren '20 en '30, en het geluid van zachte, klassieke Indiase raag muziek vult altijd de lucht.

3. PROPS (3-5 items):
- Gouden veer: symbool van vrijheid
- Toverstaf: voor magie
- Oude boeken: vol kennis";

$prompt = generateImagePrompt($characterName, $aiSummary, $characterType);

header('Content-Type: text/plain');
echo "=== IMAGE GENERATION PROMPT ===\n\n";
echo $prompt;
echo "\n\n=== PROMPT LENGTH: " . strlen($prompt) . " chars ===";
