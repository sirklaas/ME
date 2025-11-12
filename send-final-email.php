<?php
/**
 * Send final email with generated character image
 * Sent after image generation is complete
 * Now using PHPMailer with SMTP for reliable delivery
 */

// Load simple email configuration (using mail() function)
require_once __DIR__ . '/email-simple-config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$userEmail = $data['email'] ?? '';
$playerName = $data['playerName'] ?? 'Unknown';
$gameName = $data['gameName'] ?? 'The Masked Employee';
$language = $data['language'] ?? 'nl';
$characterDesc = $data['characterDescription'] ?? '';
$worldDesc = $data['worldDescription'] ?? '';
$imageUrl = $data['imageUrl'] ?? '';
$characterName = $data['characterName'] ?? 'Your Character';

// Clean up character name (remove duplicates like "Kara\n\nKara")
$nameParts = preg_split('/[\n\r]+/', $characterName);
$nameParts = array_map('trim', $nameParts);
$nameParts = array_filter($nameParts);
if (count($nameParts) > 1 && $nameParts[0] === end($nameParts)) {
    $characterName = $nameParts[0]; // Use first if duplicated
} else {
    $characterName = $nameParts[0] ?? $characterName; // Use first line
}

// Format character description: make "De [Type] genaamd [Name]" a heading
function formatCharacterDescription($desc) {
    // Remove duplicate character names (e.g., "Philip\n\nPhilip" -> "Philip")
    $desc = preg_replace('/(De\s+\w+\s+genaamd\s+)(\w+)\s*[\n\r]+\s*\2/i', '$1$2', $desc);
    
    // Replace section headers with new user-friendly versions (all formats including plain "OMGEVING:")
    $desc = preg_replace('/\d+\.\s*KARAKTER\s*\([^)]+\):\s*/i', "\n\n📖 Jouw Verhaal in Vogelvlucht\n\n", $desc);
    $desc = preg_replace('/\d+\.\s*OMGEVING\s*\([^)]+\):\s*/i', "\n\n🌍 En waar je zoal uithangt\n\n", $desc);
    $desc = preg_replace('/===\s*KARAKTER\s*===/i', "\n\n📖 Jouw Verhaal in Vogelvlucht\n\n", $desc);
    $desc = preg_replace('/===\s*OMGEVING\s*===/i', "\n\n🌍 En waar je zoal uithangt\n\n", $desc);
    $desc = preg_replace('/OMGEVING:/i', "\n\n🌍 En waar je zoal uithangt\n\n", $desc);
    
    // Replace "De [Type] genaamd [Name] is" with "Je bent"
    $desc = preg_replace('/De\s+\w+\s+genaamd\s+[^\s]+\s+is\s+/i', 'Je bent ', $desc);
    
    // Remove personality section (will be replaced with sliders)
    $desc = preg_replace('/===\s*PERSOONLIJKHEID\s*===[\s\S]*?(?=\n\n|$)/i', '', $desc);
    
    // Clean up excessive line breaks (more than 2 consecutive newlines)
    $desc = preg_replace('/\n{3,}/', "\n\n", $desc);
    
    // Extract only first character section (stop at next "De [Type] genaamd")
    $nextCharPattern = '/\n\n+De\s+\w+\s+genaamd/';
    if (preg_match($nextCharPattern, $desc, $nextMatch, PREG_OFFSET_CAPTURE, 50)) {
        $desc = substr($desc, 0, $nextMatch[0][1]);
    }
    
    // Return formatted with proper HTML
    return nl2br(htmlspecialchars($desc));
}

// Admin email
$adminEmail = 'klaas@pinkmilk.eu';

// Log request
error_log("=== FINAL EMAIL WITH IMAGE REQUEST ===");
error_log("User Email: $userEmail");
error_log("Player Name: $playerName");
error_log("Image URL: $imageUrl");

// Validate email
if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

// Email content based on language
if ($language === 'nl') {
    $subject = '🎨 Wat een plaatje! - ' . $gameName;
    
    // Create download URL
    $downloadUrl = 'https://www.pinkmilk.eu/ME/download-image.php?url=' . urlencode($imageUrl) . '&name=' . urlencode($characterName);
    
    // Create reveal page URL
    $revealUrl = 'https://www.pinkmilk.eu/ME/reveal-character.php?img=' . urlencode($imageUrl) . '&name=' . urlencode($characterName) . '&desc=' . urlencode($characterDesc);
    
    $userMessage = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { 
                font-family: Verdana, Geneva, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0; 
                background-color: #f5f5f5;
            }
            .email-wrapper { 
                max-width: 600px; 
                margin: 0 auto; 
                background-color: #ffffff; 
            }
            .header { 
                background-color: #ffffff; 
                padding: 20px; 
                text-align: center; 
            }
            .header h1 { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 16px; 
                font-weight: normal; 
                color: #000000; 
                margin: 0; 
            }
            .hero-image { 
                width: 100%; 
                height: auto; 
                display: block; 
                margin-bottom: 30px;
            }
            .hero-section { 
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); 
                padding: 40px 20px; 
                text-align: center; 
                border-radius: 20px; 
                margin: 0 20px 30px 20px;
            }
            .hero-section h2 { 
                font-family: Verdana, Geneva, sans-serif; 
                color: #ffffff; 
                font-size: 28px; 
                margin: 0 0 10px 0; 
                font-weight: normal;
            }
            .hero-section p { 
                font-family: Verdana, Geneva, sans-serif; 
                color: #cccccc; 
                font-size: 14px; 
                margin: 0;
            }
            .content { 
                padding: 0 40px 40px 40px; 
            }
            .content p { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                line-height: 1.8; 
                color: #333333; 
                margin: 0 0 20px 0;
            }
            .feature-box { 
                background-color: #f9f9f9; 
                padding: 20px; 
                margin: 25px 0; 
                border-radius: 8px;
            }
            .feature-box h3 { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 16px; 
                font-weight: bold; 
                color: #000000; 
                margin: 0 0 15px 0;
            }
            .reveal-box { 
                background: linear-gradient(135deg, #edf2f4 0%, #edf2f4 100%); 
                border: 3px solid #d90429; 
                padding: 30px; 
                margin: 30px 0; 
                border-radius: 10px; 
                text-align: center;
            }
            .reveal-box h2 {
                font-family: Verdana, Geneva, sans-serif;
                color: #03045e;
                margin-top: 0;
                font-size: 20px;
            }
            .reveal-box h3 {
                font-family: Verdana, Geneva, sans-serif;
                color: #3d348b;
                font-size: 16px;
                font-weight: normal;
            }
            .cta-button { 
                display: inline-block; 
                background-color: #d90429; 
                color: #ffffff; 
                padding: 15px 40px; 
                text-decoration: none; 
                border-radius: 25px; 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                font-weight: bold; 
                margin: 20px 0;
            }
            .gradient-box { 
                background: linear-gradient(90deg, #1A2A80 0%, #090040 100%); 
                padding: 20px; 
                margin: 20px 0; 
                border-radius: 8px;
                color: #ffffff;
            }
            .gradient-box p { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                line-height: 1.8; 
                color: #ffffff; 
                margin: 10px 0;
            }
            .gradient-divider { 
                border-top: 1px solid rgba(255, 255, 255, 0.3); 
                margin: 15px 0; 
            }
            .footer-text { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 11px; 
                color: #666666; 
                text-align: center; 
                padding: 10px 40px 30px 40px;
            }
        </style>
    </head>
    <body>
        <div class='email-wrapper'>
            <div class='header'>
                <h1>Wat een plaatje!</h1>
            </div>
            
            <img src='https://www.pinkmilk.eu/ME/MaskedHero3.webp' alt='Masked Employee' class='hero-image'>
            
            <div class='content'>
                <p><strong>Hallo " . htmlspecialchars($playerName) . "!</strong></p>
                
                <p>🎉 <strong>Geweldig nieuws!</strong> Je unieke karakter afbeelding is gegenereerd!</p>
                
                <p>Op basis van je antwoorden hebben we een bijzonder karakter voor je tot leven gebracht. Dit is jouw alter ego – een weerspiegeling van jouw geheime kant, verborgen talenten en persoonlijkheid.</p>
                
                <div class='reveal-box'>
                    <h2>🔒 GEHEIM</h2>
                    <h3>Klik hier als je heel zeker weet dat niemand op je scherm kan kijken !!</h3>
                    <p style='font-size: 14px; color: #856404; margin: 15px 0;'>⚠️ Je staat op het punt je geheime karakter te onthullen</p>
                    <a href='" . htmlspecialchars($revealUrl) . "' class='cta-button' style='text-decoration: none; display: inline-block;'>👁️ ONTHUL MIJN KARAKTER</a>
                </div>
                
                <div class='feature-box'>
                    <h3>Ook onze image generators hebben staan te zwoegen:</h3>
                    " . formatCharacterDescription($characterDesc) . "
                </div>
                
                <div class='gradient-box'>
                    <p><strong>🎭 Wat gebeurt er nu?</strong></p>
                    <p>✅ Je karakter en afbeelding zijn opgeslagen<br>
                    🎬 Binnenkort ontvang je meer informatie over de show<br>
                    🤐 <strong>Absolute geheimhouding blijft van kracht!</strong><br>
                    🎉 Bereid je voor op een onvergetelijke ervaring</p>
                    <div class='gradient-divider'></div>
                    <p><strong>⚠️ BELANGRIJK:</strong> Deel deze afbeelding of informatie niet met collega's! De <strong>€750</strong> boeteclausule blijft van kracht.</p>
                </div>
                
                <p>We kijken ernaar uit je te zien bij de show! 🎭</p>
                
                <p>Met vriendelijke groet,<br>
                Het Masked Employee Team</p>
            </div>
            
            <div class='footer-text'>
                <p>Dit is een automatisch gegenereerde email voor " . htmlspecialchars($gameName) . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $adminSubject = '🎨 Afbeelding gegenereerd: ' . $playerName;
} else {
    $subject = '🎨 Your Character Image is Ready! - ' . $gameName;
    
    // Create download URL
    $downloadUrl = 'https://www.pinkmilk.eu/ME/download-image.php?url=' . urlencode($imageUrl) . '&name=' . urlencode($characterName);
    
    // Create reveal page URL
    $revealUrl = 'https://www.pinkmilk.eu/ME/reveal-character.php?img=' . urlencode($imageUrl) . '&name=' . urlencode($characterName) . '&desc=' . urlencode($characterDesc);
    
    $userMessage = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { 
                font-family: Verdana, Geneva, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0; 
                background-color: #f5f5f5;
            }
            .email-wrapper { 
                max-width: 600px; 
                margin: 0 auto; 
                background-color: #ffffff; 
            }
            .header { 
                background-color: #ffffff; 
                padding: 20px; 
                text-align: center; 
            }
            .header h1 { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 16px; 
                font-weight: normal; 
                color: #000000; 
                margin: 0; 
            }
            .hero-image { 
                width: 100%; 
                height: auto; 
                display: block; 
                margin-bottom: 30px;
            }
            .hero-section { 
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); 
                padding: 40px 20px; 
                text-align: center; 
                border-radius: 20px; 
                margin: 0 20px 30px 20px;
            }
            .hero-section h2 { 
                font-family: Verdana, Geneva, sans-serif; 
                color: #ffffff; 
                font-size: 28px; 
                margin: 0 0 10px 0; 
                font-weight: normal;
            }
            .hero-section p { 
                font-family: Verdana, Geneva, sans-serif; 
                color: #cccccc; 
                font-size: 14px; 
                margin: 0;
            }
            .content { 
                padding: 0 40px 40px 40px; 
            }
            .content p { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                line-height: 1.8; 
                color: #333333; 
                margin: 0 0 20px 0;
            }
            .feature-box { 
                background-color: #f9f9f9; 
                padding: 20px; 
                margin: 25px 0; 
                border-radius: 8px;
            }
            .feature-box h3 { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 16px; 
                font-weight: bold; 
                color: #000000; 
                margin: 0 0 15px 0;
            }
            .reveal-box { 
                background: linear-gradient(135deg, #edf2f4 0%, #edf2f4 100%); 
                border: 3px solid #d90429; 
                padding: 30px; 
                margin: 30px 0; 
                border-radius: 10px; 
                text-align: center;
            }
            .reveal-box h2 {
                font-family: Verdana, Geneva, sans-serif;
                color: #03045e;
                margin-top: 0;
                font-size: 20px;
            }
            .reveal-box h3 {
                font-family: Verdana, Geneva, sans-serif;
                color: #3d348b;
                font-size: 16px;
                font-weight: normal;
            }
            .cta-button { 
                display: inline-block; 
                background-color: #d90429; 
                color: #ffffff; 
                padding: 15px 40px; 
                text-decoration: none; 
                border-radius: 25px; 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                font-weight: bold; 
                margin: 20px 0;
            }
            .gradient-box { 
                background: linear-gradient(90deg, #1A2A80 0%, #090040 100%); 
                padding: 20px; 
                margin: 20px 0; 
                border-radius: 8px;
                color: #ffffff;
            }
            .gradient-box p { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                line-height: 1.8; 
                color: #ffffff; 
                margin: 10px 0;
            }
            .gradient-divider { 
                border-top: 1px solid rgba(255, 255, 255, 0.3); 
                margin: 15px 0; 
            }
            .footer-text { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 11px; 
                color: #666666; 
                text-align: center; 
                padding: 10px 40px 30px 40px;
            }
        </style>
    </head>
    <body>
        <div class='email-wrapper'>
            <div class='header'>
                <h1>Wat een plaatje!</h1>
            </div>
            
            <img src='https://www.pinkmilk.eu/ME/MaskedHero3.webp' alt='Masked Employee' class='hero-image'>
            
            <div class='content'>
                <p><strong>Hello " . htmlspecialchars($playerName) . "!</strong></p>
                
                <p>🎉 <strong>Great news!</strong> Your unique character image has been generated!</p>
                
                <p>Based on your answers, we have brought a special character to life for you. This is your alter ego – a reflection of your secret side, hidden talents and personality.</p>
                
                <div class='reveal-box'>
                    <h2>🔒 SECRET</h2>
                    <h3>Click here only if you're absolutely sure no one can see your screen !!</h3>
                    <p style='font-size: 14px; color: #3d348b; margin: 15px 0;'>⚠️ You're about to reveal your secret character</p>
                    <a href='" . htmlspecialchars($revealUrl) . "' class='cta-button' style='text-decoration: none; display: inline-block;'>👁️ REVEAL MY CHARACTER</a>
                </div>
                
                <div class='feature-box'>
                    <h3>Yes, this is who you really are deep down inside:</h3>
                    <p><strong>" . htmlspecialchars($characterName) . "</strong></p>
                    <p>" . nl2br(htmlspecialchars($characterDesc)) . "</p>
                </div>
                
                <div class='gradient-box'>
                    <p><strong>🎭 What happens next?</strong></p>
                    <p>✅ Your character and image are saved<br>
                    🎬 You'll receive more information about the show soon<br>
                    🤐 <strong>Absolute confidentiality remains in effect!</strong><br>
                    🎉 Prepare for an unforgettable experience</p>
                    <div class='gradient-divider'></div>
                    <p><strong>⚠️ IMPORTANT:</strong> Don't share this image or information with colleagues! The <strong>€750</strong> penalty clause remains in effect.</p>
                </div>
                
                <p>We look forward to seeing you at the show! 🎭</p>
                
                <p>Best regards,<br>
                The Masked Employee Team</p>
            </div>
            
            <div class='footer-text'>
                <p>This is an automatically generated email for " . htmlspecialchars($gameName) . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $adminSubject = '🎨 Image generated: ' . $playerName;
}

// Admin notification email
$adminMessage = "
<html>
<body style='font-family: Arial, sans-serif;'>
    <h2>🎨 AFBEELDING COMPLEET: " . htmlspecialchars($gameName) . "</h2>
    
    <p><strong>Deelnemer:</strong> " . htmlspecialchars($playerName) . "</p>
    <p><strong>Email:</strong> " . htmlspecialchars($userEmail) . "</p>
    <p><strong>Status:</strong> ✅ VOLLEDIG COMPLEET (afbeelding gegenereerd)</p>
    <p><strong>Tijdstip:</strong> " . date('d-m-Y H:i:s') . "</p>
    
    <hr>
    
    <div style='text-align: center; margin: 20px 0;'>
        <h3>🎭 Gegenereerde Afbeelding:</h3>
        <img src='" . htmlspecialchars($imageUrl) . "' alt='Character Image' style='max-width: 500px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);' />
    </div>
    
    <p><strong>Afbeelding URL:</strong><br>
    <a href='" . htmlspecialchars($imageUrl) . "'>" . htmlspecialchars($imageUrl) . "</a></p>
    
    <hr>
    
    <h3>🎭 Karakter:</h3>
    <p>" . nl2br(htmlspecialchars($characterDesc)) . "</p>
    
    <h3>🌍 Wereld:</h3>
    <p>" . nl2br(htmlspecialchars($worldDesc)) . "</p>
    
    <p><em>Bekijk alle gegevens in PocketBase.</em></p>
</body>
</html>
";

// Send email to user using simple mail() function
$userResult = sendEmailSimple($userEmail, $subject, $userMessage);
$userEmailSent = $userResult['success'];

// Log user email attempt
error_log("User final email to $userEmail: " . ($userEmailSent ? 'SUCCESS' : 'FAILED'));
if (!$userEmailSent) {
    error_log("Error details: " . $userResult['error']);
}

// Send admin email using simple mail() function
$adminResult = sendEmailSimple($adminEmail, $adminSubject, $adminMessage, 'maskedemployee@pinkmilk.eu', 'Masked Employee', $userEmail);
$adminEmailSent = $adminResult['success'];

// Log admin email attempt
error_log("Admin final email to $adminEmail: " . ($adminEmailSent ? 'SUCCESS' : 'FAILED'));
if (!$adminEmailSent) {
    error_log("Error details: " . $adminResult['error']);
}
error_log("=== FINAL EMAIL REQUEST END ===");

// Response
if ($userEmailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Final email with image sent',
        'userEmail' => $userEmail,
        'imageUrl' => $imageUrl,
        'userEmailSent' => $userEmailSent,
        'adminEmailSent' => $adminEmailSent
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send final email',
        'userEmailSent' => $userEmailSent,
        'adminEmailSent' => $adminEmailSent
    ]);
}
?>
