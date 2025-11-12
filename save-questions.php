<?php
/**
 * Save Questions API
 * Saves updated questions back to Questions-Bilingual.json
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    // Get the JSON data from request body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }
    
    // Validate the data structure
    if (!isset($data['chapters']) || !is_array($data['chapters'])) {
        throw new Exception('Invalid data structure: missing chapters array');
    }
    
    // Create backup of current file
    $originalFile = 'Questions-Bilingual.json';
    $backupFile = 'Questions-Bilingual-backup-' . date('Y-m-d-H-i-s') . '.json';
    
    if (file_exists($originalFile)) {
        if (!copy($originalFile, $backupFile)) {
            throw new Exception('Failed to create backup file');
        }
    }
    
    // Format JSON with pretty print
    $jsonString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // Save to file
    $result = file_put_contents($originalFile, $jsonString);
    
    if ($result === false) {
        throw new Exception('Failed to write to file');
    }
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Questions saved successfully',
        'backup' => $backupFile,
        'bytes_written' => $result,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
