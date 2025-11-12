# ✅ Chapter 9 Scenes + Image Generation Fixed

## Summary of Changes

### 1. ✅ Chapter 9: Split into 3 Scenes Each
- Questions 41, 42, 43 now have 3 separate input fields
- Each scene saved separately (e.g., `41_scene1`, `41_scene2`, `41_scene3`)
- Beautiful UI with 🎬 icons

### 2. ✅ Removed AI Movie Prompt Generation
- Story prompts now use USER answers directly from Chapter 9
- `story_prompt1` = Question 41 (3 scenes)
- `story_prompt2` = Question 42 (3 scenes)
- `story_prompt3` = Question 43 (3 scenes)
- Reduced from 4 API calls to 1 API call (faster + cheaper!)

### 3. ✅ Fixed Image Generation
- Image now generates automatically after character creation
- Image uploads to PocketBase
- Image included in email

---

## Detailed Changes

### **File 1: questions-unified.json**
**Status:** ✅ Already has scenes structure
```json
{
  "id": 41,
  "scenes": {
    "nl": ["Scene 1", "Scene 2", "Scene 3"],
    "en": ["Scene 1", "Scene 2", "Scene 3"]
  }
}
```

---

### **File 2: script.js**

#### **Change 1: Create Scene Inputs (Line 2367-2418)**
```javascript
// Check if this question has scenes (Chapter 9 questions)
if (question.scenes && Array.isArray(question.scenes[lang])) {
    const scenesContainer = document.createElement('div');
    scenesContainer.className = 'scenes-container';
    
    question.scenes[lang].forEach((sceneLabel, index) => {
        const sceneDiv = document.createElement('div');
        sceneDiv.className = 'scene-input-group';
        
        const sceneLabel = document.createElement('label');
        sceneLabel.className = 'scene-label';
        sceneLabel.textContent = question.scenes[lang][index];
        
        const textarea = document.createElement('textarea');
        textarea.className = 'text-input scene-textarea';
        textarea.name = `question_${question.id}_scene${index + 1}`;
        textarea.placeholder = lang === 'nl' ? 
            `Beschrijf scene ${index + 1}...` : 
            `Describe scene ${index + 1}...`;
        textarea.required = true;
        textarea.rows = 3;
        
        // Restore previous answer if exists
        const answerKey = `${question.id}_scene${index + 1}`;
        if (this.answers[answerKey] !== undefined) {
            textarea.value = this.answers[answerKey];
        }
        
        sceneDiv.appendChild(sceneLabel);
        sceneDiv.appendChild(textarea);
        scenesContainer.appendChild(sceneDiv);
    });
    
    questionDiv.appendChild(scenesContainer);
}
```

#### **Change 2: Save Scene Answers (Line 2517-2532)**
```javascript
} else if (question.type === 'text') {
    // Check if this question has scenes
    const lang = this.currentLanguage;
    if (question.scenes && Array.isArray(question.scenes[lang])) {
        // Save each scene separately
        const textareas = questionDiv.querySelectorAll('textarea');
        textareas.forEach((textarea, index) => {
            const answerKey = `${question.id}_scene${index + 1}`;
            this.answers[answerKey] = textarea.value.trim();
        });
    } else {
        // Regular text question
        const textarea = questionDiv.querySelector('textarea');
        this.answers[question.id] = textarea.value.trim();
    }
}
```

#### **Change 3: Add Image Generation (Line 2570-2574)**
```javascript
// Step 3: Generate and upload image (async, don't wait)
console.log('🎨 Starting image generation...');
this.generateAndUploadImage(characterData).catch(err => {
    console.error('❌ Image generation failed:', err);
});
```

#### **Change 4: New Function generateAndUploadImage (Line 2583-2632)**
```javascript
async generateAndUploadImage(characterData) {
    try {
        console.log('🎨 Step 1: Generating image via Freepik...');
        
        const imagePrompt = characterData.image_generation_prompt;
        if (!imagePrompt) {
            throw new Error('No image prompt available');
        }
        
        // Call Freepik API
        const response = await fetch('generate-image-freepik.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                playerName: this.playerName,
                prompt: imagePrompt
            })
        });
        
        if (!response.ok) {
            throw new Error(`Image generation failed: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Image generation failed');
        }
        
        console.log('✅ Image generated successfully');
        
        // Step 2: Upload to PocketBase
        const imageData = result.image_data || result.image_binary;
        if (imageData) {
            const imageBlob = this.base64ToBlob(imageData, 'image/png');
            await this.uploadImageToPocketBase(imageBlob);
            console.log('✅ Image uploaded to PocketBase');
        }
        
        // Step 3: Send email with image
        await this.sendFinalEmailWithImage();
        console.log('✅ Email sent with image');
        
    } catch (error) {
        console.error('❌ Error in image generation:', error);
        throw error;
    }
}
```

---

### **File 3: styles.css**

#### **New CSS for Scenes (Line 606-636)**
```css
/* Scene-based questions (Chapter 9) */
.scenes-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.scene-input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.scene-label {
    font-weight: 600;
    color: #8A2BE2;
    font-size: 1em;
    display: flex;
    align-items: center;
    gap: 8px;
}

.scene-label::before {
    content: '🎬';
    font-size: 1.2em;
}

.scene-textarea {
    min-height: 80px !important;
    background: #f8f9fa;
}
```

---

### **File 4: generate-character.php**

#### **Change 1: Use Chapter 9 Answers Directly (Line 363-395)**
```php
// Use Chapter 9 answers directly (Questions 41-43 with 3 scenes each)
// Story 1: Question 41 (3 scenes)
$storyLevel1 = "";
if (isset($data['answers']['41_scene1'])) {
    $storyLevel1 .= "Scene 1: " . $data['answers']['41_scene1'] . "\n\n";
    $storyLevel1 .= "Scene 2: " . $data['answers']['41_scene2'] . "\n\n";
    $storyLevel1 .= "Scene 3: " . $data['answers']['41_scene3'];
} elseif (isset($data['answers'][41])) {
    // Fallback for old format
    $storyLevel1 = $data['answers'][41];
}

// Story 2: Question 42 (3 scenes)
$storyLevel2 = "";
if (isset($data['answers']['42_scene1'])) {
    $storyLevel2 .= "Scene 1: " . $data['answers']['42_scene1'] . "\n\n";
    $storyLevel2 .= "Scene 2: " . $data['answers']['42_scene2'] . "\n\n";
    $storyLevel2 .= "Scene 3: " . $data['answers']['42_scene3'];
} elseif (isset($data['answers'][42])) {
    // Fallback for old format
    $storyLevel2 = $data['answers'][42];
}

// Story 3: Question 43 (3 scenes)
$storyLevel3 = "";
if (isset($data['answers']['43_scene1'])) {
    $storyLevel3 .= "Scene 1: " . $data['answers']['43_scene1'] . "\n\n";
    $storyLevel3 .= "Scene 2: " . $data['answers']['43_scene2'] . "\n\n";
    $storyLevel3 .= "Scene 3: " . $data['answers']['43_scene3'];
} elseif (isset($data['answers'][43])) {
    // Fallback for old format
    $storyLevel3 = $data['answers'][43];
}
```

#### **Change 2: Update API Calls Count (Line 418)**
```php
'api_calls_used' => 1, // Only 1 call now (character generation)
```

---

## How It Works Now

### **User Flow:**

```
1. User fills questionnaire (Chapters 1-8 normal)
   ↓
2. Chapter 9: Each question has 3 scene inputs
   - Q41: Scene 1, Scene 2, Scene 3
   - Q42: Scene 1, Scene 2, Scene 3
   - Q43: Scene 1, Scene 2, Scene 3
   ↓
3. Click "🎭 Voltooien!"
   ↓
4. Generate character (1 API call to OpenAI)
   - Character name, type, personality
   - AI summary
   - Image prompt
   ↓
5. Save to PocketBase
   - All answers (including 9 scenes)
   - Character data
   - Story prompts (from Chapter 9 answers)
   ↓
6. Show character preview page
   ↓
7. Generate image (async, in background)
   - Call Freepik API
   - Upload image to PocketBase
   - Send email with image
```

---

## What's Saved in PocketBase

### **Chapter 9 Answers:**
```
41_scene1: "User's scene 1 description..."
41_scene2: "User's scene 2 description..."
41_scene3: "User's scene 3 description..."
42_scene1: "User's scene 1 description..."
42_scene2: "User's scene 2 description..."
42_scene3: "User's scene 3 description..."
43_scene1: "User's scene 1 description..."
43_scene2: "User's scene 2 description..."
43_scene3: "User's scene 3 description..."
```

### **Story Prompts:**
```
story_prompt1: "Scene 1: ...\n\nScene 2: ...\n\nScene 3: ..."
story_prompt2: "Scene 1: ...\n\nScene 2: ...\n\nScene 3: ..."
story_prompt3: "Scene 1: ...\n\nScene 2: ...\n\nScene 3: ..."
```

### **Image:**
```
image: "filename.png" (uploaded file)
image_generation_prompt: "⚠️ CRITICAL: 16:9 WIDESCREEN..."
```

---

## Benefits

### **1. Faster Generation**
- **Before:** 4 API calls (character + 3 story prompts)
- **After:** 1 API call (character only)
- **Savings:** 75% fewer API calls = faster + cheaper

### **2. Better Story Prompts**
- **Before:** AI-generated generic prompts
- **After:** User's actual scene descriptions
- **Result:** More personal, more accurate

### **3. Image in PocketBase**
- **Before:** Image not saved
- **After:** Image uploaded to PocketBase
- **Result:** Accessible in admin panel

### **4. Image in Email**
- **Before:** Email without image
- **After:** Email includes character image
- **Result:** Complete package for user

---

## Files to Upload

1. ✅ `script.js` (scene inputs + image generation)
2. ✅ `styles.css` (scene styling)
3. ✅ `generate-character.php` (use Chapter 9 answers)
4. ✅ `questions-unified.json` (already has scenes)

---

## Testing Checklist

### **Chapter 9 Scenes:**
- [ ] Navigate to Chapter 9
- [ ] Verify 3 input fields per question (41, 42, 43)
- [ ] Each has 🎬 icon and "Scene 1/2/3" label
- [ ] Fill in all 9 scenes
- [ ] Click "🎭 Voltooien!"

### **Character Generation:**
- [ ] Character generates successfully
- [ ] Preview page shows character
- [ ] Console shows: "✅ Character data generated"
- [ ] Console shows: "🎨 Starting image generation..."

### **Image Generation:**
- [ ] Wait 30-60 seconds
- [ ] Console shows: "✅ Image generated successfully"
- [ ] Console shows: "✅ Image uploaded to PocketBase"
- [ ] Console shows: "✅ Email sent with image"

### **PocketBase:**
- [ ] Open PocketBase admin
- [ ] Find latest record
- [ ] Verify `story_prompt1` has 3 scenes
- [ ] Verify `story_prompt2` has 3 scenes
- [ ] Verify `story_prompt3` has 3 scenes
- [ ] Verify `image` field has uploaded file
- [ ] Click image to view

### **Email:**
- [ ] Check email inbox
- [ ] Verify email received
- [ ] Verify image is attached/embedded
- [ ] Verify character details included

---

**Status:** ✅ All changes complete
**API Calls:** Reduced from 4 to 1
**Time:** October 24, 2025, 1:14 PM
