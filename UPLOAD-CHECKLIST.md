# 📤 UPLOAD CHECKLIST - SMTP EMAIL SETUP

## 🎯 FILES READY IN YOUR LOCAL REPOSITORY

All files are in: `/Users/mac/GitHubLocal/ME/`

---

## ✅ UPLOAD THESE FILES TO SERVER

Upload to: `/domains/pinkmilk.eu/public_html/ME/`

### **1. New SMTP Files:**
- [ ] `email-smtp-config.php` ← New SMTP configuration
- [ ] `test-smtp-email.php` ← Test script
- [ ] `vendor/autoload.php` ← PHPMailer autoloader

### **2. Updated Email Files:**
- [ ] `send-description-email.php` ← Updated to use SMTP
- [ ] `send-final-email.php` ← Updated to use SMTP

### **3. Configuration Files:**
- [ ] `composer.json` ← PHPMailer dependency info

---

## 📁 SERVER FILE STRUCTURE

After upload, your server should have:

```
/domains/pinkmilk.eu/public_html/ME/
├── vendor/
│   ├── autoload.php                    ← UPLOAD THIS
│   └── phpmailer/
│       └── phpmailer/
│           └── src/                    ← Already uploaded ✅
├── email-smtp-config.php               ← UPLOAD THIS
├── test-smtp-email.php                 ← UPLOAD THIS
├── send-description-email.php          ← UPLOAD THIS (updated)
├── send-final-email.php                ← UPLOAD THIS (updated)
├── composer.json                       ← UPLOAD THIS
└── ... (other existing files)
```

---

## 🧪 TESTING STEPS

### **Step 1: Test SMTP Configuration**
Visit: `https://www.pinkmilk.eu/ME/test-smtp-email.php`

**Expected output:**
```
✅ PHPMailer is installed
✅ SUCCESS! Email sent via SMTP
Check your inbox at: klaas@pinkmilk.eu
```

### **Step 2: Check Email**
- Check inbox: `klaas@pinkmilk.eu`
- Check inbox: `klaas@republick.nl`
- Check spam folder if not in inbox

### **Step 3: Test Questionnaire**
1. Go to questionnaire
2. Complete all questions
3. Verify you receive BOTH emails:
   - Email #1: Character description
   - Email #2: Final email with image

---

## ⚠️ TROUBLESHOOTING

### If test-smtp-email.php shows errors:

**"PHPMailer not installed"**
- Verify `vendor/autoload.php` is uploaded
- Verify PHPMailer files are in correct location

**"SMTP connect() failed"**
- Check email account `maskedemployee@pinkmilk.eu` exists
- Verify password is correct: `M@sked03`
- Contact Hostslim support

**"Could not authenticate"**
- Verify SMTP credentials in `email-smtp-config.php`
- Check if SMTP is enabled for the email account

---

## 📋 QUICK UPLOAD GUIDE

### **Using FTP (FileZilla):**
1. Connect to your server
2. Navigate to `/domains/pinkmilk.eu/public_html/ME/`
3. Upload the 6 files listed above
4. Preserve folder structure for `vendor/autoload.php`

### **Using File Manager:**
1. Log into Hostslim control panel
2. Open File Manager
3. Navigate to `/public_html/ME/`
4. Upload files
5. Create `vendor/` folder if needed

---

## ✅ COMPLETION CHECKLIST

- [ ] All 6 files uploaded to server
- [ ] `vendor/autoload.php` in correct location
- [ ] Visited `test-smtp-email.php` in browser
- [ ] Received test emails
- [ ] Tested with real questionnaire
- [ ] Both emails received successfully

---

**🎉 Once all checkboxes are complete, your SMTP email system is live!**

**Last Updated:** Nov 5, 2025 - SMTP Implementation for:
   ```
   📝 Request body: {playerName: "...", promptLength: XXX, promptPreview: "..."}
   ```
4. The `promptPreview` should contain:
   - ✅ "=== ENVIRONMENT & BACKGROUND ==="
   - ✅ "SETTING: [text from OMGEVING]"
   - ✅ Character name (e.g., "Cirquesia")

### **Test 2: Check Image Result**
1. Wait for image generation (~60 seconds)
2. Check the image:
   - ✅ Should be widescreen (16:9 ratio, not square/portrait)
   - ✅ Should show environment from OMGEVING
   - ✅ Should match character description

---

## ❌ **Current Problem:**

The server is still using OLD files that:
- ❌ Don't extract OMGEVING
- ❌ Don't send 16:9 size parameter
- ❌ Generate generic images without environment

**Result:** Portrait images with no environment (like the elf image you showed)

---

## ✅ **After Upload:**

Images should have:
- ✅ 16:9 widescreen format
- ✅ Character in their OMGEVING (tower with postcards, etc.)
- ✅ Correct character type (wizard, not elf)
- ✅ Correct character name used

---

## 🚀 **Upload Now:**

Use your FTP client to upload:
1. `generate-character.php` → `/public_html/ME/generate-character.php`
2. `generate-image-freepik.php` → `/public_html/ME/generate-image-freepik.php`

Then test again!
