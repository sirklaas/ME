<?php
/**
 * Send final email with generated character image
 * Sent after image generation is complete
 */

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
    $userMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 700px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .image-container { text-align: center; margin: 30px 0; }
            .character-image { max-width: 100%; border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
            .section { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #8A2BE2; border-radius: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
            h1 { margin: 0; font-size: 1.8em; }
            h3 { color: #8A2BE2; }
            .emoji { font-size: 2em; }
            .cta-box { background: #d4edda; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; border-radius: 5px; }
            .download-btn { display: inline-block; background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; box-shadow: 0 4px 15px rgba(138, 43, 226, 0.3); }
            .download-btn:hover { background: linear-gradient(135deg, #9932CC, #8A2BE2); }
        </style>
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
                
                <div class='image-container'>
                    <h3>🎭 " . htmlspecialchars($characterName) . "</h3>
                    <img src='" . htmlspecialchars($imageUrl) . "' alt='Jouw Karakter' class='character-image' />
                    <div style='margin-top: 20px;'>
                        <form method='get' action='" . htmlspecialchars($imageUrl) . "' target='_blank'>
                            <button type='submit' class='download-btn' style='border: none; cursor: pointer;'>📥 Download Afbeelding</button>
                        </form>
                    </div>
                </div>
                
                <div class='section'>
                    <h3>🎭 Jouw Karakter</h3>
                    <p><strong>" . htmlspecialchars($characterName) . "</strong></p>
                    <p>" . nl2br(htmlspecialchars($characterDesc)) . "</p>
                </div>
                
                <div class='section'>
                    <h3>🌍 En waar je zoal uithangt</h3>
                    <p>" . nl2br(htmlspecialchars($worldDesc)) . "</p>
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
    $userMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 700px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .image-container { text-align: center; margin: 30px 0; }
            .character-image { max-width: 100%; border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
            .section { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #8A2BE2; border-radius: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
            h1 { margin: 0; font-size: 1.8em; }
            h3 { color: #8A2BE2; }
            .emoji { font-size: 2em; }
            .cta-box { background: #d4edda; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; border-radius: 5px; }
            .download-btn { display: inline-block; background: linear-gradient(135deg, #8A2BE2, #9932CC); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; box-shadow: 0 4px 15px rgba(138, 43, 226, 0.3); }
            .download-btn:hover { background: linear-gradient(135deg, #9932CC, #8A2BE2); }
        </style>
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
                
                <div class='image-container'>
                    <h3>🎭 " . htmlspecialchars($characterName) . "</h3>
                    <img src='" . htmlspecialchars($imageUrl) . "' alt='Your Character' class='character-image' />
                    <div style='margin-top: 20px;'>
                        <form method='get' action='" . htmlspecialchars($imageUrl) . "' target='_blank'>
                            <button type='submit' class='download-btn' style='border: none; cursor: pointer;'>📥 Download Image</button>
                        </form>
                    </div>
                </div>
                
                <div class='section'>
                    <h3>🎭 Your Character</h3>
                    <p><strong>" . htmlspecialchars($characterName) . "</strong></p>
                    <p>" . nl2br(htmlspecialchars($characterDesc)) . "</p>
                </div>
                
                <div class='section'>
                    <h3>🌍 And where you hang out</h3>
                    <p>" . nl2br(htmlspecialchars($worldDesc)) . "</p>
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

// Email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: The Masked Employee <noreply@pinkmilk.eu>" . "\r\n";

// Send email to user
$userEmailSent = mail($userEmail, $subject, $userMessage, $headers);

// Log user email attempt
error_log("User final email to $userEmail: " . ($userEmailSent ? 'SUCCESS' : 'FAILED'));

// Admin email
$adminHeaders = "MIME-Version: 1.0" . "\r\n";
$adminHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$adminHeaders .= "From: Masked Employee <noreply@pinkmilk.eu>" . "\r\n";
$adminHeaders .= "Reply-To: $userEmail" . "\r\n";

$adminEmailSent = mail($adminEmail, $adminSubject, $adminMessage, $adminHeaders);

// Log admin email attempt
error_log("Admin final email to $adminEmail: " . ($adminEmailSent ? 'SUCCESS' : 'FAILED'));
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
