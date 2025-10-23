# OpenAI API Setup Guide

## Quick Start

### Step 1: Get Your OpenAI API Key
1. Go to https://platform.openai.com/
2. Sign up or log in to your account
3. Navigate to API Keys section
4. Click "Create new secret key"
5. Copy the key (it starts with `sk-`)
6. **Important**: Save it securely - you won't see it again!

### Step 2: Configure the Application

#### Option A: Environment Variable (Recommended for Production)

**On Mac/Linux:**
```bash
# Add to ~/.bash_profile or ~/.zshrc
export OPENAI_API_KEY="sk-your-actual-key-here"

# Then reload:
source ~/.bash_profile
```

**On Windows:**
```cmd
setx OPENAI_API_KEY "sk-your-actual-key-here"
```

**Verify it's set:**
```bash
echo $OPENAI_API_KEY
```

#### Option B: Direct Configuration (Quick Testing)

Edit `generate-character-summary.php` line 32:
```php
// Replace this line:
$apiKey = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_API_KEY_HERE';

// With:
$apiKey = 'sk-your-actual-key-here';
```

⚠️ **Warning**: Don't commit this file to Git with your API key!

### Step 3: Test the Integration

1. Open `questions.html` in your browser
2. Complete the questionnaire
3. When you reach the summary page, watch the console:
   - Success: You'll see AI-generated content
   - Failure: Check console for error messages

## API Costs

### Pricing (as of October 2024)
- **Model Used**: GPT-4
- **Estimated Cost**: ~$0.03 - $0.05 per summary
- **Token Usage**: ~1,000 tokens per request

### To Reduce Costs
You can switch to GPT-3.5-turbo in `generate-character-summary.php`:
```php
// Line ~105
'model' => 'gpt-3.5-turbo',  // Instead of 'gpt-4'
```
Cost reduction: ~90% cheaper but slightly lower quality

## Troubleshooting

### Error: "Invalid API Key"
- Check if key starts with `sk-`
- Verify no extra spaces or quotes
- Ensure environment variable is set correctly
- Try restarting your PHP server

### Error: "Quota Exceeded"
- Check your OpenAI account billing
- Verify you have credits available
- Consider adding payment method

### Error: "Rate Limit Exceeded"
- Too many requests in short time
- Wait a few minutes and try again
- Consider implementing request queuing

### Mock Mode Keeps Running
If you see mock summaries even with API key set:
1. Check PHP error logs
2. Verify `curl` is enabled in PHP
3. Test API key directly:
```bash
curl https://api.openai.com/v1/models \
  -H "Authorization: Bearer $OPENAI_API_KEY"
```

## Advanced Configuration

### Customize AI Behavior

In `generate-character-summary.php`, adjust the model parameters:

```php
$data = [
    'model' => 'gpt-4',
    'messages' => [...],
    'temperature' => 0.8,      // Higher = more creative (0-2)
    'max_tokens' => 1000,      // Response length
    'top_p' => 1,              // Diversity of output
    'frequency_penalty' => 0,  // Reduce repetition
    'presence_penalty' => 0    // Encourage new topics
];
```

### Modify the Prompt

Edit the `buildPrompt()` function to customize how the AI interprets answers:
- Make it more dramatic
- Focus on specific traits
- Add emoji or formatting preferences
- Change tone (professional, casual, humorous)

### Add Caching

To avoid duplicate API calls for the same answers:
```php
// Before calling OpenAI, check cache
$cacheKey = md5(json_encode($answers));
$cacheFile = "cache/summary_$cacheKey.txt";

if (file_exists($cacheFile)) {
    return file_get_contents($cacheFile);
}

// After getting response, save to cache
file_put_contents($cacheFile, $response);
```

## Security Best Practices

### 1. Protect Your API Key
- Never commit to Git
- Use `.env` files with `.gitignore`
- Rotate keys regularly
- Use separate keys for dev/prod

### 2. Implement Rate Limiting
Add to `generate-character-summary.php`:
```php
// Simple rate limiting
$ip = $_SERVER['REMOTE_ADDR'];
$rateFile = "rate_limit_$ip.txt";
$lastCall = file_exists($rateFile) ? file_get_contents($rateFile) : 0;

if (time() - $lastCall < 60) {
    http_response_code(429);
    die(json_encode(['error' => 'Too many requests']));
}

file_put_contents($rateFile, time());
```

### 3. Monitor Usage
Create a simple logging system:
```php
// Log each API call
$logEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'player' => $playerName,
    'tokens_used' => $result['usage']['total_tokens'] ?? 0,
    'cost' => ($result['usage']['total_tokens'] ?? 0) * 0.00003
];
file_put_contents('api_usage.log', json_encode($logEntry)."\n", FILE_APPEND);
```

## Alternatives to OpenAI

If you prefer other AI services:

### 1. Anthropic Claude
- Similar quality to GPT-4
- Different API structure
- Good at nuanced text

### 2. Google PaLM
- Google's AI platform
- May have lower costs
- Good integration with Google Cloud

### 3. Open Source (Self-Hosted)
- Llama 2 or Mistral
- No per-request costs
- Requires server with GPU

### 4. No AI (Enhanced Mock)
Create more dynamic mock summaries:
```php
function generateSmartMockSummary($answers) {
    // Analyze answers for keywords
    // Build summary from templates
    // More personalized than static mock
}
```

## Support Resources

- **OpenAI Documentation**: https://platform.openai.com/docs
- **API Status**: https://status.openai.com/
- **Community Forum**: https://community.openai.com/
- **Pricing**: https://openai.com/pricing

---

Need help? Check the console logs and `error_log` for detailed debugging information.
