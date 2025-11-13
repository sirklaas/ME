<?php
/**
 * Check what image URLs are stored in PocketBase
 * Diagnostic script to see the format of image URLs
 */

// PocketBase configuration
$pbUrl = 'https://pinkmilk.pockethost.io';
$pbCollection = 'MEQuestions';

echo "🔍 Checking Image URLs in PocketBase\n";
echo str_repeat("=", 80) . "\n\n";

// Fetch all records with images
$recordsUrl = "$pbUrl/api/collections/$pbCollection/records?filter=(image!='')&perPage=500";
$ch = curl_init($recordsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("❌ Error: Could not fetch records. HTTP Code: $httpCode\n");
}

$data = json_decode($response, true);
$records = $data['items'] ?? [];

echo "Found " . count($records) . " records with images\n\n";

$fullUrls = 0;
$filenames = 0;

foreach ($records as $index => $record) {
    $playerName = $record['nameplayer'] ?? 'Unknown';
    $imageUrl = $record['image'] ?? '';
    
    if (empty($imageUrl)) {
        continue;
    }
    
    $isFullUrl = strpos($imageUrl, 'http') === 0;
    
    if ($isFullUrl) {
        $fullUrls++;
        $type = "✅ FULL URL";
    } else {
        $filenames++;
        $type = "⚠️  FILENAME ONLY";
    }
    
    echo sprintf("[%02d] %s | %s\n", $index + 1, $type, $playerName);
    echo "     " . substr($imageUrl, 0, 100) . (strlen($imageUrl) > 100 ? '...' : '') . "\n\n";
}

echo str_repeat("=", 80) . "\n";
echo "SUMMARY:\n";
echo "✅ Full URLs: $fullUrls\n";
echo "⚠️  Filenames only: $filenames\n";
echo "\nℹ️  Only records with full URLs can be downloaded automatically.\n";
echo "   Filenames need to be manually downloaded from Leonardo dashboard.\n";
