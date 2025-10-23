# AI Character Generation System

## Overview

Simplified AI generation system using **4 API calls** instead of 6, with plain language prompts.

## Cost Savings

- **Before:** 6 API calls × $0.058 = ~$0.35 per user → $52.50 for 150 users
- **After:** 4 API calls × $0.058 = ~$0.23 per user → $35 for 150 users
- **Savings:** ~$17.50 (33% reduction)

## API Calls Breakdown

### Call 1: Combined Summary (ai_summary)
Generates in one call:
- Character description (100-150 words)
- Environment/Setting (50-75 words)
- Props (3-5 items)

**Replaces:** character_description + environment_description + props

### Call 2: Story Prompt Level 1
Surface-level prompt about work achievement (30-60 seconds)

### Call 3: Story Prompt Level 2
Hidden talent or surprising skill (60-90 seconds)

### Call 4: Story Prompt Level 3
Personal growth or meaningful moment (90-120 seconds)

## PocketBase Fields

### Required Fields (Add these to MEQuestions collection):
```
- character_name (Text)
- ai_summary (Text, Long)
- story_prompt_level1 (Text, Long)
- story_prompt_level2 (Text, Long)
- story_prompt_level3 (Text, Long)
- ai_generated_at (DateTime)
```

### Remove These Fields:
- ~~character_description~~
- ~~environment_description~~
- ~~props~~

## Files Created

### 1. `generate-character.php`
Main AI generation script that:
- Receives player data
- Formats answers for AI
- Makes 4 OpenAI API calls
- Returns structured results
- Uses simplified, plain language prompts

### 2. `result-display.html`
Result page that:
- Shows loading state during generation
- Displays character name and summary
- Shows all 3 story prompts
- Saves results back to PocketBase
- Provides print functionality

### 3. `test-generation.html`
Testing interface with:
- 3 pre-filled test users
- One-click generation testing
- Detailed result display
- Error handling

### 4. `ai-prompts-simple.json`
Simplified prompt templates:
- Plain language guidelines
- DO/DON'T style rules
- Example outputs
- No "high brow" language

### 5. `test-answers-varied.json`
3 complete test profiles:
- The Quiet Organizer
- The Energetic Creator
- The Practical Problem Solver

## Setup Instructions

### 1. Set OpenAI API Key
```bash
export OPENAI_API_KEY='sk-your-key-here'
```

### 2. Update PocketBase Schema
In PocketBase Admin:
1. Go to MEQuestions collection
2. Add new fields:
   - `character_name` (Text)
   - `ai_summary` (Text, set max length to 2000)
   - `story_prompt_level1` (Text, set max length to 500)
   - `story_prompt_level2` (Text, set max length to 500)
   - `story_prompt_level3` (Text, set max length to 500)
   - `ai_generated_at` (DateTime, optional)
3. Remove old fields (if they exist):
   - `character_description`
   - `environment_description`
   - `props`

### 3. Deploy Files
```bash
# Upload to server
./deploy.sh all

# Or manually upload:
# - generate-character.php
# - result-display.html
# - test-generation.html
# - ai-prompts-simple.json
# - test-answers-varied.json
```

## Testing

### Option 1: Test Page
1. Go to: `https://www.pinkmilk.eu/ME/test-generation.html`
2. Select a test user
3. Click "Generate Character"
4. Wait 10-15 seconds
5. Review results

### Option 2: Direct API Test
```bash
curl -X POST https://www.pinkmilk.eu/ME/generate-character.php \
  -H "Content-Type: application/json" \
  -d @test-answers-varied.json
```

### Option 3: Full Flow Test
1. Complete questionnaire at `questions.html`
2. Submit final chapter
3. Automatically redirects to `result-display.html?id=RECORD_ID`
4. AI generates character
5. Results displayed and saved to PocketBase

## Language Style

### Before (High Brow):
> "A mysterious figure who thrives in the quiet hours between dusk and dawn. The Midnight Gardener tends to ideas rather than plants, cultivating innovation in the shadows while others sleep."

### After (Simple):
> "Meet 'The Coffee Wizard' - always wearing comfy hoodies and sneakers. They're the person who knows everyone's coffee order by heart and somehow makes Monday mornings bearable."

## Troubleshooting

### "OpenAI API error"
- Check API key is set: `echo $OPENAI_API_KEY`
- Verify API key is valid at https://platform.openai.com/api-keys
- Check API credits/billing

### "PocketBase save error"
- Verify fields exist in PocketBase schema
- Check field types match (Text for all except DateTime)
- Ensure max lengths are sufficient

### "Generation takes too long"
- Normal: 10-15 seconds for 4 API calls
- If longer: Check OpenAI API status
- Consider increasing PHP timeout

### "Character name not extracted"
- Check AI summary format
- Fallback: "De Gemaskeerde Medewerker"
- Can manually edit in PocketBase

## Monitoring

Check generation success:
1. Go to PocketBase Admin
2. View MEQuestions collection
3. Look for records with:
   - `ai_generated_at` timestamp
   - `status` = 'ai_complete'
   - Filled `character_name` and `ai_summary`

## Next Steps

After testing:
1. ✅ Verify all 3 test users generate correctly
2. ✅ Check language is simple and relatable
3. ✅ Confirm PocketBase saves all fields
4. ✅ Test full questionnaire → generation flow
5. ✅ Deploy to production
6. ✅ Monitor first real submissions

## Support

If issues persist:
- Check `deploy.log` for errors
- Review PHP error logs
- Test with `test-generation.html` first
- Verify OpenAI API quota
