<?php
/**
 * Test SMTP Authentication Only
 * This will help us debug the authentication issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔐 SMTP Authentication Test</h1>";

// SMTP credentials
$host = 'sh-woe014.hostslim.nl';
$port = 587;
$username = 'maskedemployee@pinkmilk.eu';
$password = 'M@sked03';

echo "<h2>Testing Connection & Authentication</h2>";
echo "<p><strong>Host:</strong> $host<br>";
echo "<strong>Port:</strong> $port<br>";
echo "<strong>Username:</strong> $username<br>";
echo "<strong>Password:</strong> " . str_repeat('*', strlen($password)) . "</p>";

// Try to connect
$connection = @fsockopen($host, $port, $errno, $errstr, 10);

if (!$connection) {
    echo "<p style='color: red;'>❌ Cannot connect to $host:$port</p>";
    echo "<p>Error: $errstr ($errno)</p>";
    exit;
}

echo "<p style='color: green;'>✅ Connected to $host:$port</p>";

// Read server greeting
$response = fgets($connection, 515);
echo "<p><strong>Server greeting:</strong> " . htmlspecialchars($response) . "</p>";

// Send EHLO
fputs($connection, "EHLO pinkmilk.eu\r\n");
$response = '';
while ($line = fgets($connection, 515)) {
    $response .= $line;
    if (substr($line, 3, 1) == ' ') break;
}
echo "<p><strong>EHLO response:</strong><br><pre>" . htmlspecialchars($response) . "</pre></p>";

// Start TLS
fputs($connection, "STARTTLS\r\n");
$response = fgets($connection, 515);
echo "<p><strong>STARTTLS response:</strong> " . htmlspecialchars($response) . "</p>";

if (strpos($response, '220') === 0) {
    echo "<p style='color: green;'>✅ STARTTLS accepted</p>";
    
    // Enable crypto
    $crypto = stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    
    if ($crypto) {
        echo "<p style='color: green;'>✅ TLS encryption enabled</p>";
        
        // Send EHLO again after STARTTLS
        fputs($connection, "EHLO pinkmilk.eu\r\n");
        $response = '';
        while ($line = fgets($connection, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        echo "<p><strong>EHLO after TLS:</strong><br><pre>" . htmlspecialchars($response) . "</pre></p>";
        
        // Try AUTH LOGIN
        fputs($connection, "AUTH LOGIN\r\n");
        $response = fgets($connection, 515);
        echo "<p><strong>AUTH LOGIN response:</strong> " . htmlspecialchars($response) . "</p>";
        
        if (strpos($response, '334') === 0) {
            // Send username
            fputs($connection, base64_encode($username) . "\r\n");
            $response = fgets($connection, 515);
            echo "<p><strong>Username response:</strong> " . htmlspecialchars($response) . "</p>";
            
            if (strpos($response, '334') === 0) {
                // Send password
                fputs($connection, base64_encode($password) . "\r\n");
                $response = fgets($connection, 515);
                echo "<p><strong>Password response:</strong> " . htmlspecialchars($response) . "</p>";
                
                if (strpos($response, '235') === 0) {
                    echo "<h2 style='color: green;'>🎉 SUCCESS! Authentication worked!</h2>";
                    echo "<p>The credentials are correct. PHPMailer should work now.</p>";
                } else {
                    echo "<h2 style='color: red;'>❌ Authentication FAILED</h2>";
                    echo "<p><strong>Possible reasons:</strong></p>";
                    echo "<ul>";
                    echo "<li>Wrong password</li>";
                    echo "<li>Email account doesn't exist</li>";
                    echo "<li>Account is locked or disabled</li>";
                    echo "<li>SMTP authentication not enabled for this account</li>";
                    echo "</ul>";
                    echo "<p><strong>Action:</strong> Check email account in Hostslim control panel</p>";
                }
            }
        }
    } else {
        echo "<p style='color: red;'>❌ TLS encryption failed</p>";
    }
} else {
    echo "<p style='color: red;'>❌ STARTTLS not supported or failed</p>";
}

// Close connection
fputs($connection, "QUIT\r\n");
fclose($connection);

echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>If authentication succeeded: Re-upload email-smtp-config.php and try test-smtp-email.php again</li>";
echo "<li>If authentication failed: Check the email account in Hostslim control panel</li>";
echo "<li>Verify the password is exactly: M@sked03</li>";
echo "<li>Make sure SMTP is enabled for maskedemployee@pinkmilk.eu</li>";
echo "</ol>";
?>
