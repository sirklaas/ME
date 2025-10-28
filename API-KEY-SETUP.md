# 🔑 API KEY SETUP INSTRUCTIONS

## **Claude Haiku API Key**

Add this to your `api-keys.php` file on the server:

```php
<?php
if (!defined('MASKED_EMPLOYEE_APP')) {
    die('Direct access not permitted');
}

// Claude API Key (Anthropic)
define('CLAUDE_API_KEY', 'YOUR_CLAUDE_API_KEY_HERE');

// Leonardo.ai API Key (already exists)
define('LEONARDO_API_KEY', 'YOUR_LEONARDO_API_KEY_HERE');
?>
```

**Note:** Replace `YOUR_CLAUDE_API_KEY_HERE` with your actual Claude API key (starts with `sk-ant-api03-...`)

## **Important:**
- The `api-keys.php` file is in `.gitignore` (not tracked by Git)
- This keeps API keys secure and not exposed in the repository
- You must manually add the Claude API key to the server's `api-keys.php` file

---

## **Why We Switched to Claude Haiku:**

1. **40x Cheaper** - $0.00025 vs $0.01 per character
2. **Better Personality** - More creative, less stiff character descriptions
3. **Faster** - Optimized for speed
4. **Natural Language** - Better conversational Dutch

---

**Cost Comparison:**
- OpenAI GPT-4: ~$0.01 per character
- Claude Haiku: ~$0.00025 per character
- **Savings: 40x cheaper!** 💰
