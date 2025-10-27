<?php
/**
 * Leonardo.ai API Integration
 * Handles image generation using Leonardo.ai API
 */

if (!defined('MASKED_EMPLOYEE_APP')) {
    die('Direct access not permitted');
}

class LeonardoAPI {
    private $apiKey;
    private $apiUrl = 'https://cloud.leonardo.ai/api/rest/v1/generations';
    
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Generate an image from a text prompt
     * 
     * @param string $prompt The text description
     * @param array $options Additional generation options
     * @return array ['success' => bool, 'image_data' => string, 'error' => string]
     */
    public function generateImage($prompt, $options = []) {
        // Default settings optimized for character generation
        $defaults = [
            'prompt' => $prompt,
            'negative_prompt' => 'blurry, low quality, pixelated, distorted, amateur, poorly lit, deformed, ugly, bad anatomy, extra limbs, missing limbs, floating limbs, disconnected limbs, malformed hands, long neck, duplicate, mutated, mutilated, out of frame, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, bad proportions, gross proportions, watermark, signature, text, logo',
            'modelId' => 'b24e16ff-06e3-43eb-8d33-4416c2d75876', // Leonardo Phoenix (best for photorealistic)
            'width' => 1472, // 16:9 ratio (1472x832 is supported)
            'height' => 832,
            'num_images' => 1,
            'guidance_scale' => 7,
            'num_inference_steps' => 30,
            'presetStyle' => 'CINEMATIC'
        ];
        
        $data = array_merge($defaults, $options);
        
        // Step 1: Create generation request
        $response = $this->makeRequest($this->apiUrl, $data);
        
        if (!$response['success']) {
            return $response;
        }
        
        $generationId = $response['data']['sdGenerationJob']['generationId'] ?? null;
        
        if (!$generationId) {
            return [
                'success' => false,
                'error' => 'No generation ID received',
                'image_data' => null
            ];
        }
        
        // Step 2: Poll for completion (Leonardo is async)
        $maxAttempts = 60; // 60 seconds max wait
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            sleep(1); // Wait 1 second between checks
            
            $statusResponse = $this->checkGenerationStatus($generationId);
            
            if ($statusResponse['success'] && $statusResponse['status'] === 'COMPLETE') {
                // Get the image URL
                $imageUrl = $statusResponse['images'][0]['url'] ?? null;
                
                if ($imageUrl) {
                    // Download image and convert to base64
                    $imageData = file_get_contents($imageUrl);
                    $base64Data = base64_encode($imageData);
                    
                    return [
                        'success' => true,
                        'image_data' => $base64Data,
                        'image_url' => $imageUrl,
                        'error' => null
                    ];
                }
            }
            
            if ($statusResponse['status'] === 'FAILED') {
                return [
                    'success' => false,
                    'error' => 'Image generation failed',
                    'image_data' => null
                ];
            }
            
            $attempt++;
        }
        
        return [
            'success' => false,
            'error' => 'Image generation timeout',
            'image_data' => null
        ];
    }
    
    /**
     * Check generation status
     */
    private function checkGenerationStatus($generationId) {
        $url = "https://cloud.leonardo.ai/api/rest/v1/generations/{$generationId}";
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return ['success' => false, 'status' => 'FAILED'];
        }
        
        $result = json_decode($response, true);
        
        $status = $result['generations_by_pk']['status'] ?? 'PENDING';
        $images = $result['generations_by_pk']['generated_images'] ?? [];
        
        return [
            'success' => true,
            'status' => $status,
            'images' => $images
        ];
    }
    
    /**
     * Make HTTP request to Leonardo API
     */
    private function makeRequest($url, $data) {
        $ch = curl_init($url);
        
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
                'authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Handle cURL errors
        if ($curlError) {
            return [
                'success' => false,
                'error' => 'cURL error: ' . $curlError,
                'data' => null
            ];
        }
        
        // Parse response
        $result = json_decode($response, true);
        
        // Check for API errors
        if ($httpCode !== 200) {
            $errorMsg = 'HTTP ' . $httpCode . ': ' . ($result['error'] ?? 'Unknown error');
            return [
                'success' => false,
                'error' => $errorMsg,
                'data' => null
            ];
        }
        
        return [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
}
