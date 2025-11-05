<?php
/**
 * QUICK SMTP TEST
 * Upload ONLY this file and visit it in browser
 * Tests if your SMTP credentials work
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Quick SMTP Test</h1>";
echo "<p>Testing SMTP connection to mail.pinkmilk.eu...</p>";

// Test basic SMTP connection
$smtp_host = 'mail.pinkmilk.eu';
$smtp_port = 587;
$smtp_user = 'maskedemployee@pinkmilk.eu';
$smtp_pass = 'M@sked03';

echo "<h2>Step 1: Check if fsockopen works</h2>";
$connection = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 10);

if ($connection) {
    echo "✅ <strong>SUCCESS!</strong> Can connect to $smtp_host:$smtp_port<br>";
    fclose($connection);
} else {
    echo "❌ <strong>FAILED!</strong> Cannot connect to $smtp_host:$smtp_port<br>";
    echo "Error: $errstr ($errno)<br>";
    echo "<p><strong>This means:</strong> Port 587 might be blocked on your server.</p>";
    exit;
}

echo "<h2>Step 2: Test with PHP mail() function</h2>";
$to = 'klaas@pinkmilk.eu';
$subject = 'Quick Test - ' . date('H:i:s');
$message = 'This is a quick test email sent at ' . date('Y-m-d H:i:s');
$headers = "From: maskedemployee@pinkmilk.eu\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$result = mail($to, $subject, $message, $headers);

if ($result) {
    echo "✅ <strong>mail() returned TRUE</strong><br>";
    echo "Check your inbox at: $to<br>";
    echo "<p><em>Note: mail() returning TRUE doesn't guarantee delivery.</em></p>";
} else {
    echo "❌ <strong>mail() returned FALSE</strong><br>";
    echo "PHP mail() function is not working on this server.<br>";
}

echo "<h2>Step 3: Server Information</h2>";
echo "<ul>";
echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
echo "<li><strong>Server:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "<li><strong>sendmail_path:</strong> " . ini_get('sendmail_path') . "</li>";
echo "<li><strong>SMTP:</strong> " . ini_get('SMTP') . "</li>";
echo "<li><strong>smtp_port:</strong> " . ini_get('smtp_port') . "</li>";
echo "</ul>";

echo "<h2>✅ Next Steps</h2>";
echo "<ol>";
echo "<li>If Step 1 passed: SMTP connection works ✅</li>";
echo "<li>If Step 2 passed: Check your email inbox</li>";
echo "<li>If both passed: Upload the full PHPMailer setup</li>";
echo "<li>If Step 1 failed: Contact Hostslim about port 587</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
