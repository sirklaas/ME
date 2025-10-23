# Quick Start Guide - The Masked Employee
# Getting Back Up and Running After Restart

**Version:** 2.0  
**Date:** 2025-10-14  
**Purpose:** Restart and continue without glitches

---

## 🚀 Immediate Action Items

### 1. Verify Server Files (2 minutes)

**Check these files exist on server:**
```
https://www.pinkmilk.eu/ME/
├── questions.html ✅
├── script.js ✅
├── style.css ✅
├── gameshow-config-v2.json ✅
├── pocketbase-config.js ✅
├── generate-character-real.php ✅
├── freepik-api.php ✅
├── send-description-email.php ✅
├── send-final-email.php ✅
└── api-keys.php ✅ (CRITICAL - must exist!)
```

**Quick check:**
```bash
Visit: https://www.pinkmilk.eu/ME/questions.html
Should load without errors
```

### 2. Verify API Keys (1 minute)

**Visit:**
```
https://www.pinkmilk.eu/ME/test-api-keys.php
```

**Expected output:**
```
OPENAI_API_KEY: Defined ✅
OPENAI_MODEL: gpt-4o
FREEPIK_API_KEY: Defined ✅
FREEPIK_ENDPOINT: https://api.freepik.com/v1/ai/text-to-image ✅
```

**If missing:** Upload api-keys.php with:
```php
<?php
if (!defined('MASKED_EMPLOYEE_APP')) {
    die('Direct access not permitted');
}

define('OPENAI_API_KEY', 'sk-proj-YOUR_KEY_HERE');
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');
define('OPENAI_MODEL', 'gpt-4o');
define('OPENAI_TEMPERATURE', 0.8);

define('FREEPIK_API_KEY', 'YOUR_KEY_HERE');
define('FREEPIK_API_URL', 'https://api.freepik.com/v1/ai/text-to-image');
define('FREEPIK_ENDPOINT', 'https://api.freepik.com/v1/ai/text-to-image');

define('DEBUG_MODE', true);
define('USE_MOCK_DATA', false);
?>
```

### 3. Test Complete Flow (3 minutes)

**Visit:**
```
https://www.pinkmilk.eu/ME/test-actual-image-gen.php
```

**Expected output:**
```
HTTP Code: 200
✅ 200 SUCCESS!
Image generation succeeded!
Has image_data: YES
Image data length: ~150000+ bytes
```

**If 500 error:** See [Troubleshooting](#troubleshooting) below

### 4. Run TEST MODE (2 minutes)

**Steps:**
1. Visit: `https://www.pinkmilk.eu/ME/questions.html`
2. Open browser console (F12)
3. Click **"TEST MODE"** button
4. Click **"Accept character"**
5. Enter email: `test@example.com`
6. Wait 60-90 seconds
7. Check console for success messages

**Expected console output:**
```
✅ Descriptions saved to PocketBase
📧 Starting email send...
✅ Email sent successfully
🎨 Starting image generation...
✅ Image generation completed
📤 Uploading image file...
✅ Image uploaded to PocketBase
```

### 5. Verify PocketBase (1 minute)

**Check latest record:**
- ✅ `character_description`: Has text
- ✅ `character_name`: Has name  
- ✅ `ai_summary`: Has HTML
- ✅ `image_prompt`: Has JSON
- ✅ `image`: **Has image file**
- ✅ `status`: "completed_with_image"

---

## 🔧 Critical Configuration

### API Keys Required

**OpenAI:**
```
Key: sk-proj-A9D4Ci38mJMx3uShynHcXKrlfRP7gTfmKxxxxxKfQaOWyrJW9Nw3kMA
Model: gpt-4o
```

**Freepik:**
```
Key: FPSX6db865xxxx6412911dd49a
Endpoint: https://api.freepik.com/v1/ai/text-to-image
```

### PocketBase Configuration

**Collection:** MEQuestions  
**URL:** [Your PocketBase URL]  
**Admin Token:** [Set in pocketbase-config.js]

### URL Configuration (CRITICAL!)

**All API calls MUST use www:**
```javascript
✅ https://www.pinkmilk.eu/ME/generate-character-real.php
❌ https://pinkmilk.eu/ME/generate-character-real.php (causes 301)
```

**Files using URLs:**
- `script.js` (4 fetch calls)
- `test-actual-image-gen.php`

---

## 📋 System Health Check

### Run These Tests in Order:

**1. Frontend Load Test**
```
Visit: https://www.pinkmilk.eu/ME/questions.html
Check: Page loads, no console errors
```

**2. API Keys Test**
```
Visit: https://www.pinkmilk.eu/ME/test-api-keys.php
Check: All keys show "Defined ✅"
```

**3. OpenAI Test**
```
Visit: https://www.pinkmilk.eu/ME/test-image-gen-direct.php
Check: "OpenAI test PASSED ✅"
```

**4. Freepik Test**
```
Same URL as above
Check: "Freepik test PASSED ✅"
```

**5. Complete Flow Test**
```
Visit: https://www.pinkmilk.eu/ME/test-actual-image-gen.php
Check: "HTTP Code: 200" and "Has image_data: YES"
```

**6. Full Integration Test**
```
Run TEST MODE (see step 4 above)
Check: Image in PocketBase
```

---

## 🐛 Troubleshooting

### Problem: 404 on questions.html

**Cause:** Files not uploaded  
**Solution:**
```
1. Check FTP connection
2. Upload all files from /Users/mac/GitHubLocal/ME/
3. Verify file permissions (644)
```

### Problem: "Can't find variable: credentials"

**Cause:** PocketBase authentication issue  
**Solution:**
```
1. Check pocketbase-config.js exists
2. Verify PocketBase URL is correct
3. Check admin token is set
4. Refresh page
```

### Problem: 500 Error on Image Generation

**Cause:** One of these:
1. Missing api-keys.php
2. Using non-www URL (301 redirect)
3. Echo in freepik-api.php
4. Invalid Freepik parameters

**Solution:**
```
1. Check api-keys.php exists and has correct values
2. Verify all URLs use www
3. Verify freepik-api.php uses error_log() not echo
4. Verify freepik-api.php uses simple parameters
5. Check: https://www.pinkmilk.eu/ME/view-errors.php
```

### Problem: 400 Error from Freepik

**Cause:** Complex parameters not accepted  
**Solution:**
```
Check freepik-api.php generateImage() method uses:
{
    "prompt": $prompt,
    "num_images": 1,
    "image": {"size": "1024x1024"}
}
NOT complex styling, ai_model, etc.
```

### Problem: No Image in PocketBase

**Cause:** Image upload failed  
**Solution:**
```
1. Check console logs for errors
2. Verify image_data received from Freepik
3. Check PocketBase file upload permissions
4. Test: https://www.pinkmilk.eu/ME/test-actual-image-gen.php
```

### Problem: Email Not Received

**Cause:** SMTP issue or wrong email  
**Solution:**
```
1. Check spam folder
2. Verify email address correct
3. Check SMTP configuration in send-*-email.php
4. Test email sending separately
```

---

## 📁 Key File Locations

### Local (Development)
```
/Users/mac/GitHubLocal/ME/
├── script.js (2,643 lines)
├── freepik-api.php (290 lines)
├── generate-character-real.php (357 lines)
└── [all documentation]
```

### Server (Production)
```
/domains/pinkmilk.eu/public_html/ME/
├── All frontend files
├── All backend PHP files
└── api-keys.php (SECURE)
```

### Documentation
```
/Users/mac/GitHubLocal/ME/
├── PRD-COMPLETE-2025-10-14.md (MAIN DOC)
├── QUICK-START-GUIDE.md (THIS FILE)
├── TECHNICAL-DOCUMENTATION.md
├── IMAGE-GENERATION-FLOW.md
└── [all other .md files]
```

---

## 🔑 Essential Commands

### Upload Files via FTP
```
Connect to: pinkmilk.eu
Directory: /domains/pinkmilk.eu/public_html/ME/
Upload: script.js, freepik-api.php, api-keys.php
```

### Check PHP Errors
```
Visit: https://www.pinkmilk.eu/ME/view-errors.php
```

### Test APIs
```
Visit: https://www.pinkmilk.eu/ME/test-api-keys.php
Visit: https://www.pinkmilk.eu/ME/test-image-gen-direct.php
Visit: https://www.pinkmilk.eu/ME/test-actual-image-gen.php
```

### View PocketBase Records
```
Login to PocketBase admin
Navigate to MEQuestions collection
Sort by created (descending)
View latest records
```

---

## ✅ Ready to Deploy Checklist

Before deploying to 150 users:

- [ ] All files uploaded to server
- [ ] api-keys.php configured with real keys
- [ ] test-api-keys.php shows all keys defined
- [ ] test-actual-image-gen.php returns 200
- [ ] TEST MODE completes successfully
- [ ] Image appears in PocketBase
- [ ] Both emails received
- [ ] Console shows no errors
- [ ] PocketBase has all fields populated
- [ ] Image quality is good (1024x1024)
- [ ] Email formatting correct
- [ ] Tested in multiple browsers
- [ ] Mobile responsive
- [ ] 5 test users completed successfully

---

## 🎯 Production Deployment

When ready to deploy to 150 users:

**1. Final Verification (30 minutes before)**
```
- Run all tests above
- Verify all systems operational
- Check server load capacity
- Verify PocketBase storage space
- Confirm email delivery working
```

**2. Go Live**
```
- Send link to users: https://www.pinkmilk.eu/ME/questions.html
- Monitor PocketBase for new records
- Watch console for errors
- Check email delivery rates
```

**3. Monitor (First 2 hours)**
```
- PocketBase: New records appearing?
- Emails: Being delivered?
- Errors: Any 500 errors in logs?
- Performance: Server load OK?
- Support: Any user complaints?
```

**4. Post-Launch (24 hours)**
```
- Count completed records
- Verify all images generated
- Check email delivery rate
- Review any errors
- Calculate actual costs
```

---

## 💰 Cost Tracking

**Per User:**
```
OpenAI (descriptions): $0.03
OpenAI (image prompt): $0.02
Freepik (image):       $0.15
Email:                 $0.01
-------------------------
Total:                 $0.21
```

**150 Users:**
```
Total estimated: $31.50
Monitor actual usage in:
- OpenAI dashboard
- Freepik dashboard
- Email provider
```

---

## 📞 Emergency Contacts

**If system goes down:**
1. Check server status
2. View error logs: view-errors.php
3. Test API endpoints
4. Contact hosting provider if server issue
5. Contact API providers if API issue

**Fallback Plan:**
1. Switch to mock data mode (USE_MOCK_DATA = true)
2. Save user responses to PocketBase
3. Generate images later manually
4. Send emails manually

---

## 📚 Additional Resources

**Main Documentation:**
- `PRD-COMPLETE-2025-10-14.md` - Complete overview
- `TECHNICAL-DOCUMENTATION.md` - Technical details
- `IMAGE-GENERATION-FLOW.md` - Image generation specifics

**Troubleshooting:**
- `DIAGNOSTIC-CHECKLIST.md` - Detailed diagnostics
- `FIX-500-ERROR-REDIRECT.md` - 301 redirect fix
- `FREEPIK-PARAMS-FIX.md` - 400 parameter fix
- `FINAL-FIX-COMPLETE.md` - All fixes summary

**Development:**
- `FIELD-STRUCTURE-FINAL.md` - PocketBase fields
- `IMAGE-PROMPT-STRUCTURE.md` - Prompt JSON format
- `POCKETBASE-FIELDS.md` - Field mapping

---

## ✨ Success Criteria

**System is working when:**
- ✅ TEST MODE completes without errors
- ✅ Image appears in PocketBase
- ✅ Both emails received
- ✅ Image quality is good
- ✅ All PocketBase fields populated
- ✅ No 500 errors
- ✅ No console errors

**Ready for production when:**
- ✅ All above criteria met
- ✅ Tested with 5 real users
- ✅ All documentation complete
- ✅ Monitoring in place
- ✅ Support plan ready

---

**You're ready to go! 🚀**

*Last Updated: 2025-10-14 11:04*  
*Version: 2.0*  
*Status: Complete*
