<?php
/**
 * Simple Email Configuration using PHP mail() function
 * Since mail() works on your server, we'll use that instead of PHPMailer
 */

/**
 * Send email using PHP mail() function
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $htmlBody HTML email body
 * @param string $fromEmail Sender email (default: maskedemployee@pinkmilk.eu)
 * @param string $fromName Sender name (default: The Masked Employee)
 * @param string $replyTo Optional reply-to address
 * @return array ['success' => bool, 'error' => string|null]
 */
function sendEmailSimple($to, $subject, $htmlBody, $fromEmail = 'maskedemployee@pinkmilk.eu', $fromName = 'The Masked Employee', $replyTo = null) {
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: $fromName <$fromEmail>" . "\r\n";
    
    if ($replyTo) {
        $headers .= "Reply-To: $replyTo" . "\r\n";
    }
    
    // Send email
    $result = mail($to, $subject, $htmlBody, $headers);
    
    // Log result
    error_log("✉️ Email to $to: " . ($result ? 'SUCCESS' : 'FAILED'));
    error_log("   Subject: $subject");
    error_log("   From: $fromEmail");
    
    return [
        'success' => $result,
        'error' => $result ? null : 'mail() function returned false',
        'method' => 'php_mail'
    ];
}

/**
 * Test email configuration
 */
function testSimpleEmail($testEmail = 'klaas@pinkmilk.eu') {
    $subject = '🎭 Test Email - Simple mail() Function';
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
            <h1>🎉 Email Test Success!</h1>
            <div class="success">
                <strong>Success!</strong> If you receive this email, the simple mail() configuration is working.
            </div>
            <p><strong>Configuration:</strong></p>
            <ul>
                <li>Method: PHP mail() function</li>
                <li>From: maskedemployee@pinkmilk.eu</li>
                <li>Server: ' . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . '</li>
            </ul>
            <p>Sent at: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </body>
    </html>
    ';
    
    return sendEmailSimple($testEmail, $subject, $body);
}
?>
