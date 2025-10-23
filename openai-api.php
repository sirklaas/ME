<?php
/**
 * OpenAI API Integration
 * Handles text generation using GPT-4
 */

define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';

class OpenAIAPI {
    private $apiKey;
    private $apiUrl;
    private $model;
    
    public function __construct() {
        $this->apiKey = OPENAI_API_KEY;
        $this->apiUrl = OPENAI_API_URL;
        $this->model = OPENAI_MODEL;
    }
    
    /**
     * Generate text completion using GPT-4
     * 
     * @param string $systemPrompt System instructions
     * @param string $userPrompt User input
     * @param array $options Additional options
     * @return array ['success' => bool, 'content' => string, 'error' => string]
     */
    public function generateCompletion($systemPrompt, $userPrompt, $options = []) {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'content' => null,
                'error' => 'OpenAI API key not configured'
            ];
        }
        
        $this->log("Generating completion...");
        
        $data = array_merge([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt
                ]
            ],
            'temperature' => 0.8,
            'max_tokens' => 1000
        ], $options);
        
        $response = $this->makeRequest($data);
        
        if ($response['success']) {
            $this->log("Completion generated successfully");
        } else {
            $this->log("Completion failed: " . $response['error'], 'error');
        }
        
        return $response;
    }
    
    /**
     * Make HTTP request to OpenAI API
     */
    private function makeRequest($data) {
        $ch = curl_init($this->apiUrl);
        
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
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
                'content' => null,
                'error' => 'cURL error: ' . $curlError
            ];
        }
        
        // Parse response
        $result = json_decode($response, true);
        
        // Check for API errors
        if ($httpCode !== 200) {
            return [
                'success' => false,
                'content' => null,
                'error' => 'HTTP ' . $httpCode . ': ' . ($result['error']['message'] ?? 'Unknown error')
            ];
        }
        
        // Extract content from response
        if (isset($result['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'content' => $result['choices'][0]['message']['content'],
                'error' => null,
                'usage' => $result['usage'] ?? null
            ];
        }
        
        return [
            'success' => false,
            'content' => null,
            'error' => 'No content in response'
        ];
    }
    
    /**
     * Log messages
     */
    private function log($message, $level = 'info') {
        if (!DEBUG_MODE && $level === 'info') {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] OpenAI API: {$message}\n";
        
        echo $logMessage;
        
        if (defined('GENERATION_LOG')) {
            @file_put_contents(GENERATION_LOG, $logMessage, FILE_APPEND);
        }
    }
    
    /**
     * Log API calls
     */
    private function logAPICall($request, $response, $httpCode) {
        if (!defined('API_LOG')) return;
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'service' => 'OpenAI',
            'http_code' => $httpCode,
            'model' => $request['model'] ?? null,
            'response_preview' => substr(json_decode($response, true)['choices'][0]['message']['content'] ?? '', 0, 100)
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        @file_put_contents(API_LOG, $logLine, FILE_APPEND);
    }
}

?>
