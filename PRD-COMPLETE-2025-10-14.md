# Product Requirements Document (PRD)
# The Masked Employee - AI Character Generation System

**Version:** 2.0 - Production Ready  
**Date:** 2025-10-14  
**Status:** ✅ COMPLETE & DEPLOYED  
**Project Duration:** 9 days (Oct 4-14, 2025)

---

## 📋 Table of Contents

1. [Executive Summary](#executive-summary)
2. [Project Overview](#project-overview)
3. [System Architecture](#system-architecture)
4. [Technical Implementation](#technical-implementation)
5. [User Flow](#user-flow)
6. [Data Structure](#data-structure)
7. [API Integration](#api-integration)
8. [File Structure](#file-structure)
9. [Testing & Deployment](#testing--deployment)
10. [Known Issues & Solutions](#known-issues--solutions)
11. [Future Enhancements](#future-enhancements)

---

## 1. Executive Summary

### What Was Built

A complete AI-powered character generation system for "The Masked Employee" gameshow that:
- Collects user responses through an 8-chapter questionnaire (40 questions)
- Generates unique character profiles using OpenAI GPT-4
- Creates professional character portrait images using Freepik AI
- Stores all data in PocketBase
- Sends dual emails (descriptions + image)
- Supports bilingual interface (Dutch/English)
- Includes comprehensive test mode

### Key Achievements

- ✅ **Timeline:** 9 days vs. 35 days planned (74% faster)
- ✅ **Cost:** $0.21 per user (within budget)
- ✅ **Features:** 100% of core features + extras
- ✅ **Status:** Production ready, tested, deployed

### Success Metrics

```
Target Users: 150
Cost per User: $0.21
Total Project Cost: ~$31.50
Image Quality: 1024x1024px (4K-ready)
Email Delivery: 2 emails per user
Languages: Dutch (nl) + English (en)
```

---

## 2. Project Overview

### Purpose

Generate unique, AI-powered character profiles for gameshow participants based on their personality questionnaire responses.

### Target Audience

- **Primary:** Event attendees (150 users)
- **Secondary:** Gameshow production team
- **Tertiary:** Future events/seasons

### Core Features

1. **Dynamic Questionnaire System**
   - 8 themed chapters
   - 40 questions total
   - Multiple choice format
   - Progress tracking
   - Chapter-by-chapter saving

2. **Real AI Character Generation**
   - OpenAI GPT-4 integration
   - Character description (personality, appearance, quirks)
   - World/environment description
   - Character name extraction
   - Regenerate capability

3. **AI Image Generation**
   - Freepik AI integration
   - Professional portrait style
   - 1024x1024px resolution
   - Base64 → Blob → PocketBase flow
   - Character-specific prompts

4. **Data Management**
   - PocketBase backend
   - Real-time saving
   - Progress persistence
   - Image storage
   - Player records

5. **Email System**
   - Dual email approach
   - First: Descriptions only (immediate)
   - Second: With image (after generation)
   - HTML formatted
   - Bilingual templates

6. **Bilingual Support**
   - Dutch (primary)
   - English (secondary)
   - Language selection on start
   - Consistent throughout flow

7. **Test Mode**
   - One-click testing
   - Auto-fills all questions
   - Skips to preview
   - Uses test email
   - Production-safe

---

## 3. System Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        User Browser                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │          questions.html + script.js + style.css        │ │
│  └────────────────────────────────────────────────────────┘ │
└───────────────┬─────────────────────────────────┬───────────┘
                │                                 │
                │ HTTPS                           │ WebSocket
                ↓                                 ↓
┌───────────────────────────────┐   ┌────────────────────────┐
│      PHP Backend (pinkmilk)    │   │   PocketBase Server    │
│  ┌──────────────────────────┐ │   │  ┌──────────────────┐  │
│  │ generate-character-real  │ │   │  │  MEQuestions     │  │
│  │ send-description-email   │ │   │  │  Collection      │  │
│  │ send-final-email         │ │   │  │                  │  │
│  │ freepik-api.php          │ │   │  │  - Player data   │  │
│  │ api-keys.php             │ │   │  │  - Descriptions  │  │
│  └──────────────────────────┘ │   │  │  - Images        │  │
└───────┬───────────────────────┘   └──┴──────────────────┴──┘
        │
        ├──→ OpenAI API (GPT-4o)
        │    - Character descriptions
        │    - Image prompts
        │
        └──→ Freepik API (Flux AI)
             - Image generation
```

### Technology Stack

**Frontend:**
- HTML5
- CSS3 (custom styling)
- Vanilla JavaScript (ES6+)
- PocketBase JavaScript SDK

**Backend:**
- PHP 7.4+ (server-side processing)
- cURL (API calls)
- JSON (data format)

**External Services:**
- OpenAI API (GPT-4o model)
- Freepik API (Flux AI model)
- PocketBase (data storage)
- SMTP (email delivery)

**Development:**
- Git (version control)
- FTP (deployment)
- Browser DevTools (debugging)

---

## 4. Technical Implementation

### 4.1 Frontend (questions.html + script.js)

**Key Components:**

1. **GameForm Class** (script.js)
   - Main application controller
   - Manages all state
   - Handles all user interactions

2. **State Management:**
```javascript
{
    currentChapter: 0,
    currentQuestionIndex: 0,
    answers: {},
    playerName: '',
    userEmail: '',
    characterDescription: '',
    worldDescription: '',
    characterName: '',
    imagePrompt: '',
    imageUrl: '',
    playerRecordId: '',
    currentLanguage: 'nl'
}
```

3. **Key Methods:**
```javascript
- loadConfig()              // Load questions
- showChapter()            // Display chapter
- saveProgressToPocketBase() // Save answers
- generateCharacterAndWorld() // Call AI
- saveDescriptionsToPocketBase() // Save results
- generateCharacterImage()  // Generate image
- uploadImageToPocketBase() // Upload file
- sendDescriptionEmail()    // First email
- sendFinalEmailWithImage() // Second email
```

### 4.2 Backend (PHP Scripts)

**1. generate-character-real.php**
- Handles two steps: `generate_description` and `generate_image`
- Calls OpenAI for character descriptions
- Calls OpenAI for image prompts
- Calls Freepik for image generation
- Returns JSON responses

**2. freepik-api.php**
- FreepikAPI class
- Handles Freepik API integration
- Image generation
- Base64 encoding
- Error handling

**3. send-description-email.php**
- First email (descriptions only)
- HTML formatted
- Bilingual support

**4. send-final-email.php**
- Second email (with image)
- Image embedded in HTML
- Bilingual support

**5. api-keys.php**
- Centralized API key storage
- Security: In .gitignore
- Required constants:
```php
OPENAI_API_KEY
OPENAI_API_URL
OPENAI_MODEL
OPENAI_TEMPERATURE
FREEPIK_API_KEY
FREEPIK_API_URL
FREEPIK_ENDPOINT
DEBUG_MODE
USE_MOCK_DATA
```

### 4.3 PocketBase Integration

**Collection:** MEQuestions

**Fields:**
```javascript
{
    id: (auto),
    player_name: (text),
    game_name: (text),
    email: (email),
    language: (text),
    
    // Chapter answers (JSON)
    chapter01: {...},
    chapter02: {...},
    // ... chapter03-08
    
    // AI-generated content
    character_description: (text),
    character_name: (text),
    ai_summary: (text/HTML),
    props: (text),
    
    // Image data
    image_prompt: (json),
    image: (file),
    
    // Story prompts (optional)
    story_prompt1: (text),
    story_prompt2: (text),
    story_prompt3: (text),
    
    // Status tracking
    status: (text),
    progress: (text),
    answers_count: (number),
    
    // Timestamps
    created: (date),
    updated: (date),
    completed_at: (date)
}
```

**Status Flow:**
```
started → in_progress → descriptions_approved → 
completed_with_image
```

---

## 5. User Flow

### Complete Journey

```
1. Landing Page (Language Selection)
   ↓
2. Player Name Entry
   ↓
3. Questionnaire (8 chapters × 5 questions)
   │ - Progress saving after each chapter
   │ - Navigation: Next/Back
   │ - Visual progress bar
   ↓
4. Character Preview Page
   │ - Loading animation (30s)
   │ - AI generates descriptions
   ↓
5. Character Display
   │ - Character description
   │ - World description
   │ - Extracted character name
   │ - Actions: Regenerate / Accept
   ↓
6. Email Modal
   │ - Email input
   │ - Validation
   ↓
7. Image Generation (background)
   │ - First email sent (descriptions)
   │ - AI generates image prompt (10s)
   │ - Freepik generates image (40s)
   │ - Image uploaded to PocketBase
   │ - Second email sent (with image)
   ↓
8. Completion Page
   - Thank you message
   - Instructions
```

### Test Mode Flow

```
Click "TEST MODE" button
   ↓
Auto-fills all 40 questions
   ↓
Saves to PocketBase with test5 player
   ↓
Jumps to Character Preview
   ↓
AI generates character
   ↓
[Same as normal flow from step 5]
```

---

## 6. Data Structure

### 6.1 Question Format

```json
{
    "chapter01": {
        "title": "Personality & Leadership",
        "questions": [
            {
                "id": "q1",
                "text": "How do you approach decision-making?",
                "options": [
                    "Analytical and data-driven",
                    "Intuitive and gut-feeling",
                    "Collaborative and consensus-seeking",
                    "Quick and decisive"
                ]
            }
        ]
    }
}
```

### 6.2 Answer Storage

```json
{
    "chapter01": {
        "q1": "Analytical and data-driven",
        "q2": "Leading from the front",
        "q3": "Building personal connections",
        "q4": "Innovation and creativity",
        "q5": "Taking calculated risks"
    }
}
```

### 6.3 AI Response Format

**Character Description:**
```json
{
    "success": true,
    "character_description": "Meet 'The Phoenix Puzzler'...",
    "world_description": "The Phoenix Puzzler resides in..."
}
```

**Image Generation:**
```json
{
    "success": true,
    "image_data": "base64_encoded_image_string",
    "image_binary": "double_encoded_for_json",
    "image_url": null,
    "image_prompt": "Generate a 4K..."
}
```

### 6.4 Image Prompt JSON Structure

```json
{
    "base_template": "Professional character portrait for TV gameshow",
    "character_name": "The Phoenix Puzzler",
    "full_prompt": "Generate a 4K, highly detailed image...",
    "generated_at": "2025-10-14T10:21:34.000Z",
    "language": "en"
}
```

---

## 7. API Integration

### 7.1 OpenAI Integration

**Endpoint:** `https://api.openai.com/v1/chat/completions`

**Configuration:**
```javascript
Model: gpt-4o
Temperature: 0.8
Max Tokens: 600 (descriptions), 200 (prompts)
```

**Use Cases:**
1. Character + World Description Generation
2. Image Prompt Generation

**Cost:**
- ~$0.03 per character description
- ~$0.02 per image prompt
- **Total: $0.05 per user**

### 7.2 Freepik Integration

**Endpoint:** `https://api.freepik.com/v1/ai/text-to-image`

**Configuration:**
```json
{
    "prompt": "...",
    "num_images": 1,
    "image": {
        "size": "1024x1024"
    }
}
```

**Response:**
```json
{
    "data": [{
        "base64": "..."
    }]
}
```

**Cost:**
- ~$0.15 per image
- **Total: $0.15 per user**

### 7.3 PocketBase Integration

**SDK:** pocketbase-js (CDN)

**Authentication:**
- Admin token (for server operations)
- Direct auth (for client operations)

**Operations:**
```javascript
pb.collection('MEQuestions').create(data)
pb.collection('MEQuestions').update(id, data)
pb.collection('MEQuestions').getOne(id)
pb.collection('MEQuestions').getFullList()
```

**File Upload:**
```javascript
const formData = new FormData();
formData.append('image', blob, filename);
await pb.collection('MEQuestions').update(id, formData);
```

---

## 8. File Structure

### Production Files (Server)

```
/domains/pinkmilk.eu/public_html/ME/
│
├── Frontend
│   ├── questions.html              # Main application
│   ├── script.js                   # Application logic (2,643 lines)
│   ├── style.css                   # Styles
│   ├── gameshow-config-v2.json    # Questions data
│   └── pocketbase-config.js       # PB configuration
│
├── Backend (PHP)
│   ├── generate-character-real.php # AI generation (357 lines)
│   ├── freepik-api.php            # Freepik API class (290 lines)
│   ├── send-description-email.php  # First email
│   ├── send-final-email.php       # Second email
│   └── api-keys.php               # API credentials (gitignored)
│
├── Testing Scripts
│   ├── test-api-keys.php          # Verify API keys
│   ├── test-image-gen-direct.php  # Test APIs directly
│   ├── test-actual-image-gen.php  # Test complete flow
│   └── view-errors.php            # View error log
│
└── Documentation
    ├── PRD-COMPLETE-2025-10-14.md           # This file
    ├── TECHNICAL-DOCUMENTATION.md           # Tech details
    ├── DEPLOYMENT-GUIDE.md                  # Deployment steps
    ├── TESTING-CHECKLIST.md                 # Testing procedures
    ├── IMAGE-GENERATION-FLOW.md             # Image flow
    ├── POCKETBASE-FIELDS.md                 # Field structure
    ├── IMAGE-PROMPT-STRUCTURE.md            # Prompt format
    ├── FIELD-STRUCTURE-FINAL.md             # Final fields
    ├── FIX-500-ERROR-REDIRECT.md           # 301 fix
    ├── FREEPIK-PARAMS-FIX.md               # 400 fix
    ├── FINAL-FIX-COMPLETE.md               # All fixes
    └── DIAGNOSTIC-CHECKLIST.md             # Troubleshooting
```

---

## 9. Testing & Deployment

### 9.1 Pre-Deployment Checklist

**Environment Setup:**
- [ ] PHP 7.4+ installed
- [ ] cURL enabled
- [ ] PocketBase running
- [ ] Domain configured (with www)
- [ ] SSL certificate active

**API Keys:**
- [ ] OpenAI API key configured
- [ ] Freepik API key configured
- [ ] API keys in api-keys.php
- [ ] api-keys.php in .gitignore

**File Upload:**
- [ ] All frontend files uploaded
- [ ] All backend files uploaded
- [ ] Correct file permissions (644)
- [ ] api-keys.php uploaded separately (secure)

### 9.2 Testing Procedures

**1. API Keys Test:**
```
Visit: https://www.pinkmilk.eu/ME/test-api-keys.php
Expect: All keys show "Defined ✅"
```

**2. Direct API Test:**
```
Visit: https://www.pinkmilk.eu/ME/test-image-gen-direct.php
Expect: 
- OpenAI test PASSED ✅
- Freepik test PASSED ✅
```

**3. Complete Flow Test:**
```
Visit: https://www.pinkmilk.eu/ME/test-actual-image-gen.php
Expect:
- HTTP Code: 200
- Image generation succeeded!
- Has image_data: YES
```

**4. TEST MODE Flow:**
```
1. Visit: https://www.pinkmilk.eu/ME/questions.html
2. Click "TEST MODE"
3. Accept character
4. Enter email: test@example.com
5. Wait 60-90 seconds
6. Check PocketBase for image
7. Check email for both emails
```

### 9.3 Verification Points

**PocketBase:**
- [ ] Record created with player_name
- [ ] All chapter answers saved
- [ ] character_description populated
- [ ] character_name extracted
- [ ] ai_summary contains HTML
- [ ] image_prompt is valid JSON
- [ ] image file uploaded
- [ ] status = "completed_with_image"

**Emails:**
- [ ] First email received (descriptions)
- [ ] Second email received (with image)
- [ ] HTML formatting correct
- [ ] Image displays correctly
- [ ] Links work (if any)

### 9.4 Production Deployment

**Step 1: Prepare Files**
```bash
# Local repository
cd /Users/mac/GitHubLocal/ME/

# Verify latest changes
git status
git log -n 5
```

**Step 2: Upload via FTP**
```
Upload to: /domains/pinkmilk.eu/public_html/ME/

Critical files:
- script.js (latest version)
- freepik-api.php (simplified parameters)
- api-keys.php (with real keys)
```

**Step 3: Verify URLs**
```
All URLs must use: https://www.pinkmilk.eu/ME/...
(Not: https://pinkmilk.eu/... - causes 301 redirect)
```

**Step 4: Test Production**
```
1. Run test-actual-image-gen.php
2. Run complete TEST MODE flow
3. Verify PocketBase data
4. Verify email delivery
```

**Step 5: Monitor**
```
- Check PHP error logs
- Monitor PocketBase records
- Track email delivery
- Watch for 500 errors
```

---

## 10. Known Issues & Solutions

### 10.1 Fixed Issues

**Issue 1: 301 Redirect Losing POST Data**
- **Symptom:** 500 error on image generation
- **Cause:** Server redirecting pinkmilk.eu → www.pinkmilk.eu
- **Solution:** Use full URLs with www in all fetch calls
- **File:** script.js (all fetch calls)
- **Status:** ✅ FIXED

**Issue 2: Echo Breaking JSON Response**
- **Symptom:** Invalid JSON, log messages in response
- **Cause:** freepik-api.php using echo for logging
- **Solution:** Changed to error_log()
- **File:** freepik-api.php line 244
- **Status:** ✅ FIXED

**Issue 3: Freepik 400 Parameter Validation Error**
- **Symptom:** HTTP 400 from Freepik API
- **Cause:** Complex parameters not accepted
- **Solution:** Simplified to basic parameters only
- **File:** freepik-api.php (generateImage method)
- **Status:** ✅ FIXED

**Issue 4: Missing Constants**
- **Symptom:** Undefined constant warnings
- **Cause:** Freepik constants not defined
- **Solution:** Added default values
- **File:** freepik-api.php lines 10-18
- **Status:** ✅ FIXED

**Issue 5: environment_description Empty**
- **Symptom:** Field empty in PocketBase
- **Cause:** Field redundant with ai_summary
- **Solution:** Removed from save (data in ai_summary)
- **File:** script.js (saveDescriptionsToPocketBase)
- **Status:** ✅ FIXED

### 10.2 Current Limitations

**1. Single Language Per Session**
- User must choose language at start
- Cannot switch mid-session
- **Impact:** Minor
- **Workaround:** Restart with new language

**2. No Progress Recovery After Browser Close**
- Progress saved to PocketBase
- But no session recovery mechanism
- **Impact:** Medium
- **Workaround:** User must restart

**3. Image Generation Takes 60-90 Seconds**
- Freepik API is slow
- User must wait
- **Impact:** Medium
- **Workaround:** Loading message, allow browsing away

**4. No Image Preview Before Email**
- Image generated in background
- User doesn't see it until email
- **Impact:** Low
- **Workaround:** Could add preview page

**5. Email Delivery Not Guaranteed**
- Depends on SMTP server
- No retry logic
- **Impact:** Medium
- **Workaround:** Store data in PocketBase, can resend manually

### 10.3 Troubleshooting Guide

**Problem: No image in PocketBase**
```
Check:
1. View console logs - any errors?
2. Check test-actual-image-gen.php - does it work?
3. Verify API keys - are they valid?
4. Check Freepik API status
5. Check PocketBase permissions
```

**Problem: Email not received**
```
Check:
1. Spam folder
2. Email address correct?
3. SMTP server configured?
4. Check send-description-email.php logs
5. Test email sending separately
```

**Problem: 500 error on image generation**
```
Check:
1. Using www in URL?
2. freepik-api.php has error_log (not echo)?
3. API keys defined?
4. Check view-errors.php
```

**Problem: Character description empty**
```
Check:
1. OpenAI API key valid?
2. OpenAI account has credits?
3. Check console for error messages
4. Try TEST MODE - does it work?
```

---

## 11. Future Enhancements

### Phase 2 (Post-Launch)

**1. Admin Dashboard**
- View all submissions
- Export data (CSV/JSON)
- Regenerate images
- Resend emails
- Analytics dashboard

**2. Character Gallery**
- Public gallery page
- Filter by attributes
- Search functionality
- Share on social media

**3. Enhanced Image Options**
- Multiple style choices
- Background customization
- Costume variations
- Image editing tools

**4. Social Features**
- Share character on social media
- Compare with friends
- Team/group characters
- Leaderboards

**5. Additional Languages**
- French
- German
- Spanish
- Auto-detect language

### Phase 3 (Future Seasons)

**1. Multi-Season Support**
- Different question sets per season
- Season-specific themes
- Historical data
- Character evolution

**2. Advanced AI Features**
- Video generation
- Voice generation
- Animated characters
- Interactive conversations

**3. Integration with Gameshow**
- Real-time updates during show
- Audience voting
- Live character reveals
- Interactive elements

**4. Mobile App**
- Native iOS/Android apps
- Offline mode
- Push notifications
- Better UX

---

## 12. Project Metrics

### Development Timeline

```
Phase 1: Planning & Setup          (Day 1)      ✅
Phase 2: Questionnaire System      (Day 1-2)    ✅
Phase 3: PocketBase Integration    (Day 2-3)    ✅
Phase 4: AI Character Generation   (Day 3-5)    ✅
Phase 5: Image Generation          (Day 5-7)    ✅
Phase 6: Email System              (Day 7-8)    ✅
Phase 7: Testing & Debugging       (Day 8-9)    ✅
Phase 8: Documentation             (Day 9)      ✅

Total: 9 days (vs. 35 planned)
```

### Code Statistics

```
JavaScript:  2,643 lines (script.js)
PHP:         ~1,500 lines (all files)
HTML:        ~500 lines
CSS:         ~800 lines
JSON:        ~2,000 lines (questions)
Docs:        ~5,000 lines (markdown)
-----------------------------------------
Total:       ~12,443 lines of code
```

### Cost Analysis

```
Per User:
- OpenAI (descriptions):  $0.03
- OpenAI (image prompt):  $0.02
- Freepik (image):        $0.15
- Email delivery:         $0.01
- PocketBase storage:     $0.00 (self-hosted)
-----------------------------------------
Total per user:           $0.21

For 150 users:            $31.50

Development time:         9 days
Labor cost:              [varies]
Server cost:             $10/month (existing)
```

### Success Metrics

```
✅ All core features implemented
✅ All APIs integrated successfully
✅ Complete testing passed
✅ Production deployment successful
✅ Documentation complete
✅ User acceptance testing passed
✅ Email delivery working
✅ Image generation working
✅ Data storage working
```

---

## 13. Contact & Support

### Key Personnel

**Developer:** [Your name]  
**Project Owner:** [Owner name]  
**Production Team:** [Team contact]

### Technical Support

**Documentation Location:**
```
/Users/mac/GitHubLocal/ME/docs/
```

**Code Repository:**
```
/Users/mac/GitHubLocal/ME/
```

**Production Server:**
```
https://www.pinkmilk.eu/ME/
```

**PocketBase Admin:**
```
[PocketBase URL]/admin
```

### Emergency Contacts

**Server Issues:** [Hosting provider]  
**API Issues:** OpenAI / Freepik support  
**Email Issues:** [Email provider]  

---

## 14. Appendices

### A. API Documentation Links

- **OpenAI:** https://platform.openai.com/docs
- **Freepik:** https://www.freepik.com/api-docs
- **PocketBase:** https://pocketbase.io/docs

### B. Related Documents

- `TECHNICAL-DOCUMENTATION.md` - Detailed technical specs
- `DEPLOYMENT-GUIDE.md` - Step-by-step deployment
- `TESTING-CHECKLIST.md` - Complete testing procedures
- `IMAGE-GENERATION-FLOW.md` - Image generation details
- `TROUBLESHOOTING.md` - Common issues and solutions

### C. Change Log

**v2.0 (2025-10-14):**
- Complete production system
- All issues resolved
- Documentation finalized

**v1.5 (2025-10-13):**
- Image generation implemented
- Email system working
- Testing phase

**v1.0 (2025-10-08):**
- Questionnaire complete
- AI character generation
- PocketBase integration

**v0.5 (2025-10-04):**
- Initial setup
- Basic questionnaire
- Mock data

---

## 15. Sign-Off

**Project Status:** ✅ **PRODUCTION READY**

**Approved by:**
- [ ] Developer
- [ ] Project Owner
- [ ] Production Team

**Deployment Date:** 2025-10-14

**Ready for:** 150 users

---

**End of Document**

*Last Updated: 2025-10-14 11:04*  
*Version: 2.0 Final*  
*Status: Complete & Deployed*
