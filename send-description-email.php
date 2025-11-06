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
    // Replace section headers with user-friendly versions
    $desc = preg_replace('/\d+\.\s*KARAKTER\s*\([^)]+\):\s*/i', '<h4 style="color: #8A2BE2; margin-top: 20px;">🎭 Jouw Karakter</h4>', $desc);
    $desc = preg_replace('/\d+\.\s*OMGEVING\s*\([^)]+\):\s*/i', '<h4 style="color: #8A2BE2; margin-top: 20px;">🌍 Dit is jouw wereld</h4>', $desc);
    
    // Pattern to match "De [Type] genaamd [Name]" at start of description
    $pattern = '/(De\s+\w+\s+genaamd\s+[\w\s]+)/';
    
    if (preg_match($pattern, $desc, $matches)) {
        $heading = trim($matches[1]);
        // Remove the heading from description and add it as H3
        $desc = preg_replace($pattern, '', $desc, 1);
        $desc = trim($desc);
        
        // Extract only first character section (stop at next "De [Type] genaamd")
        $nextCharPattern = '/\n\n+De\s+\w+\s+genaamd/';
        if (preg_match($nextCharPattern, $desc, $nextMatch, PREG_OFFSET_CAPTURE)) {
            $desc = substr($desc, 0, $nextMatch[0][1]);
        }
        
        // Return formatted with heading
        return "<h3 style='color: #8A2BE2; margin-top: 0;'>" . htmlspecialchars($heading) . "</h3>" . nl2br(htmlspecialchars($desc));
    }
    
    // No pattern found, return as is (but still process the replacements)
    return nl2br($desc);
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
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .section { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #8A2BE2; border-radius: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
            h1 { margin: 0; font-size: 1.8em; }
            h3 { color: #8A2BE2; }
            .emoji { font-size: 2em; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='emoji'>🎭</div>
                <h1>Met behulp van de allernieuwste AI technologie hebben we op basis van jouw antwoorden een Uniek Karakter gecreëerd.</h1>
            </div>
            <div class='content'>
                <h2>Hallo " . htmlspecialchars($playerName) . "!</h2>
                
                <p>Op basis van je antwoorden hebben we een bijzonder karakter voor je tot leven gebracht. Dit is jouw alter ego – een weerspiegeling van jouw geheime kant, verborgen talenten en persoonlijkheid. Ontdek wie je bent achter het masker...</p>
                
                <div class='section'>
                    <h3>Ja dit ben je eigenlijk heel diep van binnen:</h3>
                    " . formatCharacterDescription($characterDesc) . "
                </div>
                
                <p style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>
                    <strong>🎨 Wat gebeurt er nu?</strong><br>
                    We genereren nu een unieke afbeelding van je karakter in deze wereld. 
                    Je ontvangt binnenkort een tweede email met de afbeelding!
                </p>
                
                <p style='background: #f8d7da; padding: 15px; border-left: 4px solid #f5c6cb; margin: 20px 0;'>
                    <strong>⚠️ Belangrijk:</strong> Vergeet niet dat absolute geheimhouding verplicht is! 
                    Vertel niemand over je deelname, karakter of de show.
                </p>
                
                <p>Tot snel bij de show! 🎭</p>
                
                <p>Met vriendelijke groet,<br>
                Het Masked Employee Team</p>
            </div>
            <div class='footer'>
                <p>Dit is een automatisch gegenereerde email</p>
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
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .section { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #8A2BE2; border-radius: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
            h1 { margin: 0; font-size: 1.8em; }
            h3 { color: #8A2BE2; }
            .emoji { font-size: 2em; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='emoji'>🎭</div>
                <h1>Using the latest AI technology, we have created a Unique Character based on your answers.</h1>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($playerName) . "!</h2>
                
                <p>Based on your answers, we have brought a special character to life for you. This is your alter ego – a reflection of your secret side, hidden talents and personality. Discover who you are behind the mask...</p>
                
                <div class='section'>
                    <h3>Yes, this is who you really are deep down inside:</h3>
                    " . formatCharacterDescription($characterDesc) . "
                </div>
                
                <p style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>
                    <strong>🎨 What happens next?</strong><br>
                    We're now generating a unique image of your character in this world. 
                    You'll receive a second email with the image soon!
                </p>
                
                <p style='background: #f8d7da; padding: 15px; border-left: 4px solid #f5c6cb; margin: 20px 0;'>
                    <strong>⚠️ Important:</strong> Remember that absolute confidentiality is required! 
                    Don't tell anyone about your participation, character or the show.
                </p>
                
                <p>See you at the show! 🎭</p>
                
                <p>Best regards,<br>
                The Masked Employee Team</p>
            </div>
            <div class='footer'>
                <p>This is an automatically generated email</p>
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
