# AI & Internationalization Recommendations
## Masked Employee Project

**Date:** 2025-10-04  
**Status:** Planning Phase

---

## 📊 Questions.JSON Analysis for AI Prompting

### ✅ Overall Assessment: **EXCELLENT for AI Character Generation**

Your 40-question structure is well-designed for creating rich, unique AI-generated characters. Here's the breakdown:

---

## 🎯 Question Quality by Chapter

### Chapter 1: Introductie (Questions 1-5) - **Basic Demographics**
**AI Value:** ⭐⭐⭐ Moderate
- Gender, relationship status, children, age, tenure
- **Good for:** Contextual character grounding
- **Limitation:** Multiple choice doesn't provide rich narrative detail
- **Recommendation:** ✅ Keep as-is for filtering/categorization

### Chapter 2: Masked Identity (Questions 6-10) - **Character Foundation**
**AI Value:** ⭐⭐⭐⭐⭐ EXCELLENT
- Animal representation (Q6)
- Costume color & symbolism (Q7)
- Natural element (Q8)
- Mask design concept (Q9)
- Musical entrance style (Q10)

**Why it works:**
- ✅ Open-ended text responses allow creative expression
- ✅ Directly asks for symbolic/metaphorical thinking
- ✅ Perfect for generating visual character concepts
- ✅ Natural fit for image generation prompts

**Example AI Prompt Output:**
```
"A mysterious character embodied by the wolf - loyal yet independent. 
Dressed in deep midnight blue symbolizing wisdom and depth. Their essence 
is fire - passionate and transformative. Their mask features constellation 
patterns, representing guidance through darkness. Epic orchestral music 
announces their arrival..."
```

### Chapter 3: Persoonlijke Eigenschappen (Questions 11-16) - **Personality Depth**
**AI Value:** ⭐⭐⭐⭐⭐ EXCELLENT
- Superpower (Q11)
- Bizarre fear (Q12)
- Ideal dinner guest (Q13)
- Dinner conversation topic (Q14)
- Netflix series title for their life (Q15)
- Most rebellious act (Q16)

**Why it works:**
- ✅ Reveals authentic personality traits
- ✅ Shows vulnerability (fears) and aspirations (superpower)
- ✅ Cultural references create relatable character hooks
- ✅ "Netflix title" is brilliant - forces creative self-summary

### Chapter 4: Verborgen Talenten (Questions 17-21) - **Hidden Dimensions**
**AI Value:** ⭐⭐⭐⭐⭐ EXCELLENT
- Secret talent (Q17)
- Unusual hobby/collection (Q18)
- Unexpected musical ability (Q19)
- Past sport that doesn't fit image (Q20)
- Creative outlet (Q21)

**Why it works:**
- ✅ Perfect for "surprise reveal" character traits
- ✅ Creates depth and contradiction (essential for interesting characters)
- ✅ Great for video story prompts about hidden sides

### Chapter 5: Jeugd & Verleden (Questions 22-26) - **Origin Story**
**AI Value:** ⭐⭐⭐⭐ Very Good
- Childhood dream career (Q22)
- Favorite school subjects (Q23)
- Embarrassing but funny memory (Q24)
- Missed teenage trend (Q25)
- Advice to 16-year-old self (Q26)

**Why it works:**
- ✅ Provides character backstory
- ✅ Shows evolution and growth
- ✅ Q26 reveals current wisdom/values

**Minor Gap:** Could add one question about formative childhood experience

### Chapter 6: Fantasie & Dromen (Questions 27-31) - **Aspirational Self**
**AI Value:** ⭐⭐⭐⭐⭐ EXCELLENT
- Fictional world to live in (Q27)
- First purchase with unlimited money (Q28)
- Fascinating historical period (Q29)
- Dream restaurant concept (Q30)
- Bucket list destination (Q31)

**Why it works:**
- ✅ Shows aspirations and values through choices
- ✅ Q27 is gold for environment generation
- ✅ Q30 (restaurant) reveals taste, style, creativity
- ✅ Great for generating "ideal environment" visuals

### Chapter 7: Eigenaardigheden (Questions 32-36) - **Quirks & Authenticity**
**AI Value:** ⭐⭐⭐⭐ Very Good
- Strange habit (Q32)
- Superstitions/rituals (Q33)
- Weird food combo (Q34)
- Most productive time (Q35)
- Unusual stress relief (Q36)

**Why it works:**
- ✅ Humanizes the character with relatable quirks
- ✅ Q34 (food) is surprisingly revealing of personality
- ✅ Creates memorable, specific character details

### Chapter 8: Onverwachte Voorkeuren (Questions 37-40) - **Guilty Pleasures**
**AI Value:** ⭐⭐⭐⭐ Very Good
- Secret music genre (Q37)
- Guilty pleasure TV (Q38)
- Hypothetical tattoo (Q39)
- Personality color (Q40)

**Why it works:**
- ✅ Q39 (tattoo) is excellent for visual symbolism
- ✅ Q40 (color) directly feeds into environment/costume design
- ✅ Reveals authentic self vs. presented self

---

## 🎨 AI Generation Coverage Assessment

### ✅ Your Questions Cover:

| AI Output Needed | Coverage | Quality | Notes |
|-----------------|----------|---------|-------|
| **Character Description** | 95% | ⭐⭐⭐⭐⭐ | Chapters 2, 3, 4 are perfect |
| **Visual Appearance** | 90% | ⭐⭐⭐⭐⭐ | Q6-10, Q39-40 excellent for visuals |
| **Environment Design** | 85% | ⭐⭐⭐⭐ | Q27, Q30, Q31 good; could add "ideal workspace" |
| **Props/Signature Items** | 70% | ⭐⭐⭐⭐ | Q18, Q39 work; **ADD:** "3 items you'd bring to desert island" |
| **Personality Depth** | 95% | ⭐⭐⭐⭐⭐ | Exceptional coverage |
| **Backstory** | 80% | ⭐⭐⭐⭐ | Good but could be deeper |
| **Video Story Prompts** | 90% | ⭐⭐⭐⭐⭐ | Chapters 4, 5, 7 provide rich material |

---

## 💡 Recommended Question Additions (Optional)

To make AI generation even richer, consider adding:

**For Props Generation:**
```json
{
  "id": 41,
  "type": "text",
  "question": "Als je drie voorwerpen naar een onbewoond eiland zou brengen, welke zouden dat zijn?",
  "placeholder": "Noem de voorwerpen en leg uit waarom..."
}
```

**For Environment Detail:**
```json
{
  "id": 42,
  "type": "text",
  "question": "Beschrijf je ideale werkplek of creatieve ruimte - binnen of buiten, dag of nacht, stil of levendig?",
  "placeholder": "Beschrijf de sfeer en omgeving..."
}
```

**For Deeper Backstory (Video Stories):**
```json
{
  "id": 43,
  "type": "text",
  "question": "Wat is het moedigste dat je ooit hebt gedaan?",
  "placeholder": "Vertel over een moment van moed..."
}
```

---

## 🤖 Best AI Services (October 2024)

### For Text Generation (Character Descriptions)

#### **1. OpenAI GPT-4 Turbo** ⭐ RECOMMENDED
- **Quality:** ⭐⭐⭐⭐⭐ Best creative writing
- **Cost:** ~$0.01 per character (very affordable)
- **Speed:** 2-5 seconds per generation
- **API:** Easy integration
- **Best for:** Character descriptions, story prompts, creative narratives
- **Pricing:** https://openai.com/pricing
  - Input: $0.01 / 1K tokens
  - Output: $0.03 / 1K tokens
  - **Est. cost per person:** $0.15-0.25 (all 6 generations)

**Why GPT-4 for this project:**
- ✅ Exceptional at creative, narrative storytelling
- ✅ Understands nuance and metaphor
- ✅ Can generate in both Dutch AND English
- ✅ Consistent quality across generations
- ✅ Mature API with good documentation

#### **2. Anthropic Claude 3.5 Sonnet** ⭐ Alternative
- **Quality:** ⭐⭐⭐⭐⭐ Equally excellent
- **Cost:** Similar to GPT-4
- **Advantage:** Longer context window (200K tokens)
- **Best for:** Complex character analysis, nuanced descriptions
- **Note:** Slightly more formal/literary style than GPT-4

#### **3. Local Options (Not Recommended for Production)**
- Llama 3 70B - Free but requires GPU server
- Mistral Large - Good but less creative than GPT-4/Claude

---

### For Image Generation

#### **FREE Options:**

**1. Stable Diffusion XL (via Hugging Face Inference API)** ⭐ BEST FREE
- **Quality:** ⭐⭐⭐⭐ Very good
- **Cost:** FREE (with rate limits)
- **Speed:** 5-15 seconds
- **API:** https://huggingface.co/docs/api-inference/
- **Best for:** Character portraits, masked figures, fantasy art
- **Limitations:** 
  - Rate limited (1-2 images per minute free tier)
  - Requires API token (free signup)
  - Less consistent style than paid options

**Setup:**
```javascript
// Free Hugging Face Inference API
const response = await fetch(
  "https://api-inference.huggingface.co/models/stabilityai/stable-diffusion-xl-base-1.0",
  {
    headers: { Authorization: "Bearer YOUR_HF_TOKEN" },
    method: "POST",
    body: JSON.stringify({
      inputs: "Your character prompt here...",
    }),
  }
);
```

**2. Leonardo.AI** ⭐ FREE TIER AVAILABLE
- **Quality:** ⭐⭐⭐⭐⭐ Excellent
- **Cost:** 150 FREE tokens/day (~30 images)
- **Website:** https://leonardo.ai
- **Best for:** Consistent character design, professional quality
- **Note:** Has API for automation

**3. Playground AI** ⭐ FREE TIER
- **Quality:** ⭐⭐⭐⭐ Good
- **Cost:** 50 images/day free
- **Website:** https://playgroundai.com
- **Best for:** Quick experimentation

**4. Craiyon (formerly DALL-E mini)** 
- **Quality:** ⭐⭐⭐ Basic
- **Cost:** FREE unlimited
- **Best for:** Testing concepts only
- **Not recommended for production** (lower quality)

---

#### **PAID Options (Better Quality):**

**1. DALL-E 3 (via OpenAI)** ⭐ RECOMMENDED for Production
- **Quality:** ⭐⭐⭐⭐⭐ Best consistency
- **Cost:** $0.040 per 1024×1024 image
- **Speed:** 10-20 seconds
- **Best for:** High-quality character portraits in costume/environment
- **Advantage:** 
  - Understands complex prompts
  - Integrates with GPT-4 API
  - Good at text-to-image from descriptions
  - **Can generate character IN environment together**

**Est. cost per person:** $0.08-0.12 (1-2 images)

**2. Midjourney** ⭐ HIGHEST QUALITY
- **Quality:** ⭐⭐⭐⭐⭐ Best artistic style
- **Cost:** $10/month (200 images)
- **Speed:** 30-60 seconds
- **Limitation:** No direct API (Discord-based)
- **Best for:** Artistic character portraits
- **Not ideal for automation** (manual Discord workflow)

**3. Stable Diffusion API (DreamStudio)**
- **Quality:** ⭐⭐⭐⭐ Very Good
- **Cost:** $10 = 5,000 credits (~500 images)
- **Speed:** Fast (2-5 seconds)
- **Best for:** High volume, budget-conscious

**4. Banana.dev (Serverless GPU)** ⭐ GREAT VALUE
- **Quality:** ⭐⭐⭐⭐⭐ Excellent (runs any model)
- **Cost:** Pay-per-second GPU usage (~$0.0005-0.002 per image)
- **Speed:** 2-8 seconds depending on model
- **Website:** https://banana.dev
- **Best for:** 
  - Cost-effective at scale
  - Custom model deployment (SDXL, Flux, etc.)
  - Serverless - no infrastructure management
- **Pricing:** ~$0.001-0.003 per image (much cheaper than DALL-E 3)
- **API:** Clean REST API, easy integration
- **Models Available:** 
  - Stable Diffusion XL
  - Flux.1
  - Custom fine-tuned models
  
**Why Banana.dev for Masked Employee:**
- ✅ **Extremely cost-effective** - ~$0.15-0.45 for 150 people (vs $6-12 with DALL-E)
- ✅ **High quality** - Can run SDXL or Flux.1
- ✅ **Fast** - Cold start ~3-5s, warm ~2s
- ✅ **Scalable** - Serverless autoscaling
- ✅ **Full control** - Deploy custom models if needed
- ✅ **No rate limits** like free tiers

**Estimated Cost for 150 People:**
- 2 images per person = 300 images
- @ $0.001-0.003 per image = **$0.30-0.90 total** 🎉

---

**5. Nano Banana (Mobile App - Gemini-based)**
- **Type:** Mobile app (iOS/Android) - NOT an API service
- **Quality:** ⭐⭐⭐ Basic to Good
- **Cost:** Freemium model (likely in-app purchases)
- **Technology:** Google Gemini AI image generation
- **Best for:** Consumer photo editing and personal use
- **API Availability:** ❌ None - Mobile app only
- **For Masked Employee:** ⚠️ **NOT SUITABLE**
  - No API for automation
  - Consumer-focused, not enterprise
  - Would require manual image generation (not scalable for 150 people)
  - Cannot integrate with your questionnaire system

**Note:** While "Nano Banana" sounds relevant, it's a consumer photo editing app, not a developer platform. For automated character generation at scale, you need API-based services like Banana.dev, DALL-E 3, or Stable Diffusion services.

---

**6. Freepik API** ⭐⭐⭐ EXCELLENT VALUE + AUTOMATION
- **Quality:** ⭐⭐⭐⭐⭐ Excellent (Flux.1, SDXL models)
- **Cost:** Very competitive - Credits-based pricing (~$0.002-0.005 per image)
- **Speed:** 3-8 seconds per image
- **Website:** https://www.freepik.com/api
- **API:** ✅ Full REST API with excellent documentation
- **Models Available:**
  - Flux.1 (state-of-the-art)
  - Stable Diffusion XL
  - Freepik's fine-tuned models
- **Pricing Plans:**
  - Pay-as-you-go with credits
  - Monthly subscriptions available
  - ~100-500 images for $10-20 depending on plan

**Why Freepik API is Great:**
- ✅ **Cost-effective** - Similar to Banana.dev (~$0.60-1.50 for 150 people)
- ✅ **High quality** - Uses latest models (Flux.1, SDXL)
- ✅ **Excellent automation support** - Works great with n8n, Zapier, Make
- ✅ **Google Workspace integration** - Can connect to Sheets/Drive easily
- ✅ **Bulk generation friendly** - Built for volume
- ✅ **No cold starts** - Always fast
- ✅ **Good documentation** - Easy to implement

**Automation Workflow Suggestion (n8n):**
```
PocketBase → n8n → Freepik API → Google Drive → PocketBase
    ↓
1. Trigger: New submission in PocketBase
2. n8n: Format prompt from questionnaire answers
3. Freepik API: Generate character image
4. Google Drive: Store image with submission ID
5. PocketBase: Update record with image URL
6. (Optional) Email: Send character to user
```

**Estimated Cost for 150 People:**
- 2 images per person = 300 images
- @ $0.002-0.005 per image = **$0.60-1.50 total** 🎉
- Similar to Banana.dev, potentially even cheaper with subscriptions

**Advantages over other options:**
- More polished API than Banana.dev
- Easier automation with n8n/Zapier than DALL-E
- Better documentation than Hugging Face
- Built specifically for bulk generation use cases
- Includes prompt enhancement (AI improves your prompts automatically)

---

## 🎯 Recommended AI Stack for Masked Employee

### **Option A: Freepik API + n8n Automation** ⭐⭐⭐ TOP CHOICE
```
Text: GPT-4 Turbo
Images: Freepik API (Flux.1 or SDXL)
Automation: n8n workflow
Storage: Google Drive (optional)
Cost per person: ~$0.21 total ($30 text + $1.50 images for 150 people)
Total for 150: ~$31.50
Advantage: Best automation, excellent quality, bulk-friendly, prompt enhancement
```

**Why This is Best:**
- ✅ Built-in n8n integration for hands-off automation
- ✅ Automatic prompt enhancement (AI improves your prompts)
- ✅ Google Sheets/Drive integration for easy management
- ✅ Professional support and documentation
- ✅ Designed specifically for bulk generation

### **Option B: Banana.dev (Developer-Focused)** ⭐⭐⭐ ALSO EXCELLENT
```
Text: GPT-4 Turbo
Images: Banana.dev (SDXL or Flux.1)
Cost per person: ~$0.20 total ($30 text + $0.50 images for 150 people)
Total for 150: ~$30.50
Advantage: Lowest cost, flexible custom models, serverless infrastructure
```

### **Option C: All OpenAI (Simplest)** ⭐⭐ SIMPLE
```
Text: GPT-4 Turbo
Images: DALL-E 3
Cost per person: ~$0.24 total
Total for 150: ~$36
Advantage: Single API, easiest integration, no additional tools needed
```

### **Option D: Free Tier Testing** ⭐ TESTING ONLY
```
Text: GPT-4 Turbo ($30)
Images: Leonardo.AI (150 free/day) or Hugging Face SDXL (free)
Total for 150: ~$30
Advantage: Minimal cost for testing
Limitation: Rate limits, manual workflow for images
```

---

## 🌍 Multi-Language Support Strategy

### Architecture for Dutch + English

#### **Option 1: Separate JSON Files** ⭐ RECOMMENDED
```
/ME/
├── Questions-NL.json    (Current Dutch version)
├── Questions-EN.json    (New English version)
├── gameshow-config-NL.json
├── gameshow-config-EN.json
├── questions.html?lang=nl
└── questions.html?lang=en
```

**Implementation:**
```javascript
// Detect language from URL or browser
const urlParams = new URLSearchParams(window.location.search);
const lang = urlParams.get('lang') || navigator.language.includes('nl') ? 'nl' : 'en';

// Load appropriate config
const config = await fetch(`gameshow-config-${lang}.json`);
const questions = await fetch(`Questions-${lang}.json`);
```

#### **Option 2: Single JSON with Language Keys**
```json
{
  "gameshow": {
    "title": {
      "nl": "🎭 THE MASKED EMPLOYEE 🎭",
      "en": "🎭 THE MASKED EMPLOYEE 🎭"
    },
    "welcome_title": {
      "nl": "ONTDEK DE MYSTERIEUZE HELD IN JEZELF!",
      "en": "DISCOVER THE MYSTERIOUS HERO WITHIN!"
    }
  }
}
```

#### **Option 3: Translation Service** (Future Scale)
- Use i18next library
- Separate translation files
- Runtime language switching
- Best for 3+ languages

---

## 🚀 Implementation Priority

### Phase 1: AI Text Generation (Week 1-2)
1. Set up OpenAI GPT-4 Turbo API
2. Create prompt templates for:
   - Character description
   - Environment description
   - Props list
   - 3 video story prompts
3. Test with real questionnaire data from PocketBase
4. Refine prompts based on output quality

### Phase 2: Image Generation (Week 2-3)
1. Test free options (SDXL, Leonardo.AI)
2. If quality sufficient → implement
3. If not → budget for DALL-E 3
4. Create image generation pipeline
5. Store images in PocketBase or file system

### Phase 3: English Version (Week 3-4)
1. Translate Questions.JSON to English
2. Create Questions-EN.json
3. Add language detection/switching
4. Test AI generation in both languages
5. Deploy bilingual version

---

## 📝 Next Steps

### Immediate Actions:
1. ✅ **Copied PRD.md, PLANNING.md, TASKS.md, README.md** to GitHubLocal/ME
2. **Sign up for OpenAI API** (if not already done)
3. **Create English version** of Questions.JSON
4. **Build AI generation script**
5. **Test with 5 sample questionnaires**

### This Week:
- Implement GPT-4 character generation
- Test with Dutch responses
- Create English translation
- Build admin view for generated characters

---

## 💰 Budget Summary (150 People)

### 🏆 TOP CHOICE: GPT-4 + Freepik API + n8n
- Text: GPT-4 Turbo (6 calls × $0.20): **$30 total**
- Images: Freepik API (300 images × $0.005): **$1.50 total**
- Automation: n8n (self-hosted free or cloud $20/month)
- **TOTAL FOR 150 PEOPLE: ~$31.50** 🎉
- **Best for:** Hands-off automation, Google Workspace integration

### 🥈 CHEAPEST: GPT-4 + Banana.dev
- Text: GPT-4 Turbo: **$30 total**
- Images: Banana.dev: **$0.50-0.90 total**
- **Total for 150 people:** ~$30.50-31
- **Best for:** Lowest cost, maximum flexibility

### 🥉 SIMPLEST: All OpenAI
- Text: GPT-4 Turbo: **$30 total**
- Images: DALL-E 3: **$6 total**
- **Total for 150 people:** ~$36
- **Best for:** Single API, easiest implementation

### Testing Option:
- Text: GPT-4 ($30)
- Images: Leonardo.AI or Hugging Face (Free with rate limits)
- **Total for 150 people:** ~$30
- **Best for:** Initial quality testing

### Recommended Approach:
1. **Best overall:** GPT-4 + Freepik API + n8n (~$31.50) - Full automation
2. **Cheapest:** GPT-4 + Banana.dev (~$30.50) - Manual integration
3. **Simplest:** GPT-4 + DALL-E 3 (~$36) - One API to manage
4. **Budget target:** $30-36 for professional quality

---

## 🎬 Conclusion

**Your Questions.JSON is excellent!** It provides rich, creative material for AI generation. The structure naturally creates:
- Vivid character descriptions
- Visual design elements
- Personality depth
- Story hooks for videos

**Recommended AI Stack:**
- **Text:** OpenAI GPT-4 Turbo (~$30 for 150 people)
- **Images:** Freepik API with Flux.1 (~$1.50 for 150 people) ⭐ TOP CHOICE
  - *Automation:* n8n workflow for hands-off processing
  - *Alternative:* Banana.dev for lowest cost (~$0.50)
  - *Simplest:* DALL-E 3 for single API (~$6)
- **Multi-language:** Separate JSON files for NL/EN
- **Total Cost:** ~$31.50 for 150 people with full automation 🎉

**Why Freepik API + n8n Wins:**
- ✅ Built-in automation support (n8n, Zapier, Make)
- ✅ Prompt enhancement AI (automatically improves prompts)
- ✅ Google Sheets/Drive integration for easy management
- ✅ Bulk generation optimized
- ✅ Professional documentation and support
- ✅ Latest models (Flux.1, SDXL)
- ✅ Hands-off workflow: PocketBase → n8n → Freepik → Drive → Email

**Alternative: Banana.dev** if you want lowest cost ($0.50 vs $1.50) and don't need n8n automation

**Priority:** Build AI generation system first, then add English version.

---

**Questions or ready to build the AI generation system?**
