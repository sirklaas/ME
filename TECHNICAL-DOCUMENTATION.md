# The Masked Employee - Technical Documentation

**Version:** 3.0.0 (Production Ready)  
**Last Updated:** 2025-11-06  
**Status:** ✅ WORKING - DO NOT MODIFY WITHOUT TESTING

---

## ⚠️ CRITICAL WARNING

**This system is now FULLY FUNCTIONAL and TESTED.**

Before making ANY changes:
1. Read this entire document
2. Understand the data flow
3. Test changes locally first
4. Never modify core functions without backup
5. Always check dependencies before editing

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Core Components](#core-components)
4. [Data Flow](#data-flow)
5. [Critical Functions](#critical-functions)
6. [Field Name Reference](#field-name-reference)
7. [Email System](#email-system)
8. [Character Generation](#character-generation)
9. [Image Generation](#image-generation)
10. [Common Issues & Solutions](#common-issues--solutions)
11. [Safe Modification Guidelines](#safe-modification-guidelines)

---

## System Overview

### What It Does
1. User completes questionnaire (49 questions across 9 chapters)
2. AI generates character description based on answers
3. System generates character image using Leonardo.ai
4. User receives 2 emails:
   - Email #1: Character description
   - Email #2: Character image with reveal link
5. All data saved to PocketBase
6. Gallery displays all characters

### Technology Stack
- **Frontend:** Vanilla JavaScript, HTML5, CSS3
- **Backend:** PHP 8.x
- **Database:** PocketBase (hosted at pinkmilk.pockethost.io)
- **AI:** OpenAI GPT-4 (character generation)
- **Image AI:** Leonardo.ai API
- **Email:** PHP mail() function
- **Fonts:** Barlow Semi Condensed (Google Fonts)

---

## Architecture

### File Structure
```
ME/
├── questions.html          # Main questionnaire interface
├── script.js              # Core application logic (3,263 lines)
├── styles.css             # All styling
├── images.html            # Character gallery
├── reveal-character.php   # Image reveal page
│
├── PHP Backend:
├── generate-character.php # AI character generation
├── generate-image.php     # Leonardo.ai image generation
├── send-description-email.php  # First email
├── send-final-email.php   # Second email with image
├── download-image.php     # Image download handler
│
├── Configuration:
├── questions-unified.json # All questions data
├── character-options-80.json  # Character type options
├── image-prompt-requirements.json  # Image generation rules
│
└── Documentation:
    ├── README.md
    ├── TECHNICAL-DOCUMENTATION.md (this file)
    ├── DATA-FLOW-DIAGRAM.md
    └── COMPLETE-FLOW-DIAGRAM.md
```

---

## Core Components

### 1. Questionnaire System (`script.js`)

**Main Class:** `GameShowApp`

**Critical Properties:**
```javascript
this.currentLanguage = 'nl'  // or 'en'
this.playerName = ''
this.playerEmail = ''
this.playerRecordId = null   // PocketBase record ID
this.currentChapter = 1
this.answers = {}            // All user answers
this.gameName = ''           // Loaded from PocketBase
```

**DO NOT MODIFY:**
- `saveToPocketBase()` - Creates NEW records (not updates)
- `submitFinalAnswers()` - Handles complete submission flow
- `generateAndUploadImage()` - Image generation pipeline
- `loadGameName()` - Dynamic gamename loading

### 2. PocketBase Integration

**Collection:** `MEQuestions`

**Connection:**
```javascript
const pb = new PocketBase('https://pinkmilk.pockethost.io');
const credentials = 'biknu8-pyrnaB-mytvyx';
pb.authStore.save(credentials, { admin: true });
```

**⚠️ NEVER CHANGE:**
- PocketBase URL
- Credentials
- Collection name
- Authentication method

### 3. Character Generation (`generate-character.php`)

**Input:** User answers + character type
**Output:** AI-generated character description

**Format:**
```
De [Type] genaamd [Name]

1. KARAKTER (100-150 woorden):
[Character description]

2. OMGEVING (30-50 woorden):
[Environment description]
```

**⚠️ CRITICAL:**
- Section headers are REPLACED in emails/reveal page
- "1. KARAKTER" → "🎭 Jouw Karakter"
- "2. OMGEVING" → "🌍 Dit is jouw wereld"
- DO NOT change these patterns without updating all 3 files

### 4. Image Generation (`generate-image.php`)

**API:** Leonardo.ai
**Model:** Leonardo Phoenix
**Size:** 832x1216 (portrait)

**Process:**
1. Receives character description
2. Extracts character type
3. Loads requirements from `image-prompt-requirements.json`
4. Generates image via Leonardo.ai
5. Returns base64 + URL

**⚠️ DO NOT MODIFY:**
- Image dimensions
- Model ID
- Prompt structure

---

## Data Flow

### Complete User Journey

```
1. User starts questionnaire
   ↓
2. Answers saved progressively to PocketBase
   ↓
3. User completes all questions → clicks "Voltooien"
   ↓
4. AI generates character description
   ↓
5. NEW PocketBase record created (always new, never update)
   ↓
6. Character preview shown
   ↓
7. User accepts → enters email
   ↓
8. Email #1 sent (description only)
   ↓
9. Image generation starts (Leonardo.ai)
   ↓
10. Image uploaded to PocketBase
    ↓
11. Email #2 sent (with image + reveal link)
    ↓
12. User clicks reveal link → sees character
    ↓
13. Character appears in gallery (images.html)
```

### PocketBase Record Creation

**⚠️ CRITICAL BEHAVIOR:**
```javascript
// ALWAYS creates NEW record (line 608-613 in script.js)
const record = await pb.collection('MEQuestions').create(submissionData);
this.playerRecordId = record.id;
```

**WHY:** Each questionnaire completion = unique record
**DO NOT:** Change to update existing records
**REASON:** Multiple plays should create multiple records

---

## Critical Functions

### 1. `formatCharacterDescription()` (3 files)

**Files:**
- `send-description-email.php` (line 43-56)
- `send-final-email.php` (line 44-57)
- `reveal-character.php` (line 17-34)

**What it does:**
```php
// Replaces section headers
"1. KARAKTER (100-150 woorden):" → "\n\n🎭 Jouw Karakter\n\n"
"2. OMGEVING (30-50 woorden):" → "\n\n🌍 Dit is jouw wereld\n\n"

// Removes multiple characters (keeps only first)
// Cleans excessive line breaks
```

**⚠️ IF YOU CHANGE:**
- Must update in ALL 3 files
- Test email rendering
- Test reveal page display
- Verify no HTML code shows as text

### 2. Character Name Cleaning (Multiple Files)

**Pattern:**
```php
// Removes duplicates like "Leo\n\nLeo" → "Leo"
$nameParts = preg_split('/[\n\r]+/', $characterName);
if ($nameParts[0] === end($nameParts)) {
    $characterName = $nameParts[0];
}
```

**Used in:**
- `send-description-email.php`
- `send-final-email.php`
- `reveal-character.php`
- `script.js` (lines 1582-1589)

**⚠️ DO NOT REMOVE:** Prevents "Philip Philip" display bug

### 3. Image Field Names

**Gallery (`images.html`):**
```javascript
// Checks multiple possible field names (line 308-313)
const hasImage = record.image || record.character_image || 
                 record.generated_image || record.image_url;
```

**Lightbox (`images.html`):**
```javascript
// Line 351
const imageField = char.image || char.character_image || 
                   char.generated_image || char.image_url || '';
```

**⚠️ PRIORITY ORDER MATTERS:**
- `image` is the PRIMARY field name
- Others are fallbacks for compatibility
- DO NOT change order without testing

---

## Field Name Reference

### PocketBase Collection: `MEQuestions`

**Core Fields:**
```
gamename          - Game show name
nameplayer        - Player name
email             - Player email
character_name    - Generated character name
ai_summary        - Full AI-generated description
image             - Character image filename
character_type    - Type of character (e.g., "fairy_tales")
```

**Chapter Fields:**
```
chapter01 - Chapter 1 answers (JSON)
chapter02 - Chapter 2 answers (JSON)
...
chapter09 - Chapter 9 answers (JSON)
```

**Metadata:**
```
created           - Timestamp
updated           - Timestamp
id                - Unique record ID
collectionId      - Collection ID
```

**⚠️ NEVER RENAME:**
- `image` - Used throughout system
- `ai_summary` - Contains character description
- `character_name` - Displayed in gallery/emails
- `email` - Used for sending emails

---

## Email System

### Email #1: Description (`send-description-email.php`)

**Sent:** Immediately after user accepts character
**Contains:**
- Character name
- Full description (formatted)
- "What happens next" message

**Key Code:**
```php
// Line 107-117 (Dutch version)
<div class='section'>
    <h3>Ja dit ben je eigenlijk heel diep van binnen:</h3>
    " . formatCharacterDescription($characterDesc) . "
</div>
```

**⚠️ DO NOT:**
- Add back `<p><strong>$characterName</strong></p>` (creates duplicate)
- Use `htmlspecialchars()` on formatted output (shows HTML as text)
- Remove `formatCharacterDescription()` call

### Email #2: Image (`send-final-email.php`)

**Sent:** After image generation completes
**Contains:**
- Reveal button/link
- Character description (formatted)
- Character image
- Download link

**Reveal URL:**
```php
// Line 97-98
$revealUrl = 'https://www.pinkmilk.eu/ME/reveal-character.php?img=' . 
             urlencode($imageUrl) . '&name=' . urlencode($characterName) . 
             '&desc=' . urlencode($characterDesc);
```

**⚠️ CRITICAL:**
- URL parameters must be URL-encoded
- Description parameter can be very long (2000+ chars)
- Reveal page decodes with `urldecode()`

---

## Character Generation

### AI Prompt Structure

**File:** `generate-character.php`

**Input Data:**
```php
$answers        // All questionnaire answers
$characterType  // Selected character type
$department     // Player department (optional)
```

**Output Format:**
```
De [Type] genaamd [Name]

1. KARAKTER (100-150 woorden):
[Detailed character description]

2. OMGEVING (30-50 woorden):
[Environment description]
```

**⚠️ FORMATTING RULES:**
1. First line MUST be "De [Type] genaamd [Name]"
2. Section headers MUST match exactly
3. Character name may appear multiple times (cleaned later)
4. Line breaks are important for parsing

### Character Types

**File:** `character-options-80.json`

**Structure:**
```json
{
  "character_types": {
    "fairy_tales": ["Prins", "Prinses", "Draak", ...],
    "animals": ["Leeuw", "Panter", "Uil", ...],
    ...
  }
}
```

**⚠️ DO NOT:**
- Remove character types without updating prompts
- Change type IDs (used in code)
- Modify without testing image generation

---

## Image Generation

### Leonardo.ai Configuration

**File:** `generate-image.php`

**Settings:**
```php
'modelId' => 'b24e16ff-06e3-43eb-8d33-4416c2d75876'  // Leonardo Phoenix
'width' => 832
'height' => 1216
'num_images' => 1
'photoReal' => true
```

**⚠️ NEVER CHANGE:**
- Model ID (Phoenix model)
- Dimensions (optimized for portraits)
- photoReal setting

### Image Prompt Requirements

**File:** `image-prompt-requirements.json`

**Contains:**
- Style guidelines per character type
- Forbidden elements
- Required elements
- Quality settings

**⚠️ IF YOU MODIFY:**
- Test with multiple character types
- Verify image quality
- Check generation time (should be <30 seconds)

---

## Common Issues & Solutions

### Issue 1: "No characters yet" in gallery

**Cause:** Field name mismatch
**Solution:** Check `image` field exists in PocketBase
**Code:** `images.html` line 308-313

### Issue 2: Duplicate character names ("Leo Leo")

**Cause:** AI returns name twice
**Solution:** Already fixed with name cleaning function
**Files:** All email/reveal files
**DO NOT REMOVE:** Name cleaning code

### Issue 3: HTML code showing in emails

**Cause:** Using `htmlspecialchars()` on HTML output
**Solution:** Already fixed - uses plain text replacements
**DO NOT:** Add `htmlspecialchars()` to `formatCharacterDescription()` output

### Issue 4: Multiple characters showing on reveal page

**Cause:** AI summary contains multiple characters
**Solution:** Already fixed - extracts only first character
**Code:** `reveal-character.php` line 22-27

### Issue 5: Excessive line breaks

**Cause:** AI adds many newlines
**Solution:** Already fixed with regex cleanup
**Code:** `preg_replace('/\n{3,}/', "\n\n", $aiSummary)`

### Issue 6: Records being overwritten

**Cause:** Code was updating instead of creating
**Solution:** Changed to ALWAYS create new records
**Code:** `script.js` line 608-613
**⚠️ DO NOT REVERT:** Each play must be unique record

---

## Safe Modification Guidelines

### Before Making Changes

1. **Backup Files:**
   ```bash
   cp file.php file.php.backup
   ```

2. **Test Locally:**
   - Complete full questionnaire
   - Check both emails
   - Verify gallery display
   - Test reveal page

3. **Document Changes:**
   - Update this file
   - Add comments in code
   - Note in git commit

### Safe to Modify

✅ **Styling (CSS):**
- Colors, fonts, spacing
- Responsive breakpoints
- Animations

✅ **Text Content:**
- Email messages
- Button labels
- Help text

✅ **Questions:**
- Add/remove questions in JSON
- Change question text
- Modify options

### DANGEROUS to Modify

❌ **Field Names:**
- PocketBase field names
- JavaScript property names
- URL parameters

❌ **Core Functions:**
- `saveToPocketBase()`
- `formatCharacterDescription()`
- `generateAndUploadImage()`

❌ **Data Flow:**
- Record creation logic
- Email sending sequence
- Image upload process

❌ **API Configuration:**
- PocketBase credentials
- Leonardo.ai settings
- OpenAI model selection

### If You Must Modify Core Functions

1. **Create Test Environment:**
   - Separate PocketBase collection
   - Test email address
   - Local development server

2. **Make Incremental Changes:**
   - One function at a time
   - Test after each change
   - Keep backup of working version

3. **Test Thoroughly:**
   - Complete questionnaire 3+ times
   - Test all character types
   - Verify all emails received
   - Check gallery updates

4. **Update Documentation:**
   - This file
   - Code comments
   - README.md

---

## Testing Checklist

### Before Deployment

- [ ] Complete questionnaire from start to finish
- [ ] Verify Email #1 received with correct formatting
- [ ] Verify Email #2 received with image
- [ ] Click reveal link - image displays correctly
- [ ] Check gallery - new character appears
- [ ] Click character card - lightbox shows correct data
- [ ] Test with different character types
- [ ] Verify no duplicate names
- [ ] Verify no HTML code visible
- [ ] Check mobile responsiveness

### After Deployment

- [ ] Test on production server
- [ ] Monitor PocketBase for new records
- [ ] Check email delivery
- [ ] Verify image generation
- [ ] Test gallery loading
- [ ] Check console for errors

---

## Emergency Rollback

If something breaks:

1. **Identify Last Working Version:**
   ```bash
   git log --oneline
   ```

2. **Revert to Working Commit:**
   ```bash
   git revert <commit-hash>
   git push origin main
   ```

3. **Re-upload to Server:**
   - Upload reverted files
   - Clear browser cache
   - Test functionality

4. **Document Issue:**
   - What was changed
   - What broke
   - How it was fixed

---

## Version History

### v3.0.0 (2025-11-06) - CURRENT
- ✅ Complete working system
- ✅ Fixed all duplicate name issues
- ✅ Fixed HTML display in emails
- ✅ Fixed excessive line breaks
- ✅ Gallery shows gamename from PocketBase
- ✅ 9-column grid for large screens
- ✅ Barlow Semi Condensed font throughout
- ✅ All formatting issues resolved

### v2.0.0 (2025-11-05)
- Character generation working
- Image generation integrated
- Email system functional
- Gallery implemented

### v1.0.0 (2025-10-06)
- Initial questionnaire system
- PocketBase integration
- Basic email functionality

---

## Support Contacts

**Developer:** Klaas (klaas@republick.nl)
**PocketBase:** pinkmilk.pockethost.io
**Server:** www.pinkmilk.eu/ME/

---

## Final Notes

**This system is WORKING and TESTED.**

The biggest risks to functionality are:
1. Changing field names
2. Modifying core functions
3. Altering data flow
4. Removing "redundant" code that actually fixes bugs

**When in doubt:**
- Don't change it
- Test locally first
- Keep backups
- Document everything

**Remember:** Code that looks redundant often fixes edge cases!

---

**Last Updated:** 2025-11-06  
**Status:** ✅ PRODUCTION READY  
**Next Review:** Before any major changes
