<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Last 50 Lines of Error Log</h1>";

$logFile = __DIR__ . '/error_log';

if (file_exists($logFile)) {
    echo "<pre>";
    $lines = file($logFile);
    $last50 = array_slice($lines, -50);
    echo htmlspecialchars(implode('', $last50));
    echo "</pre>";
} else {
    echo "<p>No error_log file found at: $logFile</p>";
    
    // Try parent directory
    $logFile2 = dirname(__DIR__) . '/error_log';
    if (file_exists($logFile2)) {
        echo "<h2>Found in parent directory:</h2>";
        echo "<pre>";
        $lines = file($logFile2);
        $last50 = array_slice($lines, -50);
        echo htmlspecialchars(implode('', $last50));
        echo "</pre>";
    }
}
?>