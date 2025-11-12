# 🔄 Regenerate & Email Flow

## Overview
After character generation, users have two options:
1. **Regenerate** - Generate a different character with variation
2. **Accept** - Confirm character and receive emails

---

## 🎭 Complete Flow

```
User completes all 43 questions
    ↓
Clicks "🎭 Voltooien!" on Chapter 9
    ↓
Character Generation (OpenAI - 4 API calls)
    ↓
═══════════════════════════════════════════════
CHARACTER PREVIEW PAGE
═══════════════════════════════════════════════
Displays:
- Character Name (e.g., "De Slimme Vos")
- Character Type (e.g., "animals")
- Personality Traits
- AI Summary (full description)
- Story Prompts (optional display)

Two Buttons:
┌─────────────────────────────────────────┐
│  🔄 Genereer opnieuw  │  ✅ Ja, dat ben ik!  │
└─────────────────────────────────────────┘
```

---

## Option 1: 🔄 Regenerate Character

### **User Action:**
Clicks "🔄 Genereer opnieuw" button

### **What Happens:**

```javascript
// script.js - regenerateCharacter()
1. Show loading spinner
2. Call generateCharacterData() with regenerate: true flag
3. PHP receives regenerate flag
4. OpenAI temperature increased: 0.8 → 1.0 (more creative)
5. Special instruction added to prompt:
   "⚠️ REGENERATION REQUEST: Create a DIFFERENT character..."
6. New character generated with:
   - Different name
   - Different animal/fruit/hero from 80 options
   - Different clothing style
   - Different personality emphasis
7. Display new character on same page
```

### **Technical Details:**

**JavaScript (`script.js`):**
```javascript
async regenerateCharacter() {
    const submissionData = {
        timestamp: new Date().toISOString(),
        playerName: this.playerName,
        answers: this.answers,
        totalQuestions: Object.keys(this.answers).length,
        regenerate: true  // ← FLAG
    };
    
    const characterData = await this.generateCharacterData(submissionData);
    this.displayCharacterData(characterData);
}
```

**PHP (`generate-character.php`):**
```php
// Detect regenerate flag
$isRegenerate = isset($data['regenerate']) && $data['regenerate'] === true;

// Add variation instruction
if ($isRegenerate) {
    $formattedAnswers .= "\n⚠️ REGENERATION REQUEST: Create a DIFFERENT character...";
}

// Increase temperature for more variation
function callOpenAI($apiKey, $systemPrompt, $userPrompt, $maxTokens, $isRegenerate) {
    $temperature = $isRegenerate ? 1.0 : 0.8;  // Higher = more creative
    // ...
}
```

### **Result:**
- User can regenerate unlimited times
- Each generation costs ~$0.05 (OpenAI)
- Character stays on same personality base but varies in expression
- **NOT saved to PocketBase** until user accepts

---

## Option 2: ✅ Accept Character

### **User Action:**
Clicks "✅ Ja, dat ben ik!" button

### **What Happens:**

```
Click "✅ Ja, dat ben ik!"
    ↓
acceptCharacterAndContinue()
    ↓
Save character data to PocketBase (if not already saved)
    ↓
═══════════════════════════════════════════════
EMAIL INPUT MODAL
═══════════════════════════════════════════════
"📧 Waar mogen we de uitkomst naar toe mailen?"
[email input field]
[Verstuur button]
    ↓
User enters email and clicks "Verstuur"
    ↓
═══════════════════════════════════════════════
EMAIL #1: CHARACTER DESCRIPTION
═══════════════════════════════════════════════
Sent immediately to user's email

Subject: "🎭 Je Masked Employee Karakter: [Character Name]"

Content:
- Character Name
- Character Type
- Personality Traits
- Full AI Summary
- Story Prompt 1 (Work Achievement)
- Story Prompt 2 (Hidden Talent)
- Story Prompt 3 (Personal Growth)
- Note: "Je karakterbeeld volgt binnenkort!"

    ↓
═══════════════════════════════════════════════
TRIGGER IMAGE GENERATION (Freepik)
═══════════════════════════════════════════════
Server-side process (automatic or manual trigger)

1. Read image_generation_prompt from PocketBase
2. Call Freepik API:
   - Model: flux-kontext-pro
   - Size: 1280x720 (16:9)
   - Prompt: Full detailed prompt
3. Freepik processes (20-40 seconds)
4. Receive base64 image
5. Upload to PocketBase as file
6. Update record with image URL

    ↓
═══════════════════════════════════════════════
EMAIL #2: CHARACTER IMAGE
═══════════════════════════════════════════════
Sent after image generation completes

Subject: "🎨 Je Masked Employee Karakter Beeld!"

Content:
- "Hier is je karakter!"
- [Embedded character image - 16:9 format]
- Character Name
- Brief reminder text
- Link to event details (optional)

    ↓
═══════════════════════════════════════════════
COMPLETION PAGE
═══════════════════════════════════════════════
"✅ Bedankt! Check je email voor je karakter!"
```

---

## 📧 Email Templates

### **Email #1: Character Description**

```
Subject: 🎭 Je Masked Employee Karakter: De Slimme Vos

Hallo [Player Name],

Bedankt voor het invullen van de vragenlijst! Op basis van je antwoorden 
hebben we dit unieke karakter voor je gecreëerd:

═══════════════════════════════════════════════
🎭 DE SLIMME VOS
═══════════════════════════════════════════════

Type: Dier (Animal)

Persoonlijkheid:
Creative: 8
Adventurous: 7
Analytical: 6
Social: 5
Mysterious: 9

Karakter Beschrijving:
De Vos genaamd Luna is een mysterieuze figuur die zich graag in de 
schaduw beweegt. Met haar sluwe blik en elegante bewegingen weet ze 
altijd de aandacht te trekken zonder te veel prijs te geven...

[Full AI Summary]

═══════════════════════════════════════════════
🎬 JOUW VERHAAL PROMPTS
═══════════════════════════════════════════════

Video 1 - Werk Prestatie:
Als De Slimme Vos, vertel over een moment waarop je...

Video 2 - Verborgen Talent:
Als De Slimme Vos, deel iets verrassends over jezelf...

Video 3 - Persoonlijke Groei:
Als De Slimme Vos, deel een moment dat je veranderde...

═══════════════════════════════════════════════

Je karakterbeeld volgt binnenkort in een aparte email!

Met vriendelijke groet,
The Masked Employee Team
```

### **Email #2: Character Image**

```
Subject: 🎨 Je Masked Employee Karakter Beeld!

Hallo [Player Name],

Hier is je karakter!

[EMBEDDED IMAGE - 1280x720, 16:9 format]

🎭 DE SLIMME VOS

Dit is hoe jouw karakter eruit ziet voor The Masked Employee show!

Bewaar deze email goed - je hebt hem nodig voor het evenement.

Tot snel!
The Masked Employee Team
```

---

## 🗄️ PocketBase Data Flow

### **After Regeneration (Not Saved):**
- Character data exists in JavaScript memory
- NOT written to PocketBase yet
- User can regenerate unlimited times

### **After Acceptance (Saved):**
```json
{
  "id": "abc123",
  "gamename": "The Masked Employee 2025",
  "nameplayer": "John Doe",
  "email": "john@example.com",  // ← Added after email input
  "chapter01": {...},
  "chapter02": {...},
  // ... all chapters
  "chapter09": {...},
  
  // Character Data (from OpenAI)
  "character_name": "De Slimme Vos",
  "character_type": "animals",
  "personality_traits": "Creative: 8\nAdventurous: 7\n...",
  "ai_summary": "De Vos genaamd Luna is een...",
  "story_prompt1": "Als De Slimme Vos, vertel over...",
  "story_prompt2": "Als De Slimme Vos, deel iets verrassends...",
  "story_prompt3": "Als De Slimme Vos, deel een moment...",
  "image_generation_prompt": "⚠️ CRITICAL: 16:9 WIDESCREEN...",
  "character_generation_success": true,
  
  // Email Status
  "email_sent_description": true,  // Email #1 sent
  "email_sent_image": false,       // Email #2 pending
  
  // Image (added later)
  "character_image": ""  // Empty until Freepik generates
}
```

---

## 🔧 Implementation Files

### **Frontend:**
- `questions.html` - Character preview page with buttons
- `script.js` - `regenerateCharacter()`, `acceptCharacterAndContinue()`
- `styles.css` - Button styling

### **Backend:**
- `generate-character.php` - Character generation with regenerate flag
- `freepik-api.php` - Image generation (Flux Kontext Pro)
- `send-email-description.php` - Send Email #1 (NEW - needs creation)
- `send-email-image.php` - Send Email #2 (NEW - needs creation)

### **Configuration:**
- `api-keys.php` - OpenAI, Freepik, Email SMTP credentials

---

## 💰 Cost Breakdown

### **Per User (No Regeneration):**
- OpenAI (4 calls): ~$0.05
- Freepik (1 image): Varies by plan
- Email (2 emails): Free (SMTP) or minimal cost

### **Per User (With 3 Regenerations):**
- OpenAI (4 × 4 = 16 calls): ~$0.20
- Freepik (1 image): Varies by plan
- Email (2 emails): Free (SMTP) or minimal cost

### **For 150 Users (average 1 regeneration each):**
- OpenAI: ~$15
- Freepik: Check plan limits
- Total: ~$15 + Freepik costs

---

## ⏱️ Timing

| Step | Duration | Notes |
|------|----------|-------|
| Character Generation | 15-30 sec | 4 OpenAI API calls |
| Regeneration | 15-30 sec | Same as generation |
| Email #1 Send | 1-2 sec | Immediate |
| Image Generation | 20-40 sec | Freepik processing |
| Email #2 Send | 1-2 sec | After image ready |
| **Total (no regen)** | **~1 minute** | From accept to Email #2 |

---

## 🚀 Next Steps

### **To Implement:**

1. ✅ Regenerate functionality (DONE)
2. ✅ Variation in regeneration (DONE - temperature + prompt)
3. ⏳ Email sending scripts:
   - Create `send-email-description.php`
   - Create `send-email-image.php`
4. ⏳ Email templates (HTML)
5. ⏳ Freepik trigger automation
6. ⏳ Update PocketBase schema (add email fields)

### **Testing Checklist:**

- [ ] Regenerate creates different character
- [ ] Accept saves to PocketBase
- [ ] Email modal appears
- [ ] Email #1 sends with character description
- [ ] Image generates via Freepik
- [ ] Email #2 sends with image
- [ ] 16:9 image format correct
- [ ] No "mask" mentions in text

---

**Status:** ✅ Regenerate flow implemented
**Next:** Create email sending scripts
**Time:** October 24, 2025, 10:44 AM
