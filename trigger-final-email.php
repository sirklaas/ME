<?php
/**
 * Manually trigger final email for a specific record
 * Usage: trigger-final-email.php?recordId=xxxxx
 */

// PocketBase configuration
$pbUrl = 'https://pinkmilk.pockethost.io';
$pbCollection = 'MEQuestions';

// Get record ID from URL parameter
$recordId = $_GET['recordId'] ?? '';

if (empty($recordId)) {
    die('Error: No recordId provided. Usage: trigger-final-email.php?recordId=xxxxx');
}

// Fetch record from PocketBase
$recordUrl = "$pbUrl/api/collections/$pbCollection/records/$recordId";
$ch = curl_init($recordUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("Error: Could not fetch record from PocketBase. HTTP Code: $httpCode");
}

$record = json_decode($response, true);

// Validate record has required data
if (empty($record['email'])) {
    die('Error: Record has no email address');
}

if (empty($record['image'])) {
    die('Error: Record has no image URL. Please add the image URL to PocketBase first.');
}

// Prepare data for send-final-email.php
$emailData = [
    'email' => $record['email'],
    'playerName' => $record['nameplayer'] ?? 'Unknown',
    'gameName' => $record['gamename'] ?? 'The Masked Employee',
    'language' => $record['language'] ?? 'nl',
    'characterDescription' => $record['characterdescription'] ?? '',
    'worldDescription' => $record['worlddescription'] ?? '',
    'imageUrl' => $record['image'],
    'characterName' => $record['charactername'] ?? 'Your Character'
];

// Call send-final-email.php
$emailUrl = 'https://www.pinkmilk.eu/ME/send-final-email.php';
$ch = curl_init($emailUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$emailResponse = curl_exec($ch);
$emailHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Display result
echo "<h2>Trigger Final Email - Result</h2>";
echo "<p><strong>Record ID:</strong> $recordId</p>";
echo "<p><strong>Player Name:</strong> " . htmlspecialchars($emailData['playerName']) . "</p>";
echo "<p><strong>Email:</strong> " . htmlspecialchars($emailData['email']) . "</p>";
echo "<p><strong>Image URL:</strong> " . htmlspecialchars($emailData['imageUrl']) . "</p>";
echo "<hr>";
echo "<p><strong>Email Send Status:</strong> HTTP $emailHttpCode</p>";
echo "<pre>" . htmlspecialchars($emailResponse) . "</pre>";

if ($emailHttpCode === 200) {
    $result = json_decode($emailResponse, true);
    if ($result['success'] ?? false) {
        echo "<h3 style='color: green;'>✅ Email sent successfully!</h3>";
    } else {
        echo "<h3 style='color: red;'>❌ Email failed: " . htmlspecialchars($result['error'] ?? 'Unknown error') . "</h3>";
    }
} else {
    echo "<h3 style='color: red;'>❌ Failed to call send-final-email.php</h3>";
}
