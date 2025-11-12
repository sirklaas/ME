# 🔄 Complete Flow After Questions Are Filled In

## Overview
This document explains the complete flow from question completion to final data storage.

---

## 📊 Step-by-Step Flow

### **Step 1: User Completes All Questions**
```
User answers 43 questions across 9 chapters
├── Chapter 1: Questions 1-5 (Introductie)
├── Chapter 2: Questions 6-10 (Masked Identity)
├── Chapter 3: Questions 11-16 (Persoonlijke Eigenschappen)
├── Chapter 4: Questions 17-21 (Verborgen Talenten)
├── Chapter 5: Questions 22-26 (Jeugd & Verleden)
├── Chapter 6: Questions 27-31 (Fantasie & Dromen)
├── Chapter 7: Questions 32-36 (Eigenaardigheden)
├── Chapter 8: Questions 37-40 (Onverwachte Voorkeuren)
└── Chapter 9: Questions 41-43 (Film Maken)
```

**Data stored in:** `this.answers` object in JavaScript

---

### **Step 2: User Clicks "Voltooien!" Button**
```
User clicks → submitAllAnswers() triggered
```

**What happens:**
1. Shows loading screen
2. Prepares submission data
3. Starts character generation

---

### **Step 3: Character Generation (OpenAI API)**

**File:** `generate-character.php`

```
submitAllAnswers()
    ↓
generateCharacterData(submissionData)
    ↓
HTTP POST to generate-character.php
    ↓
PHP receives: {
    playerName: "John Doe",
    answers: { 1: "answer1", 2: "answer2", ... },
    chapters: [...]
}
```

#### **3.1: Personality Analysis**
```php
analyzePersonality($answers)
    ↓
Returns: {
    creative: 8,
    adventurous: 7,
    analytical: 6,
    social: 5,
    mysterious: 9
}
    ↓
determineCharacterType($traits)
    ↓
Returns: "animals" (or fruits_vegetables, fantasy_heroes, etc.)
```

#### **3.2: OpenAI API Calls (4 calls total)**

**Call 1: Character Description**
```
Prompt: "Create character based on personality..."
Model: GPT-4
Max Tokens: 600
    ↓
Returns: "De Vos genaamd Luna is een mysterieuze..."
```

**Call 2: Story Prompt Level 1**
```
Prompt: "Create work achievement prompt..."
Model: GPT-4
Max Tokens: 200
    ↓
Returns: "Als De Slimme Vos, vertel over een moment..."
```

**Call 3: Story Prompt Level 2**
```
Prompt: "Create hidden talent prompt..."
Model: GPT-4
Max Tokens: 200
    ↓
Returns: "Als De Slimme Vos, deel iets verrassends..."
```

**Call 4: Story Prompt Level 3**
```
Prompt: "Create personal growth prompt..."
Model: GPT-4
Max Tokens: 250
    ↓
Returns: "Als De Slimme Vos, deel een moment dat je..."
```

#### **3.3: Image Prompt Generation**
```php
generateImagePrompt($characterName, $aiSummary, $characterType)
    ↓
Returns: "⚠️ CRITICAL: 16:9 WIDESCREEN ASPECT RATIO...
         STUDIO QUALITY PHOTO, hyper-realistic:
         anthropomorphic animal character named 'De Slimme Vos'..."
```

#### **3.4: Return Character Data**
```json
{
  "success": true,
  "character_name": "De Slimme Vos",
  "character_type": "animals",
  "personality_traits": "Creative: 8\nAdventurous: 7\nAnalytical: 6\nSocial: 5\nMysterious: 9",
  "ai_summary": "De Vos genaamd Luna is een mysterieuze figuur...",
  "story_prompt_level1": "Als De Slimme Vos, vertel over...",
  "story_prompt_level2": "Als De Slimme Vos, deel iets verrassends...",
  "story_prompt_level3": "Als De Slimme Vos, deel een moment dat je...",
  "image_generation_prompt": "⚠️ CRITICAL: 16:9 WIDESCREEN...",
  "api_calls_used": 4,
  "timestamp": "2025-10-24 10:30:00"
}
```

**Cost:** ~$0.05 per submission (OpenAI GPT-4)

---

### **Step 4: Save to PocketBase**

**File:** `script.js` - `saveToPocketBase()`

```javascript
saveToPocketBase(submissionData, characterData)
    ↓
Organizes answers by chapter
    ↓
Creates PocketBase record
    ↓
HTTP POST to PocketBase API
```

#### **PocketBase Record Structure:**

```json
{
  // Basic Info
  "gamename": "The Masked Employee 2025",
  "nameplayer": "John Doe",
  "submission_date": "2025-10-24T10:30:00.000Z",
  "total_questions": 43,
  "status": "completed",
  
  // Chapter Answers (JSON fields)
  "chapter01": { "1": "answer1", "2": "answer2", ... },
  "chapter02": { "6": "answer6", "7": "answer7", ... },
  "chapter03": { "11": "answer11", ... },
  "chapter04": { "17": "answer17", ... },
  "chapter05": { "22": "answer22", ... },
  "chapter06": { "27": "answer27", ... },
  "chapter07": { "32": "answer32", ... },
  "chapter08": { "37": "answer37", ... },
  "chapter09": { "41": "answer41", "42": "answer42", "43": "answer43" },
  
  // Character Data (from OpenAI)
  "character_name": "De Slimme Vos",
  "character_type": "animals",
  "personality_traits": "Creative: 8\nAdventurous: 7\n...",
  "ai_summary": "De Vos genaamd Luna is een mysterieuze...",
  "story_prompt1": "Als De Slimme Vos, vertel over...",
  "story_prompt2": "Als De Slimme Vos, deel iets verrassends...",
  "story_prompt3": "Als De Slimme Vos, deel een moment...",
  "image_generation_prompt": "⚠️ CRITICAL: 16:9 WIDESCREEN...",
  "character_generation_success": true,
  
  // Image (added later by Freepik)
  "character_image": "character_xxx_timestamp.jpg"
}
```

---

### **Step 5: Image Generation (Freepik API)**

**Note:** This happens separately (likely server-side trigger or manual process)

```
PocketBase record created
    ↓
Server-side script detects new record
    ↓
Reads ai_summary or image_generation_prompt
    ↓
Calls Freepik API
```

#### **Freepik API Call:**
```php
// File: freepik-api.php
$freepik->generateCharacterImage($prompt)
    ↓
HTTP POST to Freepik API
{
  "prompt": "Professional character portrait...",
  "num_images": 1,
  "model": "flux-kontext-pro",
  "image": {
    "size": "1280x720"  // 16:9 aspect ratio
  }
}
    ↓
Freepik processes (20-40 seconds)
    ↓
Returns: {
  "data": [{
    "base64": "iVBORw0KGgoAAAANS..."
  }]
}
    ↓
Convert base64 to image file
    ↓
Upload to PocketBase as file
    ↓
Update record with image URL
```

**Result:**
```
Image URL: https://pinkmilk.pockethost.io/api/files/gcw86ul2vh8hlhb/bk7gt2rdfaiweez/character_bk7gt2rdfaiweez_1761293250435_mH3V82Q4Aq.jpg
```

**Cost:** Depends on Freepik plan (credits or subscription)

---

### **Step 6: Show Completion Page**

```javascript
showCompletionPage(characterData)
    ↓
showCharacterPreviewPage(characterData)
    ↓
displayCharacterData(characterData)
```

**Displays:**
- ✅ Character name
- ✅ Character type
- ✅ Personality traits
- ✅ AI summary
- ✅ Story prompts (3 levels)
- ✅ (Image shown if available)

---

## 🗄️ PocketBase Fields Reference

### **Collection:** `submissions`

| Field Name | Type | Description | Source |
|------------|------|-------------|--------|
| `gamename` | Text | Game name | Config |
| `nameplayer` | Text | Player name | User input (Q1) |
| `submission_date` | DateTime | Submission timestamp | Auto-generated |
| `total_questions` | Number | Total questions answered | Calculated (43) |
| `status` | Text | Submission status | "completed" |
| **Chapter Answers** | | | |
| `chapter01` | JSON | Questions 1-5 | User answers |
| `chapter02` | JSON | Questions 6-10 | User answers |
| `chapter03` | JSON | Questions 11-16 | User answers |
| `chapter04` | JSON | Questions 17-21 | User answers |
| `chapter05` | JSON | Questions 22-26 | User answers |
| `chapter06` | JSON | Questions 27-31 | User answers |
| `chapter07` | JSON | Questions 32-36 | User answers |
| `chapter08` | JSON | Questions 37-40 | User answers |
| `chapter09` | JSON | Questions 41-43 | User answers |
| **Character Data** | | | |
| `character_name` | Text | Generated character name | OpenAI (generate-character.php) |
| `character_type` | Text | Character category | OpenAI (animals/fruits/fantasy/etc) |
| `personality_traits` | Text | Personality analysis | OpenAI (formatted string) |
| `ai_summary` | Text | Full character description | OpenAI (Call 1) |
| `story_prompt1` | Text | Work achievement prompt | OpenAI (Call 2) |
| `story_prompt2` | Text | Hidden talent prompt | OpenAI (Call 3) |
| `story_prompt3` | Text | Personal growth prompt | OpenAI (Call 4) |
| `image_generation_prompt` | Text | Detailed image prompt | Generated by PHP |
| `character_generation_success` | Boolean | Generation success flag | OpenAI response |
| **Image** | | | |
| `character_image` | File | Character portrait | Freepik API (1280x720, 16:9) |

---

## 🔍 Field Usage Examples

### **chapter01 (JSON):**
```json
{
  "1": "John Doe",
  "2": "john@example.com",
  "3": "Developer",
  "4": "Technology",
  "5": "5 years"
}
```

### **personality_traits (Text):**
```
Creative: 8
Adventurous: 7
Analytical: 6
Social: 5
Mysterious: 9
```

### **ai_summary (Text):**
```
De Vos genaamd Luna is een mysterieuze figuur die zich graag in de schaduw beweegt. 
Met haar sluwe blik en elegante bewegingen weet ze altijd de aandacht te trekken 
zonder te veel prijs te geven. Ze draagt een donkerblauwe cape met sterren...
```

### **image_generation_prompt (Text):**
```
⚠️ CRITICAL: 16:9 WIDESCREEN ASPECT RATIO (1920x1080 or 1280x720) - MANDATORY

STUDIO QUALITY PHOTO, hyper-realistic: anthropomorphic animal character 
(actual animal with clothes) named 'De Slimme Vos'. Wearing donkerblauwe cape 
met sterren. 

=== TECHNICAL SPECS ===
ASPECT RATIO: 16:9 widescreen (1920x1080 or 1280x720) - MANDATORY
STYLE: Hyper-realistic, professional studio photography
LIGHTING: Professional studio lighting, soft shadows, dramatic highlights
...
```

---

## 💰 Cost Breakdown

### **Per Submission:**
- OpenAI GPT-4: ~$0.05 (4 API calls)
- Freepik Image: Varies by plan (credits or subscription)
- PocketBase: Free (included in hosting)

### **For 150 Users:**
- OpenAI: ~$7.50
- Freepik: Check your plan limits
- Total: ~$7.50 + Freepik costs

---

## ⏱️ Timing

| Step | Duration | Notes |
|------|----------|-------|
| Questions | 10-15 min | User input time |
| Character Generation | 15-30 sec | 4 OpenAI API calls |
| Save to PocketBase | 1-2 sec | Database write |
| Image Generation | 20-40 sec | Freepik processing |
| **Total** | **11-16 min** | End-to-end |

---

## 🔧 Files Involved

### **Frontend:**
- `questions.html` - Question interface
- `script.js` - Main application logic
- `styles.css` - Styling

### **Backend:**
- `generate-character.php` - Character generation (OpenAI)
- `freepik-api.php` - Image generation (Freepik)
- `api-keys.php` - API credentials

### **Configuration:**
- `gameshow-config-v2.json` - Game settings
- `chapter1-9.json` - Question definitions
- `character-options-80.json` - Character type options

### **Database:**
- PocketBase: `submissions` collection

---

## 🐛 Troubleshooting

### **Character data empty in PocketBase:**
- ✅ Fixed: `personality_traits` now converted to string
- ✅ Fixed: All fields properly mapped

### **Image not generating:**
- Check Freepik API key
- Verify model: `flux-kontext-pro`
- Check aspect ratio: `1280x720`

### **Chapter 9 not appearing:**
- Upload `gameshow-config-v2.json`
- Upload `chapter9-film-maken.json`
- Clear browser cache

---

**Status:** ✅ Complete flow documented
**Last Updated:** October 24, 2025, 10:31 AM
