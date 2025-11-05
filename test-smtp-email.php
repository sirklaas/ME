<?php
/**
 * Test SMTP Email Configuration
 * Run this to verify PHPMailer and SMTP are working
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== SMTP EMAIL CONFIGURATION TEST ===\n\n";

// Load SMTP configuration
require_once __DIR__ . '/email-smtp-config.php';

echo "Step 1: Check PHPMailer Installation\n";
echo "-------------------------------------\n";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer is installed\n\n";
} else {
    echo "❌ PHPMailer is NOT installed\n";
    echo "   Run: composer install\n\n";
    exit(1);
}

echo "Step 2: SMTP Configuration\n";
echo "-------------------------------------\n";
echo "Host: sh-woe014.hostslim.nl\n";
echo "Port: 587 (STARTTLS)\n";
echo "Username: maskedemployee@pinkmilk.eu\n";
echo "Password: " . str_repeat('*', 8) . "\n\n";

echo "Step 3: Send Test Email\n";
echo "-------------------------------------\n";
$testEmail = 'klaas@pinkmilk.eu'; // Change this to your email
echo "Sending test email to: $testEmail\n";

$result = testSMTPConfig($testEmail);

if ($result['success']) {
    echo "✅ SUCCESS! Email sent via SMTP\n";
    echo "   Check your inbox at: $testEmail\n";
    echo "   (Also check spam folder)\n\n";
} else {
    echo "❌ FAILED! Email could not be sent\n";
    echo "   Error: " . $result['error'] . "\n\n";
    
    echo "Troubleshooting:\n";
    echo "1. Verify SMTP credentials are correct\n";
    echo "2. Check if port 587 is open on your server\n";
    echo "3. Verify maskedemployee@pinkmilk.eu email account exists\n";
    echo "4. Contact Hostslim support if issue persists\n\n";
}

echo "Step 4: Test Different Recipient\n";
echo "-------------------------------------\n";
$testEmail2 = 'klaas@republick.nl'; // External domain
echo "Sending test email to: $testEmail2\n";

$result2 = testSMTPConfig($testEmail2);

if ($result2['success']) {
    echo "✅ SUCCESS! Email sent to external domain\n";
    echo "   Check your inbox at: $testEmail2\n\n";
} else {
    echo "❌ FAILED! Email could not be sent\n";
    echo "   Error: " . $result2['error'] . "\n\n";
}

echo "=== TEST COMPLETE ===\n";
echo "\nIf both tests passed, your SMTP configuration is working!\n";
echo "You can now use the questionnaire and emails will be delivered.\n";
?>
