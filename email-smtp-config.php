<?php
/**
 * SMTP Email Configuration using PHPMailer
 * Hostslim SMTP Settings for pinkmilk.eu
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    // Fallback error if PHPMailer not installed
    error_log("PHPMailer not installed. Run: composer install");
    die(json_encode(['success' => false, 'error' => 'PHPMailer not installed. Run: composer install']));
}

/**
 * Send email using SMTP
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $htmlBody HTML email body
 * @param string $fromEmail Sender email (default: maskedemployee@pinkmilk.eu)
 * @param string $fromName Sender name (default: The Masked Employee)
 * @param string $replyTo Optional reply-to address
 * @return array ['success' => bool, 'error' => string|null]
 */
function sendEmailSMTP($to, $subject, $htmlBody, $fromEmail = 'maskedemployee@pinkmilk.eu', $fromName = 'The Masked Employee', $replyTo = null) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'mail.pinkmilk.eu';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'maskedemployee@pinkmilk.eu';
        $mail->Password   = 'M@sked03';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Enable verbose debug output (disable in production)
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = function($str, $level) {
        //     error_log("SMTP Debug level $level: $str");
        // };
        
        // Recipients
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);
        
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody); // Plain text version
        
        // Send email
        $mail->send();
        
        error_log("✅ SMTP Email sent successfully to: $to");
        error_log("   Subject: $subject");
        
        return [
            'success' => true,
            'error' => null,
            'method' => 'phpmailer_smtp'
        ];
        
    } catch (Exception $e) {
        error_log("❌ SMTP Email failed to: $to");
        error_log("   Error: {$mail->ErrorInfo}");
        error_log("   Subject: $subject");
        
        return [
            'success' => false,
            'error' => $mail->ErrorInfo,
            'method' => 'phpmailer_smtp'
        ];
    }
}

/**
 * Test SMTP configuration
 */
function testSMTPConfig($testEmail = 'klaas@pinkmilk.eu') {
    $subject = '🎭 Test Email - SMTP Configuration';
    $body = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .success { background: #d4edda; padding: 15px; border-radius: 5px; color: #155724; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎉 SMTP Configuration Test</h1>
            <div class="success">
                <strong>Success!</strong> If you receive this email, your SMTP configuration is working correctly.
            </div>
            <p><strong>Configuration:</strong></p>
            <ul>
                <li>Host: mail.pinkmilk.eu</li>
                <li>Port: 587 (STARTTLS)</li>
                <li>From: maskedemployee@pinkmilk.eu</li>
                <li>Method: PHPMailer with SMTP</li>
            </ul>
            <p>Sent at: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </body>
    </html>
    ';
    
    return sendEmailSMTP($testEmail, $subject, $body);
}
?>
