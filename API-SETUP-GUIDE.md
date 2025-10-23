# API Setup Guide 🔑

**Last Updated:** 2025-10-13  
**Status:** Ready to configure

---

## 🎯 What You Need

### 1. OpenAI API Key
**Used for:**
- Character description generation
- World description generation  
- Image prompt generation

**Get it here:** https://platform.openai.com/api-keys

**Steps:**
1. Go to https://platform.openai.com/api-keys
2. Log in or create account
3. Click "Create new secret key"
4. Name it "Masked Employee" or similar
5. Copy the key (starts with `sk-...`)
6. **Important:** Copy it now! You can't see it again!

**Cost:** ~$0.02 per user (GPT-4) or ~$0.002 per user (GPT-3.5-turbo)

---

### 2. Freepik API Key
**Used for:**
- Character image generation

**Get it here:** https://www.freepik.com/api

**Steps:**
1. Go to https://www.freepik.com/api
2. Sign up for API access
3. Subscribe to a plan (check pricing)
4. Get your API key from dashboard
5. Copy the key

**Cost:** Depends on your Freepik plan (check their pricing)

---

## 📝 Setup Instructions

### Step 1: Open your api-keys.php file

```bash
# On your server or local machine
cd /domains/pinkmilk.eu/public_html/ME/
nano api-keys.php
```

Or use FTP/SFTP to edit the file.

---

### Step 2: Add OpenAI Configuration

Add these lines to your `api-keys.php`:

```php
// OpenAI API Configuration
define('OPENAI_API_KEY', 'sk-YOUR_ACTUAL_KEY_HERE');
define('OPENAI_MODEL', 'gpt-4'); // or 'gpt-3.5-turbo' for cheaper
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');
```

**Replace** `sk-YOUR_ACTUAL_KEY_HERE` with your real OpenAI key!

---

### Step 3: Verify Freepik Configuration

Make sure these are in your `api-keys.php`:

```php
// Freepik API Configuration
define('FREEPIK_API_KEY', 'YOUR_ACTUAL_FREEPIK_KEY');
define('FREEPIK_API_URL', 'https://api.freepik.com/v1/ai/text-to-image');
define('FREEPIK_AI_MODEL', 'flux');
define('FREEPIK_IMAGE_SIZE', '1024x1024');
define('FREEPIK_STYLE', 'realistic');
define('FREEPIK_NUM_INFERENCE_STEPS', 50);
define('FREEPIK_GUIDANCE_SCALE', 7.5);
```

**Replace** `YOUR_ACTUAL_FREEPIK_KEY` with your real Freepik key!

---

### Step 4: Add Image Storage Configuration

```php
// Image Storage
define('IMAGE_STORAGE_PATH', __DIR__ . '/generated-images/');
define('IMAGE_PUBLIC_URL', 'https://pinkmilk.eu/ME/generated-images/');
```

---

### Step 5: Create Image Storage Directory

```bash
cd /domains/pinkmilk.eu/public_html/ME/
mkdir generated-images
chmod 755 generated-images
```

Or create via FTP and set permissions to 755.

---

### Step 6: Create Logs Directory (Optional but Recommended)

```bash
mkdir logs
chmod 755 logs
touch logs/generation.log
touch logs/api-calls.log
chmod 644 logs/*.log
```

---

## 🧪 Testing

### Test 1: Check API Keys Loaded

Create a test file `test-api-keys.php`:

```php
<?php
define('MASKED_EMPLOYEE_APP', true);
require_once 'api-keys.php';

echo "OpenAI Key: " . (defined('OPENAI_API_KEY') ? '✅ SET' : '❌ NOT SET') . "\n";
echo "Freepik Key: " . (defined('FREEPIK_API_KEY') ? '✅ SET' : '❌ NOT SET') . "\n";
echo "Image Path: " . (defined('IMAGE_STORAGE_PATH') ? '✅ SET' : '❌ NOT SET') . "\n";

if (defined('OPENAI_API_KEY')) {
    echo "OpenAI Key starts with: " . substr(OPENAI_API_KEY, 0, 7) . "...\n";
}
?>
```

Run: `php test-api-keys.php`

Expected output:
```
OpenAI Key: ✅ SET
Freepik Key: ✅ SET
Image Path: ✅ SET
OpenAI Key starts with: sk-proj...
```

---

### Test 2: Test OpenAI Connection

Create `test-openai.php`:

```php
<?php
define('MASKED_EMPLOYEE_APP', true);
require_once 'api-keys.php';

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => 'gpt-4',
        'messages' => [['role' => 'user', 'content' => 'Say "API works!"']],
        'max_tokens' => 10
    ])
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    echo "✅ OpenAI API is working!\n";
} else {
    echo "❌ OpenAI API error!\n";
}
?>
```

Run: `php test-openai.php`

---

### Test 3: Test Complete Flow

1. Go to your questionnaire
2. Click "TEST MODE" button
3. Wait for character preview
4. Check console for logs:
   - Should say "Generating REAL character + world description"
   - Should NOT say "Using fallback mock"
5. Accept character
6. Enter email
7. Wait for image generation
8. Check both emails arrive

---

## 📊 Monitoring

### Check Logs

```bash
# View generation log
tail -f logs/generation.log

# View API calls
tail -f logs/api-calls.log
```

### Check Image Storage

```bash
# List generated images
ls -lh generated-images/

# Check disk space
du -sh generated-images/
```

---

## 💰 Cost Monitoring

### OpenAI
Track usage: https://platform.openai.com/usage

**Per user estimates:**
- GPT-4: ~$0.02 per user
- GPT-3.5-turbo: ~$0.002 per user

**For 150 users:**
- GPT-4: ~$3
- GPT-3.5-turbo: ~$0.30

### Freepik
Check your plan dashboard for credit usage.

---

## 🐛 Troubleshooting

### "OpenAI API key not configured"
- Check key is correctly added to api-keys.php
- Check key starts with `sk-`
- Verify no extra spaces or quotes
- Run test-api-keys.php to confirm

### "HTTP 401 Unauthorized" (OpenAI)
- Your API key is invalid
- Generate a new key
- Check OpenAI account has credits

### "HTTP 429 Too Many Requests" (OpenAI)
- You've hit rate limits
- Wait a few minutes
- Consider upgrading OpenAI plan

### "Image generation failed" (Freepik)
- Check Freepik API key
- Verify you have credits/quota
- Check Freepik API status
- Look at logs/api-calls.log

### Images not saving
- Check generated-images/ folder exists
- Check folder permissions (755)
- Check disk space
- Verify IMAGE_STORAGE_PATH is correct

---

## 🔒 Security Checklist

- [ ] api-keys.php is in .gitignore
- [ ] api-keys.php has proper permissions (644)
- [ ] Never commit API keys to git
- [ ] Use environment variables in production (optional)
- [ ] Enable HTTPS for all API calls
- [ ] Monitor API usage regularly
- [ ] Rotate keys periodically

---

## ✅ Final Checklist

Before going live:

- [ ] OpenAI API key added to api-keys.php
- [ ] Freepik API key added to api-keys.php
- [ ] generated-images/ folder created with 755 permissions
- [ ] logs/ folder created (optional)
- [ ] Tested with test-api-keys.php
- [ ] Tested with test-openai.php
- [ ] Tested complete flow with TEST MODE
- [ ] Both emails received (user + admin)
- [ ] Image generated and displayed
- [ ] Image saved to PocketBase
- [ ] All data in PocketBase correct

---

## 📞 Support

### OpenAI Issues
- Documentation: https://platform.openai.com/docs
- Status: https://status.openai.com/
- Support: https://help.openai.com/

### Freepik Issues
- Documentation: https://www.freepik.com/api/documentation
- Support: Check your Freepik dashboard

---

**Next Step:** Add your API keys to `api-keys.php` and test! 🚀
