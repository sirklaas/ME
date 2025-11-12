<?php
// Minimal test - just echo JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Test works']);
