# Bilingual Questions System Update
**Date:** 2025-10-08  
**Status:** ✅ Completed

---

## Overview
Combined Dutch (`QuestionsNL.json`) and English question structures into a single bilingual JSON file with automatic language switching support.

---

## Files Created

### **Questions-Bilingual.json**
- ✅ Complete bilingual question structure
- ✅ All 40 questions across 8 chapters
- ✅ Both Dutch (nl) and English (en) translations
- ✅ Gameshow metadata in both languages

**Structure:**
```json
{
  "gameshow": {
    "title": {
      "nl": "Dutch text",
      "en": "English text"
    }
  },
  "chapters": [
    {
      "title": { "nl": "...", "en": "..." },
      "questions": [
        {
          "question": { "nl": "...", "en": "..." },
          "placeholder": { "nl": "...", "en": "..." },
          "options": {
            "nl": ["..."],
            "en": ["..."]
          }
        }
      ]
    }
  ]
}
```

---

## Files Modified

### **script.js** - Updated Question Rendering
All question rendering logic now supports bilingual structure with automatic fallbacks:

#### **1. Chapter Display (Line 1485-1493)**
```javascript
const lang = this.currentLanguage;
document.getElementById('chapterTitle').textContent = chapter.title[lang] || chapter.title;
document.getElementById('chapterDescription').textContent = chapter.description[lang] || chapter.description;
```

#### **2. Question Rendering (Line 1517-1527)**
```javascript
const lang = this.currentLanguage;
const questionText = question.question[lang] || question.question;
questionTitle.innerHTML = `
    <span class="question-number">${question.id}</span>
    ${questionText}
`;
```

#### **3. Multiple Choice Options (Line 1535-1536)**
```javascript
const options = question.options[lang] || question.options;
options.forEach((option, index) => {
    // ... render option
});
```

#### **4. Text Input Placeholders (Line 1562-1563)**
```javascript
const placeholder = question.placeholder ? 
    (question.placeholder[lang] || question.placeholder) : 
    (lang === 'nl' ? 'Vul je antwoord hier in...' : 'Enter your answer here...');
```

#### **5. Error Messages (Line 1582)**
```javascript
errorDiv.textContent = lang === 'nl' ? 'Dit veld is verplicht.' : 'This field is required.';
```

#### **6. Answer Formatting (Line 773-780)**
```javascript
const lang = this.currentLanguage;
const questionText = question.question[lang] || question.question;
const answerText = question.type === 'multiple-choice' ? 
    (question.options[lang] ? question.options[lang][answer] : question.options[answer]) : answer;
formatted[questionText] = answerText;
```

---

## How It Works

### **Automatic Language Detection**
The system uses `this.currentLanguage` (set to 'nl' or 'en') to automatically:
1. Display the correct question text
2. Show appropriate placeholders
3. Render translated options
4. Display error messages in the selected language

### **Fallback Support**
If a translation is missing, the system falls back to the original text:
```javascript
chapter.title[lang] || chapter.title
```

This ensures compatibility with both:
- ✅ New bilingual structure: `{ "nl": "...", "en": "..." }`
- ✅ Legacy single-language structure: `"Direct text"`

---

## Question Breakdown

### **Chapter 1: Basic Information (5 questions)**
- Gender preference
- Relationship status  
- Imagination level
- Character age category
- Company tenure

### **Chapter 2: Masked Identity (5 questions)**
- Animal personality match
- Costume color
- Nature element
- Mask design
- Entrance music style

### **Chapter 3: Personal Traits & Character (6 questions)**
- Superpower choice
- Bizarre fear
- Perfect dinner guest
- Dinner conversation topics
- Life as Netflix series
- Craziest moment

### **Chapter 4: Hidden Talents & Hobbies (5 questions)**
- Secret talents
- Unusual hobbies/collections
- Unexpected instruments
- Past sports/activities
- Creative outlets

### **Chapter 5: Youth & Past (5 questions)**
- Childhood career dreams
- Favorite school subjects
- Embarrassing but funny memories
- Missed teenage trends
- Advice to 16-year-old self

### **Chapter 6: Fantasy & Dreams (5 questions)**
- Fictional world to live in
- First purchase with Musk's wealth
- Fascinating historical period
- Dream restaurant concept
- Bucket list destination

### **Chapter 7: Quirks & Habits (5 questions)**
- Strange family-laughed-at habit
- Illogical superstition/ritual
- Weird food combinations
- Most productive time of day
- Unusual stress relief method

### **Chapter 8: Unexpected Preferences (4 questions)**
- Guilty pleasure music genre
- Guilty pleasure TV shows
- Hypothetical tattoo
- Personality-describing color

**Total:** 40 questions

---

## Benefits

### **1. Maintainability**
- ✅ Single source of truth for all languages
- ✅ Easy to add new languages (just add new language key)
- ✅ Questions stay synchronized across languages

### **2. User Experience**
- ✅ Seamless language switching
- ✅ All content translates automatically
- ✅ Consistent experience in both languages

### **3. Developer Experience**
- ✅ Simple syntax: `question.text[lang]`
- ✅ Automatic fallbacks prevent errors
- ✅ Works with existing PocketBase backend

---

## Migration Path

### **For Local Development:**
Use `Questions-Bilingual.json` instead of separate language files

### **For PocketBase:**
Upload bilingual structure to PocketBase with language-aware fields

### **Current Files:**
- `QuestionsNL.json` - Keep as reference (can be deprecated)
- `Questions.json` - Old English version (can be deprecated)
- `Questions-Bilingual.json` - **New single source of truth**

---

## Testing Checklist

- [ ] Test Dutch language selection → All questions in Dutch
- [ ] Test English language selection → All questions in English
- [ ] Verify all 40 questions render correctly
- [ ] Check multiple-choice options translate
- [ ] Verify placeholders translate
- [ ] Test error messages in both languages
- [ ] Confirm answer summary uses correct language
- [ ] Test mid-questionnaire language switching (if supported)

---

## Next Steps

1. **Upload to Production:**
   - Replace Questions.json with Questions-Bilingual.json
   - Upload updated script.js
   - Test both languages thoroughly

2. **PocketBase Integration:**
   - Update PocketBase schema to support bilingual structure
   - Migrate existing questions to new format

3. **Future Enhancements:**
   - Add more languages (French, German, etc.)
   - Admin panel for managing translations
   - Translation export/import tools

---

## Code Examples

### **Accessing Bilingual Text:**
```javascript
// Get current language
const lang = this.currentLanguage; // 'nl' or 'en'

// Access question text
const questionText = question.question[lang];

// Access with fallback
const title = chapter.title[lang] || chapter.title;

// Access options array
const options = question.options[lang];
```

### **Adding New Language:**
```json
{
  "question": {
    "nl": "Dutch text",
    "en": "English text",
    "fr": "French text",
    "de": "German text"
  }
}
```

---

**Status:** Ready for testing and deployment ✅  
**Compatibility:** Backward compatible with existing code  
**Performance:** No impact - same data structure complexity
