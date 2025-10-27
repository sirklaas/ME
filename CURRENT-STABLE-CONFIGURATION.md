# 🔒 CURRENT STABLE CONFIGURATION
**Date:** October 27, 2025  
**Status:** ✅ WORKING - DO NOT CHANGE UNLESS NECESSARY

---

## 📋 **SYSTEM OVERVIEW**

### **Image Generation Provider:**
- **Provider:** Leonardo.ai (NOT Freepik)
- **Model:** Phoenix `b24e16ff-06e3-43eb-8d33-4416c2d75876`
- **Aspect Ratio:** 16:9 widescreen (1472x832)
- **Inference Steps:** 30
- **Guidance Scale:** 7
- **Preset Style:** CINEMATIC

### **API Endpoints:**
- **Character Generation:** `generate-character.php`
- **Image Generation:** `generate-image-leonardo.php`
- **Leonardo API Class:** `leonardo-api.php`

---

## 🎭 **CHARACTER TYPE SYSTEM**

### **User Selection Method:**
Users choose their character type in **Chapter 1, Question 7**

### **5 Character Types:**
1. **animals** - 🐾 Dieren (Animals)
2. **fruits_vegetables** - 🍅 Groente & Fruit (Fruits & Vegetables)
3. **fantasy_heroes** - ⚔️ Fantasy Helden (Fantasy Heroes)
4. **pixar_disney** - 🎬 Pixar/Disney Figuren
5. **fairy_tales** - 📚 Sprookjesfiguren (Fairy Tales)

### **How It Works:**
1. User answers Question 7 in Chapter 1
2. JavaScript extracts `characterType` from answer
3. Stored in `this.characterType`
4. Sent to backend in character generation request
5. PHP uses user selection (NOT AI determination)

---

## 📝 **CHAPTER 1 QUESTIONS**

### **Total Questions:** 7 (was 5)

1. **Q1:** Wat zou je willen worden? (Gender)
2. **Q2:** Ben je getrouwd, in een relatie, of single? (Relationship)
3. **Q3:** Hoeveel fantasie heb je (Imagination)
4. **Q4:** In welke leeftijdscategorie valt jouw character? (Age)
5. **Q5:** Hoe lang werk je bij dit bedrijf? (Company tenure)
6. **Q6:** Op welke afdeling werk je? ✨ NEW - Text input
7. **Q7:** Hoe zou je jouw favoriete karakter omschrijven? ✨ NEW - Character type selection

---

## 🖼️ **IMAGE PROMPT STRUCTURE**

### **Prompt Length Limit:** 1500 characters (Leonardo.ai requirement)

### **Prompt Components:**
```
1. Character type description (SHORT - 50-100 chars)
2. Character name
3. Character details (150 chars max)
4. Environment/Setting (80 chars max)
5. Technical specs (SHORT - 50 chars)
```

### **Character Type Descriptions:**
- **animals:** "anthropomorphic animal with clothes"
- **fruits_vegetables:** "anthropomorphic fruit/vegetable with cartoon face, eyes, mouth, arms, legs, wearing clothes. Pixar style, NOT human"
- **fantasy_heroes:** "fantasy character with costume"
- **pixar_disney:** "Pixar 3D animated character"
- **fairy_tales:** "fairy tale character"

### **Technical Specs:**
"16:9 widescreen, 4K, cinematic lighting, full body, rule of thirds."

### **What Was REMOVED to Stay Under 1500 Chars:**
- ❌ PROPS section (was generating 3 items)
- ❌ Verbose fruit/vegetable instructions
- ❌ Long technical specifications
- ❌ Camera details

---

## 🤖 **AI CHARACTER GENERATION**

### **OpenAI Prompt Structure:**
```
1. KARAKTER (100-150 words)
   - Begin with: "De [TYPE] genaamd [NAME]"
   - Describe appearance, clothing, personality
   - For fruits/vegetables: MUST describe eyes, mouth, arms, legs

2. OMGEVING (30-50 words)
   - ONE SPECIFIC LOCATION
   - Simple and concrete (NO abstract concepts)
   - Example: "een zonnige tuin", "een moderne keuken"
```

### **What Was REMOVED:**
- ❌ "3. PROPS" section (was 3 items)

---

## 📧 **EMAIL FLOW**

### **Two Emails Sent:**

**Email 1: Character Description**
- **When:** After character generation
- **Contains:** AI-generated character description
- **File:** `send-description-email.php`

**Email 2: Character Image**
- **When:** After image generation
- **Contains:** Character image attachment
- **File:** `send-final-email.php`

---

## 🧪 **TEST MODE**

### **How to Activate:**
1. Go to language selection page
2. Enter your name
3. Click "🧪 TEST MODE" button

### **Test Mode Features:**
- ✅ **Character Type Selector Popup** - Choose which type to test
- ✅ Loads last PocketBase record
- ✅ Jumps to Chapter 9
- ✅ Sets `this.characterType` to selected type
- ✅ Click "Voltooien" to regenerate character

### **5 Test Options:**
1. 🐾 Dieren (Animals)
2. 🍅 Groente & Fruit (Fruits & Vegetables)
3. ⚔️ Fantasy Helden (Fantasy Heroes)
4. 🎬 Pixar/Disney Figuren
5. 📚 Sprookjesfiguren (Fairy Tales)

---

## 📁 **KEY FILES**

### **Frontend:**
- `questions.html` - Main questionnaire page
- `script.js` - Main JavaScript logic
- `questions-unified.json` - ALL questions (this is what the app loads)
- `chapter1-introductie.json` - Chapter 1 questions (backup/reference)

### **Backend:**
- `generate-character.php` - Character generation (OpenAI)
- `generate-image-leonardo.php` - Image generation endpoint
- `leonardo-api.php` - Leonardo.ai API wrapper class
- `send-description-email.php` - Email 1 (description)
- `send-final-email.php` - Email 2 (image)

### **Configuration:**
- `character-options-80.json` - 80 options per character type
- `api-keys.php` - API keys (OpenAI, Leonardo.ai)

---

## 🔑 **IMPORTANT SETTINGS**

### **Leonardo.ai Settings:**
```php
'modelId' => 'b24e16ff-06e3-43eb-8d33-4416c2d75876', // Phoenix (latest)
'width' => 1472,  // 16:9 ratio
'height' => 832,
'num_images' => 1,
'guidance_scale' => 7,
'num_inference_steps' => 30,
'presetStyle' => 'CINEMATIC'
```

### **Negative Prompt:**
```
human face, realistic human, person, man, woman, human body, human skin, 
close-up, portrait only, headshot, cropped body, blurry, low quality, 
pixelated, distorted, amateur, poorly lit, deformed, ugly, bad anatomy, 
extra limbs, missing limbs, floating limbs, disconnected limbs, 
malformed hands, long neck, duplicate, mutated, mutilated, out of frame, 
extra fingers, mutated hands, poorly drawn hands, poorly drawn face, 
mutation, deformed, bad proportions, gross proportions, watermark, 
signature, text, logo, no character, empty scene, landscape only
```

---

## ⚠️ **CRITICAL RULES**

### **DO NOT CHANGE:**
1. ❌ Leonardo.ai model (Phoenix is working)
2. ❌ Prompt structure (under 1500 chars)
3. ❌ Character type selection method (Question 7)
4. ❌ Image dimensions (1472x832 = 16:9)
5. ❌ Inference steps (30 works well)

### **NEVER ADD BACK:**
1. ❌ PROPS section (makes prompt too long)
2. ❌ Freepik references (we use Leonardo.ai only)
3. ❌ Verbose fruit/vegetable instructions (makes prompt too long)
4. ❌ AI character type determination (user selects now)

---

## 🐛 **KNOWN ISSUES (RESOLVED)**

### **Issue 1: Prompt Too Long (>1500 chars)**
- **Error:** "Invalid prompt, maximum length of 1500 characters exceeded"
- **Solution:** Shortened all prompt components, removed PROPS
- **Status:** ✅ FIXED

### **Issue 2: Human Face Instead of Tomato**
- **Error:** Generated realistic human instead of fruit/vegetable
- **Solution:** User selects character type, stronger negative prompts
- **Status:** ✅ FIXED

### **Issue 3: Wrong Aspect Ratio (3:4)**
- **Error:** Images were portrait instead of landscape
- **Solution:** Set 1472x832 (16:9), removed conflicting parameters
- **Status:** ✅ FIXED

### **Issue 4: Character Name Incorrect**
- **Error:** Extracted "20 en" instead of "Paarse Nebula"
- **Solution:** Improved regex to handle multi-word names
- **Status:** ✅ FIXED

### **Issue 5: Close-up Instead of Full Body**
- **Error:** Generated headshot/portrait
- **Solution:** Added "full body" emphasis, removed "centered"
- **Status:** ✅ FIXED

---

## 📊 **TOKEN USAGE**

### **Leonardo.ai Free Tier:**
- **Daily Limit:** 150 tokens
- **Cost per Image (30 steps):** ~8 tokens
- **Images per Day:** ~18 images

### **OpenAI Usage:**
- **Character Generation:** ~600 tokens per request
- **Cost:** ~$0.01 per character

---

## 🚀 **DEPLOYMENT CHECKLIST**

### **Files to Upload:**
1. ✅ `questions-unified.json` (has Questions 6 & 7)
2. ✅ `script.js` (character type extraction + test mode popup)
3. ✅ `generate-character.php` (shortened prompts, no PROPS)
4. ✅ `leonardo-api.php` (Phoenix model, negative prompts)
5. ✅ `generate-image-leonardo.php` (Leonardo.ai endpoint)

### **Files NOT Used (Don't Upload):**
- ❌ `generate-image-freepik.php` (deprecated)
- ❌ `freepik-api.php` (deprecated)

---

## 📝 **CHANGE LOG**

### **October 27, 2025:**
1. ✅ Added Question 6 (Department) to Chapter 1
2. ✅ Added Question 7 (Character Type Selection) to Chapter 1
3. ✅ Switched from Freepik to Leonardo.ai
4. ✅ Switched from Kino XL to Phoenix model
5. ✅ Shortened image prompts to under 1500 chars
6. ✅ Removed PROPS section
7. ✅ Added character type selector popup in test mode
8. ✅ Improved character name extraction regex
9. ✅ Changed from "centered" to "rule of thirds" composition
10. ✅ Reduced inference steps from 40 to 30

---

## 🎯 **SUCCESS CRITERIA**

### **Working System:**
- ✅ Users can select character type in Chapter 1
- ✅ Character generation completes successfully
- ✅ Image generation completes without 1500 char error
- ✅ Images are 16:9 widescreen
- ✅ Characters match selected type (no human faces for vegetables)
- ✅ Both emails arrive (description + image)
- ✅ Test mode allows testing different character types

---

## 📞 **SUPPORT**

### **If Something Breaks:**
1. Check console logs for errors
2. Verify prompt length < 1500 chars
3. Confirm Leonardo.ai API key is valid
4. Check PocketBase connection
5. Verify all files are uploaded

### **Common Fixes:**
- **Prompt too long:** Check `generateImagePrompt()` function
- **Wrong character type:** Check Question 7 mapping
- **No image:** Check Leonardo.ai API response
- **No email:** Check email PHP files

---

## ✅ **FINAL STATUS**

**System Status:** 🟢 STABLE AND WORKING  
**Last Tested:** October 27, 2025  
**Next Review:** Only if issues arise

**DO NOT MODIFY UNLESS ABSOLUTELY NECESSARY!**

---

*This configuration is locked and documented. Any changes should be carefully considered and tested.*
