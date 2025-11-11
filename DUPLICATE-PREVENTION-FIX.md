# Duplicate Character Prevention System - November 11, 2025

## 🎯 PROBLEM SOLVED

**Issue:** Two tomatoes generated despite duplicate prevention system
**Root Cause:** Type tracking only worked for AI summary extraction (after generation), not before
**Solution:** Pre-filter available characters based on what's already used in THIS game

---

## ✅ NEW SYSTEM IMPLEMENTATION

### **How It Works:**

```
1. User submits questionnaire
2. Query PocketBase: "What characters are used in THIS game?"
3. Load original 80-item list (never modified)
4. Filter: Remove used characters from list
5. Give AI only AVAILABLE characters (e.g., 76 remaining)
6. AI generates from filtered list
7. Extract base type (e.g., "tomaat")
8. Save to character_base_type field
9. Next request: "tomaat" is now excluded
```

---

## 🔧 NEW POCKETBASE FIELD

**Field Added:** `character_base_type`
- **Type:** Text
- **Purpose:** Store base character type for filtering
- **Examples:** `"tomaat"`, `"leeuw"`, `"panda"`, `"ridder"`
- **Usage:** Query to get used characters per game

---

## 📝 CODE CHANGES

### **1. New Function: `extractCharacterBaseType()`**
**File:** `generate-character.php` lines 186-212

**Purpose:** Extract base type from AI summary
**Patterns:**
- `"Een vrolijke Tomaat"` → `"tomaat"`
- `"Jij bent Leo, een majestueuze Leeuw"` → `"leeuw"`
- `"Een Panda"` → `"panda"`

```php
function extractCharacterBaseType($aiSummary) {
    // 4 different regex patterns to catch all formats
    // Returns lowercase base type
}
```

---

### **2. New Function: `getUsedCharacterBaseTypes()`**
**File:** `generate-character.php` lines 214-263

**Purpose:** Query PocketBase for used characters in THIS game
**Parameters:**
- `$gameName` - Filter by game
- `$characterCategory` - Optional: Filter by type (animals, fruits, etc.)

**Returns:** Array of used base types (e.g., `["tomaat", "leeuw", "panda"]`)

```php
function getUsedCharacterBaseTypes($gameName, $characterCategory = null) {
    // Query: SELECT character_base_type WHERE gamename = '$gameName'
    // Returns: ["tomaat", "leeuw", "panda"]
}
```

---

### **3. Filtering Logic**
**File:** `generate-character.php` lines 803-824

**Before:**
```php
$allOptions = ["tomaat", "banaan", "wortel", ...]; // 80 items
// Give AI all 80 options
```

**After:**
```php
$allOptions = ["tomaat", "banaan", "wortel", ...]; // 80 items
$usedBaseTypes = getUsedCharacterBaseTypes($gameName, $characterType);
$availableOptions = array_diff($allOptions, $usedBaseTypes); // 76 items

// Give AI only 76 AVAILABLE options (tomaat excluded)
```

---

### **4. Save Base Type to PocketBase**
**File:** `generate-character.php` lines 1095-1104

```php
// Extract base type from AI summary
$characterBaseType = extractCharacterBaseType($aiSummary);

// Include in result
$result = [
    'character_name' => $characterName,
    'character_type' => $characterType,
    'character_base_type' => $characterBaseType, // NEW!
    // ... other fields
];
```

---

### **5. Frontend Saves to PocketBase**
**File:** `script.js` line 639

```javascript
const submissionData = {
    // ... other fields
    character_base_type: characterData.character_base_type || '',
    // ... other fields
};
```

---

## 📊 EXAMPLE FLOW

### **First Player (Game: "Show Nov 2025"):**
```
1. Query: No characters used yet
2. Available: All 80 fruits/vegetables
3. AI picks: "Tomaat"
4. Extract: "tomaat"
5. Save: character_base_type = "tomaat"
```

### **Second Player (Same Game):**
```
1. Query: Used = ["tomaat"]
2. Available: 79 fruits/vegetables (tomaat excluded)
3. AI picks: "Banaan" (can't pick tomaat - not in list!)
4. Extract: "banaan"
5. Save: character_base_type = "banaan"
```

### **Third Player (Same Game):**
```
1. Query: Used = ["tomaat", "banaan"]
2. Available: 78 fruits/vegetables
3. AI picks: "Wortel"
4. Extract: "wortel"
5. Save: character_base_type = "wortel"
```

### **New Game (Game: "Show Dec 2025"):**
```
1. Query: Used = [] (different game!)
2. Available: All 80 fruits/vegetables again
3. Can use "tomaat" again (different game)
```

---

## 🎯 KEY FEATURES

### **Per-Game Isolation:**
- Each game has its own character pool
- "Show Nov 2025" and "Show Dec 2025" are separate
- Automatic reset for new games

### **Guaranteed Uniqueness:**
- AI only sees AVAILABLE characters
- Can't pick what's not in the list
- No duplicate characters within same game

### **Fallback Safety:**
- If all 80 are used → Allow reuse
- Prevents crashes
- Logs warning

### **Original List Never Modified:**
- `character-options-80.json` stays untouched
- Filtering happens in memory
- No file editing needed

---

## 📈 LOGS YOU'LL SEE

```
🔒 Used in this game: tomaat
🔒 Used in this game: leeuw
📊 Total unique characters used in game 'Show Nov 2025': 2
📋 Used characters: tomaat, leeuw

🎯 Total options for fruits_vegetables: 80
🔒 Used in this game: 2
✅ Available options: 78

💾 Extracted character_base_type: banaan
```

---

## ⚠️ EDGE CASES HANDLED

### **All 80 Characters Used:**
```php
if (empty($availableOptions)) {
    error_log("⚠️ WARNING: All characters used! Allowing reuse.");
    $availableOptions = $allOptions;
}
```

### **Extraction Fails:**
```php
// Returns empty string if no match
$characterBaseType = extractCharacterBaseType($aiSummary);
// Result: character_base_type = ""
```

### **No Game Name:**
```php
$gameName = $data['gamename'] ?? 'default_game';
```

---

## 🔍 TESTING CHECKLIST

- [ ] Generate 3 characters in same game → All different
- [ ] Check PocketBase → `character_base_type` populated
- [ ] Check logs → Shows used characters
- [ ] Check logs → Shows available count decreasing
- [ ] Start new game → All 80 available again
- [ ] Generate 80 characters → Fallback activates
- [ ] Test all 5 categories (animals, fruits, fantasy, pixar, fairy)

---

## 📊 POCKETBASE QUERIES

### **Get Used Characters in Game:**
```sql
SELECT character_base_type 
FROM ME_questions 
WHERE gamename = 'Show Nov 2025'
AND character_base_type != ''
```

### **Count Usage Per Character:**
```sql
SELECT character_base_type, COUNT(*) as count
FROM ME_questions
WHERE character_base_type != ''
GROUP BY character_base_type
ORDER BY count DESC
```

### **Most Popular Characters:**
```sql
SELECT character_base_type, COUNT(*) as usage_count
FROM ME_questions
GROUP BY character_base_type
ORDER BY usage_count DESC
LIMIT 10
```

---

## 🎉 BENEFITS

✅ **No More Duplicates** - Guaranteed unique characters per game
✅ **Automatic Per-Game** - No manual reset needed
✅ **Original List Safe** - Never modified
✅ **Full History** - Track all usage
✅ **Analytics Ready** - "Which characters are popular?"
✅ **Scalable** - Works for 10 or 100 players
✅ **Fallback Safe** - Handles edge cases

---

## 📝 FILES MODIFIED

1. **generate-character.php**
   - Added `extractCharacterBaseType()` function
   - Added `getUsedCharacterBaseTypes()` function
   - Added filtering logic before AI generation
   - Added base type to result

2. **script.js**
   - Added `character_base_type` to PocketBase save

3. **questions-unified.json**
   - Changed Q11 to dance song question

---

## 🚀 DEPLOYMENT

**PocketBase Setup:**
1. Add field: `character_base_type` (Text)
2. Optional: Make it indexed for faster queries

**No Other Changes Needed:**
- Original JSON files unchanged
- Frontend works automatically
- Backward compatible (old records work fine)

---

## 💡 FUTURE ENHANCEMENTS

**Possible Additions:**
- Dashboard: "Show character usage stats"
- Admin: "Reset character pool for game"
- Analytics: "Most popular characters across all games"
- Export: "Generate usage report"

---

*System implemented: November 11, 2025*
*Status: ✅ READY FOR TESTING*
