<?php
/**
 * Delete test records that have no ai_summary
 */

// PocketBase configuration
$pbUrl = 'https://pinkmilk.pockethost.io';
$pbToken = 'biknu8-pyrnaB-mytvyx';

echo "🗑️  Starting deletion of test records (no ai_summary)...\n\n";

try {
    // Get all records
    $url = $pbUrl . '/api/collections/MEQuestions/records?perPage=500';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $pbToken
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || !$response) {
        die("❌ Error fetching records: HTTP $httpCode\n");
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['items']) || empty($data['items'])) {
        die("⚠️ No records found\n");
    }
    
    $recordsToDelete = [];
    
    // Find records without ai_summary
    foreach ($data['items'] as $record) {
        if (empty($record['ai_summary'])) {
            $recordsToDelete[] = [
                'id' => $record['id'],
                'name' => $record['nameplayer'] ?? 'Unknown',
                'game' => $record['gamename'] ?? 'Unknown'
            ];
        }
    }
    
    if (empty($recordsToDelete)) {
        echo "✅ No test records to delete (all records have ai_summary)\n";
        exit(0);
    }
    
    echo "📋 Found " . count($recordsToDelete) . " test records to delete:\n\n";
    
    foreach ($recordsToDelete as $record) {
        echo "   - {$record['name']} ({$record['game']})\n";
    }
    
    echo "\n⚠️  Are you sure you want to delete these " . count($recordsToDelete) . " records? (yes/no): ";
    $confirmation = trim(fgets(STDIN));
    
    if (strtolower($confirmation) !== 'yes') {
        echo "❌ Deletion cancelled\n";
        exit(0);
    }
    
    echo "\n🗑️  Deleting records...\n\n";
    
    $deleted = 0;
    $failed = 0;
    
    foreach ($recordsToDelete as $record) {
        $deleteUrl = $pbUrl . '/api/collections/MEQuestions/records/' . $record['id'];
        
        $ch = curl_init($deleteUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . $pbToken
        ]);
        
        $deleteResponse = curl_exec($ch);
        $deleteHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($deleteHttpCode === 204 || $deleteHttpCode === 200) {
            echo "✅ Deleted: {$record['name']} ({$record['game']})\n";
            $deleted++;
        } else {
            echo "❌ Failed to delete: {$record['name']} ({$record['game']}) - HTTP $deleteHttpCode\n";
            $failed++;
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 DELETION SUMMARY:\n";
    echo "   Total found: " . count($recordsToDelete) . "\n";
    echo "   ✅ Deleted: $deleted\n";
    echo "   ❌ Failed: $failed\n";
    echo str_repeat("=", 60) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
