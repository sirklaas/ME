# Safe Modification Guide

**Quick Reference for Making Changes Without Breaking Functionality**

---

## 🚨 GOLDEN RULES

1. **NEVER modify without testing locally first**
2. **ALWAYS backup files before editing**
3. **TEST the complete user journey after changes**
4. **DOCUMENT what you changed and why**

---

## ✅ SAFE TO CHANGE

### Styling & Visual Design

**Files:** `styles.css`, inline styles in HTML/PHP

```css
/* SAFE: Colors, fonts, spacing */
.character-card {
    background: white;        /* ✅ Change color */
    border-radius: 15px;      /* ✅ Change size */
    box-shadow: 0 8px 30px;   /* ✅ Change shadow */
}

/* SAFE: Responsive breakpoints */
@media (max-width: 1800px) {
    .gallery {
        grid-template-columns: repeat(6, 1fr);  /* ✅ Change columns */
    }
}
```

**What you can change:**
- Colors and gradients
- Font sizes and weights
- Margins and padding
- Border radius
- Shadows
- Animations
- Grid/flex layouts
- Responsive breakpoints

**How to test:**
- View on different screen sizes
- Check all pages (questionnaire, gallery, reveal)
- Verify readability

---

### Text Content

**Files:** Email PHP files, HTML files

```php
/* SAFE: Email subject lines */
$subject = '🎭 Your Character - ' . $gameName;  // ✅ Change text

/* SAFE: Email body text */
<p>Op basis van je antwoorden...</p>  // ✅ Change message

/* SAFE: Button labels */
<button>ONTHUL MIJN KARAKTER</button>  // ✅ Change text
```

**What you can change:**
- Email subject lines
- Email body text
- Button labels
- Help text
- Instructions
- Error messages

**How to test:**
- Complete questionnaire
- Check both emails
- Verify all text displays correctly

---

### Questions & Options

**File:** `questions-unified.json`

```json
{
  "question": "What is your favorite color?",  // ✅ Change text
  "type": "radio",                             // ⚠️ Don't change
  "options": [
    "Red",      // ✅ Change/add/remove options
    "Blue",
    "Green"
  ]
}
```

**What you can change:**
- Question text
- Option labels
- Add/remove questions
- Add/remove options
- Question descriptions

**How to test:**
- Complete questionnaire with new questions
- Verify answers save to PocketBase
- Check character generation still works

---

## ⚠️ MODIFY WITH CAUTION

### Email Templates

**Files:** `send-description-email.php`, `send-final-email.php`

```php
/* CAUTION: Don't break the formatting function */
<div class='section'>
    <h3>Ja dit ben je eigenlijk heel diep van binnen:</h3>
    " . formatCharacterDescription($characterDesc) . "  // ⚠️ Don't remove
</div>
```

**What to watch out for:**
- Don't remove `formatCharacterDescription()` calls
- Don't add `htmlspecialchars()` to formatted output
- Don't remove character name cleaning code
- Keep URL encoding for reveal link

**How to test:**
- Send test emails
- Check formatting (no HTML code visible)
- Verify no duplicate names
- Test reveal link works

---

### Gallery Display

**File:** `images.html`

```javascript
/* CAUTION: Field name priority matters */
const imageField = char.image ||              // ⚠️ Keep order
                   char.character_image ||
                   char.generated_image ||
                   char.image_url || '';
```

**What to watch out for:**
- Don't change field name priority
- Don't remove fallback field names
- Keep gamename loading logic
- Maintain grid responsiveness

**How to test:**
- View gallery
- Click character cards
- Check lightbox display
- Test on different screen sizes

---

## ❌ DANGEROUS - DO NOT MODIFY

### PocketBase Configuration

```javascript
/* NEVER CHANGE */
const pb = new PocketBase('https://pinkmilk.pockethost.io');
const credentials = 'biknu8-pyrnaB-mytvyx';
pb.authStore.save(credentials, { admin: true });
```

**Why:** Breaks database connection

---

### Record Creation Logic

```javascript
/* NEVER CHANGE THIS TO UPDATE */
const record = await pb.collection('MEQuestions').create(submissionData);
this.playerRecordId = record.id;
```

**Why:** Each play must create NEW record, not update existing

---

### Field Names

```javascript
/* NEVER RENAME */
record.image              // Used everywhere
record.ai_summary         // Contains character description
record.character_name     // Displayed in gallery/emails
record.email              // For sending emails
```

**Why:** Used throughout system, changing breaks everything

---

### Format Character Description Function

```php
/* NEVER REMOVE OR SIGNIFICANTLY MODIFY */
function formatCharacterDescription($desc) {
    $desc = preg_replace('/\d+\.\s*KARAKTER\s*\([^)]+\):\s*/i', "\n\n🎭 Jouw Karakter\n\n", $desc);
    $desc = preg_replace('/\d+\.\s*OMGEVING\s*\([^)]+\):\s*/i', "\n\n🌍 Dit is jouw wereld\n\n", $desc);
    // ... rest of function
}
```

**Why:** Fixes section headers in emails/reveal page. Must be identical in 3 files.

---

### Character Name Cleaning

```php
/* NEVER REMOVE */
$nameParts = preg_split('/[\n\r]+/', $characterName);
if ($nameParts[0] === end($nameParts)) {
    $characterName = $nameParts[0];
}
```

**Why:** Prevents "Leo Leo" duplicate name bug

---

### Image Generation Settings

```php
/* NEVER CHANGE */
'modelId' => 'b24e16ff-06e3-43eb-8d33-4416c2d75876'  // Leonardo Phoenix
'width' => 832
'height' => 1216
```

**Why:** Optimized settings, changing breaks image quality

---

## 🔧 COMMON MODIFICATION SCENARIOS

### Scenario 1: Change Email Subject Line

**File:** `send-description-email.php` or `send-final-email.php`

```php
// BEFORE
$subject = '🎭 Your Character - ' . $gameName;

// AFTER
$subject = '✨ Your Secret Character - ' . $gameName;  // ✅ SAFE
```

**Test:** Send test email, verify subject appears correctly

---

### Scenario 2: Add New Question

**File:** `questions-unified.json`

```json
{
  "id": 50,
  "question": "What is your dream vacation?",
  "type": "textarea",
  "required": true
}
```

**Test:** Complete questionnaire, verify answer saves

---

### Scenario 3: Change Gallery Grid Columns

**File:** `images.html`

```css
/* BEFORE */
.gallery {
    grid-template-columns: repeat(9, 1fr);
}

/* AFTER */
.gallery {
    grid-template-columns: repeat(12, 1fr);  /* ✅ SAFE */
}
```

**Test:** View gallery on large screen, verify layout

---

### Scenario 4: Change Button Color

**File:** Any HTML/PHP file

```html
<!-- BEFORE -->
<button style="background: #8A2BE2;">Click Me</button>

<!-- AFTER -->
<button style="background: #FF6B6B;">Click Me</button>  <!-- ✅ SAFE -->
```

**Test:** View page, verify color change

---

### Scenario 5: Add New Character Type

**File:** `character-options-80.json`

```json
{
  "character_types": {
    "superheroes": [  // ✅ Add new type
      "Superman",
      "Wonder Woman",
      "Batman"
    ]
  }
}
```

**Also update:** `image-prompt-requirements.json` with style guidelines

**Test:** 
- Select new character type
- Complete questionnaire
- Verify character generates correctly
- Check image generation works

---

## 📋 PRE-MODIFICATION CHECKLIST

Before making ANY change:

- [ ] Read relevant section in TECHNICAL-DOCUMENTATION.md
- [ ] Backup file(s) you're modifying
- [ ] Understand what the code does
- [ ] Know how to test the change
- [ ] Have rollback plan ready

---

## 🧪 TESTING CHECKLIST

After making changes:

### Quick Test (5 minutes)
- [ ] Page loads without errors
- [ ] Changed element displays correctly
- [ ] No console errors

### Full Test (20 minutes)
- [ ] Complete questionnaire start to finish
- [ ] Verify Email #1 received correctly
- [ ] Verify Email #2 received with image
- [ ] Click reveal link - works correctly
- [ ] Check gallery - new character appears
- [ ] Click character card - lightbox works
- [ ] Test on mobile device

---

## 🚑 EMERGENCY ROLLBACK

If something breaks:

### Step 1: Restore Backup
```bash
cp file.php.backup file.php
```

### Step 2: Re-upload to Server
Upload the backup file via FTP

### Step 3: Clear Cache
- Clear browser cache
- Refresh page

### Step 4: Test
- Verify functionality restored
- Document what went wrong

---

## 📞 WHEN TO ASK FOR HELP

Ask before modifying if:
- You're not sure what the code does
- It involves PocketBase or API calls
- It's in the "DANGEROUS" section
- Multiple files need to change together
- You're changing data flow logic

---

## 💡 TIPS FOR SAFE MODIFICATIONS

1. **Start Small**
   - Make one change at a time
   - Test immediately
   - Don't combine multiple changes

2. **Use Version Control**
   ```bash
   git add .
   git commit -m "Descriptive message"
   git push
   ```

3. **Comment Your Changes**
   ```javascript
   // Changed from 9 to 12 columns for larger displays - 2025-11-06
   grid-template-columns: repeat(12, 1fr);
   ```

4. **Keep Notes**
   - What you changed
   - Why you changed it
   - What you tested
   - Any issues encountered

5. **Test in Production**
   - Even if it works locally
   - Server environment may differ
   - Always verify after upload

---

## 🎯 MODIFICATION DIFFICULTY LEVELS

### 🟢 Easy (Low Risk)
- Text content changes
- Color changes
- Font size adjustments
- Adding CSS classes
- Changing button labels

### 🟡 Medium (Moderate Risk)
- Adding new questions
- Modifying email templates
- Changing grid layouts
- Adding new character types
- Updating image requirements

### 🔴 Hard (High Risk)
- Modifying core functions
- Changing data flow
- Updating PocketBase logic
- Altering API calls
- Changing field names

**Rule of Thumb:** If it's 🔴 Hard, consult documentation first!

---

## 📚 Additional Resources

- **TECHNICAL-DOCUMENTATION.md** - Complete system reference
- **DATA-FLOW-DIAGRAM.md** - How data moves through system
- **COMPLETE-FLOW-DIAGRAM.md** - Full user journey
- **README.md** - Project overview

---

**Remember:** When in doubt, DON'T change it. Test first, deploy later!

**Last Updated:** 2025-11-06
