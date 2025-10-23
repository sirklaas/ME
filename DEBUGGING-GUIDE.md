# Debugging Guide - Image Generation Issues

**Date:** 2025-10-13  
**Issues:** Character name extraction + 500 error on image generation

---

## ✅ Fixes Applied

### 1. **Character Name Extraction - IMPROVED**

**Changed Pattern Order:**
```javascript
// Pattern 1: ANY single-quoted text (now FIRST and most reliable)
/'([^']+)'/

// This catches:
- "Stepping in from the shadows, 'The Majestic Scribe'" → "The Majestic Scribe" ✅
- "Meet 'The Majestic Lion'" → "The Majestic Lion" ✅
- "'The Phoenix Alchemist'" → "The Phoenix Alchemist" ✅
```

**Added Debug Logging:**
```javascript
console.log('🔍 Extracting character name from:', description.substring(0, 150));
console.log('✅ Found quoted name:', name);
```

**Test in Console:**
After generation, you should now see:
```
🔍 Extracting character name from: Stepping in from the shadows, 'The Majestic Scribe'...
✅ Found quoted name: The Majestic Scribe
Character Name: The Majestic Scribe  ← NOT "The Mysterious Character of test3"
```

---

### 2. **Image Generation 500 Error - ENHANCED DEBUGGING**

**Added Comprehensive Logging:**

**PHP Side (generate-character-real.php):**
```php
error_log('=== IMAGE GENERATION START ===');
error_log('Character desc length: ' . strlen($characterDesc));
error_log('Generating image prompt with OpenAI...');
error_log('Image prompt generated: ' . substr($imagePrompt, 0, 100));
error_log('Calling Freepik to generate image...');
error_log('Freepik response received, success: YES/NO');
error_log('Has image_data: YES/NO');
error_log('Has image_binary: YES/NO');
error_log('Has image_url: YES/NO');
```

**JavaScript Side (script.js):**
```javascript
console.log('✅ Image generation response received');
console.log('🔍 Has image_data:', true/false);
console.log('🔍 Has image_binary:', true/false);
console.log('🔍 Has image_url:', true/false);
```

**Error Handling:**
```javascript
// Now shows actual server error message
console.error('❌ Server error:', errorMsg);
console.error('Details:', details);  // Stack trace if debug mode
```

---

## 🔍 How to Debug 500 Error

### Step 1: Check PHP Error Log

**Location:** 
- `/domains/pinkmilk.eu/public_html/ME/error_log`
- Or server-wide: `/var/log/php_errors.log`

**Look for:**
```
=== IMAGE GENERATION START ===
Character desc length: 837
World desc length: 822
Generating image prompt with OpenAI...
Image prompt generated: Professional character portrait...
Loading Freepik API...
Calling Freepik to generate image...
```

**If it stops here → Freepik API issue**

**Continue looking for:**
```
Freepik response received, success: YES
Has image_data: YES
Has image_binary: YES
✅ Image generated successfully
```

**If it stops before this → Check what Freepik returned:**
```
Full Freepik result: Array ( ... )
```

---

### Step 2: Check Browser Console

**After clicking "Accept" and entering email:**

```
🎨 Generating character image...
📝 Character: Stepping in from the shadows...
🌍 World: The Majestic Scribe exists...
```

**Wait 30-60 seconds, then look for:**

**SUCCESS:**
```
✅ Image generation response received
📝 Prompt used: Professional character portrait...
🔍 Has image_data: true
🔍 Has image_binary: true
🔍 Has image_url: false
📤 Converting image to blob...
📤 Uploading image to PocketBase...
✅ Image uploaded, URL: https://...
📧 Sending final email with image...
✅ Final email with image sent!
```

**FAILURE:**
```
❌ Server error: [error message here]
Details: [stack trace]
❌ Error generating image: [error message]
```

---

### Step 3: Common Errors and Solutions

#### Error: "No image data returned from Freepik"

**Cause:** Freepik API response format different than expected

**Check PHP log for:**
```
Full Freepik result: Array ( ... )
```

**Solutions:**
1. Verify Freepik API key is correct
2. Check Freepik API response format in docs
3. May need to update `freepik-api.php` parsing

---

#### Error: "OpenAI API error"

**Cause:** OpenAI couldn't generate image prompt

**Check PHP log for:**
```
Generating image prompt with OpenAI...
[ERROR HERE]
```

**Solutions:**
1. Check OpenAI API key
2. Check OpenAI credits
3. Check character/world descriptions are valid

---

#### Error: "Freepik API error"

**Cause:** Freepik couldn't generate image

**Check PHP log for:**
```
Calling Freepik to generate image...
Freepik response received, success: NO
❌ Image generation failed: [error]
```

**Solutions:**
1. Check Freepik API key
2. Check Freepik credits/quota
3. Check prompt isn't too long or has invalid characters
4. Try different Freepik model

---

#### Error: "No image data received from server"

**Cause:** Server returned success but no image data

**Check browser console for:**
```
🔍 Has image_data: false
🔍 Has image_binary: false
🔍 Has image_url: false
```

**Solution:**
Check PHP log for what Freepik actually returned. May need to update response parsing.

---

## 🧪 Testing Steps

### Test 1: Character Name
```
1. Run TEST MODE
2. Wait for character generation
3. Check console for:
   🔍 Extracting character name from: ...
   ✅ Found quoted name: The Majestic Scribe
4. Verify NOT showing "The Mysterious Character of test3"
5. Check PocketBase character_name field
```

### Test 2: Image Generation
```
1. Accept character
2. Enter email
3. Watch console for 30-60 seconds
4. Look for each step:
   - ✅ Image generation response received
   - 🔍 Has image_data: true
   - 📤 Converting image to blob...
   - 📤 Uploading image to PocketBase...
   - ✅ Image uploaded
5. If error, check PHP error log for details
```

---

## 📝 Files Changed

### Updated Files:
1. ✅ `script.js`
   - Better character name extraction (ANY quoted text)
   - Debug logging for extraction
   - Better error handling for image gen
   - Logs what data received
   - Handles direct URL or base64

2. ✅ `generate-character-real.php`
   - Comprehensive error logging
   - Checks if image data exists
   - Returns detailed error messages
   - Stack trace in debug mode

### Files to Upload:
```
script.js
generate-character-real.php
```

---

## 🎯 What to Look For

### Character Name Working:
```
✅ Console shows: "✅ Found quoted name: The Majestic Scribe"
✅ NOT showing: "The Mysterious Character of test3"
✅ PocketBase has correct name
✅ Email header shows correct name
```

### Image Generation Working:
```
✅ Console shows all steps complete
✅ No 500 error
✅ Image uploads to PocketBase
✅ Image URL in console
✅ Image in email
✅ PHP log shows success
```

### Image Generation Failing:
```
❌ 500 error in console
❌ Check PHP error log for exact point of failure
❌ Look for Freepik API response in log
❌ May need to adjust Freepik response parsing
```

---

## 🔧 Next Steps Based on Results

### If Character Name Still Wrong:
1. Check console for extraction log
2. See what pattern matched (or fallback)
3. May need to add another pattern

### If 500 Error Persists:
1. **CRITICAL:** Check PHP error log
2. Find exact error message
3. Check which step failed:
   - OpenAI prompt generation?
   - Freepik API call?
   - Response parsing?
4. Share PHP error log excerpt for specific fix

### If Image Data Missing:
1. Check Freepik API response in PHP log
2. May need to update `freepik-api.php` parsing
3. Freepik might use different response format

---

## 📧 Email PHP Error Log

If you see persistent 500 errors, email me the PHP error log section showing:
```
=== IMAGE GENERATION START ===
[all the lines until error]
```

This will show exactly where it's failing and what Freepik is returning.

---

**Status:** Enhanced debugging ready!  
**Next:** Upload files and check logs for exact error 🔍
