<?php
header('Content-Type: text/plain');

echo "=== PHP ERROR LOG (Last 100 lines) ===\n\n";

// Try multiple possible log locations
$logFiles = [
    __DIR__ . '/error_log',
    dirname(__DIR__) . '/error_log',
    '/var/log/php_errors.log',
    ini_get('error_log')
];

$foundLog = false;

foreach ($logFiles as $logFile) {
    if ($logFile && file_exists($logFile)) {
        echo "Found log at: $logFile\n";
        echo "==================\n\n";
        
        $lines = file($logFile);
        $last100 = array_slice($lines, -100);
        echo implode('', $last100);
        
        $foundLog = true;
        break;
    }
}

if (!$foundLog) {
    echo "No error log found. Tried:\n";
    foreach ($logFiles as $path) {
        echo "- $path\n";
    }
    
    echo "\n\nCurrent PHP error_log setting: " . ini_get('error_log') . "\n";
    echo "Display errors: " . ini_get('display_errors') . "\n";
}
?>
