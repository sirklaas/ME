# 📝 Question Files Structure

## ⚠️ IMPORTANT: Which File to Edit?

The website loads questions from **individual chapter JSON files**, NOT from `Questions-Bilingual.json`!

---

## 📁 File Structure

### **Active Files (Used by Website):**

```
gameshow-config-v2.json          ← Main config (lists all chapters)
    ↓
    References these chapter files:
    ├── chapter1-introductie.json       (Questions 1-5)
    ├── chapter2-masked-identity.json   (Questions 6-10)
    ├── chapter3-persoonlijke.json      (Questions 11-16)
    ├── chapter4-verborgen-talenten.json (Questions 17-21)
    ├── chapter5-jeugd-verleden.json    (Questions 22-26)
    ├── chapter6-fantasie-dromen.json   (Questions 27-31)
    ├── chapter7-eigenaardigheden.json  (Questions 32-36)
    ├── chapter8-onverwachte.json       (Questions 37-40)
    └── chapter9-film-maken.json        (Questions 41-43)
```

### **Dashboard File (Used by Admin Dashboard):**

```
Questions-Bilingual.json  ← Used by questions-dashboard.html
QuestionsNL.json         ← OLD reference file (NOT used)
```

**Important:** `Questions-Bilingual.json` is used by the **admin dashboard**, not the main questionnaire!

---

## 🎯 Question ID to File Mapping

| Question IDs | Chapter | File Name |
|-------------|---------|-----------|
| 1-5 | Chapter 1: Introductie | `chapter1-introductie.json` |
| **6-10** | **Chapter 2: Masked Identity** | **`chapter2-masked-identity.json`** |
| 11-16 | Chapter 3: Persoonlijke Eigenschappen | `chapter3-persoonlijke.json` |
| 17-21 | Chapter 4: Verborgen Talenten | `chapter4-verborgen-talenten.json` |
| 22-26 | Chapter 5: Jeugd & Verleden | `chapter5-jeugd-verleden.json` |
| 27-31 | Chapter 6: Fantasie & Dromen | `chapter6-fantasie-dromen.json` |
| 32-36 | Chapter 7: Eigenaardigheden | `chapter7-eigenaardigheden.json` |
| 37-40 | Chapter 8: Onverwachte Voorkeuren | `chapter8-onverwachte.json` |
| 41-43 | Chapter 9: Film Maken | `chapter9-film-maken.json` |

---

## 📝 How to Edit Questions

### **Example: Change Question 6**

**❌ WRONG:**
```
Edit Questions-Bilingual.json  ← This file is NOT used!
```

**✅ CORRECT:**
```
Edit chapter2-masked-identity.json  ← This is the active file!
```

### **Steps:**

1. **Find which chapter file contains the question:**
   - Question 6 = Chapter 2 = `chapter2-masked-identity.json`

2. **Edit the file:**
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

3. **Upload to server:**
   - Upload `chapter2-masked-identity.json`

4. **Clear browser cache:**
   - Cmd+Shift+R (Mac)
   - Ctrl+Shift+R (Windows)

5. **Verify:**
   - Refresh page
   - Check question 6 shows new text

---

## 🔍 How the Website Loads Questions

### **Loading Process:**

```javascript
// 1. Load main config
fetch('gameshow-config-v2.json')
    ↓
// 2. Read chapter file paths
chapters: [
    { file: "chapter1-introductie.json" },
    { file: "chapter2-masked-identity.json" },
    ...
]
    ↓
// 3. Load each chapter file
fetch('chapter2-masked-identity.json')
    ↓
// 4. Display questions
Display questions 6-10 from chapter2-masked-identity.json
```

**Key Point:** `Questions-Bilingual.json` is NEVER loaded by the website!

---

## 📋 Complete Chapter Files List

### **Chapter 1: Introductie & Basisinformatie**
- **File:** `chapter1-introductie.json`
- **Questions:** 1-5
- **Topics:** Name, email, department, role, experience

### **Chapter 2: Masked Identity**
- **File:** `chapter2-masked-identity.json`
- **Questions:** 6-10
- **Topics:** Animal personality, costume color, nature element, mask design, entrance music

### **Chapter 3: Persoonlijke Eigenschappen**
- **File:** `chapter3-persoonlijke.json`
- **Questions:** 11-16
- **Topics:** Strengths, weaknesses, work style, stress handling, motivation, team role

### **Chapter 4: Verborgen Talenten**
- **File:** `chapter4-verborgen-talenten.json`
- **Questions:** 17-21
- **Topics:** Hidden talents, hobbies, creative outlets, learning interests, unique skills

### **Chapter 5: Jeugd & Verleden**
- **File:** `chapter5-jeugd-verleden.json`
- **Questions:** 22-26
- **Topics:** Childhood dreams, formative experiences, role models, life lessons, proud moments

### **Chapter 6: Fantasie & Dromen**
- **File:** `chapter6-fantasie-dromen.json`
- **Questions:** 27-31
- **Topics:** Dream vacation, superpower, dinner guest, perfect day, bucket list

### **Chapter 7: Eigenaardigheden**
- **File:** `chapter7-eigenaardigheden.json`
- **Questions:** 32-36
- **Topics:** Quirks, guilty pleasures, unusual habits, secret obsessions, weird collections

### **Chapter 8: Onverwachte Voorkeuren**
- **File:** `chapter8-onverwachte.json`
- **Questions:** 37-40
- **Topics:** Unexpected favorites, surprising dislikes, contradictions, paradoxes

### **Chapter 9: Film Maken**
- **File:** `chapter9-film-maken.json`
- **Questions:** 41-43
- **Topics:** Video scene descriptions (3 levels of revelation)

---

## 🚀 Quick Edit Guide

### **To change a question:**

1. **Identify the question ID** (e.g., Question 6)
2. **Find the chapter file** (e.g., `chapter2-masked-identity.json`)
3. **Edit the file** (change question text)
4. **Upload to server** (same file name)
5. **Clear cache** (Cmd+Shift+R)
6. **Test** (verify change appears)

### **To add a new question:**

1. **Choose the chapter** (e.g., Chapter 2)
2. **Open chapter file** (`chapter2-masked-identity.json`)
3. **Add question object** (with unique ID)
4. **Upload to server**
5. **Test**

### **To remove a question:**

1. **Open chapter file**
2. **Delete question object**
3. **Update question IDs** (if needed)
4. **Upload to server**
5. **Test**

---

## ⚠️ Common Mistakes

### **Mistake 1: Editing wrong file**
```
❌ Edit Questions-Bilingual.json
✅ Edit chapter2-masked-identity.json
```

### **Mistake 2: Not uploading after edit**
```
❌ Edit locally but forget to upload
✅ Edit + Upload + Clear cache
```

### **Mistake 3: Not clearing cache**
```
❌ Upload but browser shows old version
✅ Upload + Clear cache (Cmd+Shift+R)
```

### **Mistake 4: Breaking JSON syntax**
```
❌ Missing comma, bracket, or quote
✅ Validate JSON before uploading
```

---

## 🧪 Testing After Changes

### **Checklist:**

- [ ] Edit correct chapter file
- [ ] Validate JSON syntax
- [ ] Upload to server
- [ ] Clear browser cache (Cmd+Shift+R)
- [ ] Refresh page
- [ ] Navigate to changed question
- [ ] Verify new text appears
- [ ] Test question functionality
- [ ] Check both languages (NL/EN)

---

## 📊 File Sizes Reference

```
chapter1-introductie.json        ~2 KB   (5 questions)
chapter2-masked-identity.json    ~3 KB   (5 questions)
chapter3-persoonlijke.json       ~4 KB   (6 questions)
chapter4-verborgen-talenten.json ~3 KB   (5 questions)
chapter5-jeugd-verleden.json     ~3 KB   (5 questions)
chapter6-fantasie-dromen.json    ~3 KB   (5 questions)
chapter7-eigenaardigheden.json   ~3 KB   (5 questions)
chapter8-onverwachte.json        ~2 KB   (4 questions)
chapter9-film-maken.json         ~2 KB   (3 questions)
```

---

**Status:** ✅ Question 6 updated in correct file
**File:** `chapter2-masked-identity.json`
**Next:** Upload file to server and clear cache
**Time:** October 24, 2025, 11:20 AM
