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
    // Replace section headers with user-friendly versions
    $desc = preg_replace('/\d+\.\s*KARAKTER\s*\([^)]+\):\s*/i', "\n\n🎭 Jouw Karakter\n\n", $desc);
    $desc = preg_replace('/\d+\.\s*OMGEVING\s*\([^)]+\):\s*/i', "\n\n🌍 Dit is jouw wereld\n\n", $desc);
    
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
    $subject = '🎨 Jouw Karakter Afbeelding is Klaar! - ' . $gameName;
    
    // Create download URL
    $downloadUrl = 'https://www.pinkmilk.eu/ME/download-image.php?url=' . urlencode($imageUrl) . '&name=' . urlencode($characterName);
    
    // Create reveal page URL
    $revealUrl = 'https://www.pinkmilk.eu/ME/reveal-character.php?img=' . urlencode($imageUrl) . '&name=' . urlencode($characterName) . '&desc=' . urlencode($characterDesc);
    
    $userMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 700px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .image-container { text-align: center; margin: 30px 0; }
            .character-image { max-width: 100%; border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); margin-top: 20px; }
            .section { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #8A2BE2; border-radius: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
            h1 { margin: 0; font-size: 1.8em; }
            h3 { color: #8A2BE2; }
            .emoji { font-size: 2em; }
            .cta-box { background: #d4edda; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; border-radius: 5px; }
            .download-btn { display: inline-block; background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; box-shadow: 0 4px 15px rgba(138, 43, 226, 0.3); border: none; cursor: pointer; }
            .download-btn:hover { background: linear-gradient(135deg, #9932CC, #8A2BE2); }
            .reveal-box { background: #fff3cd; border: 3px solid #ffc107; padding: 30px; margin: 30px 0; border-radius: 10px; text-align: center; }
            .reveal-btn { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 20px 50px; font-size: 1.2em; font-weight: bold; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4); transition: all 0.3s; }
            .reveal-btn:hover { background: linear-gradient(135deg, #c82333, #dc3545); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(220, 53, 69, 0.5); }
            .hidden-content { display: none; }
        </style>
        <script>
            function revealImage() {
                document.getElementById('revealBox').style.display = 'none';
                document.getElementById('imageContent').style.display = 'block';
            }
        </script>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='emoji'>🎨</div>
                <h1>Met behulp van de allernieuwste AI technologie hebben we op basis van jouw antwoorden een Uniek Karakter gecreëerd.</h1>
            </div>
            <div class='content'>
                <h2>Hallo " . htmlspecialchars($playerName) . "!</h2>
                
                <p>🎉 <strong>Geweldig nieuws!</strong> Je unieke karakter afbeelding is gegenereerd!</p>
                
                <p>Op basis van je antwoorden hebben we een bijzonder karakter voor je tot leven gebracht. Dit is jouw alter ego – een weerspiegeling van jouw geheime kant, verborgen talenten en persoonlijkheid.</p>
                
                <!-- Reveal Box -->
                <div class='reveal-box'>
                    <h2 style='color: #856404; margin-top: 0;'>🔒 GEHEIM</h2>
                    <h3 style='color: #856404;'>Klik hier als je heel zeker weet dat niemand op je scherm kan kijken !!</h3>
                    <p style='font-size: 0.95em; color: #856404;'>⚠️ Je staat op het punt je geheime karakter te onthullen</p>
                    <a href='" . htmlspecialchars($revealUrl) . "' class='reveal-btn' style='text-decoration: none; display: inline-block;'>👁️ ONTHUL MIJN KARAKTER</a>
                </div>
                
                <div class='section'>
                    <h3>Ja dit ben je eigenlijk heel diep van binnen:</h3>
                    " . formatCharacterDescription($characterDesc) . "
                </div>
                
                <div class='cta-box'>
                    <h3 style='color: #155724; margin-top: 0;'>🎭 Wat gebeurt er nu?</h3>
                    <ul>
                        <li>✅ Je karakter en afbeelding zijn opgeslagen</li>
                        <li>🎬 Binnenkort ontvang je meer informatie over de show</li>
                        <li>🤐 <strong>Absolute geheimhouding blijft van kracht!</strong></li>
                        <li>🎉 Bereid je voor op een onvergetelijke ervaring</li>
                    </ul>
                </div>
                
                <p style='background: #f8d7da; padding: 15px; border-left: 4px solid #f5c6cb; margin: 20px 0;'>
                    <strong>⚠️ BELANGRIJK:</strong> Deel deze afbeelding of informatie niet met collega's! 
                    De €9,750 boeteclausule blijft van kracht.
                </p>
                
                <p>We kijken ernaar uit je te zien bij de show! 🎭</p>
                
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
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 700px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .image-container { text-align: center; margin: 30px 0; }
            .character-image { max-width: 100%; border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); margin-top: 20px; }
            .section { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #8A2BE2; border-radius: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
            h1 { margin: 0; font-size: 1.8em; }
            h3 { color: #8A2BE2; }
            .emoji { font-size: 2em; }
            .cta-box { background: #d4edda; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; border-radius: 5px; }
            .download-btn { display: inline-block; background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; box-shadow: 0 4px 15px rgba(138, 43, 226, 0.3); border: none; cursor: pointer; }
            .download-btn:hover { background: linear-gradient(135deg, #9932CC, #8A2BE2); }
            .reveal-box { background: #fff3cd; border: 3px solid #ffc107; padding: 30px; margin: 30px 0; border-radius: 10px; text-align: center; }
            .reveal-btn { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 20px 50px; font-size: 1.2em; font-weight: bold; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4); transition: all 0.3s; }
            .reveal-btn:hover { background: linear-gradient(135deg, #c82333, #dc3545); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(220, 53, 69, 0.5); }
            .hidden-content { display: none; }
        </style>
        <script>
            function revealImage() {
                document.getElementById('revealBox').style.display = 'none';
                document.getElementById('imageContent').style.display = 'block';
            }
        </script>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='emoji'>🎨</div>
                <h1>Using the latest AI technology, we have created a Unique Character based on your answers.</h1>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($playerName) . "!</h2>
                
                <p>🎉 <strong>Great news!</strong> Your unique character image has been generated!</p>
                
                <p>Based on your answers, we have brought a special character to life for you. This is your alter ego – a reflection of your secret side, hidden talents and personality.</p>
                
                <!-- Reveal Box -->
                <div class='reveal-box'>
                    <h2 style='color: #856404; margin-top: 0;'>🔒 SECRET</h2>
                    <h3 style='color: #856404;'>Click here only if you're absolutely sure no one can see your screen !!</h3>
                    <p style='font-size: 0.95em; color: #856404;'>⚠️ You're about to reveal your secret character</p>
                    <a href='" . htmlspecialchars($revealUrl) . "' class='reveal-btn' style='text-decoration: none; display: inline-block;'>👁️ REVEAL MY CHARACTER</a>
                </div>
                
                <div class='section'>
                    <h3>Yes, this is who you really are deep down inside:</h3>
                    <p><strong>" . htmlspecialchars($characterName) . "</strong></p>
                    <p>" . nl2br(htmlspecialchars($characterDesc)) . "</p>
                </div>
                
                <div class='cta-box'>
                    <h3 style='color: #155724; margin-top: 0;'>🎭 What happens next?</h3>
                    <ul>
                        <li>✅ Your character and image are saved</li>
                        <li>🎬 You'll receive more information about the show soon</li>
                        <li>🤐 <strong>Absolute confidentiality remains in effect!</strong></li>
                        <li>🎉 Prepare for an unforgettable experience</li>
                    </ul>
                </div>
                
                <p style='background: #f8d7da; padding: 15px; border-left: 4px solid #f5c6cb; margin: 20px 0;'>
                    <strong>⚠️ IMPORTANT:</strong> Don't share this image or information with colleagues! 
                    The €9,750 penalty clause remains in effect.
                </p>
                
                <p>We look forward to seeing you at the show! 🎭</p>
                
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
