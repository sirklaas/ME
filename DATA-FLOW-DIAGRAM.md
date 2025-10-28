# 📊 DATA FLOW DIAGRAM
**Complete breakdown of what we send to OpenAI and Leonardo.ai**

---

## 🤖 **Q1: WHAT DO WE SEND TO OPENAI?**

### **Input to OpenAI:**

```
┌─────────────────────────────────────────────────────────────┐
│                    OPENAI API CALL                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  SYSTEM PROMPT:                                             │
│  "You are creating diverse, creative character              │
│   descriptions for a workplace game show.                   │
│   CRITICAL RULES:                                           │
│   1) Characters MUST be actual animals/fruits/etc           │
│   2) Pick ONE specific option from the list                 │
│   3) NO MASKS - character IS the animal/fruit               │
│   4) Characters wear clothes and have personality           │
│   5) NEVER mention masks                                    │
│   Write in Dutch."                                          │
│                                                             │
│  USER PROMPT:                                               │
│  ┌──────────────────────────────────────────────┐          │
│  │ CHARACTER TYPE: fruits_vegetables             │          │
│  │                                               │          │
│  │ VERPLICHT: Kies EEN groente/fruit uit lijst: │          │
│  │ Tomaat, Banaan, Wortel, Aardbei, Appel...    │          │
│  │ (80 options total)                            │          │
│  │                                               │          │
│  │ SPECIAL INSTRUCTIONS (for fruits/veg):       │          │
│  │ - MUST have expressive eyes                  │          │
│  │ - MUST have mouth                             │          │
│  │ - MUST have arms                              │          │
│  │ - MUST have legs                              │          │
│  │ - Think Pixar style!                          │          │
│  │                                               │          │
│  │ CREATE 2 SECTIONS:                            │          │
│  │                                               │          │
│  │ 1. KARAKTER (100-150 words):                  │          │
│  │    - Begin: "De [TYPE] genaamd [NAME]"       │          │
│  │    - Describe eyes, mouth, arms, legs        │          │
│  │    - Describe clothing                        │          │
│  │    - Describe personality                     │          │
│  │                                               │          │
│  │ 2. OMGEVING (30-50 words):                    │          │
│  │    - ONE SPECIFIC LOCATION                    │          │
│  │    - Simple and concrete                      │          │
│  │    - Example: "een zonnige tuin"             │          │
│  │                                               │          │
│  │ PLAYER ANSWERS:                               │          │
│  │ ┌─────────────────────────────────┐          │          │
│  │ │ Q1: Gender: Vrouw                │          │          │
│  │ │ Q2: Relationship: Getrouwd       │          │          │
│  │ │ Q3: Imagination: heel veel       │          │          │
│  │ │ Q4: Age: 20-40 jaar              │          │          │
│  │ │ Q5: Company tenure: 1-3 jaar     │          │          │
│  │ │ Q6: Department: Marketing        │          │          │
│  │ │ Q7: Character type: Groente/Fruit│          │          │
│  │ │ Q8-40: All other answers...      │          │          │
│  │ └─────────────────────────────────┘          │          │
│  │                                               │          │
│  │ PERSONALITY ANALYSIS:                         │          │
│  │ - Creativity: 8                               │          │
│  │ - Humor: 7                                    │          │
│  │ - Adventure: 6                                │          │
│  │ - etc...                                      │          │
│  └──────────────────────────────────────────────┘          │
│                                                             │
│  MAX TOKENS: 600                                            │
└─────────────────────────────────────────────────────────────┘
```

---

### **Output from OpenAI:**

```
┌─────────────────────────────────────────────────────────────┐
│                    OPENAI RESPONSE                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. KARAKTER:                                               │
│  De Tomaat genaamd Paarse Paradox is een tastbare paradox  │
│  op zichzelf. Hij is gehuld in een diep paars gewaad, dat  │
│  zijn mysterieuze uitstraling benadrukt en de normen van   │
│  zijn soort uitdaagt. Paarse Paradox is een observerende   │
│  dromer, flexibel en in staat om zich aan te passen aan    │
│  verschillende situaties, net als een kameleon. In zijn    │
│  wereld is hij een kunstenaar, met zijn penseel creëert    │
│  hij meesterwerken die zijn dynamische aard                │
│  weerspiegelen. Zijn gezicht, glad en rond, draagt altijd  │
│  een uitdrukking van diepe overpeinzing en regelmatig      │
│  verandert zijn kleur, variërend van diep paars tot een    │
│  helderder rood, afhankelijk van zijn stemming.            │
│                                                             │
│  2. OMGEVING:                                               │
│  Paarse Paradox bevindt zich in een wereld vol kleuren en  │
│  fantasie, ergens tussen de sterren en de maan en de       │
│  aardse realiteit. Hier vind je een mengeling van          │
│  felgekleurde planten en dromerige landschappen,           │
│  onbegrensde ruimtes waar de tijd lijkt te bevriezen en    │
│  de zwaartekracht haar grip verliest.                      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

### **What We Extract from OpenAI Response:**

```php
// 1. Extract Character Name
$characterName = "Paarse Paradox"  // Using regex patterns

// 2. Extract KARAKTER section (first 150 chars)
$karakterText = "De Tomaat genaamd Paarse Paradox is een tastbare 
                 paradox op zichzelf. Hij is gehuld in een diep 
                 paars gewaad, dat zijn mysterieuze..."

// 3. Extract OMGEVING section (first 80 chars)
$environmentText = "Paarse Paradox bevindt zich in een wereld vol 
                    kleuren en fantasie, ergens..."
```

---

## 🎨 **Q2: WHAT DO WE SEND TO LEONARDO.AI?**

### **Input to Leonardo.ai:**

```
┌─────────────────────────────────────────────────────────────┐
│                  LEONARDO.AI API CALL                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  PROMPT (< 1500 characters):                                │
│  ┌──────────────────────────────────────────────┐          │
│  │ Full body portrait of anthropomorphic        │          │
│  │ fruit/vegetable with cartoon face, eyes,     │          │
│  │ mouth, arms, legs, wearing clothes. Pixar    │          │
│  │ style, NOT human named Paarse Paradox.       │          │
│  │                                               │          │
│  │ De Tomaat genaamd Paarse Paradox is een      │          │
│  │ tastbare paradox op zichzelf. Hij is gehuld  │          │
│  │ in een diep paars gewaad, dat zijn           │          │
│  │ mysterieuze uitstraling benadrukt...         │          │
│  │ (150 chars max)                               │          │
│  │                                               │          │
│  │ Setting: Paarse Paradox bevindt zich in een  │          │
│  │ wereld vol kleuren en fantasie...            │          │
│  │ (80 chars max)                                │          │
│  │                                               │          │
│  │ 16:9 widescreen, 4K, cinematic lighting,     │          │
│  │ full body, rule of thirds.                   │          │
│  └──────────────────────────────────────────────┘          │
│                                                             │
│  NEGATIVE PROMPT:                                           │
│  human face, realistic human, person, man, woman,           │
│  human body, human skin, close-up, portrait only,           │
│  headshot, cropped body, blurry, low quality,               │
│  pixelated, distorted, amateur, poorly lit,                 │
│  deformed, ugly, bad anatomy, extra limbs,                  │
│  missing limbs, floating limbs, disconnected limbs,         │
│  malformed hands, long neck, duplicate, mutated,            │
│  mutilated, out of frame, extra fingers,                    │
│  mutated hands, poorly drawn hands,                         │
│  poorly drawn face, mutation, deformed,                     │
│  bad proportions, gross proportions, watermark,             │
│  signature, text, logo, no character,                       │
│  empty scene, landscape only                                │
│                                                             │
│  SETTINGS:                                                  │
│  - Model: Phoenix (b24e16ff-06e3-43eb-8d33-4416c2d75876)   │
│  - Width: 1472 (16:9 ratio)                                │
│  - Height: 832                                              │
│  - Num Images: 1                                            │
│  - Guidance Scale: 7                                        │
│  - Inference Steps: 30                                      │
│  - Preset Style: CINEMATIC                                  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

### **Output from Leonardo.ai:**

```
┌─────────────────────────────────────────────────────────────┐
│                  LEONARDO.AI RESPONSE                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Generation ID: "abc123..."                                 │
│                                                             │
│  Status: "PENDING" → "COMPLETE" (after polling)            │
│                                                             │
│  Image URL: "https://cdn.leonardo.ai/users/..."            │
│                                                             │
│  Image Data: Base64 encoded image                           │
│                                                             │
│  Format: PNG                                                │
│  Dimensions: 1472 x 832 (16:9)                             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 📊 **COMPLETE DATA FLOW:**

```
USER ANSWERS (Questions 1-40)
        ↓
┌───────────────────────────────────────────────────────────┐
│                  FRONTEND (script.js)                     │
├───────────────────────────────────────────────────────────┤
│  • Collects all answers                                   │
│  • Extracts characterType from Q7                         │
│  • Extracts department from Q6                            │
│  • Sends to backend                                       │
└───────────────────────────────────────────────────────────┘
        ↓
┌───────────────────────────────────────────────────────────┐
│            BACKEND (generate-character.php)               │
├───────────────────────────────────────────────────────────┤
│  STEP 1: Analyze personality from answers                │
│  STEP 2: Format answers for AI                           │
│  STEP 3: Call OpenAI                                      │
│          ↓                                                │
│     ┌─────────────────────────────────────┐              │
│     │        OPENAI API                   │              │
│     │  Input: System + User Prompt        │              │
│     │  Output: Character description      │              │
│     │         (KARAKTER + OMGEVING)       │              │
│     └─────────────────────────────────────┘              │
│          ↓                                                │
│  STEP 4: Extract character name                          │
│  STEP 5: Extract KARAKTER text (150 chars)               │
│  STEP 6: Extract OMGEVING text (80 chars)                │
│  STEP 7: Build image prompt (< 1500 chars)               │
│  STEP 8: Return to frontend                              │
└───────────────────────────────────────────────────────────┘
        ↓
┌───────────────────────────────────────────────────────────┐
│                  FRONTEND (script.js)                     │
├───────────────────────────────────────────────────────────┤
│  • Receives character data                                │
│  • Sends Email 1 (character description)                  │
│  • Calls image generation                                 │
└───────────────────────────────────────────────────────────┘
        ↓
┌───────────────────────────────────────────────────────────┐
│        BACKEND (generate-image-leonardo.php)              │
├───────────────────────────────────────────────────────────┤
│  STEP 1: Receive image prompt                            │
│  STEP 2: Call Leonardo.ai API                            │
│          ↓                                                │
│     ┌─────────────────────────────────────┐              │
│     │      LEONARDO.AI API                │              │
│     │  Input: Prompt + Settings           │              │
│     │  Process: Generate image (30 steps) │              │
│     │  Output: Image URL + Base64 data    │              │
│     └─────────────────────────────────────┘              │
│          ↓                                                │
│  STEP 3: Return image data                               │
└───────────────────────────────────────────────────────────┘
        ↓
┌───────────────────────────────────────────────────────────┐
│                  FRONTEND (script.js)                     │
├───────────────────────────────────────────────────────────┤
│  • Receives image data                                    │
│  • Uploads to PocketBase                                  │
│  • Sends Email 2 (with image)                             │
│  • Shows completion page                                  │
└───────────────────────────────────────────────────────────┘
```

---

## 📝 **EXAMPLE DATA:**

### **OpenAI Input Example:**
```
CHARACTER TYPE: fruits_vegetables

VERPLICHT: Kies EEN groente/fruit uit deze lijst:
Tomaat, Banaan, Wortel, Aardbei, Appel, Sinaasappel...

Player Answers:
Q1: Vrouw
Q2: Getrouwd
Q3: heel veel fantasie
Q4: 20-40 jaar
Q5: 1-3 jaar
Q6: Marketing
Q7: Groente of Fruit
...

Personality:
Creativity: 8
Humor: 7
Adventure: 6
```

### **OpenAI Output Example:**
```
1. KARAKTER:
De Tomaat genaamd Paarse Paradox is een tastbare paradox...
(~150 words)

2. OMGEVING:
Paarse Paradox bevindt zich in een wereld vol kleuren...
(~50 words)
```

### **Leonardo.ai Input Example:**
```
Full body portrait of anthropomorphic fruit/vegetable with 
cartoon face, eyes, mouth, arms, legs, wearing clothes. 
Pixar style, NOT human named Paarse Paradox. 

De Tomaat genaamd Paarse Paradox is een tastbare paradox 
op zichzelf. Hij is gehuld in een diep paars gewaad... 

Setting: Paarse Paradox bevindt zich in een wereld vol 
kleuren en fantasie... 

16:9 widescreen, 4K, cinematic lighting, full body, 
rule of thirds.
```

### **Leonardo.ai Output Example:**
```
{
  "success": true,
  "image_url": "https://cdn.leonardo.ai/users/...",
  "image_data": "iVBORw0KGgoAAAANSUhEUgAA..."
}
```

---

## 💰 **COSTS:**

### **OpenAI:**
- **Model:** GPT-4
- **Tokens per request:** ~600 tokens
- **Cost:** ~$0.01 per character

### **Leonardo.ai:**
- **Model:** Phoenix
- **Tokens per image:** ~8 tokens (30 steps)
- **Free tier:** 150 tokens/day = ~18 images/day

---

## 🔍 **KEY DIFFERENCES:**

| Aspect | OpenAI | Leonardo.ai |
|--------|--------|-------------|
| **Input** | Text prompts + answers | Short image prompt |
| **Output** | Text description | Image (PNG) |
| **Length** | ~600 tokens | < 1500 chars |
| **Purpose** | Create character story | Visualize character |
| **Cost** | $0.01/request | 8 tokens/image |

---

*This document explains the complete data flow from user input to final image generation.*
