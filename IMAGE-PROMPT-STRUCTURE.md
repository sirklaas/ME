# Image Prompt JSON Structure

**Date:** 2025-10-13  
**Field:** image_prompt (PocketBase)  
**Type:** JSON (stored as string)

---

## 📊 **Current Structure:**

### **Basic Version (v1):**
```json
{
  "base_template": "Professional character portrait for TV gameshow",
  "character_name": "The Majestic Lion",
  "full_prompt": "Professional character portrait for TV gameshow. A mysterious masked figure in deep purple robes with golden accents, wearing an ornate mask with a phoenix rising from flames design. Lion-like confident demeanor, professional photography, centered composition, dramatic lighting, 4K quality.",
  "generated_at": "2025-10-13T19:23:00.000Z",
  "language": "nl"
}
```

---

## 🎯 **Enhanced Structure (Recommended):**

### **Full Version with Reusable Components:**
```json
{
  "version": "2.0",
  "generated_at": "2025-10-13T19:23:00.000Z",
  "language": "nl",
  
  "base_template": {
    "context": "Professional character portrait for TV gameshow",
    "format": "portrait",
    "purpose": "gameshow_character"
  },
  
  "character": {
    "name": "The Majestic Lion",
    "appearance": "mysterious masked figure in deep purple robes with golden accents",
    "mask_design": "ornate mask with a phoenix rising from flames design",
    "demeanor": "Lion-like confident demeanor",
    "key_elements": ["purple robes", "golden accents", "phoenix mask", "lion demeanor"]
  },
  
  "style": {
    "type": "realistic",
    "quality": "4K quality",
    "photography_style": "professional photography",
    "composition": "centered composition",
    "lighting": "dramatic lighting"
  },
  
  "technical": {
    "size": "1024x1024",
    "ai_model": "flux",
    "inference_steps": 50,
    "guidance_scale": 7.5
  },
  
  "full_prompt": "Professional character portrait for TV gameshow. A mysterious masked figure in deep purple robes with golden accents, wearing an ornate mask with a phoenix rising from flames design. Lion-like confident demeanor, professional photography, centered composition, dramatic lighting, 4K quality."
}
```

---

## 🔄 **Why JSON is Better:**

### **1. Reusable Templates**
```javascript
// All characters use the same base
base_template.context = "Professional character portrait for TV gameshow"
base_template.format = "portrait"

// Change once, applies to all future generations
```

### **2. Easy Variations**
```javascript
// Regenerate with different style
character.data + style.cinematic
character.data + style.artistic  
character.data + style.photorealistic
```

### **3. Analytics**
```sql
-- Find all purple characters
SELECT * FROM MEQuestions 
WHERE image_prompt LIKE '%purple robes%'

-- Or parse JSON properly:
SELECT * FROM MEQuestions 
WHERE json_extract(image_prompt, '$.character.key_elements') LIKE '%purple%'
```

### **4. A/B Testing**
```javascript
// Test different lighting
style.lighting = "dramatic lighting"  vs  "soft natural light"

// Track which generates better images
```

---

## 💾 **How to Update PocketBase Field:**

### **Option 1: Keep as Text Field**
- Store JSON.stringify() output
- Parse with JSON.parse() when reading
- PocketBase sees it as text

### **Option 2: Change to JSON Field** ✅ Recommended
1. Go to PocketBase admin
2. Collections → MEQuestions
3. Edit `image_prompt` field
4. Change type from "Text" to "JSON"
5. PocketBase will automatically parse/validate

---

## 📝 **Usage Examples:**

### **Reading in JavaScript:**
```javascript
// Get from PocketBase
const record = await pb.collection('MEQuestions').getOne(recordId);

// Parse if stored as text
const promptData = JSON.parse(record.image_prompt);

// Access structured data
console.log(promptData.character.name);        // "The Majestic Lion"
console.log(promptData.style.lighting);        // "dramatic lighting"
console.log(promptData.base_template.context); // "Professional character portrait..."
```

### **Regenerating with Different Style:**
```javascript
// Keep character, change style
const newPromptData = {
    ...promptData,
    style: {
        type: "artistic",
        quality: "8K ultra detailed",
        lighting: "soft natural light"
    }
};

// Build new prompt
const newPrompt = `${newPromptData.base_template.context}. ${newPromptData.character.appearance}. ${newPromptData.style.type}, ${newPromptData.style.lighting}, ${newPromptData.style.quality}`;

// Send to Freepik
await generateImage(newPrompt);
```

### **Batch Update All Prompts:**
```javascript
// Change base template for all characters
const records = await pb.collection('MEQuestions').getFullList();

for (const record of records) {
    const promptData = JSON.parse(record.image_prompt);
    
    // Update base template
    promptData.base_template.context = "Ultra realistic character portrait for premium TV show";
    
    // Save back
    await pb.collection('MEQuestions').update(record.id, {
        image_prompt: JSON.stringify(promptData)
    });
}
```

---

## 🎨 **Prompt Template Library:**

### **Create Reusable Style Presets:**
```javascript
const STYLE_PRESETS = {
    dramatic: {
        type: "realistic",
        lighting: "dramatic lighting",
        composition: "centered composition",
        quality: "4K quality"
    },
    
    cinematic: {
        type: "cinematic",
        lighting: "volumetric lighting",
        composition: "rule of thirds",
        quality: "8K ultra detailed"
    },
    
    artistic: {
        type: "digital art",
        lighting: "soft natural light",
        composition: "dynamic angle",
        quality: "highly detailed"
    },
    
    gameshow: {
        type: "realistic",
        lighting: "studio lighting",
        composition: "centered portrait",
        quality: "professional photography, 4K"
    }
};

// Use preset
const promptData = {
    base_template: { context: "Character portrait for TV gameshow" },
    character: { name: "The Majestic Lion", ... },
    style: STYLE_PRESETS.gameshow,
    ...
};
```

---

## 🔧 **Migration Plan:**

### **Step 1: Update Field Type in PocketBase**
```
1. PocketBase Admin → Collections → MEQuestions
2. Fields → image_prompt
3. Change Type: "Text" → "JSON"
4. Save
```

### **Step 2: Migrate Existing Records (if any)**
```javascript
// Convert old text prompts to JSON structure
const records = await pb.collection('MEQuestions').getFullList({
    filter: 'image_prompt != ""'
});

for (const record of records) {
    // If it's already JSON, skip
    try {
        JSON.parse(record.image_prompt);
        continue;
    } catch (e) {
        // It's plain text, convert it
        const promptData = {
            version: "1.0",
            full_prompt: record.image_prompt,
            character_name: record.character_name || "Unknown",
            generated_at: record.completed_at || record.updated,
            language: "nl"
        };
        
        await pb.collection('MEQuestions').update(record.id, {
            image_prompt: JSON.stringify(promptData)
        });
    }
}
```

---

## ✅ **Benefits Summary:**

### **JSON Structure Gives You:**
1. ✅ **Reusable templates** - Change once, apply everywhere
2. ✅ **Easy variations** - Regenerate with different styles
3. ✅ **Better analytics** - Query specific elements
4. ✅ **A/B testing** - Compare different approaches
5. ✅ **Version control** - Track prompt evolution
6. ✅ **Batch updates** - Change all at once
7. ✅ **Style library** - Predefined presets
8. ✅ **Documentation** - Clear structure for team

---

## 📋 **Current Implementation:**

### **In script.js (Now):**
```javascript
const promptData = {
    base_template: "Professional character portrait for TV gameshow",
    character_name: this.characterName,
    full_prompt: this.imagePrompt,
    generated_at: new Date().toISOString(),
    language: this.currentLanguage
};

// Save as JSON string
await pb.collection('MEQuestions').update(recordId, {
    image_prompt: JSON.stringify(promptData)
});
```

### **To Read:**
```javascript
const record = await pb.collection('MEQuestions').getOne(recordId);
const promptData = JSON.parse(record.image_prompt);
console.log(promptData.character_name);
console.log(promptData.full_prompt);
```

---

## 🚀 **Next Steps:**

1. **Upload updated script.js** - Now saves as JSON
2. **Test with one character** - Verify JSON saves correctly
3. **Check PocketBase** - Can view JSON in admin
4. **Optionally: Change field type to JSON** in PB admin
5. **Build style library** - Create reusable presets

---

**Now saves image_prompt as structured JSON for maximum reusability!** ✅
