<?php
/**
 * Email Configuration using PHPMailer
 * Requires: composer require phpmailer/phpmailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if PHPMailer is available
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    // Fallback to basic mail() if PHPMailer not installed
    function sendEmail($to, $subject, $htmlBody, $fromName = 'The Masked Employee', $fromEmail = 'noreply@pinkmilk.eu', $replyTo = null) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: $fromName <$fromEmail>" . "\r\n";
        
        if ($replyTo) {
            $headers .= "Reply-To: $replyTo" . "\r\n";
        }
        
        $result = mail($to, $subject, $htmlBody, $headers);
        
        error_log("Email sent to $to: " . ($result ? 'SUCCESS' : 'FAILED'));
        error_log("Subject: $subject");
        
        return [
            'success' => $result,
            'method' => 'php_mail',
            'error' => $result ? null : 'mail() function returned false'
        ];
    }
} else {
    // Use PHPMailer with SMTP
    function sendEmail($to, $subject, $htmlBody, $fromName = 'The Masked Employee', $fromEmail = 'noreply@pinkmilk.eu', $replyTo = null) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com'; // Hostinger SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'noreply@pinkmilk.eu'; // Your SMTP username
            $mail->Password   = 'YOUR_SMTP_PASSWORD'; // Your SMTP password - CHANGE THIS!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';
            
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
            $mail->AltBody = strip_tags($htmlBody);
            
            $mail->send();
            
            error_log("Email sent to $to via SMTP: SUCCESS");
            error_log("Subject: $subject");
            
            return [
                'success' => true,
                'method' => 'phpmailer_smtp',
                'error' => null
            ];
            
        } catch (Exception $e) {
            error_log("Email failed to $to via SMTP: {$mail->ErrorInfo}");
            error_log("Subject: $subject");
            
            return [
                'success' => false,
                'method' => 'phpmailer_smtp',
                'error' => $mail->ErrorInfo
            ];
        }
    }
}

/**
 * Test email configuration
 */
function testEmailConfig($testEmail = 'klaas@pinkmilk.eu') {
    $testSubject = '🎭 Test Email - Masked Employee';
    $testBody = '<h1>Test Email</h1><p>If you receive this, email configuration is working!</p>';
    
    $result = sendEmail($testEmail, $testSubject, $testBody);
    
    return $result;
}
?>
