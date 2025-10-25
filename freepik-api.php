<?php
/**
 * Freepik API Integration
 * Handles image generation using Freepik AI API
 */

// Check if constant is already defined before defining it
if (!defined('MASKED_EMPLOYEE_APP')) {
    define('MASKED_EMPLOYEE_APP', true);
}
require_once __DIR__ . '/api-keys.php';

// Define default Freepik settings if not set in api-keys.php
if (!defined('FREEPIK_IMAGE_SIZE')) define('FREEPIK_IMAGE_SIZE', '1280x720');  // 16:9 aspect ratio
if (!defined('FREEPIK_STYLE')) define('FREEPIK_STYLE', 'realistic');
if (!defined('FREEPIK_AI_MODEL')) define('FREEPIK_AI_MODEL', 'flux-kontext-pro');  // Flux Kontext Pro
if (!defined('FREEPIK_NUM_INFERENCE_STEPS')) define('FREEPIK_NUM_INFERENCE_STEPS', 50);
if (!defined('FREEPIK_GUIDANCE_SCALE')) define('FREEPIK_GUIDANCE_SCALE', 7.5);
if (!defined('LOG_API_CALLS')) define('LOG_API_CALLS', false);
if (!defined('IMAGE_STORAGE_PATH')) define('IMAGE_STORAGE_PATH', __DIR__ . '/generated-images/');
if (!defined('IMAGE_PUBLIC_URL')) define('IMAGE_PUBLIC_URL', '/ME/generated-images/');

class FreepikAPI {
    private $apiKey;
    private $apiUrl;
    
    public function __construct() {
        $this->apiKey = FREEPIK_API_KEY;
        $this->apiUrl = FREEPIK_API_URL;
    }
    
    /**
     * Generate an image from a text prompt
     * 
     * @param string $prompt The text description
     * @param array $options Additional generation options
     * @return array ['success' => bool, 'image_url' => string, 'error' => string]
     */
    public function generateImage($prompt, $options = []) {
        $this->log("Generating image with prompt: " . substr($prompt, 0, 100) . "...");
        
        // Use ONLY the parameters Freepik accepts (simple version)
        // Complex parameters were causing "400: parameters didn't validate" error
        
        // Truncate prompt if too long (Freepik has character limits)
        $maxLength = 1000;
        if (strlen($prompt) > $maxLength) {
            $this->log("Prompt too long (" . strlen($prompt) . " chars), truncating to $maxLength");
            $prompt = substr($prompt, 0, $maxLength);
        }
        
        $data = array_merge([
            'prompt' => $prompt,
            'num_images' => 1
        ], $options);
        
        // Make API request
        $response = $this->makeRequest($data);
        
        if ($response['success']) {
            $this->log("Image generated successfully: " . $response['image_url']);
        } else {
            $this->log("Image generation failed: " . $response['error'], 'error');
        }
        
        return $response;
    }
    
    /**
     * Generate character image with optimized settings
     */
    public function generateCharacterImage($prompt) {
        // Don't enhance - prompt from generate-character.php is already detailed
        // $enhancedPrompt = $this->enhanceCharacterPrompt($prompt);
        
        // Use simple parameters - Freepik doesn't accept complex styling options
        return $this->generateImage($prompt);
    }
    
    /**
     * Generate environment/background image
     */
    public function generateEnvironmentImage($prompt) {
        $enhancedPrompt = $this->enhanceEnvironmentPrompt($prompt);
        
        // Use simple parameters - Freepik doesn't accept complex styling options
        return $this->generateImage($enhancedPrompt);
    }
    
    /**
     * Enhance character prompt for better results
     */
    private function enhanceCharacterPrompt($prompt) {
        $enhancement = "Professional character portrait for TV gameshow. ";
        $enhancement .= $prompt;
        $enhancement .= " High quality, centered composition, dramatic lighting, ";
        $enhancement .= "masked mysterious figure, professional photography, 4K quality.";
        
        return $enhancement;
    }
    
    /**
     * Enhance environment prompt
     */
    private function enhanceEnvironmentPrompt($prompt) {
        $enhancement = "Cinematic environment scene for TV production. ";
        $enhancement .= $prompt;
        $enhancement .= " Atmospheric, high quality, professional photography, ";
        $enhancement .= "dramatic lighting, 4K quality, wide angle.";
        
        return $enhancement;
    }
    
    /**
     * Make HTTP request to Freepik API
     */
    private function makeRequest($data) {
        $ch = curl_init($this->apiUrl);
        
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-freepik-api-key: ' . $this->apiKey
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Log the API call
        if (LOG_API_CALLS) {
            $this->logAPICall($data, $response, $httpCode);
        }
        
        // Handle cURL errors
        if ($curlError) {
            return [
                'success' => false,
                'error' => 'cURL error: ' . $curlError,
                'image_url' => null
            ];
        }
        
        // Parse response
        $result = json_decode($response, true);
        
        // Check for API errors
        if ($httpCode !== 200) {
            $errorMsg = 'HTTP ' . $httpCode . ': ' . ($result['message'] ?? 'Unknown error');
            if (isset($result['detail'])) {
                $errorMsg .= ' - ' . $result['detail'];
            }
            error_log("Freepik API Error: " . $errorMsg);
            error_log("Full response: " . $response);
            
            return [
                'success' => false,
                'error' => $errorMsg,
                'image_url' => null,
                'http_code' => $httpCode,
                'full_response' => $result
            ];
        }
        
        // Extract image from response (Freepik returns base64)
        if (isset($result['data'][0]['base64'])) {
            // Save base64 image directly
            $base64Data = $result['data'][0]['base64'];
            $imageData = base64_decode($base64Data);
            
            $this->log("Image data received, size: " . strlen($imageData) . " bytes");
            
            // Return base64 data for uploading to PocketBase
            // We'll handle PocketBase upload in the calling code
            return [
                'success' => true,
                'image_data' => $base64Data, // Base64 encoded image
                'image_binary' => $imageData, // Decoded binary data
                'image_url' => null, // Will be set after PocketBase upload
                'error' => null,
                'full_response' => $result
            ];
        }
        
        // Fallback: check for direct URL
        if (isset($result['data'][0]['url'])) {
            return [
                'success' => true,
                'image_url' => $result['data'][0]['url'],
                'error' => null,
                'full_response' => $result
            ];
        }
        
        return [
            'success' => false,
            'error' => 'No image data in response',
            'image_url' => null
        ];
    }
    
    /**
     * Download image from URL and save locally
     */
    public function downloadAndSaveImage($imageUrl, $filename) {
        // Create directory if it doesn't exist
        if (!file_exists(IMAGE_STORAGE_PATH)) {
            mkdir(IMAGE_STORAGE_PATH, 0755, true);
        }
        
        $filepath = IMAGE_STORAGE_PATH . $filename;
        
        // Download image
        $imageData = file_get_contents($imageUrl);
        
        if ($imageData === false) {
            $this->log("Failed to download image from: " . $imageUrl, 'error');
            return false;
        }
        
        // Save to file
        $result = file_put_contents($filepath, $imageData);
        
        if ($result === false) {
            $this->log("Failed to save image to: " . $filepath, 'error');
            return false;
        }
        
        $this->log("Image saved successfully: " . $filename);
        
        // Return public URL
        return IMAGE_PUBLIC_URL . $filename;
    }
    
    /**
     * Log messages
     */
    private function log($message, $level = 'info') {
        if (!DEBUG_MODE && $level === 'info') {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] Freepik API: {$message}";
        
        // Use error_log instead of echo (echo breaks JSON responses!)
        error_log($logMessage);
        
        // File logging
        if (defined('GENERATION_LOG')) {
            @file_put_contents(GENERATION_LOG, $logMessage . "\n", FILE_APPEND);
        }
    }
    
    /**
     * Log API calls for debugging
     */
    private function logAPICall($request, $response, $httpCode) {
        if (!defined('API_LOG')) return;
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'service' => 'Freepik',
            'http_code' => $httpCode,
            'request' => $request,
            'response' => json_decode($response, true)
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        @file_put_contents(API_LOG, $logLine, FILE_APPEND);
    }
}

// Example usage (commented out):
/*
$freepik = new FreepikAPI();

$prompt = "A mysterious masked hero in a blue costume with wolf symbolism, dramatic lighting, professional portrait";
$result = $freepik->generateCharacterImage($prompt);

if ($result['success']) {
    echo "Image generated: " . $result['image_url'];
    
    // Download and save locally
    $localUrl = $freepik->downloadAndSaveImage($result['image_url'], 'character_001.jpg');
    echo "Saved locally: " . $localUrl;
} else {
    echo "Error: " . $result['error'];
}
*/

?>
