<?php
/**
 * API Keys Configuration Template
 * Copy this content to your api-keys.php file
 * 
 * IMPORTANT: api-keys.php should be in .gitignore!
 */

// Security check
if (!defined('MASKED_EMPLOYEE_APP')) {
    die('Direct access not permitted');
}

// ============================================
// OpenAI API Configuration
// ============================================
// Get your API key from: https://platform.openai.com/api-keys
define('OPENAI_API_KEY', 'sk-YOUR_OPENAI_KEY_HERE');
define('OPENAI_MODEL', 'gpt-4'); // or 'gpt-3.5-turbo' for cheaper option
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');

// ============================================
// Freepik API Configuration
// ============================================
// Get your API key from: https://www.freepik.com/api
define('FREEPIK_API_KEY', 'YOUR_FREEPIK_KEY_HERE');
define('FREEPIK_API_URL', 'https://api.freepik.com/v1/ai/text-to-image');

// Freepik Image Settings
define('FREEPIK_AI_MODEL', 'flux'); // or 'flux-realism', 'flux-3d'
define('FREEPIK_IMAGE_SIZE', '1024x1024'); // or '512x512', '1024x1792'
define('FREEPIK_STYLE', 'realistic'); // or 'digital-art', 'fantasy', etc.
define('FREEPIK_NUM_INFERENCE_STEPS', 50); // Higher = better quality (20-100)
define('FREEPIK_GUIDANCE_SCALE', 7.5); // How closely to follow prompt (1-20)

// ============================================
// Image Storage Configuration
// ============================================
// Where to save generated images on server
define('IMAGE_STORAGE_PATH', __DIR__ . '/generated-images/');
define('IMAGE_PUBLIC_URL', 'https://pinkmilk.eu/ME/generated-images/');

// ============================================
// Logging Configuration
// ============================================
define('DEBUG_MODE', true); // Set to false in production
define('LOG_API_CALLS', true); // Log all API calls for debugging
define('GENERATION_LOG', __DIR__ . '/logs/generation.log');
define('API_LOG', __DIR__ . '/logs/api-calls.log');

// ============================================
// Email Configuration
// ============================================
define('ADMIN_EMAIL', 'klaas@pinkmilk.eu');
define('FROM_EMAIL', 'noreply@pinkmilk.eu');
define('FROM_NAME', 'The Masked Employee');

?>
