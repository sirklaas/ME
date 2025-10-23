# Product Requirements Document (PRD)
## The Masked Employee - Gameshow Questionnaire

**Version:** 3.0  
**Last Updated:** 2025-10-13  
**Project Code:** ME (Masked Employee)  
**Status:** Production Ready (Mock Mode) / AI Integration Pending

---

## Current Implementation Status (v3.0)

### ✅ Completed (Production Ready)
- **Core System**
  - Multi-language questionnaire system (Dutch/English)
  - Language selection page with hero imagery
  - Welcome page with theatrical introduction
  - Confidentiality modal with penalty clauses (€9,750 fine)
  - 8-chapter question system (40 questions total)
  - PocketBase backend integration for data storage
  - Responsive design for all devices
  - Level 2 shadow design system
  - Purple gradient theme throughout

- **Two-Step Character Generation** ⭐ NEW
  - Character Preview Page (short 100-150 word description)
  - Regenerate functionality (users can request new character)
  - Accept/Continue flow to full summary
  - Mock preview fallback when AI unavailable

- **Full Character Summary**
  - AI-generated character persona with name
  - Environment description with sensory details
  - 4 signature props with symbolic meanings
  - 3 progressive video story prompts (Level 1-3)
  - Mock summary fallback system

- **Email System**
  - Email collection modal after confirmation
  - User confirmation email (HTML formatted)
  - Admin notification email (to klaas@pinkmilk.eu)
  - Bilingual email templates
  - Email validation and error handling

- **Test & Debug Features**
  - Test Mode button (auto-fills 40 questions)
  - test-answers.json with realistic mock data
  - Console debugging tools
  - 10-second timeout protection on API calls

### 🔌 AI Integration Status
- **Ready for Integration:**
  - generate-character-preview.php (short preview)
  - generate-character-summary.php (full summary)
  - OpenAI API key configuration required
  - Currently using mock fallback data

### 📋 Planned / Future Enhancements
- OpenAI API key setup (when client ready)
- Admin dashboard for viewing submissions
- Image generation integration (Freepik/DALL-E)
- Video recording interface
- Analytics and reporting
- Admin email delivery optimization

---

## 1. Executive Summary

The Masked Employee system is an interactive platform that transforms employee profiles into anonymous "fantasy characters" through AI-generated descriptions, environments, and video story prompts. Inspired by the "Masked Singer/Employee" concept, this system creates engaging, anonymous employee profiles that reveal progressively deeper personal stories.

**Target:** Initial deployment for 150 employees at first company  
**Core Output:** Character profiles with AI-generated descriptions, environments, props, and 3 progressive video story prompts

---

## 2. Problem Statement

Organizations want to foster deeper employee connections and understanding while maintaining privacy and creating engaging, gamified experiences. Traditional employee profiles are static and don't encourage authentic storytelling or mystery-building engagement.

---

## 3. Objectives

### Primary Objectives
1. **Data Collection:** Gather structured employee responses across 3 question categories
2. **AI Generation:** Automatically generate character descriptions, environments, and prop suggestions
3. **Story Prompts:** Create 3 progressive video story prompts revealing increasing depth
4. **Scalability:** Support 150+ users with reliable data storage and retrieval

### Success Metrics
- 100% completion rate for questionnaire submissions
- Unique character generation for each participant
- 3 distinct, progressively deeper story prompts per user
- System handles 150+ concurrent users

---

## 4. User Journey

### 4.1 User Flow (v3.0 - Two-Step Generation)
```
1. User receives invitation link
2. Language Selection
   ├─ Choose Dutch or English
   └─ Enter player name
   
3. Welcome Page
   ├─ Gameshow introduction
   ├─ Confidentiality modal (€9,750 penalty clause)
   └─ Accept terms to continue
   
4. Questionnaire (8 Chapters, 40 Questions)
   ├─ Chapter 1: Basic Information (5 questions)
   ├─ Chapter 2-7: Character traits, preferences, experiences
   └─ Chapter 8: Deep personal stories
   └─ Auto-save to PocketBase after each chapter
   
5. Character Preview Page ⭐ NEW
   ├─ AI generates SHORT character preview (100-150 words)
   ├─ Shows character name + essence description
   ├─ User Options:
   │   ├─ 🔄 Regenerate (create new character)
   │   └─ ✅ Accept character (continue to full summary)
   
6. Full Character Summary
   ├─ Complete character description (150-250 words)
   ├─ Environment description with sensory details
   ├─ 4 signature props with meanings
   ├─ 3 progressive video story prompts
   └─ User confirms or requests redo
   
7. Email Collection
   ├─ Modal popup requesting email address
   ├─ Validation and submission
   └─ Emails sent:
       ├─ User confirmation (HTML formatted)
       └─ Admin notification (klaas@pinkmilk.eu)
       
8. Processing Complete
   └─ Thank you message + next steps
```

---

## 5. Feature Requirements

### 5.1 Questionnaire System

#### Part 1: General Information
**Purpose:** Identify and contact participants

**Required Fields:**
- Email address (unique identifier)
- Name (optional for anonymity)
- Department/Role (optional)
- Submission timestamp

#### Part 2: Character Generation
**Purpose:** Generate AI prompt for fantasy character description

**Question Categories:**
- Personality traits (5-7 questions)
- Interests and hobbies (5-7 questions)
- Values and beliefs (3-5 questions)
- Style preferences (3-5 questions)
- Fantasy/fictional character affinities (2-3 questions)

**Example Questions:**
1. "If you were an animal, which would you be and why?"
2. "What's your ideal superpower?"
3. "Describe your perfect weekend in 3 words"
4. "What fictional world would you live in?"
5. "Your personal motto or philosophy?"

#### Part 3: Environment & Props
**Purpose:** Generate contextual setting and characteristic props

**Question Categories:**
- Preferred environments (indoor/outdoor, urban/nature, etc.)
- Signature items or accessories
- Color preferences
- Atmosphere preferences (calm, energetic, mysterious, etc.)

**Example Questions:**
1. "Describe your ideal workspace or environment"
2. "What's one object you always carry or use?"
3. "What colors represent your energy?"
4. "What setting makes you feel most yourself?"

#### Part 4: Video Story Prompts
**Purpose:** Generate 3 progressively revealing story prompts

**Question Categories:**
- Surface-level stories (achievements, known facts)
- Mid-depth stories (interesting experiences, surprising facts)
- Deep stories (unknown secrets, transformative experiences)

**Example Questions:**
1. "Share a professional achievement you're proud of"
2. "What's a hidden talent no one at work knows about?"
3. "What life experience shaped who you are today?"
4. "What's a secret aspiration or dream you haven't shared?"
5. "What's a vulnerable moment that changed your perspective?"

### 5.2 AI Generation System

#### Character Description Prompt
**Input:** Part 2 responses  
**Output:** AI prompt that generates character description

**Format:**
```
Generate a fantasy character description based on the following traits:
- Personality: [processed responses]
- Interests: [processed responses]
- Values: [processed responses]
- Style: [processed responses]
- Affinities: [processed responses]

Create a vivid, anonymous character with a unique name, appearance, backstory, and personality that embodies these traits without revealing the real person's identity.
```

#### Environment Prompt
**Input:** Part 3 environment responses  
**Output:** AI prompt for character's environment

**Format:**
```
Generate a detailed environment description for this character:
- Setting preferences: [processed responses]
- Atmosphere: [processed responses]
- Color palette: [processed responses]

Describe a signature location where this character would be found, including sensory details and mood.
```

#### Props Prompt
**Input:** Part 3 props responses  
**Output:** AI prompt for characteristic items

**Format:**
```
Generate 3-5 signature props/items for this character:
- Personal items: [processed responses]
- Style elements: [processed responses]

Each prop should be symbolic and help identify the character's personality.
```

#### Video Story Prompts (3 Progressive Levels)

**Level 1 - Surface Story (Public)**
- Based on professional achievements and known facts
- Safe, shareable content
- Duration: 30-60 seconds

**Level 2 - Hidden Depths (Semi-Private)**
- Based on surprising facts and hidden talents
- Reveals unexpected sides
- Duration: 60-90 seconds

**Level 3 - Deep Secrets (Private)**
- Based on transformative experiences and vulnerabilities
- Most revealing and authentic
- Duration: 90-120 seconds

**Output Format per Level:**
```
Story Prompt Level [X]: "[Compelling opening hook] Share the story about [specific aspect from their responses]. Include [key elements to cover]. End with [emotional resonance or lesson learned]."
```

### 5.3 Data Storage

#### Storage Format
- **File:** JSON structure
- **Location:** Server-side secure storage
- **Backup:** Automated daily backups

#### Data Schema
```json
{
  "submissions": [
    {
      "id": "unique_uuid",
      "timestamp": "ISO_8601_datetime",
      "email": "user@company.com",
      "part1_general": {
        "email": "",
        "name": "",
        "department": ""
      },
      "part2_character": {
        "question_1": "answer",
        "question_2": "answer"
      },
      "part3_environment": {
        "question_1": "answer",
        "question_2": "answer"
      },
      "part4_stories": {
        "question_1": "answer",
        "question_2": "answer"
      },
      "generated_prompts": {
        "character_prompt": "",
        "environment_prompt": "",
        "props_prompt": "",
        "video_stories": [
          {
            "level": 1,
            "title": "",
            "prompt": ""
          },
          {
            "level": 2,
            "title": "",
            "prompt": ""
          },
          {
            "level": 3,
            "title": "",
            "prompt": ""
          }
        ]
      },
      "generated_content": {
        "character_description": "",
        "environment_description": "",
        "props": []
      }
    }
  ]
}
```

### 5.4 Admin Dashboard

**Features:**
- View all submissions
- Export data (JSON/CSV)
- Regenerate AI prompts
- Manual prompt editing
- Analytics overview
  - Total submissions
  - Completion rates
  - Response distributions

---

## 6. Technical Requirements

### 6.1 Frontend
- Responsive web form (mobile-friendly)
- Multi-step progress indicator
- Form validation (client-side)
- Auto-save functionality (prevent data loss)

### 6.2 Backend
- PHP for form processing
- JSON file-based storage
- Email validation and deduplication
- AI API integration (OpenAI/Claude)

### 6.3 Security
- HTTPS only
- Email validation
- Rate limiting
- Data encryption at rest
- Access control for admin dashboard

### 6.4 Performance
- Page load < 2 seconds
- Form submission < 3 seconds
- AI generation < 10 seconds per profile

---

## 7. User Interface Requirements

### 7.1 Questionnaire Form
- Clean, modern design
- Progress bar showing completion %
- Previous/Next navigation
- Save & Continue Later option
- Character counter for text fields
- Tooltips for guidance

### 7.2 Confirmation Page
- Thank you message
- Expected timeline for character generation
- Option to edit responses (within time window)

### 7.3 Admin Dashboard
- Login authentication
- Submission list view
- Individual submission detail view
- Bulk export functionality
- Search and filter capabilities

---

## 8. Integration Requirements

### 8.1 AI API Integration
**Preferred:** OpenAI GPT-4 or Claude API

**Alternative:** Local LLM if privacy concerns

**API Calls per Submission:**
1. Character description generation
2. Environment description generation
3. Props list generation
4. Video story prompt 1 generation
5. Video story prompt 2 generation
6. Video story prompt 3 generation

**Total:** 6 API calls per complete submission

### 8.2 Email System (Optional)
- Send confirmation emails
- Send completed character profiles
- Reminder emails for incomplete submissions

---

## 9. Content Guidelines

### Character Descriptions
- **Length:** 150-250 words
- **Tone:** Creative, mysterious, engaging
- **Requirements:** Anonymous, fictional name, vivid imagery

### Environment Descriptions
- **Length:** 100-150 words
- **Tone:** Atmospheric, sensory-rich
- **Requirements:** Setting details, mood, colors

### Props List
- **Quantity:** 3-5 items
- **Format:** Item name + brief symbolic meaning
- **Requirements:** Unique, character-defining

### Video Story Prompts
- **Structure:** Hook + Story direction + Key elements + Closing
- **Tone:** Progressively more vulnerable
- **Requirements:** Specific, actionable, emotionally engaging

---

## 10. Phases & Timeline

### Phase 1: Foundation (Week 1-2)
- Set up project structure
- Create Questions.JSON structure
- Build basic form interface
- Implement data storage

### Phase 2: Form Development (Week 2-3)
- Complete all question sections
- Add validation and UX features
- Implement save functionality
- Testing and refinement

### Phase 3: AI Integration (Week 3-4)
- Set up AI API
- Build prompt templates
- Implement generation logic
- Test output quality

### Phase 4: Admin & Polish (Week 4-5)
- Build admin dashboard
- Add export functionality
- Security hardening
- Final testing

### Phase 5: Deployment (Week 5)
- Deploy to production
- User testing with small group
- Bug fixes and optimization
- Full rollout to 150 users

---

## 11. Risks & Mitigations

### Risk 1: AI Quality
**Risk:** Generated content is generic or inappropriate  
**Mitigation:** Detailed prompt engineering, human review option, regeneration capability

### Risk 2: Privacy Concerns
**Risk:** Users uncomfortable with data collection  
**Mitigation:** Clear privacy policy, data encryption, anonymous character generation

### Risk 3: Incomplete Submissions
**Risk:** Users abandon questionnaire mid-way  
**Mitigation:** Auto-save, progress indication, break into smaller chunks

### Risk 4: API Costs
**Risk:** AI API costs exceed budget with 150+ users  
**Mitigation:** Cost estimation, rate limiting, batch processing

---

## 12. Success Criteria

### Launch Success
- ✅ 150 employees successfully submit questionnaires
- ✅ All 150 character profiles generated
- ✅ Zero data loss incidents
- ✅ 95%+ user satisfaction with character accuracy

### Long-term Success
- ✅ Video stories recorded and shared
- ✅ Employee engagement metrics increase
- ✅ System scales to additional companies
- ✅ Positive ROI on development investment

---

## 13. Future Enhancements (Post-MVP)

### Phase 2 Features
- Video recording interface integrated
- Character reveal events
- Voting/guessing game mechanics
- Social sharing capabilities
- Character galleries
- Mobile app version

### Phase 3 Features
- Multi-language support
- Integration with HR systems
- Advanced analytics dashboard
- Character evolution tracking
- Community features

---

## 14. Appendix

### A. Sample Full Questionnaire Structure
*(See Questions.JSON for detailed implementation)*

### B. Sample Generated Character Profile
```
CHARACTER: "The Midnight Gardener"

DESCRIPTION:
A mysterious figure who thrives in the quiet hours between dusk and dawn. 
The Midnight Gardener tends to ideas rather than plants, cultivating innovation 
in the shadows while others sleep. Dressed in deep indigo robes with silver 
constellation patterns, they carry an ancient leather journal and a compass 
that points toward possibilities rather than directions. Their presence brings 
calm to chaos, and they're known for planting seeds of wisdom that bloom 
unexpectedly.

ENVIRONMENT:
A rooftop garden under starlight, where bioluminescent plants glow softly 
and the city hums far below. Vintage lanterns cast warm pools of light across 
weathered wooden benches and stone pathways. The air smells of jasmine and 
possibility, with wind chimes creating ambient music from collected memories.

SIGNATURE PROPS:
- Ancient leather journal with hand-drawn maps of dreams
- Brass compass that spins counterclockwise
- Small vial of bioluminescent seeds
- Worn gardening gloves embroidered with constellations

VIDEO STORY PROMPTS:
Level 1: "As the Midnight Gardener, share the project you planted that grew 
         beyond expectations. What made you believe in it when others didn't?"
         
Level 2: "Reveal the hidden skill the Midnight Gardener possesses—one that 
         no colleague has ever witnessed. How did you discover this talent?"
         
Level 3: "Tell the story of the night the Midnight Gardener first learned to 
         tend gardens in darkness. What loss or transformation taught you to 
         find beauty in the unseen?"
```

---

## 15. Two-Step Character Generation (v3.0 Innovation)

### Rationale
Traditional one-step generation was overwhelming and didn't allow user input. The two-step approach provides:
- **User Control:** Preview before committing to full generation
- **Better UX:** Short preview loads faster, reduces anxiety
- **Iterative Refinement:** Regenerate option if character doesn't fit
- **Progressive Disclosure:** Information revealed in digestible chunks

### Step 1: Character Preview
**Purpose:** Quick character concept validation

**Features:**
- 100-150 word description
- Character name (e.g., "De Stille Wolf", "The Midnight Architect")
- Core essence and personality snapshot
- Visual/atmospheric preview
- Generate in ~3-5 seconds

**User Actions:**
- 🔄 **Regenerate:** Create new character (unlimited attempts)
- ✅ **Accept:** Proceed to full detailed summary

**Technical:**
- PHP: `generate-character-preview.php`
- API: OpenAI GPT-4 (temperature: 0.9 for creativity)
- Fallback: Mock preview with 3 variations
- Timeout: 10 seconds

### Step 2: Full Character Summary
**Purpose:** Complete character profile with all details

**Features:**
- Full character description (150-250 words)
- Environment description (100-150 words)
- 4 signature props with symbolic meanings
- 3 progressive video story prompts (Level 1-3)
- Generate in ~8-12 seconds

**User Actions:**
- 🔄 **Redo from Chapter 2:** Restart questionnaire
- ✅ **Confirm:** Accept and request email

**Technical:**
- PHP: `generate-character-summary.php`
- API: OpenAI GPT-4 (temperature: 0.8)
- Fallback: Mock summary (bilingual)
- Timeout: 10 seconds

---

## 16. File Structure (Current)

### Frontend Files
```
questions.html          - Main questionnaire interface
styles.css             - Styling (purple gradient theme)
script.js              - Core application logic (2,200+ lines)
test-answers.json      - Test mode data (40 pre-filled answers)
```

### Backend Files
```
generate-character-preview.php   - Short preview generation
generate-character-summary.php   - Full character generation
send-completion-email.php        - Email delivery system
prompt-builder.php              - Helper for AI prompts
```

### Data Storage
```
PocketBase Collection: MEQuestions
- gamename
- nameplayer
- email
- answers (chapter01-chapter08)
- ai_summary
- character_name
- character_description
- environment_description
- props
- story_prompt_level1/2/3
- status
- language
- completed_at
```

---

## 17. Session Summaries

### Session 1 (2025-10-04)
- **Created:** PRD.md with complete product requirements
- **Defined:** 4-part questionnaire structure (General, Character, Environment, Stories)
- **Specified:** AI generation system for character descriptions, environments, props, and 3 progressive video story prompts
- **Established:** JSON data schema and storage requirements
- **Outlined:** 5-phase timeline for development and deployment
- **Set:** Success criteria for 150-person rollout

### Session 2 (2025-10-06 - 2025-10-08)
- **Implemented:** Complete bilingual questionnaire system (Dutch/English)
- **Created:** 8-chapter structure with 40 questions
- **Built:** Language selection page with hero imagery
- **Developed:** Confidentiality modal with €9,750 penalty clause
- **Integrated:** PocketBase for data storage
- **Designed:** Purple gradient theme with Level 2 shadows
- **Added:** Test mode functionality for rapid testing

### Session 3 (2025-10-08)
- **Innovated:** Two-step character generation system
  - Character Preview Page (short description + regenerate)
  - Full Character Summary (detailed profile)
- **Implemented:** Email collection modal
- **Created:** Bilingual email system
  - User confirmation emails (HTML formatted)
  - Admin notification emails
- **Built:** PHP generators:
  - `generate-character-preview.php`
  - `send-completion-email.php`
- **Added:** Mock fallback system for AI-less testing
- **Implemented:** 10-second timeout protection
- **Status:** Production ready in mock mode, AI integration pending

---

**Document Owner:** Klaas (Pink Milk EU)  
**Last Updated:** 2025-10-13  
**Next Review:** After OpenAI API integration  
**Production URL:** https://pinkmilk.eu/ME/
