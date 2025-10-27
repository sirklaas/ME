# 🎭 Character Type Selection - Implementation Plan

## ✅ **Why This is Better:**

### **Current Problem:**
- AI randomly chooses character type
- Users get unexpected results (human face instead of tomato)
- No control over what they become

### **New Solution:**
- User chooses character type FIRST
- Clear expectations
- Better image generation (we know exactly what to create)
- More engaging UX

---

## 📋 **New Flow:**

```
1. Language Selection (existing)
   ↓
2. **CHARACTER TYPE SELECTION** (NEW!)
   ↓
3. Player Name Input
   ↓
4. Welcome Page
   ↓
5. Questions (9 chapters)
   ↓
6. Character Generation (uses selected type)
   ↓
7. Image Generation (knows exact character type)
```

---

## 🎨 **Character Type Selection Page:**

### **5 Options:**

1. **🐾 Dieren (Animals)**
   - "Word een grappig dier met kleding en persoonlijkheid"
   - Examples: Vos, Leeuw, Pinguïn, Uil

2. **🍅 Groente & Fruit (Fruits & Vegetables)**
   - "Word een levendig fruit of groente met gezicht en armpjes"
   - Examples: Tomaat, Banaan, Wortel, Aardbei

3. **⚔️ Fantasy Helden (Fantasy Heroes)**
   - "Word een magisch wezen uit een fantasiewereld"
   - Examples: Tovenaar, Elf, Ridder, Draak

4. **🎬 Pixar/Disney Figuren**
   - "Word een charismatisch Pixar-stijl karakter"
   - Examples: Uitvinder, Avonturier, Kok, Kunstenaar

5. **📚 Sprookjesfiguren (Fairy Tales)**
   - "Word een modern sprookjeskarakter"
   - Examples: Prins, Prinses, Heks, Reus

---

## 💻 **Implementation:**

### **1. Add New Page in HTML**
Insert after language selection, before welcome page

### **2. Store Selection in JavaScript**
```javascript
this.characterType = 'fruits_vegetables'; // User's choice
```

### **3. Pass to Backend**
Include in character generation request

### **4. Update Image Prompt**
Use selected type directly (no AI guessing)

---

## 🎯 **Benefits:**

✅ **No more human faces** - User knows they're getting a tomato
✅ **Better control** - User gets what they expect  
✅ **Clearer prompts** - We know exactly what to generate
✅ **Better UX** - User makes conscious choice
✅ **More engaging** - User invests in their choice

---

## 📝 **Next Steps:**

1. Create character type selection page HTML
2. Add to script.js flow
3. Store selection in localStorage
4. Pass to generate-character.php
5. Test all 5 types

---

**Ready to implement?** 🚀
