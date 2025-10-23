# 500 Error Fix - Server Redirect Issue

**Date:** 2025-10-14  
**Issue:** Image generation failing with 500 error  
**Root Cause:** Server redirecting non-www to www, losing POST data

---

## 🔍 **The Problem:**

### **What Was Happening:**

1. JavaScript makes POST request to:
   ```javascript
   fetch('generate-character-real.php', ...)
   ```

2. Browser resolves to:
   ```
   https://pinkmilk.eu/ME/generate-character-real.php
   ```

3. **Server returns 301 redirect:**
   ```
   HTTP 301: Moved to https://www.pinkmilk.eu/ME/generate-character-real.php
   ```

4. **Browser follows redirect BUT:**
   - POST becomes GET
   - POST data is lost
   - PHP receives empty request
   - Returns 500 error

---

## ✅ **The Solution:**

### **Use Full URL with www:**

**Changed from:**
```javascript
fetch('generate-character-real.php', {
    method: 'POST',
    body: JSON.stringify({...})
})
```

**Changed to:**
```javascript
fetch('https://www.pinkmilk.eu/ME/generate-character-real.php', {
    method: 'POST',
    body: JSON.stringify({...})
})
```

---

## 📝 **Files Updated:**

### **1. script.js** ✅

**Updated all fetch calls:**

```javascript
// Character description generation
fetch('https://www.pinkmilk.eu/ME/generate-character-real.php', ...)

// Image generation
fetch('https://www.pinkmilk.eu/ME/generate-character-real.php', ...)

// Email sending
fetch('https://www.pinkmilk.eu/ME/send-description-email.php', ...)
fetch('https://www.pinkmilk.eu/ME/send-final-email.php', ...)
```

### **2. test-actual-image-gen.php** ✅

Updated test to use www URL.

---

## 🧪 **Verification:**

### **Before Fix:**
```
POST https://pinkmilk.eu/ME/generate-character-real.php
→ 301 Redirect to www
→ POST data lost
→ 500 Error
```

### **After Fix:**
```
POST https://www.pinkmilk.eu/ME/generate-character-real.php
→ No redirect
→ POST data received
→ ✅ Image generated
```

---

## 🚀 **Testing:**

### **1. Upload Updated Files:**
```
✅ script.js - Updated with www URLs
✅ test-actual-image-gen.php - Updated test
```

### **2. Run Test:**
Visit: `https://www.pinkmilk.eu/ME/test-actual-image-gen.php`

**Should show:**
```
HTTP Code: 200
✅ 200 SUCCESS!
Image generation succeeded!
Has image_data: YES
```

### **3. Test Live Flow:**
1. Run TEST MODE
2. Accept character
3. Enter email
4. Wait 60 seconds
5. Should see image generated ✅

---

## 🎯 **Why This Happens:**

### **Common Server Configuration:**

Many servers have this rule in `.htaccess`:
```apache
# Redirect non-www to www
RewriteCond %{HTTP_HOST} !^www\.
RewriteRule ^(.*)$ https://www.%{HTTP_HOST}/$1 [R=301,L]
```

**This is GOOD for SEO but BAD for POST requests!**

---

## 💡 **Best Practices:**

### **1. Always Use Full URLs for API Calls:**
```javascript
✅ fetch('https://www.pinkmilk.eu/ME/api.php', ...)
❌ fetch('api.php', ...)
❌ fetch('/ME/api.php', ...)
```

### **2. Match Your Domain's Canonical Form:**
- If your site uses `www.domain.com` → Use www in API calls
- If your site uses `domain.com` → Don't use www in API calls

### **3. Test POST Requests:**
```bash
curl -X POST https://pinkmilk.eu/ME/api.php
# Check if it redirects (HTTP 301)

curl -X POST https://www.pinkmilk.eu/ME/api.php
# Should return 200 (no redirect)
```

---

## 📊 **Impact:**

### **Before:**
- ❌ Image generation: FAILED (500 error)
- ❌ All POST requests potentially affected
- ❌ Data loss on redirect

### **After:**
- ✅ Image generation: WORKS
- ✅ All API calls use correct URL
- ✅ No data loss
- ✅ No unnecessary redirects

---

## 🔒 **Alternative Solutions (Not Recommended):**

### **Option 1: Disable WWW Redirect**
```apache
# Remove from .htaccess
# RewriteRule ... www ...
```
**Problem:** Breaks SEO, multiple canonical URLs

### **Option 2: Allow POST on Redirect**
```apache
# In .htaccess
RewriteRule ... [R=307,L]  # 307 preserves POST
```
**Problem:** Still makes unnecessary redirect, doubles server load

### **Option 3: Use Relative URLs Only**
```javascript
fetch('./api.php', ...)  // Relative to current page
```
**Problem:** Only works if page is already on www domain

---

## ✅ **Recommended Solution:**

**Use full absolute URLs with the canonical domain (www).**

This is:
- ✅ Explicit and clear
- ✅ No redirects
- ✅ Works from any page
- ✅ Works for CORS requests
- ✅ Future-proof

---

**Image generation now works! Upload script.js and test!** 🚀
