# PocketBase Schema Update for Character Generation
## MEQuestions Collection - New Fields

**Updated:** October 5, 2025

---

## 🔧 How to Update PocketBase Schema

### Step 1: Login to PocketBase Admin
1. Go to: https://pinkmilk.pockethost.io/_/
2. Login with your admin credentials

### Step 2: Navigate to Collections
1. Click on "Collections" in the left sidebar
2. Find and click on "MEQuestions" collection
3. Click the "Edit" or settings icon

### Step 3: Add New Fields

Click "Add field" for each of the following:

---

## 📋 New Fields to Add

### 1. Language Field
- **Name:** `language`
- **Type:** Text
- **Options:**
  - Max length: 2
  - Pattern: (leave empty)
  - Required: No
  - Unique: No
  - Default: nl

### 2. AI Summary (Full HTML)
- **Name:** `ai_summary`
- **Type:** Text (or Editor if available)
- **Options:**
  - Max length: 50000 (or unlimited)
  - Required: No
  - This stores the complete HTML character profile

### 3. Character Name
- **Name:** `character_name`
- **Type:** Text
- **Options:**
  - Max length: 200
  - Required: No
  - Example: "The Silent Innovator", "The Midnight Gardener"

### 4. Character Description
- **Name:** `character_description`
- **Type:** Text (or Editor)
- **Options:**
  - Max length: 10000
  - Required: No
  - Stores: 150-250 word character description

### 5. Environment Description
- **Name:** `environment_description`
- **Type:** Text (or Editor)
- **Options:**
  - Max length: 5000
  - Required: No
  - Stores: 100-150 word environment description

### 6. Props (JSON Array)
- **Name:** `props`
- **Type:** JSON (or Text if JSON not available)
- **Options:**
  - Required: No
  - Stores: Array of 3-5 props with meanings

### 7. Story Prompt Level 1
- **Name:** `story_prompt_level1`
- **Type:** Text (or Editor)
- **Options:**
  - Max length: 2000
  - Required: No
  - Stores: Surface story prompt (30-60 sec)

### 8. Story Prompt Level 2
- **Name:** `story_prompt_level2`
- **Type:** Text (or Editor)
- **Options:**
  - Max length: 2000
  - Required: No
  - Stores: Hidden depths prompt (60-90 sec)

### 9. Story Prompt Level 3
- **Name:** `story_prompt_level3`
- **Type:** Text (or Editor)
- **Options:**
  - Max length: 2000
  - Required: No
  - Stores: Deep secrets prompt (90-120 sec)

### 10. Completed At (Timestamp)
- **Name:** `completed_at`
- **Type:** Date/DateTime
- **Options:**
  - Required: No
  - Stores: When user confirmed final submission

### 11. Status Update
- **Name:** `status` (if already exists, just update)
- **Type:** Text or Select
- **Options:**
  - If Select, add option: "completed_with_confirmation"
  - Current values should include:
    - "in_progress"
    - "submitted"
    - "completed_with_confirmation" ← NEW

---

## 📊 Complete Field List for MEQuestions Collection

After updates, your collection should have these fields:

**Existing Fields:**
- id (auto)
- created (auto)
- updated (auto)
- gamename
- nameplayer
- chapter1_answers
- chapter2_answers
- chapter3_answers
- chapter4_answers
- chapter5_answers
- chapter6_answers
- chapter7_answers
- chapter8_answers
- status

**New Fields:**
- language ✨
- ai_summary ✨
- character_name ✨
- character_description ✨
- environment_description ✨
- props ✨
- story_prompt_level1 ✨
- story_prompt_level2 ✨
- story_prompt_level3 ✨
- completed_at ✨

---

## 🧪 Test After Update

1. Go to your site: https://pinkmilk.eu/ME/questions.html
2. Complete a test submission
3. In PocketBase admin, check the MEQuestions record
4. Verify all new fields are populated with data

---

## 🔄 If You Need to Import/Export

### Export Current Schema (Backup)
1. In PocketBase admin
2. Go to Settings → Export collections
3. Save the JSON file

### Import Updated Schema
If I provide you with a complete schema JSON, you can import it via:
1. Settings → Import collections
2. Paste the JSON
3. Confirm import

---

## 📝 Quick Copy-Paste Field Definitions

If PocketBase supports JSON schema import, here's the field definitions:

```json
{
  "language": {
    "type": "text",
    "maxLength": 2,
    "required": false
  },
  "ai_summary": {
    "type": "text",
    "maxLength": 50000,
    "required": false
  },
  "character_name": {
    "type": "text",
    "maxLength": 200,
    "required": false
  },
  "character_description": {
    "type": "text",
    "maxLength": 10000,
    "required": false
  },
  "environment_description": {
    "type": "text",
    "maxLength": 5000,
    "required": false
  },
  "props": {
    "type": "json",
    "required": false
  },
  "story_prompt_level1": {
    "type": "text",
    "maxLength": 2000,
    "required": false
  },
  "story_prompt_level2": {
    "type": "text",
    "maxLength": 2000,
    "required": false
  },
  "story_prompt_level3": {
    "type": "text",
    "maxLength": 2000,
    "required": false
  },
  "completed_at": {
    "type": "date",
    "required": false
  }
}
```

---

## 🆘 Support

If you encounter issues:
1. Check field types match (Text vs Editor vs JSON)
2. Ensure max lengths are sufficient
3. All fields should be "Not Required" (optional)
4. Check PocketBase version compatibility

---

**After updating the schema, the character generation will save all data properly!** ✅
