<?php
define('MASKED_EMPLOYEE_APP', true);
require_once 'api-keys.php';

header('Content-Type: text/plain');

echo "API Keys Check:\n\n";

echo "OPENAI_API_KEY: " . (defined('OPENAI_API_KEY') ? 'Defined ✅' : 'Missing ❌') . "\n";
echo "OPENAI_MODEL: " . (defined('OPENAI_MODEL') ? OPENAI_MODEL : 'Not set') . "\n\n";

echo "FREEPIK_API_KEY: " . (defined('FREEPIK_API_KEY') ? 'Defined ✅' : 'Missing ❌') . "\n";
echo "FREEPIK_ENDPOINT: " . (defined('FREEPIK_ENDPOINT') ? FREEPIK_ENDPOINT : 'Missing ❌') . "\n";
echo "FREEPIK_API_URL: " . (defined('FREEPIK_API_URL') ? FREEPIK_API_URL : 'Missing ❌') . "\n\n";

echo "All constants:\n";
print_r(get_defined_constants(true)['user']);
?>