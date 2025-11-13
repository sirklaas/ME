<?php
/**
 * Download all character images from PocketBase - LOCAL VERSION
 * Run this on your local machine: php download-images-local.php
 */

// PocketBase configuration
$pbUrl = 'https://pinkmilk.pockethost.io';
$pbCollection = 'MEQuestions';

// Create downloads folder
$downloadFolder = __DIR__ . '/character-images-' . date('Y-m-d-His');
if (!file_exists($downloadFolder)) {
    mkdir($downloadFolder, 0755, true);
}

echo "📥 Downloading Character Images\n";
echo "Saving to: " . basename($downloadFolder) . "\n";
echo str_repeat("=", 60) . "\n\n";

// Fetch all records with images
$recordsUrl = "$pbUrl/api/collections/$pbCollection/records?filter=(image!='')&perPage=500";
$ch = curl_init($recordsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("❌ Error: Could not fetch records from PocketBase. HTTP Code: $httpCode\n");
}

$data = json_decode($response, true);
$records = $data['items'] ?? [];

echo "Found " . count($records) . " records with images\n\n";

// Prepare CSV data
$csvData = [];
$csvData[] = ['#', 'Player Name', 'Character Name', 'Game Name', 'Language', 'Date', 'Image Filename'];

$successCount = 0;
$failCount = 0;

foreach ($records as $index => $record) {
    $playerName = $record['nameplayer'] ?? 'Unknown';
    $characterName = $record['charactername'] ?? 'Unknown';
    $gameName = $record['gamename'] ?? 'Unknown';
    $language = $record['language'] ?? 'nl';
    $imageUrl = $record['image'] ?? '';
    $created = $record['created'] ?? '';
    
    if (empty($imageUrl)) {
        continue;
    }
    
    // Determine if it's a full URL or PocketBase filename
    if (strpos($imageUrl, 'http') === 0) {
        $fullImageUrl = $imageUrl;
        $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
    } else {
        // It's a PocketBase filename - construct the file URL
        // PocketBase file URL format: {pbUrl}/api/files/{collection}/{recordId}/{filename}
        $recordId = $record['id'];
        $fullImageUrl = "$pbUrl/api/files/$pbCollection/$recordId/$imageUrl";
        $filename = $imageUrl;
    }
    
    // Create safe filename
    $safePlayerName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $playerName);
    $safeCharName = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($characterName, 0, 30));
    $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg';
    $newFilename = sprintf("%02d_%s_%s.%s", 
        $index + 1, 
        $safePlayerName, 
        $safeCharName,
        $extension
    );
    
    // Download image
    echo sprintf("[%02d/%02d] %s (%s)... ", $index + 1, count($records), $playerName, $characterName);
    
    $imageData = @file_get_contents($fullImageUrl);
    
    if ($imageData === false) {
        echo "❌ FAILED\n";
        $failCount++;
        continue;
    }
    
    // Save image
    $savePath = $downloadFolder . '/' . $newFilename;
    if (file_put_contents($savePath, $imageData)) {
        $fileSize = strlen($imageData);
        echo "✅ OK (" . round($fileSize / 1024, 1) . " KB)\n";
        $successCount++;
        
        // Add to CSV
        $csvData[] = [
            $index + 1,
            $playerName,
            $characterName,
            $gameName,
            $language,
            date('Y-m-d H:i', strtotime($created)),
            $newFilename
        ];
    } else {
        echo "❌ SAVE FAILED\n";
        $failCount++;
    }
    
    // Small delay to avoid overwhelming the server
    usleep(200000); // 0.2 second
}

// Save CSV file
$csvPath = $downloadFolder . '/character-list.csv';
$csvFile = fopen($csvPath, 'w');
foreach ($csvData as $row) {
    fputcsv($csvFile, $row);
}
fclose($csvFile);

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat("=", 60) . "\n";
echo "✅ Successfully downloaded: $successCount images\n";
echo "❌ Failed: $failCount images\n";
echo "📁 Saved to: " . basename($downloadFolder) . "\n";
echo "📄 Character list: character-list.csv\n";
echo "\n🎉 Done!\n";
