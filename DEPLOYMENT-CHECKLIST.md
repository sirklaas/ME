# Deployment Checklist
## Files to Upload to Server

**Last Updated:** October 5, 2025

---

## 🚀 Essential Files (REQUIRED)

### Core Application Files
```
✅ questions.html          - Main questionnaire page
✅ script.js               - JavaScript application logic
✅ styles.css              - All styling and design
✅ mask_hero.webp          - Hero image
```

### PHP Backend Files
```
✅ generate-character-summary.php   - NEW: Character generation with OpenAI
✅ api-keys.php                     - API keys configuration
```

### Data Files (JSON)
```
✅ Questions.JSON                   - Master questions file
✅ gameshow-config-v2.json         - Game configuration
✅ chapter1-introductie.json
✅ chapter2-masked-identity.json
✅ chapter3-persoonlijke-eigenschappen.json
✅ chapter4-verborgen-talenten.json
✅ chapter5-jeugd-verleden.json
✅ chapter6-fantasie-dromen.json
✅ chapter7-eigenaardigheden.json
✅ chapter8-onverwachte-voorkeuren.json
```

### Directories
```
✅ generated-images/    - Create empty directory (for future use)
✅ logs/                - Create empty directory (for logs)
```

---

## 📋 Optional Files (Useful but not required)

### Testing Files (Keep on server for testing)
```
⚪ test-freepik-only.php        - Test Freepik API
⚪ test-ai-generation.php       - Test AI generation
⚪ test-ai-auto.php             - Test automated AI
```

### Additional Backend Files (If you need them)
```
⚪ freepik-api.php              - Freepik integration
⚪ openai-api.php               - OpenAI helper functions
⚪ generate-character.php       - Character generation
⚪ prompt-builder.php           - Prompt building utilities
```

---

## 🚫 DO NOT Upload (Documentation/Local Only)

```
❌ All .md files (PRD.md, README.md, TASKS.md, etc.)
❌ .gitignore
❌ .env.example
❌ .DS_Store
❌ deploy.log
❌ auto-deploy.sh
❌ start-auto-deploy.sh
❌ stop-auto-deploy.sh
```

---

## 📂 Server Directory Structure

After upload, your server should look like this:

```
/your-server-path/ME/
├── questions.html
├── script.js
├── styles.css
├── mask_hero.webp
├── generate-character-summary.php
├── api-keys.php
├── Questions.JSON
├── gameshow-config-v2.json
├── chapter1-introductie.json
├── chapter2-masked-identity.json
├── chapter3-persoonlijke-eigenschappen.json
├── chapter4-verborgen-talenten.json
├── chapter5-jeugd-verleden.json
├── chapter6-fantasie-dromen.json
├── chapter7-eigenaardigheden.json
├── chapter8-onverwachte-voorkeuren.json
├── generated-images/        (empty directory)
└── logs/                    (empty directory)
```

---

## ⚙️ Post-Upload Configuration

### 1. Set File Permissions
```bash
# Make PHP files executable
chmod 644 *.php
chmod 644 *.html
chmod 644 *.js
chmod 644 *.css

# Make directories writable
chmod 755 generated-images/
chmod 755 logs/
```

### 2. Configure API Keys

Edit `api-keys.php` on the server:
```php
<?php
// OpenAI API Key for character generation
define('OPENAI_API_KEY', 'sk-your-actual-key-here');

// Other API keys if needed
define('FREEPIK_API_KEY', 'your-freepik-key-here');
?>
```

**IMPORTANT:** Never commit this file to Git with real keys!

### 3. Test the Application

Visit in browser:
1. `https://yourdomain.com/ME/questions.html` - Main questionnaire
2. Complete a test submission
3. Verify character generation works
4. Check PocketBase connection

### 4. Verify PocketBase Connection

The script already includes:
```javascript
const pb = new PocketBase('https://pinkmilk.pockethost.io');
```

Make sure this URL is correct and accessible.

---

## 🔒 Security Checklist

### Before Going Live
- [ ] API keys are in `api-keys.php` (not hardcoded)
- [ ] `api-keys.php` is excluded from Git
- [ ] File permissions are set correctly
- [ ] HTTPS is enabled (not HTTP)
- [ ] PocketBase credentials are secure
- [ ] Test files are removed or password-protected
- [ ] Error reporting is off in production
- [ ] Generated images directory is writable

---

## 🧪 Testing After Deployment

### Manual Testing
1. **Welcome Page**
   - [ ] Language toggle works (NL/EN)
   - [ ] Player name input validates
   - [ ] Start button triggers confidentiality modal

2. **Confidentiality Modal**
   - [ ] Modal displays correctly
   - [ ] Checkbox enables accept button
   - [ ] Accept button starts questionnaire

3. **Questionnaire (All 8 Chapters)**
   - [ ] Questions load from JSON files
   - [ ] Navigation works (next/previous)
   - [ ] Progress bar updates
   - [ ] Answers save to PocketBase

4. **Character Summary Page**
   - [ ] AI generates character profile
   - [ ] Displays: Character, Environment, Props, 3 Story Prompts
   - [ ] Color-coded sections render correctly
   - [ ] Confirmation options work

5. **Final Submission**
   - [ ] Data saves to PocketBase
   - [ ] Character data extracted correctly
   - [ ] Processing page shows

---

## 📊 Quick Upload Methods

### Method 1: FTP/SFTP (Recommended)
Use FileZilla, Cyberduck, or command-line:
```bash
# Using SCP
scp questions.html script.js styles.css user@yourserver.com:/path/to/ME/
scp *.php user@yourserver.com:/path/to/ME/
scp *.json user@yourserver.com:/path/to/ME/
scp mask_hero.webp user@yourserver.com:/path/to/ME/
```

### Method 2: Git (If server has Git)
```bash
# On your local machine
git add questions.html script.js styles.css generate-character-summary.php
git commit -m "Deploy enhanced questionnaire with character generation"
git push origin main

# On server
cd /path/to/ME/
git pull origin main
```

### Method 3: cPanel File Manager
1. Login to cPanel
2. Navigate to File Manager
3. Go to your ME directory
4. Upload files using the upload button
5. Upload each file type (HTML, JS, CSS, PHP, JSON)

---

## 🔄 Update Workflow (For Future Changes)

When you make changes:

1. **Test Locally First**
   - Open `questions.html` in browser
   - Complete full flow
   - Verify everything works

2. **Upload Only Changed Files**
   - Modified HTML? Upload `questions.html`
   - Modified JS? Upload `script.js`
   - Modified CSS? Upload `styles.css`
   - Modified PHP? Upload the specific PHP file
   - Modified questions? Upload specific chapter JSON

3. **Clear Browser Cache**
   - After upload, hard refresh (Cmd+Shift+R or Ctrl+F5)
   - Or add version query: `script.js?v=2`

4. **Test on Server**
   - Complete a test submission
   - Verify changes applied correctly

---

## 🆘 Troubleshooting

### Issue: "Questions not loading"
**Fix:** Check that all JSON files uploaded correctly

### Issue: "Character summary not generating"
**Fix:** 
1. Check OpenAI API key in `api-keys.php`
2. Check PHP error logs
3. Verify `generate-character-summary.php` uploaded

### Issue: "Styles not applying"
**Fix:**
1. Clear browser cache
2. Check `styles.css` uploaded
3. Check file permissions (should be 644)

### Issue: "PocketBase connection failed"
**Fix:**
1. Check internet connection
2. Verify PocketBase URL in `script.js`
3. Check PocketBase credentials

---

## 📞 Support Files Location

All documentation is in `/Users/mac/GitHubLocal/ME/`:
- `IMPLEMENTATION_SUMMARY.md` - Feature documentation
- `PRD_INTEGRATION_SUMMARY.md` - PRD alignment guide
- `OPENAI_SETUP.md` - OpenAI configuration
- `README.md` - General setup

Keep these locally for reference!

---

**Ready to Deploy?** Follow the checklist above and you'll be live in minutes! 🚀
