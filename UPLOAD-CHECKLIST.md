# 📤 UPLOAD CHECKLIST - October 26, 2025

## ⚠️ CRITICAL: These files MUST be uploaded to https://www.pinkmilk.eu/ME/

### **Files to Upload:**

1. ✅ **generate-character.php**
   - Contains OMGEVING extraction (line 207-222)
   - Extracts environment from AI summary
   - Uses it in image prompt

2. ✅ **generate-image-freepik.php**
   - Contains 16:9 size parameter (line 50-52)
   - Sends `'image' => ['size' => '1216x832']` to Freepik

---

## 🔍 **How to Verify Upload Worked:**

### **Test 1: Check Character Generation**
1. Complete questionnaire
2. Generate character
3. Look at console logs for:
   ```
   📝 Request body: {playerName: "...", promptLength: XXX, promptPreview: "..."}
   ```
4. The `promptPreview` should contain:
   - ✅ "=== ENVIRONMENT & BACKGROUND ==="
   - ✅ "SETTING: [text from OMGEVING]"
   - ✅ Character name (e.g., "Cirquesia")

### **Test 2: Check Image Result**
1. Wait for image generation (~60 seconds)
2. Check the image:
   - ✅ Should be widescreen (16:9 ratio, not square/portrait)
   - ✅ Should show environment from OMGEVING
   - ✅ Should match character description

---

## ❌ **Current Problem:**

The server is still using OLD files that:
- ❌ Don't extract OMGEVING
- ❌ Don't send 16:9 size parameter
- ❌ Generate generic images without environment

**Result:** Portrait images with no environment (like the elf image you showed)

---

## ✅ **After Upload:**

Images should have:
- ✅ 16:9 widescreen format
- ✅ Character in their OMGEVING (tower with postcards, etc.)
- ✅ Correct character type (wizard, not elf)
- ✅ Correct character name used

---

## 🚀 **Upload Now:**

Use your FTP client to upload:
1. `generate-character.php` → `/public_html/ME/generate-character.php`
2. `generate-image-freepik.php` → `/public_html/ME/generate-image-freepik.php`

Then test again!
