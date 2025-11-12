# 🔍 Debug 500 Error - Enhanced Logging

## Changes Made

Added comprehensive error logging to `generate-image-freepik.php` to identify the exact cause of the 500 error.

---

## New Error Handling

### **1. Check API Key on Load**
```php
// Check if API key is defined
if (!defined('FREEPIK_API_KEY') || empty(FREEPIK_API_KEY)) {
    error_log("❌ FREEPIK_API_KEY not defined or empty");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Freepik API key not configured']);
    exit;
}
```

### **2. Catch Fatal Errors**
```php
} catch (Error $e) {
    error_log("❌ Fatal Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
```

### **3. Log Stack Traces**
```php
} catch (Exception $e) {
    error_log("❌ Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
```

---

## Upload & Test

### **Upload:**
✅ `generate-image-freepik.php` (enhanced error logging)

### **Test:**
1. Complete questionnaire
2. Click "🎭 Voltooien!"
3. Open browser console (F12)
4. Look for detailed error message

### **Expected Console Output:**

**If API key missing:**
```
Error: Freepik API key not configured
```

**If class not found:**
```
Fatal error: Class 'FreepikAPI' not found
trace: [stack trace]
```

**If API call fails:**
```
Error: HTTP 500: [Freepik error message]
trace: [stack trace]
```

---

## Check Server Error Log

After upload, check your server's PHP error log:

```bash
tail -f /var/log/php_errors.log
# or
tail -f /path/to/your/error.log
```

Look for:
```
=== FREEPIK IMAGE GENERATION ===
Player: [name]
Prompt length: [number]
API Key loaded: FPSX6db865...
Calling Freepik API...
```

If you see an error before "Calling Freepik API...", that's where the problem is.

---

## Common Issues

### **1. API Key Not Loaded**
```
❌ FREEPIK_API_KEY not defined or empty
```
**Fix:** Check `api-keys.php` exists and has:
```php
define('FREEPIK_API_KEY', 'FPSX6db86501e35162e1c0fb6412911dd49a');
```

### **2. Class Not Found**
```
Fatal error: Class 'FreepikAPI' not found
```
**Fix:** Make sure `freepik-api.php` is uploaded and in same directory

### **3. Freepik API Rejects Request**
```
HTTP 400: parameters didn't validate
```
**Fix:** Prompt might be invalid or too long

### **4. Freepik API Down**
```
HTTP 500: Internal Server Error
```
**Fix:** Wait and retry - Freepik's servers might be down

---

## Next Steps

1. **Upload** `generate-image-freepik.php`
2. **Test** the questionnaire
3. **Check** browser console for detailed error
4. **Share** the error message with me

The error message will now tell us exactly what's wrong!

---

**Status:** ✅ Enhanced logging added
**File:** `generate-image-freepik.php`
**Time:** October 24, 2025, 1:41 PM
