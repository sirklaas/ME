# PocketBase Fields - Final Structure

**Date:** 2025-10-13  
**Collection:** MEQuestions

---

## 📊 **Field Mapping**

### **JavaScript Variables → PocketBase Fields:**

```javascript
// Internal JavaScript variables (for logic)
this.characterDescription  → character_description  (PB field)
this.worldDescription      → environment_description (PB field) ⚠️ Different name!
this.characterName         → character_name         (PB field)
this.aiSummary            → ai_summary             (PB field)
this.imagePrompt          → image_prompt           (PB field)
this.imageUrl             → image                  (PB field - file)
this.userEmail            → email                  (PB field)
```

---

## 🗂️ **PocketBase Field Structure:**

### **Text Fields:**
```
✅ character_description
   - Type: Text/Long text
   - Content: Raw AI-generated character description
   - Example: "Meet 'The Majestic Lion', a man cloaked in..."

✅ environment_description
   - Type: Text/Long text
   - Content: Raw AI-generated world/environment description
   - Note: In JavaScript this is called worldDescription
   - Example: "The Majestic Lion navigates a world known as..."

✅ character_name
   - Type: Text
   - Content: Extracted character name
   - Example: "The Majestic Lion"

✅ ai_summary
   - Type: Text/Long text
   - Content: HTML combining character + environment
   - Format: <div class="character-section">...</div><div class="world-section">...</div>

✅ props
   - Type: Text/JSON
   - Content: Empty string "" (reserved for future use)
   - Purpose: Can store additional metadata as JSON later
```

### **File Field:**
```
✅ image
   - Type: File
   - Content: Generated character image (JPEG)
   - Stored: In PocketBase internal storage
   - URL: https://pinkmilk.pockethost.io/api/files/MEQuestions/[record_id]/[filename].jpg
```

### **Other Fields:**
```
✅ image_prompt
   - Type: Text/Long text
   - Content: AI-generated prompt used for image generation
   - Example: "Professional portrait of a mysterious masked figure..."

✅ email
   - Type: Email/Text
   - Content: User's email address
   - Example: "user@example.com"

✅ player_name
   - Type: Text
   - Content: Player's name from questionnaire
   - Example: "test4"

✅ status
   - Type: Text
   - Content: Workflow status
   - Values: 
     - "in_progress"
     - "descriptions_approved" 
     - "completed_with_image"

✅ completed_at
   - Type: Date
   - Content: Timestamp when image was completed
   - Format: ISO 8601
```

---

## 🔄 **Why Different Field Names?**

### **JavaScript: `worldDescription`**
- Semantic: "world" is clearer in code
- Consistent with AI generation flow
- Pairs nicely with characterDescription

### **PocketBase: `environment_description`**
- Your existing field name in database
- Already created in PB schema
- Matches your business terminology

### **Mapping:**
```javascript
// In code, we think "world"
this.worldDescription = "The Majestic Lion navigates..."

// But save as "environment" to match PB schema
updateData = {
    environment_description: this.worldDescription
}
```

---

## ✅ **Complete PocketBase Record Example:**

```json
{
  "id": "abc123xyz",
  "player_name": "test4",
  "email": "user@example.com",
  
  "character_description": "Meet 'The Majestic Lion', a man cloaked in enigma and shrouded in deep purple royalty. A layer of gold that shimmers in his attire hints at his ambition and confidence. A powerful presence exudes from him, making him a natural-born leader. Behind his mask, a phoenix rises from flames, symbolizing his resilience and ability to transform in the face of adversity. His lion-like demeanor is both protective and strategic, whilst a playful façade hides a surprising fear of butterflies.",
  
  "environment_description": "The Majestic Lion navigates a world known as 'The Midnight Realm'. It's a world that mirrors his character, filled with the colors of a night sky swathed in deep teal and accented by the glimmer of distant stars. Unlike the cold, dark night one might imagine, this world is warm and inviting, emitting a sense of calm mystery. The air smells of aged paper and oil paint, hinting at his love for vintage typewriters and abstract art.",
  
  "character_name": "The Majestic Lion",
  
  "ai_summary": "<div class='character-section'><h3>🎭 The Majestic Lion</h3>Meet 'The Majestic Lion', a man cloaked in enigma...</div><div class='world-section'><h3>🌍 Your World</h3>The Majestic Lion navigates a world...</div>",
  
  "props": "",
  
  "image_prompt": "Professional character portrait for TV gameshow. A mysterious masked figure in deep purple robes with golden accents, wearing an ornate mask with a phoenix rising from flames design. Lion-like confident demeanor, professional photography, centered composition, dramatic lighting, 4K quality.",
  
  "image": "character_abc123xyz_1760350000000.jpg",
  
  "status": "completed_with_image",
  "completed_at": "2025-10-13T10:30:00.000Z",
  "created": "2025-10-13T09:15:00.000Z",
  "updated": "2025-10-13T10:30:00.000Z"
}
```

---

## 📝 **Field Usage Summary:**

### **For Display/Email:**
```javascript
✅ Use: ai_summary
   → Pre-formatted HTML
   → Ready for email templates
```

### **For Search/Filter:**
```javascript
✅ Use: character_name
   → Quick queries
   → "Find all Lions"
```

### **For Regeneration:**
```javascript
✅ Use: character_description + environment_description
   → Raw AI text
   → Can regenerate image with same data
```

### **For Analytics:**
```javascript
✅ Use: character_description + environment_description
   → Parse for keywords
   → Count character types
   → Analyze trends
```

---

## 🎯 **Important Notes:**

1. **No `world_description` field in PocketBase** - We use `environment_description` instead

2. **JavaScript keeps `worldDescription`** - Internal variable name for clarity

3. **One source of truth** - `environment_description` in PB contains the world data

4. **props field** - Empty now, but reserved for future metadata (JSON)

5. **Image storage** - File stored IN PocketBase, not on server disk

---

## ✅ **What Changed:**

### **Before (Wrong):**
```javascript
updateData = {
    world_description: this.worldDescription,        // ❌ Field doesn't exist in PB
    environment_description: this.worldDescription,  // ✅ Correct field
}
```

### **After (Correct):**
```javascript
updateData = {
    environment_description: this.worldDescription,  // ✅ Only field in PB
    // No world_description field
}
```

---

**Now correctly saves only to `environment_description` in PocketBase!** ✅  
**Upload script.js and test!** 🚀
