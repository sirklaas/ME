<?php
/**
 * Reveal Character Page
 * Shows character image with dramatic reveal
 */

// Get parameters
$imageUrl = $_GET['img'] ?? '';
$characterName = $_GET['name'] ?? 'Your Character';
$description = $_GET['desc'] ?? '';

if (empty($imageUrl)) {
    die('No image URL provided');
}

// Format character description
function extractFirstCharacter($aiSummary) {
    // Replace section headers with user-friendly versions
    $aiSummary = preg_replace('/\d+\.\s*KARAKTER\s*\([^)]+\):\s*/i', "\n\n🎭 Jouw Karakter\n\n", $aiSummary);
    $aiSummary = preg_replace('/\d+\.\s*OMGEVING\s*\([^)]+\):\s*/i', "\n\n🌍 Dit is jouw wereld\n\n", $aiSummary);
    
    // Extract only first character section (stop at next "De [Type] genaamd")
    $nextPattern = '/\n\n+De\s+\w+\s+genaamd/';
    if (preg_match($nextPattern, $aiSummary, $nextMatches, PREG_OFFSET_CAPTURE, 50)) {
        // Extract from start to next character
        $aiSummary = substr($aiSummary, 0, $nextMatches[0][1]);
    }
    
    // Clean up excessive line breaks (more than 2 consecutive newlines)
    $aiSummary = preg_replace('/\n{3,}/', "\n\n", $aiSummary);
    
    // If no pattern found or single character, return as is
    return trim($aiSummary);
}

$description = extractFirstCharacter(urldecode($description));

// Clean up character name (remove duplicates like "Philip\nPhilip")
$nameParts = preg_split('/[\n\r]+/', $characterName);
$nameParts = array_map('trim', $nameParts);
$nameParts = array_filter($nameParts);
if (count($nameParts) > 1 && $nameParts[0] === end($nameParts)) {
    $characterName = $nameParts[0]; // Use first if duplicated
} else {
    $characterName = $nameParts[0] ?? $characterName; // Use first line
}

// Create download URL
$downloadUrl = 'download-image.php?url=' . urlencode($imageUrl) . '&name=' . urlencode($characterName);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎭 Reveal Your Character</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Barlow Semi Condensed', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
        }

        .reveal-box {
            background: #fff3cd;
            border: 3px solid #ffc107;
            padding: 50px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .reveal-box h1 {
            color: #856404;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .reveal-box h2 {
            color: #856404;
            font-size: 1.5em;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .reveal-box p {
            color: #856404;
            font-size: 1.1em;
            margin-bottom: 30px;
        }

        .reveal-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 25px 60px;
            font-size: 1.4em;
            font-weight: bold;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
            transition: all 0.3s;
        }

        .reveal-btn:hover {
            background: linear-gradient(135deg, #c82333, #dc3545);
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(220, 53, 69, 0.5);
        }

        .character-content {
            display: none;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .character-content.active {
            display: block;
        }

        .character-image {
            width: 100%;
            max-width: 600px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin: 20px auto;
            display: block;
        }

        .character-name {
            font-size: 2.5em;
            font-weight: 700;
            color: #8A2BE2;
            margin: 0 0 30px 0;
            text-align: center;
            font-family: 'Barlow Semi Condensed', sans-serif;
        }

        .character-description {
            color: #333;
            line-height: 1.8;
            font-size: 1.1em;
            margin: 20px 0;
            white-space: pre-line;
        }

        .download-btn {
            display: inline-block;
            background: linear-gradient(135deg, #8A2BE2, #9932CC);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 20px 0;
            box-shadow: 0 6px 20px rgba(138, 43, 226, 0.3);
            transition: all 0.3s;
        }

        .download-btn:hover {
            background: linear-gradient(135deg, #9932CC, #8A2BE2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(138, 43, 226, 0.4);
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Reveal Box -->
        <div class="reveal-box" id="revealBox">
            <h1>🔒 GEHEIM</h1>
            <h2>Klik hier als je heel zeker weet dat niemand op je scherm kan kijken !!</h2>
            <p>⚠️ Je staat op het punt je geheime karakter te onthullen</p>
            <button class="reveal-btn" onclick="revealCharacter()">👁️ ONTHUL MIJN KARAKTER</button>
        </div>

        <!-- Character Content (Hidden) -->
        <div class="character-content" id="characterContent">
            <h1 class="character-name"><?php echo htmlspecialchars($characterName); ?></h1>
            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Your Character" class="character-image">
            
            <?php if (!empty($description)): ?>
            <div class="character-description">
                <?php echo nl2br($description); ?>
            </div>
            <?php endif; ?>
            
            <div class="button-container">
                <a href="<?php echo htmlspecialchars($downloadUrl); ?>" class="download-btn">📥 Download Afbeelding</a>
            </div>
        </div>
    </div>

    <script>
        function revealCharacter() {
            document.getElementById('revealBox').style.display = 'none';
            document.getElementById('characterContent').classList.add('active');
        }
    </script>
</body>
</html>
