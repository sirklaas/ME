<?php
/**
 * Test Email Configuration
 * Run this to check if emails are working
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== EMAIL CONFIGURATION TEST ===\n\n";

// Test 1: Check if mail() function is available
echo "Test 1: PHP mail() function\n";
echo "----------------------------\n";
if (function_exists('mail')) {
    echo "✓ mail() function is available\n";
    
    // Try to send a test email
    $to = 'klaas@pinkmilk.eu';
    $subject = 'Test Email from Masked Employee';
    $message = '<html><body><h1>Test Email</h1><p>This is a test email from the Masked Employee system.</p></body></html>';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: The Masked Employee <noreply@pinkmilk.eu>" . "\r\n";
    
    $result = mail($to, $subject, $message, $headers);
    
    if ($result) {
        echo "✓ mail() returned TRUE (but this doesn't guarantee delivery)\n";
        echo "  Check your inbox at: $to\n";
    } else {
        echo "✗ mail() returned FALSE\n";
        echo "  Email was NOT sent\n";
    }
} else {
    echo "✗ mail() function is NOT available\n";
}

echo "\n";

// Test 2: Check server email configuration
echo "Test 2: Server Configuration\n";
echo "----------------------------\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";

echo "\n";

// Test 3: Check if PHPMailer is available
echo "Test 3: PHPMailer\n";
echo "----------------------------\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "✓ PHPMailer is installed\n";
        echo "  You can use SMTP for reliable email delivery\n";
    } else {
        echo "✗ PHPMailer class not found\n";
    }
} else {
    echo "✗ PHPMailer is NOT installed\n";
    echo "  Run: composer require phpmailer/phpmailer\n";
    echo "  Or install manually from: https://github.com/PHPMailer/PHPMailer\n";
}

echo "\n";

// Test 4: Check error logs
echo "Test 4: Recent Email Logs\n";
echo "----------------------------\n";
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    echo "Log file: $logFile\n";
    $logs = file_get_contents($logFile);
    $emailLogs = array_filter(explode("\n", $logs), function($line) {
        return strpos($line, 'email') !== false || strpos($line, 'Email') !== false;
    });
    
    if (count($emailLogs) > 0) {
        echo "Recent email-related logs:\n";
        foreach (array_slice($emailLogs, -10) as $log) {
            echo "  " . $log . "\n";
        }
    } else {
        echo "No email-related logs found\n";
    }
} else {
    echo "Error log not accessible\n";
}

echo "\n";

// Recommendations
echo "=== RECOMMENDATIONS ===\n\n";
echo "1. If mail() returns TRUE but you don't receive emails:\n";
echo "   - Emails might be caught by spam filters\n";
echo "   - Server might not have proper email configuration\n";
echo "   - Install PHPMailer and use SMTP instead\n\n";

echo "2. To install PHPMailer:\n";
echo "   composer require phpmailer/phpmailer\n\n";

echo "3. Configure SMTP in email-config.php:\n";
echo "   - Use your hosting provider's SMTP settings\n";
echo "   - For Hostinger: smtp.hostinger.com, port 587\n";
echo "   - Add your SMTP username and password\n\n";

echo "4. Check with your hosting provider:\n";
echo "   - Is mail() function enabled?\n";
echo "   - What are the SMTP settings?\n";
echo "   - Are there any email sending limits?\n\n";

?>
