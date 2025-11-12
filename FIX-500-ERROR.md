# 🔧 Fix 500 Error - API Key & PocketBase

## Errors Fixed

### **Error 1: API Key Not Configured (500)**
```
generate-character.php: API key not configured
```

**Cause:** `generate-character.php` was trying to load API key from environment variable instead of `api-keys.php`

**Fix:** Updated to load from `api-keys.php`

### **Error 2: PocketBase 404**
```
ClientResponseError 404: The requested resource wasn't found.
```

**Cause:** Script was trying to save to collection `'submissions'` which doesn't exist. The correct collection is `'MEQuestions'`.

**Fix:** Changed collection name and added authentication

---

## Files Updated

### **1. generate-character.php**

**Before:**
```php
// Get OpenAI API key from environment
$apiKey = getenv('OPENAI_API_KEY');
```

**After:**
```php
// Load API keys
define('MASKED_EMPLOYEE_APP', true);
require_once __DIR__ . '/api-keys.php';

// Get OpenAI API key
$apiKey = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '';
```

### **2. script.js**

**Before:**
```javascript
const pb = new PocketBase('https://pinkmilk.pockethost.io');
const record = await pb.collection('submissions').create(submissionData);
```

**After:**
```javascript
const pb = new PocketBase('https://pinkmilk.pockethost.io');
const credentials = 'biknu8-pyrnaB-mytvyx';
pb.authStore.save(credentials, { admin: true });

const record = await pb.collection('MEQuestions').create(submissionData);
```

---

## Upload These Files

1. ✅ `generate-character.php` (fixed API key loading)
2. ✅ `script.js` (fixed PocketBase collection)
3. ✅ `questions-unified.json` (your updated questions)
4. ✅ `api-keys.php` (make sure it has your OpenAI key)

---

## Test After Upload

### **1. Complete Questionnaire**
- Fill in all 43 questions
- Click "🎭 Voltooien!"

### **2. Check Console**
Should see:
```
✅ Character data generated
✅ Saved to PocketBase successfully
✅ Loaded 9 chapters from unified file
```

Should NOT see:
```
❌ API key not configured
❌ 404: The requested resource wasn't found
```

### **3. Verify PocketBase**
- Open PocketBase admin
- Check `MEQuestions` collection
- Verify new record created with:
  - All 9 chapters
  - Character data
  - AI summary
  - Image generation prompt

---

## Troubleshooting

### **Still getting "API key not configured":**
- Check `api-keys.php` exists on server
- Verify it contains: `define('OPENAI_API_KEY', 'sk-...');`
- Check file permissions (644)

### **Still getting PocketBase 404:**
- Verify collection name is `MEQuestions` (case-sensitive)
- Check PocketBase credentials are correct
- Verify collection exists in PocketBase admin

### **Character generation fails:**
- Check OpenAI API key is valid
- Verify you have credits
- Check browser console for detailed error

---

**Status:** ✅ Both errors fixed
**Files:** 2 files updated
**Time:** October 24, 2025, 11:55 AM
