# 🎯 Single-Source Question System

## Overview
**ONE file** controls all questions for both the questionnaire and dashboard!

---

## 📁 File Structure

### **✅ SINGLE SOURCE FILE:**
```
questions-unified.json  ← EDIT THIS FILE ONLY!
```

### **Used By:**
```
questions-unified.json
    ↓
    ├── questions.html (Main Questionnaire)
    └── questions-dashboard.html (Admin Dashboard)
```

### **Old Files (No Longer Used):**
```
❌ Questions-Bilingual.json  (deprecated)
❌ gameshow-config-v2.json   (deprecated)
❌ chapter1-introductie.json (deprecated)
❌ chapter2-masked-identity.json (deprecated)
❌ chapter3-persoonlijke.json (deprecated)
... etc
```

---

## ✏️ How to Edit Questions

### **Step 1: Open the File**
```
/Users/mac/GitHubLocal/ME/questions-unified.json
```

### **Step 2: Find the Question**
Questions are organized by chapter:
- Chapter 1: Questions 1-5
- Chapter 2: Questions 6-10
- Chapter 3: Questions 11-16
- etc.

### **Step 3: Edit the Question**
```json
{
  "id": 6,
  "type": "text",
  "question": {
    "nl": "Wat omschrijft jouw persoonlijkheid het beste?",
    "en": "What best reflects your personality?"
  }
}
```

### **Step 4: Save & Upload**
1. Save `questions-unified.json`
2. Upload to server
3. Clear browser cache (Cmd+Shift+R)
4. Refresh page

---

## 📊 File Structure

```json
{
  "gameshow": {
    "id": "masked_employee_2025",
    "name": { "nl": "...", "en": "..." },
    "title": { "nl": "...", "en": "..." },
    ...
  },
  "chapters": [
    {
      "id": 1,
      "title": { "nl": "...", "en": "..." },
      "questions": [
        { "id": 1, "type": "...", "question": {...} },
        { "id": 2, "type": "...", "question": {...} }
      ]
    },
    {
      "id": 2,
      "title": { "nl": "...", "en": "..." },
      "questions": [...]
    }
  ],
  "meta": {
    "version": "2.0",
    "total_questions": 43,
    "total_chapters": 9
  }
}
```

---

## ✅ Benefits

1. **Single Source of Truth**
   - Edit once, updates everywhere
   - No sync issues

2. **Easier Management**
   - One file to maintain
   - No confusion about which file to edit

3. **Always in Sync**
   - Questionnaire and dashboard always match
   - No version conflicts

4. **Simpler Deployment**
   - Upload one file instead of 10+
   - Faster updates

---

## 🔧 Technical Details

### **How It Works:**

**Main Questionnaire (`script.js`):**
```javascript
// Load unified questions file
const response = await fetch('questions-unified.json');
const config = await response.json();

// Chapters are already included
this.questionsData = config;
this.totalChapters = config.chapters.length;
```

**Dashboard (`questions-dashboard.html`):**
```javascript
// Load same unified file
const response = await fetch('questions-unified.json');
questionsData = await response.json();
```

### **Cache Busting:**
The questionnaire uses aggressive cache busting:
```javascript
fetch(`questions-unified.json?v=${timestamp}&r=${random}&nocache=1`)
```

---

## 📝 Editing Examples

### **Change Question Text:**
```json
// Before
"question": {
  "nl": "Als je een dier zou moeten kiezen...",
  "en": "If you had to choose an animal..."
}

// After
"question": {
  "nl": "Wat omschrijft jouw persoonlijkheid het beste?",
  "en": "What best reflects your personality?"
}
```

### **Change Placeholder:**
```json
"placeholder": {
  "nl": "Bijvoorbeeld: een leeuw, omdat...",
  "en": "For example: a lion, because..."
}
```

### **Change Options (Multiple Choice):**
```json
"options": {
  "nl": [
    "Optie 1",
    "Optie 2",
    "Optie 3"
  ],
  "en": [
    "Option 1",
    "Option 2",
    "Option 3"
  ]
}
```

---

## 🚀 Deployment

### **Files to Upload:**
```
✅ questions-unified.json  ← MAIN FILE
✅ script.js              ← Updated to use unified file
✅ questions-dashboard.html ← Updated to use unified file
```

### **Optional (Keep as Backup):**
```
⚠️ gameshow-config-v2.json
⚠️ chapter*.json files
```

---

## 🧪 Testing

### **After Upload:**

1. **Test Questionnaire:**
   - Open `questions.html`
   - Check console: "✅ Loaded 9 chapters from unified file"
   - Navigate through all chapters
   - Verify all questions appear correctly

2. **Test Dashboard:**
   - Open `questions-dashboard.html`
   - Check console: "✅ Loaded unified questions"
   - Verify all chapters and questions visible
   - Test edit mode

3. **Test Changes:**
   - Edit a question in `questions-unified.json`
   - Upload file
   - Clear cache (Cmd+Shift+R)
   - Verify change appears in BOTH questionnaire and dashboard

---

## ⚠️ Important Notes

### **Always Edit questions-unified.json:**
- ✅ Edit `questions-unified.json`
- ❌ Don't edit individual chapter files
- ❌ Don't edit `Questions-Bilingual.json`

### **After Editing:**
1. Validate JSON syntax
2. Upload to server
3. Clear browser cache
4. Test both questionnaire and dashboard

### **Backup:**
- Keep a local copy before major changes
- Use version control (Git)
- Dashboard has download function

---

## 🔍 Troubleshooting

### **Questions not updating:**
- Clear browser cache (Cmd+Shift+R)
- Check file uploaded correctly
- Verify JSON syntax is valid
- Check browser console for errors

### **404 Error:**
- File name must be exactly: `questions-unified.json`
- Check file is in correct directory
- Verify file permissions (644)

### **Dashboard not loading:**
- Check console for errors
- Verify `questions-unified.json` exists
- Test JSON validity: https://jsonlint.com

---

## 📋 Quick Reference

| Task | Action |
|------|--------|
| **Edit question** | Edit `questions-unified.json` |
| **Add question** | Add to appropriate chapter in `questions-unified.json` |
| **Change chapter** | Edit chapter object in `questions-unified.json` |
| **Deploy** | Upload `questions-unified.json` |
| **Test** | Clear cache + refresh both pages |

---

**Status:** ✅ Single-source system implemented
**File:** `questions-unified.json`
**Time:** October 24, 2025, 11:29 AM
