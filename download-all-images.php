<?php
/**
 * Download all character images from PocketBase
 * Creates a folder with all images and a CSV file with character info
 */

// PocketBase configuration
$pbUrl = 'https://pinkmilk.pockethost.io';
$pbCollection = 'MEQuestions';

// Create downloads folder
$downloadFolder = __DIR__ . '/character-images-' . date('Y-m-d-His');
if (!file_exists($downloadFolder)) {
    mkdir($downloadFolder, 0755, true);
}

echo "<h2>📥 Downloading Character Images</h2>";
echo "<p>Saving to: <code>" . basename($downloadFolder) . "</code></p>";
echo "<hr>";

// Fetch all records with images
$recordsUrl = "$pbUrl/api/collections/$pbCollection/records?filter=(image!='')&perPage=500";
$ch = curl_init($recordsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("❌ Error: Could not fetch records from PocketBase. HTTP Code: $httpCode");
}

$data = json_decode($response, true);
$records = $data['items'] ?? [];

echo "<p>Found <strong>" . count($records) . "</strong> records with images</p>";
echo "<hr>";

// Prepare CSV data
$csvData = [];
$csvData[] = ['Player Name', 'Character Name', 'Game Name', 'Language', 'Date', 'Image Filename', 'Image URL'];

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
    
    // Determine if it's a full URL or just filename
    if (strpos($imageUrl, 'http') === 0) {
        $fullImageUrl = $imageUrl;
        $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
    } else {
        // It's a Leonardo filename, construct full URL
        $fullImageUrl = "https://cdn.leonardo.ai/users/[USER_ID]/generations/" . $imageUrl;
        $filename = $imageUrl;
    }
    
    // Create safe filename
    $safePlayerName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $playerName);
    $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg';
    $newFilename = sprintf("%02d_%s_%s.%s", 
        $index + 1, 
        $safePlayerName, 
        substr($characterName, 0, 20),
        $extension
    );
    $newFilename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $newFilename);
    
    // Download image
    echo "<p>[$index] Downloading: <strong>$playerName</strong> ($characterName)...</p>";
    
    $imageData = @file_get_contents($fullImageUrl);
    
    if ($imageData === false) {
        echo "<p style='color: red;'>❌ Failed to download</p>";
        $failCount++;
        continue;
    }
    
    // Save image
    $savePath = $downloadFolder . '/' . $newFilename;
    if (file_put_contents($savePath, $imageData)) {
        echo "<p style='color: green;'>✅ Saved as: $newFilename</p>";
        $successCount++;
        
        // Add to CSV
        $csvData[] = [
            $playerName,
            $characterName,
            $gameName,
            $language,
            $created,
            $newFilename,
            $fullImageUrl
        ];
    } else {
        echo "<p style='color: red;'>❌ Failed to save</p>";
        $failCount++;
    }
    
    echo "<hr>";
    
    // Small delay to avoid overwhelming the server
    usleep(100000); // 0.1 second
}

// Save CSV file
$csvPath = $downloadFolder . '/character-list.csv';
$csvFile = fopen($csvPath, 'w');
foreach ($csvData as $row) {
    fputcsv($csvFile, $row);
}
fclose($csvFile);

echo "<h3>📊 Summary</h3>";
echo "<p>✅ Successfully downloaded: <strong>$successCount</strong> images</p>";
echo "<p>❌ Failed: <strong>$failCount</strong> images</p>";
echo "<p>📁 Saved to folder: <strong>" . basename($downloadFolder) . "</strong></p>";
echo "<p>📄 Character list saved to: <strong>character-list.csv</strong></p>";

echo "<h3>🎉 Done!</h3>";
echo "<p>You can now download the entire folder from your server via FTP/SFTP.</p>";
