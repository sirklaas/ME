<?php
/**
 * Send email with character + world descriptions
 * Sent immediately after user approves descriptions
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
    
    // Replace personality section heading
    $desc = preg_replace('/===\s*PERSOONLIJKHEID\s*===/i', '-----   Persoonlijkheid   -----', $desc);
    
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
error_log("=== DESCRIPTION EMAIL REQUEST ===");
error_log("User Email: $userEmail");
error_log("Player Name: $playerName");
error_log("Language: $language");

// Validate email
if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

// Email content based on language
if ($language === 'nl') {
    $subject = '🎭 Jouw Karakter - ' . $gameName;
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
            }
            .hero-text { 
                background-color: #ffffff; 
                padding: 20px 40px; 
                text-align: center;
            }
            .hero-text p { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                line-height: 1.6; 
                color: #333333; 
                margin: 0;
            }
            .divider { 
                border-top: 1px solid #cccccc; 
                margin: 20px 40px; 
            }
            .content { 
                padding: 0 40px 20px 40px; 
            }
            .content p { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                line-height: 1.8; 
                color: #333333; 
                margin: 0 0 15px 0;
            }
            .character-box { 
                background-color: #caf0f8; 
                padding: 20px; 
                margin: 20px 0; 
                border-radius: 8px;
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
                <h1>TaTa, we zijn eruit!</h1>
            </div>
            
            <img src='https://www.pinkmilk.eu/ME/mask_hero.webp' alt='Masked Employee' class='hero-image'>
            
            <div class='hero-text'>
                <p>Met behulp van de allernieuwste AI-technologie en op basis van de ingevulde antwoorden hebben we jouw unieke karakter gecreëerd.</p>
            </div>
            
            <div class='divider'></div>
            
            <div class='content'>
                <p><strong>Hallo " . htmlspecialchars($playerName) . "!</strong></p>
                
                <p>Op basis van je antwoorden hebben we een bijzonder karakter voor je tot leven gebracht. Dit is jouw alter ego – een weerspiegeling van jouw geheime kant, verborgen talenten en persoonlijkheid.</p>
                
                <div class='character-box'>
                    " . formatCharacterDescription($characterDesc) . "
                </div>
                
                <div class='gradient-box'>
                    <p><strong>🎨 Wat gebeurt er nu?</strong></p>
                    <p>We genereren nu een unieke afbeelding van je karakter. Je ontvangt binnenkort een tweede email met de afbeelding!</p>
                    <div class='gradient-divider'></div>
                    <p><strong>⚠️ Belangrijk:</strong> Vergeet niet dat absolute geheimhouding verplicht is! Vertel niemand over je deelname, karakter of de show.</p>
                </div>
                
                <p>Tot snel bij de show! 🎭</p>
                
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
    
    $adminSubject = '🎭 Karakter goedgekeurd: ' . $playerName;
} else {
    $subject = '🎭 Your Character - ' . $gameName;
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
                padding: 30px 20px 10px 20px; 
                text-align: center; 
            }
            .header h1 { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 24px; 
                font-weight: normal; 
                color: #000000; 
                margin: 0 0 20px 0; 
            }
            .hero-section { 
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); 
                padding: 40px 20px; 
                text-align: center; 
                border-radius: 20px; 
                margin: 0 20px 30px 20px;
                position: relative;
                overflow: hidden;
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
            .cta-button { 
                display: inline-block; 
                background-color: #00bcd4; 
                color: #ffffff; 
                padding: 15px 40px; 
                text-decoration: none; 
                border-radius: 25px; 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 14px; 
                font-weight: bold; 
                margin: 20px 0;
                text-align: center;
            }
            .footer { 
                background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%); 
                color: #ffffff; 
                padding: 30px 40px; 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 12px;
            }
            .footer h4 { 
                font-family: Verdana, Geneva, sans-serif; 
                font-size: 16px; 
                font-weight: bold; 
                margin: 0 0 15px 0; 
                color: #ffffff;
            }
            .footer p { 
                font-family: Verdana, Geneva, sans-serif; 
                margin: 5px 0; 
                color: #cccccc; 
                line-height: 1.6;
            }
            .footer a { 
                color: #00bcd4; 
                text-decoration: none;
            }
            .warning-box {
                background-color: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
                border-radius: 4px;
            }
            .danger-box {
                background-color: #f8d7da;
                border-left: 4px solid #dc3545;
                padding: 15px;
                margin: 20px 0;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <div class='email-wrapper'>
            <div class='header'>
                <h1>The Masked Employee</h1>
            </div>
            
            <div class='hero-section'>
                <h2>🎭 Your Character</h2>
                <p>Using the latest AI technology</p>
            </div>
            
            <div class='content'>
                <p><strong>Hello " . htmlspecialchars($playerName) . "!</strong></p>
                
                <p>Based on your answers, we have brought a special character to life for you. This is your alter ego – a reflection of your secret side, hidden talents and personality.</p>
                
                <div class='feature-box'>
                    <h3>Yes, this is who you really are deep down inside:</h3>
                    " . formatCharacterDescription($characterDesc) . "
                </div>
                
                <div class='warning-box'>
                    <strong>🎨 What happens next?</strong><br>
                    We're now generating a unique image of your character. You'll receive a second email with the image soon!
                </div>
                
                <div class='danger-box'>
                    <strong>⚠️ Important:</strong> Remember that absolute confidentiality is required! Don't tell anyone about your participation, character or the show.
                </div>
                
                <p>See you at the show! 🎭</p>
                
                <p>Best regards,<br>
                The Masked Employee Team</p>
            </div>
            
            <div class='footer'>
                <h4>Need help?</h4>
                <p>This is an automatically generated email for " . htmlspecialchars($gameName) . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $adminSubject = '🎭 Character approved: ' . $playerName;
}

// Admin notification email
$adminMessage = "
<html>
<body style='font-family: Arial, sans-serif;'>
    <h2>🎭 Karakter goedgekeurd: " . htmlspecialchars($gameName) . "</h2>
    
    <p><strong>Deelnemer:</strong> " . htmlspecialchars($playerName) . "</p>
    <p><strong>Email:</strong> " . htmlspecialchars($userEmail) . "</p>
    <p><strong>Taal:</strong> " . strtoupper($language) . "</p>
    <p><strong>Status:</strong> Beschrijvingen goedgekeurd, afbeelding wordt gegenereerd</p>
    <p><strong>Tijdstip:</strong> " . date('d-m-Y H:i:s') . "</p>
    
    <hr>
    
    <h3>🎭 Karakter:</h3>
    <p>" . nl2br(htmlspecialchars($characterDesc)) . "</p>
    
    <h3>🌍 Wereld:</h3>
    <p>" . nl2br(htmlspecialchars($worldDesc)) . "</p>
    
    <p><em>Bekijk de volledige gegevens in PocketBase.</em></p>
</body>
</html>
";

// Send email to user using simple mail() function
$userResult = sendEmailSimple($userEmail, $subject, $userMessage);
$userEmailSent = $userResult['success'];

// Log user email attempt
error_log("User description email to $userEmail: " . ($userEmailSent ? 'SUCCESS' : 'FAILED'));
if (!$userEmailSent) {
    error_log("Error details: " . $userResult['error']);
}

// Send admin email using simple mail() function
$adminResult = sendEmailSimple($adminEmail, $adminSubject, $adminMessage, 'maskedemployee@pinkmilk.eu', 'Masked Employee', $userEmail);
$adminEmailSent = $adminResult['success'];

// Log admin email attempt
error_log("Admin description email to $adminEmail: " . ($adminEmailSent ? 'SUCCESS' : 'FAILED'));
if (!$adminEmailSent) {
    error_log("Error details: " . $adminResult['error']);
}
error_log("=== EMAIL REQUEST END ===");

// Response
if ($userEmailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Description email sent',
        'userEmail' => $userEmail,
        'userEmailSent' => $userEmailSent,
        'adminEmailSent' => $adminEmailSent
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send user email',
        'userEmailSent' => $userEmailSent,
        'adminEmailSent' => $adminEmailSent
    ]);
}
?>
