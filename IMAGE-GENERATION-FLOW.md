# Image Generation Flow - Complete Route Map

**Date:** 2025-10-13  
**Purpose:** Document the complete image generation and storage route

---

## 📊 **Complete Data Flow**

### **Phase 1: Character Generation** ✅

```
User completes questionnaire
    ↓
JavaScript calls: generate-character-real.php?step=generate_description
    ↓
PHP calls OpenAI GPT-4 API
    Input: All 40 questionnaire answers
    Output: character_description + world_description
    ↓
JavaScript receives descriptions
    ↓
JavaScript extracts character_name from description
    ↓
JavaScript creates ai_summary (HTML combining both)
```

---

### **Phase 2: User Accepts & Saves to PocketBase** ✅

```
User clicks "✅ Ja, dit ben ik!"
    ↓
JavaScript saves to PocketBase:
    ✅ character_description   - Raw AI text about character
    ✅ world_description       - Raw AI text about world
    ✅ environment_description - Copy of world_description
    ✅ character_name          - Extracted name (e.g., "The Majestic Lion")
    ✅ ai_summary             - HTML combining character + world
    ✅ props                  - Empty string (for future use)
    ✅ status                 - "descriptions_approved"
    ✅ updated_at             - Timestamp
```

**PocketBase Record After Phase 2:**
```json
{
  "id": "abc123",
  "player_name": "test4",
  "character_description": "Meet 'The Majestic Lion'...",
  "world_description": "The Majestic Lion navigates...",
  "environment_description": "The Majestic Lion navigates...",
  "character_name": "The Majestic Lion",
  "ai_summary": "<div class='character-section'>...</div>",
  "props": "",
  "status": "descriptions_approved",
  "email": null,
  "image": null,
  "image_prompt": null
}
```

---

### **Phase 3: Email Modal** ✅

```
Email modal appears
    ↓
User enters email
    ↓
JavaScript stores email in this.userEmail
    ↓
Calls: sendDescriptionEmail()
    → Sends Email #1 with descriptions only
    ✅ Email sent to user
    ✅ Email sent to admin (klaas@pinkmilk.eu)
```

---

### **Phase 4: Generate Image Prompt** ✅

```
JavaScript calls: generate-character-real.php?step=generate_image
    Body: {
        character_description: "Meet 'The Majestic Lion'...",
        world_description: "The Majestic Lion navigates...",
        language: "nl"
    }
    ↓
PHP calls OpenAI GPT-4 API (generateImagePrompt function)
    Input: character_description + world_description
    Prompt: "Create a detailed image prompt for..."
    Output: image_prompt (text description for image generation)
    Example: "Professional portrait of a mysterious masked figure in deep purple robes..."
    ↓
PHP logs: "Image prompt generated: Professional portrait..."
```

---

### **Phase 5: Call Freepik API** ✅

```
PHP requires freepik-api.php
    ↓
PHP creates FreepikAPI instance
    ↓
PHP calls: $freepik->generateCharacterImage($imagePrompt)
    ↓
freepik-api.php sends HTTP POST to Freepik:
    URL: https://api.freepik.com/v1/ai/text-to-image
    Headers: x-freepik-api-key: [YOUR_KEY]
    Body: {
        "prompt": "Professional portrait of...",
        "num_images": 1,
        "image": { "size": "1024x1024" },
        "styling": {
            "style": "realistic",
            "color": "vibrant",
            "lighting": "dramatic"
        }
    }
    ↓
Freepik API Response (takes 20-40 seconds):
    {
        "data": [{
            "base64": "iVBORw0KGgoAAAANS...",  ← Base64 encoded image
            "url": "https://..."  (optional)
        }]
    }
    ↓
freepik-api.php returns to generate-character-real.php:
    {
        "success": true,
        "image_data": "iVBORw0KGgoAAAANS...",  ← Base64
        "image_binary": [binary data]
    }
    ↓
generate-character-real.php logs: "Image generated successfully"
    ↓
generate-character-real.php returns JSON to JavaScript:
    {
        "success": true,
        "image_data": "iVBORw0KGgoAAAANS...",
        "image_prompt": "Professional portrait of..."
    }
```

---

### **Phase 6: Upload Image to PocketBase** ✅

```
JavaScript receives image_data (base64)
    ↓
JavaScript stores: this.imagePrompt = result.image_prompt
    ↓
JavaScript calls: base64ToBlob(image_data)
    → Converts base64 string to Blob object
    → Blob size: ~200KB - 2MB (depending on image)
    ↓
JavaScript calls: uploadImageToPocketBase(imageBlob)
    ↓
Step 1: Save image_prompt first
    await pb.collection('MEQuestions').update(recordId, {
        image_prompt: this.imagePrompt,
        email: this.userEmail
    })
    ↓
Step 2: Create FormData with image file
    formData.append('image', imageBlob, 'character_abc123_1760350000000.jpg')
    formData.append('status', 'completed_with_image')
    formData.append('completed_at', '2025-10-13T...')
    ↓
Step 3: Upload to PocketBase
    await pb.collection('MEQuestions').update(recordId, formData)
    ↓
PocketBase receives file upload:
    → Stores file in PocketBase storage
    → Generates URL: https://pinkmilk.pockethost.io/api/files/MEQuestions/[record_id]/[filename].jpg
    ↓
PocketBase returns updated record:
    {
        "image": "character_abc123_1760350000000.jpg",  ← Filename
        "image_prompt": "Professional portrait of...",
        "email": "user@example.com",
        "status": "completed_with_image"
    }
    ↓
JavaScript gets image URL:
    this.imageUrl = pb.files.getUrl(record, record.image)
    → Full URL: https://pinkmilk.pockethost.io/api/files/MEQuestions/abc123/character_abc123_1760350000000.jpg
    ↓
JavaScript logs: "✅ Image uploaded to PocketBase: https://..."
```

**PocketBase Record After Phase 6:**
```json
{
  "id": "abc123",
  "image": "character_abc123_1760350000000.jpg",  ← Filename stored
  "image_prompt": "Professional portrait of a mysterious masked figure...",
  "email": "user@example.com",
  "status": "completed_with_image",
  "completed_at": "2025-10-13T10:30:00.000Z"
}
```

**Image Location:**
- **NOT on your server** (no generated-images/ folder)
- **Stored in PocketBase** internal storage
- **Access via URL:** https://pinkmilk.pockethost.io/api/files/MEQuestions/[record_id]/[filename].jpg

---

### **Phase 7: Send Final Email** ✅

```
JavaScript calls: sendFinalEmailWithImage()
    ↓
Sends to send-final-email.php:
    Body: {
        email: "user@example.com",
        characterName: "The Majestic Lion",
        characterDescription: "Meet 'The Majestic Lion'...",
        worldDescription: "The Majestic Lion navigates...",
        imageUrl: "https://pinkmilk.pockethost.io/api/files/..."
    }
    ↓
PHP sends Email #2:
    To: user@example.com
    CC: klaas@pinkmilk.eu
    Subject: "🎨 The Majestic Lion - Your Character is Complete!"
    Body: HTML with embedded image
    <img src="https://pinkmilk.pockethost.io/api/files/...">
    ↓
JavaScript logs: "✅ Final email with image sent!"
```

---

## 🔍 **Debugging Checklist**

### **Check Phase 4: Image Prompt Generation**

**Browser Console:**
```
🎨 Generating character image...
📝 Character: Meet 'The Majestic Lion'...
🌍 World: The Majestic Lion navigates...
```

**If this stops here → Check PHP error log**

### **Check Phase 5: Freepik API Call**

**PHP Error Log:**
```
=== IMAGE GENERATION START ===
Character desc length: 944
World desc length: 1047
Generating image prompt with OpenAI...
Image prompt generated: Professional portrait of...
Loading Freepik API...
Calling Freepik to generate image...
```

**If it stops at "Calling Freepik":**
- ❌ Freepik API key missing/invalid
- ❌ Freepik API credits exhausted
- ❌ Freepik API endpoint changed

**Should Continue:**
```
Freepik response received, success: YES
Has image_data: YES
Has image_binary: YES
✅ Image generated successfully, returning base64 data
```

### **Check Phase 6: PocketBase Upload**

**Browser Console:**
```
✅ Image generation response received
📝 Prompt used: Professional portrait of...
🔍 Has image_data: true
🔍 Has image_binary: true
📤 Converting image to blob...
📝 Saving image_prompt to PocketBase first...
📤 Uploading image file (blob size: 234567 bytes)
📤 Uploading to PocketBase record: abc123
✅ Image uploaded to PocketBase: https://pinkmilk.pockethost.io/api/files/...
```

**If it stops at "Converting to blob":**
- ❌ Base64 data is corrupted
- ❌ Base64 conversion failed
- Check console for error

**If it stops at "Uploading to PocketBase":**
- ❌ PocketBase connection issue
- ❌ File size too large (check PB limits)
- ❌ Record ID invalid
- Check PB admin panel for errors

---

## 🐛 **Common Issues**

### **Issue 1: No image in PocketBase**

**Possible Causes:**
1. Freepik API not called (check PHP log)
2. Freepik returns error (check PHP log for error)
3. Base64 conversion failed (check console)
4. PocketBase upload failed (check console)

**Check:**
```
1. PHP error log: Did Freepik return image data?
2. Browser console: Did JavaScript receive image_data?
3. Browser console: Did blob conversion succeed?
4. Browser console: Did PB upload succeed?
5. PocketBase admin: Check record for image field
```

### **Issue 2: image_prompt not in PocketBase**

**Cause:** Not saved before image upload

**Fixed:** Now saves image_prompt BEFORE uploading image file

**Check:**
```
Browser console: "📝 Saving image_prompt to PocketBase first..."
```

### **Issue 3: environment_description empty**

**Cause:** Was removed in cleanup

**Fixed:** Now saves again (copy of world_description)

**Check:**
```
PocketBase record should have:
- world_description: [text]
- environment_description: [same text]
```

### **Issue 4: props field empty**

**Expected:** props is empty string by default

**Purpose:** Reserved for future use (can store additional JSON data)

---

## ✅ **What Should Be in PocketBase After Complete Flow:**

```json
{
  "id": "abc123",
  "player_name": "test4",
  "email": "user@example.com",
  
  "character_description": "Meet 'The Majestic Lion'...",  ✅
  "world_description": "The Majestic Lion navigates...",   ✅
  "environment_description": "The Majestic Lion navigates...",  ✅
  "character_name": "The Majestic Lion",  ✅
  "ai_summary": "<div>...</div>",  ✅
  "props": "",  ✅ (empty for now)
  
  "image_prompt": "Professional portrait of a mysterious masked figure...",  ✅
  "image": "character_abc123_1760350000000.jpg",  ✅ (filename)
  
  "status": "completed_with_image",  ✅
  "completed_at": "2025-10-13T10:30:00.000Z"  ✅
}
```

**To View Image:**
Click the `image` field in PocketBase admin → Opens image in new tab

**Image URL Format:**
```
https://pinkmilk.pockethost.io/api/files/MEQuestions/[record_id]/[filename].jpg
```

---

## 📝 **Next Steps for Debugging:**

1. **Upload updated script.js**
2. **Run TEST MODE**
3. **Watch browser console** for each phase
4. **Check PHP error log** if it stops
5. **Check PocketBase** for all fields
6. **Share console output** if image doesn't appear

---

**All fields including environment_description and props now saved!** ✅  
**Image upload route fully documented!** ✅  
**Upload script.js and test!** 🚀
