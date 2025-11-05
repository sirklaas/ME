# 📧 SMTP EMAIL INSTALLATION GUIDE

## ✅ WHAT WE'VE BUILT:

PHPMailer with SMTP authentication for reliable email delivery using your Hostslim credentials.

**Configuration:**
- Host: `mail.pinkmilk.eu`
- Port: `587` (STARTTLS)
- Username: `maskedemployee@pinkmilk.eu`
- Password: `M@sked03`

---

## 🚀 INSTALLATION STEPS:

### Step 1: Install PHPMailer (on your server)

**Option A: Via Composer (Recommended)**
```bash
cd /path/to/your/ME/folder
composer install
```

**Option B: Manual Installation**
If composer is not available on Hostslim:

1. Download PHPMailer: https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip
2. Extract to `/vendor/phpmailer/phpmailer/` folder
3. Make sure the structure is:
   ```
   /ME/
   ├── vendor/
   │   └── phpmailer/
   │       └── phpmailer/
   │           ├── src/
   │           │   ├── PHPMailer.php
   │           │   ├── SMTP.php
   │           │   └── Exception.php
   │           └── ...
   ```

---

### Step 2: Upload Files to Server

Upload these NEW files to your server:
- ✅ `composer.json`
- ✅ `email-smtp-config.php`
- ✅ `test-smtp-email.php`
- ✅ `send-description-email.php` (updated)
- ✅ `send-final-email.php` (updated)

**Plus the vendor folder if installed manually**

---

### Step 3: Test SMTP Configuration

Run the test script on your server:

```bash
php test-smtp-email.php
```

**Expected output:**
```
=== SMTP EMAIL CONFIGURATION TEST ===

Step 1: Check PHPMailer Installation
-------------------------------------
✅ PHPMailer is installed

Step 2: SMTP Configuration
-------------------------------------
Host: mail.pinkmilk.eu
Port: 587 (STARTTLS)
Username: maskedemployee@pinkmilk.eu
Password: ********

Step 3: Send Test Email
-------------------------------------
Sending test email to: klaas@pinkmilk.eu
✅ SUCCESS! Email sent via SMTP
   Check your inbox at: klaas@pinkmilk.eu

Step 4: Test Different Recipient
-------------------------------------
Sending test email to: klaas@republick.nl
✅ SUCCESS! Email sent to external domain
   Check your inbox at: klaas@republick.nl

=== TEST COMPLETE ===
```

---

### Step 4: Verify Email Delivery

Check your inbox for test emails:
- ✅ Email to `klaas@pinkmilk.eu`
- ✅ Email to `klaas@republick.nl`
- ✅ Check spam folder if not in inbox

---

### Step 5: Test with Real Questionnaire

1. Go to your questionnaire
2. Fill in answers
3. Complete the flow
4. Check if you receive BOTH emails:
   - Email #1: Character description
   - Email #2: Final email with image

---

## 🔧 TROUBLESHOOTING:

### Error: "PHPMailer not installed"
**Solution:** Run `composer install` or install manually

### Error: "SMTP connect() failed"
**Solution:** 
- Verify port 587 is open on server
- Check firewall settings
- Try port 465 with SSL instead

### Error: "SMTP Error: Could not authenticate"
**Solution:**
- Verify email account `maskedemployee@pinkmilk.eu` exists in Hostslim
- Check password is correct: `M@sked03`
- Verify SMTP is enabled for this email account

### Emails not received
**Solution:**
- Check spam folder
- Verify recipient email is correct
- Check server error logs
- Contact Hostslim support

---

## 📁 FILE STRUCTURE:

```
/ME/
├── composer.json                 ← NEW (PHPMailer dependency)
├── email-smtp-config.php         ← NEW (SMTP configuration)
├── test-smtp-email.php           ← NEW (Test script)
├── send-description-email.php    ← UPDATED (Uses SMTP)
├── send-final-email.php          ← UPDATED (Uses SMTP)
├── vendor/                       ← NEW (PHPMailer library)
│   ├── autoload.php
│   └── phpmailer/
│       └── phpmailer/
│           └── src/
│               ├── PHPMailer.php
│               ├── SMTP.php
│               └── Exception.php
└── ... (other files)
```

---

## 🔐 SECURITY NOTES:

### 1. Protect SMTP Credentials

The password is currently in `email-smtp-config.php`. For better security:

**Option A: Use environment variables**
```php
$mail->Password = getenv('SMTP_PASSWORD');
```

**Option B: Use .gitignore**
Add to `.gitignore`:
```
email-smtp-config.php
```

### 2. File Permissions

Set proper permissions:
```bash
chmod 644 email-smtp-config.php
chmod 755 vendor/
```

---

## ✅ BENEFITS OF SMTP:

- ✅ **Reliable delivery** - Authenticated SMTP is more reliable than mail()
- ✅ **Better deliverability** - Less likely to be marked as spam
- ✅ **Error reporting** - Get detailed error messages if sending fails
- ✅ **Works everywhere** - Independent of server mail() configuration
- ✅ **Professional** - Uses proper email authentication

---

## 📊 WHAT CHANGED:

### Before (mail() function):
```php
$headers = "From: noreply@pinkmilk.eu";
mail($to, $subject, $message, $headers);
// Returns TRUE even if email fails
```

### After (PHPMailer SMTP):
```php
$result = sendEmailSMTP($to, $subject, $message);
// Returns detailed success/error information
// Uses authenticated SMTP connection
// Proper error handling
```

---

## 📞 SUPPORT:

If you encounter issues:

1. **Check test script output** - Run `test-smtp-email.php`
2. **Check server logs** - Look for SMTP errors
3. **Verify credentials** - Ensure email account exists
4. **Contact Hostslim** - Ask about SMTP configuration
5. **Check this guide** - Review troubleshooting section

---

## 🎯 NEXT STEPS:

1. ✅ Upload all files to server
2. ✅ Run `composer install` (or install PHPMailer manually)
3. ✅ Run `test-smtp-email.php`
4. ✅ Verify test emails received
5. ✅ Test with real questionnaire
6. ✅ Celebrate working emails! 🎉

---

**Your emails will now be delivered reliably via SMTP!** 📧✨
