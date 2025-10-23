# Freepik API Setup Guide
## For Masked Employee Project

**Status:** You already have Freepik Premium ✅  
**Date:** 2025-10-04

---

## ✅ What You Already Have

Since you have a **Freepik Premium account**, you likely have:
- ✅ API access included (most premium plans include API credits)
- ✅ Familiarity with Freepik platform
- ✅ Possibly existing credits for AI generation
- ✅ Access to latest models (Flux.1, SDXL)

---

## 🔑 Step 1: Get Your API Key

### Access API Credentials:
1. **Log in** to your Freepik account: https://www.freepik.com
2. **Go to API Section**: 
   - Navigate to Account Settings → API Access
   - OR visit: https://www.freepik.com/api/developer
3. **Generate API Key**:
   - Create a new API key for "Masked Employee Project"
   - Copy and save it securely
4. **Check Your Credits**:
   - See how many API credits are included with your premium plan
   - Additional credits can be purchased if needed

### API Documentation:
- **Docs:** https://www.freepik.com/api/docs
- **Image Generation:** https://www.freepik.com/api/docs/image-generation
- **Models Available:** Flux.1, SDXL, Freepik Fine-tuned

---

## 💰 Cost Estimate (With Your Premium Account)

### What's Included:
- Premium accounts typically include **monthly API credits**
- Check your plan details for exact amount

### If You Need Additional Credits:
- **300 images for 150 people** = ~$1.50 (at standard rates)
- Your premium plan likely covers this!
- If not, credits are very affordable

### Total Project Cost:
```
GPT-4 Turbo (text): $30
Freepik API (images): $0-1.50 (likely FREE with premium!)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
TOTAL: ~$30 (or less if premium includes enough credits) 🎉
```

---

## 🚀 Quick Start Integration

### Option A: Direct API Integration (PHP/JavaScript)

**JavaScript Example:**
```javascript
// Freepik API - Generate Character Image
async function generateCharacterImage(characterPrompt) {
    const apiKey = 'YOUR_FREEPIK_API_KEY';
    
    const response = await fetch('https://api.freepik.com/v1/ai/text-to-image', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'x-freepik-api-key': apiKey
        },
        body: JSON.stringify({
            prompt: characterPrompt,
            num_images: 1,
            image: {
                size: "1024x1024"
            },
            styling: {
                style: "realistic", // or "illustration", "digital-art", etc.
                color: "vibrant"
            },
            ai_model: "flux-1" // or "sdxl", "freepik-default"
        })
    });
    
    const data = await response.json();
    return data.data[0].url; // Image URL
}

// Example usage with PocketBase data
async function processSubmission(submission) {
    const characterPrompt = generateCharacterPrompt(submission);
    const imageUrl = await generateCharacterImage(characterPrompt);
    
    // Save image URL to PocketBase
    await pb.collection('submissions').update(submission.id, {
        character_image_url: imageUrl
    });
}
```

**PHP Example:**
```php
<?php
function generateCharacterImage($characterPrompt) {
    $apiKey = 'YOUR_FREEPIK_API_KEY';
    
    $data = [
        'prompt' => $characterPrompt,
        'num_images' => 1,
        'image' => [
            'size' => '1024x1024'
        ],
        'styling' => [
            'style' => 'realistic',
            'color' => 'vibrant'
        ],
        'ai_model' => 'flux-1'
    ];
    
    $ch = curl_init('https://api.freepik.com/v1/ai/text-to-image');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-freepik-api-key: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return $result['data'][0]['url'];
}
?>
```

---

## 🔄 Option B: n8n Automation Workflow (Recommended)

### Why n8n with Freepik:
- ✅ Visual workflow builder (no code needed)
- ✅ Native Freepik node available
- ✅ PocketBase integration
- ✅ Google Drive storage
- ✅ Email automation
- ✅ Error handling built-in

### n8n Setup:

**1. Install n8n:**
```bash
# Self-hosted (FREE)
npm install n8n -g
n8n start

# OR use cloud version: https://n8n.io (starts at $20/month)
```

**2. Create Workflow:**
```
Workflow: "Masked Employee Character Generator"

┌─────────────────┐
│  PocketBase     │ ← Webhook trigger on new submission
│  Webhook        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Function       │ ← Extract questionnaire data
│  Transform Data │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  OpenAI GPT-4   │ ← Generate character description
│  Node           │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Freepik AI     │ ← Generate character image (using prompt)
│  Node           │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Google Drive   │ ← Upload image to Drive
│  Upload         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  PocketBase     │ ← Update record with image URL
│  Update Record  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Send Email     │ ← Send character to participant
│  Gmail/SendGrid │
└─────────────────┘
```

**3. Freepik Node Configuration in n8n:**
- **Operation:** Generate Image
- **Prompt:** `{{ $json.characterDescription }}`
- **API Key:** Your Freepik API key
- **Model:** Flux.1 or SDXL
- **Size:** 1024x1024
- **Style:** Realistic/Illustration

---

## 📝 Prompt Engineering for Best Results

### Character Image Prompt Template:
```
Create a professional character portrait in the style of [STYLE_PREFERENCE].

Character Details:
- Represents: [ANIMAL_FROM_Q6]
- Costume Color: [COLOR_FROM_Q7]
- Element: [ELEMENT_FROM_Q8]
- Mask Design: [MASK_DESIGN_FROM_Q9]
- Overall Vibe: [MUSICAL_STYLE_FROM_Q10]

Personality Traits:
- Superpower: [Q11]
- Style: [Q40_COLOR]

Setting/Background:
- Environment: [Q27_FICTIONAL_WORLD or Q30_RESTAURANT_CONCEPT]
- Atmosphere: Mysterious, dramatic lighting, professional quality

Technical Requirements:
- High quality digital art
- 1024x1024 resolution
- Centered composition
- Professional game show aesthetic
- Masked figure (face partially hidden)
- Vibrant but sophisticated colors

Style: Cinematic, professional, TV-quality character design
```

### Freepik Prompt Enhancement:
**Good news:** Freepik API has built-in prompt enhancement!
- You send a basic prompt
- Freepik's AI automatically improves it
- Results in better, more consistent images

---

## 🎨 Recommended Settings for Masked Employee

### Image Generation Parameters:
```json
{
  "prompt": "[Your generated prompt]",
  "num_images": 1,
  "image": {
    "size": "1024x1024"  // Square format for versatility
  },
  "styling": {
    "style": "realistic",  // or "digital-art" for more stylized
    "color": "vibrant",
    "lighting": "dramatic"
  },
  "ai_model": "flux-1",  // Latest and best model
  "num_inference_steps": 50,  // Higher = better quality (25-100)
  "guidance_scale": 7.5  // How closely to follow prompt (5-15)
}
```

### Style Variations to Test:
- **Realistic:** For lifelike character portraits
- **Digital Art:** For stylized, game-show aesthetic
- **Illustration:** For more artistic, hand-drawn feel
- **Concept Art:** For dramatic, cinematic look

---

## 🔗 Integration with Your Existing System

### Current Setup:
```
questions.html → PocketBase → (manual admin review)
```

### With Freepik API:
```
questions.html → PocketBase → [NEW: Auto AI Generation] → Admin Dashboard
                                      ↓
                              1. GPT-4: Generate text descriptions
                              2. Freepik: Generate images
                              3. Store in PocketBase
                              4. Email to participant
```

### Files to Create:
```
/Users/mac/GitHubLocal/ME/
├── generate-character.php     ← Main AI generation script
├── freepik-api.php           ← Freepik API helper functions
├── openai-api.php            ← OpenAI API helper functions
├── config-api.php            ← Store API keys securely
└── cron-process-queue.php    ← Batch process submissions
```

---

## 🎯 Next Steps

### Immediate:
1. ✅ **Get Freepik API key** from your account settings
2. ✅ **Check included credits** in your premium plan
3. ✅ **Read API docs:** https://www.freepik.com/api/docs
4. ✅ **Test API** with a simple prompt

### This Week:
1. **Build GPT-4 integration** for text generation
2. **Build Freepik API integration** for image generation
3. **Test with 5 sample submissions** from PocketBase
4. **Refine prompts** based on results
5. **Set up n8n workflow** (optional but recommended)

### Questions to Answer:
- [ ] How many API credits are included in your Freepik premium?
- [ ] Do you prefer direct API integration or n8n automation?
- [ ] Should we build this in PHP or JavaScript?
- [ ] Do you have n8n already or want to set it up?

---

## 💡 Advantages You Now Have

With your existing Freepik Premium account:

1. **Lower Cost** - Premium credits likely included
2. **Familiar Platform** - You know Freepik already
3. **Consistent Brand** - Same platform for images as you may use for other assets
4. **Premium Support** - Access to Freepik customer service
5. **Higher Quality** - Premium accounts get priority processing
6. **More Credits** - Premium plans include generous API allowances

---

## 🚀 Ready to Build?

Your setup is now clear:

**Text:** OpenAI GPT-4 (~$30)  
**Images:** Freepik API (FREE with your premium plan!)  
**Total:** ~$30 for 150 professional character profiles

**Want me to start building the integration code?**
