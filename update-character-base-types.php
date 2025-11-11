<?php
/**
 * One-time script to populate character_base_type for existing records
 * Extracts base type from ai_summary field and updates PocketBase
 */

// PocketBase configuration
$pbUrl = 'https://pinkmilk.pockethost.io';
$pbToken = 'biknu8-pyrnaB-mytvyx'; // Admin token for authentication

/**
 * Extract character base type from AI summary
 */
function extractCharacterBaseType($aiSummary) {
    if (empty($aiSummary)) {
        return '';
    }
    
    $characterType = null;
    
    // Pattern 1: "Een [adjective] [TYPE]" (e.g., "Een vrolijke Panda", "Een sappige Tomaat")
    if (preg_match('/Een\s+\w+\s+([A-Z][a-zëïöüáéíóú]+)/u', $aiSummary, $matches)) {
        $characterType = trim($matches[1]);
    }
    // Pattern 2: "Jij bent [Name], een [adjective] [TYPE]" (e.g., "Jij bent Leo, een majestueuze Leeuw")
    elseif (preg_match('/Jij bent\s+\w+,\s+een\s+\w+\s+([A-Z][a-zëïöüáéíóú]+)/u', $aiSummary, $matches)) {
        $characterType = trim($matches[1]);
    }
    // Pattern 3: Just "Een [TYPE]" at start of line (e.g., "Een Leeuw", "Een Tomaat")
    elseif (preg_match('/^Een\s+([A-Z][a-zëïöüáéíóú]+)/um', $aiSummary, $matches)) {
        $characterType = trim($matches[1]);
    }
    // Pattern 4: "Jij bent [Name]" followed by newline and "Een [TYPE]"
    elseif (preg_match('/Jij bent\s+\w+.*?\n+Een\s+([A-Z][a-zëïöüáéíóú]+)/us', $aiSummary, $matches)) {
        $characterType = trim($matches[1]);
    }
    
    // Return lowercase for consistency
    return $characterType ? strtolower($characterType) : '';
}

echo "🔄 Starting character_base_type update for existing records...\n\n";

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
    
    $totalRecords = count($data['items']);
    $updated = 0;
    $skipped = 0;
    $failed = 0;
    
    echo "📊 Found $totalRecords records to process\n\n";
    
    foreach ($data['items'] as $record) {
        $recordId = $record['id'];
        $playerName = $record['nameplayer'] ?? 'Unknown';
        $gameName = $record['gamename'] ?? 'Unknown';
        
        // Skip if already has character_base_type
        if (!empty($record['character_base_type'])) {
            echo "⏭️  Skipping $playerName ($gameName) - already has base type: {$record['character_base_type']}\n";
            $skipped++;
            continue;
        }
        
        // Skip if no ai_summary
        if (empty($record['ai_summary'])) {
            echo "⚠️  Skipping $playerName ($gameName) - no ai_summary\n";
            $skipped++;
            continue;
        }
        
        // Extract base type
        $baseType = extractCharacterBaseType($record['ai_summary']);
        
        if (empty($baseType)) {
            echo "⚠️  Could not extract base type for $playerName ($gameName)\n";
            $failed++;
            continue;
        }
        
        // Update record
        $updateUrl = $pbUrl . '/api/collections/MEQuestions/records/' . $recordId;
        $updateData = json_encode(['character_base_type' => $baseType]);
        
        $ch = curl_init($updateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $updateData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . $pbToken,
            'Content-Length: ' . strlen($updateData)
        ]);
        
        $updateResponse = curl_exec($ch);
        $updateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($updateHttpCode === 200) {
            echo "✅ Updated $playerName ($gameName) → character_base_type: $baseType\n";
            $updated++;
        } else {
            echo "❌ Failed to update $playerName ($gameName) - HTTP $updateHttpCode\n";
            $failed++;
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 SUMMARY:\n";
    echo "   Total records: $totalRecords\n";
    echo "   ✅ Updated: $updated\n";
    echo "   ⏭️  Skipped: $skipped\n";
    echo "   ❌ Failed: $failed\n";
    echo str_repeat("=", 60) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
