# The Masked Employee - Gameshow Questionnaire

**Version:** 2.0.0  
**Status:** Active Development  
**Last Updated:** 2025-10-06  
**Target:** Multiple gameshows with custom questions

## Overview

The Masked Employee is an interactive gameshow questionnaire system that creates engaging, multi-language experiences for discovering hidden talents and personalities. The system features a modern, theatrical interface with progressive disclosure and confidentiality agreements.

### Key Features
- 🌍 **Multi-language support** (Dutch/English) with dynamic translation
- 🎭 **Theatrical welcome experience** with masked hero imagery
- 📋 **Chapter-based question system** with progress tracking
- 🔒 **Confidentiality modal** with penalty clauses and agreements
- 💾 **PocketBase backend** for secure data storage
- 📊 **Real-time gameshow data** loaded from JSON
- 🎨 **Modern UI** with purple gradients and level 2 shadows
- 📱 **Fully responsive** design for all devices
- ✨ **Smooth animations** including fade-in and slide-up effects

---

## Quick Start

### Prerequisites
- Modern web browser (Chrome, Firefox, Safari, Edge)
- PocketBase instance (local or hosted)
- Web server for hosting static files
- Internet connection for loading external resources

### Installation

1. **Clone/Navigate to project directory:**
```bash
cd /Users/mac/CascadeProjects/windsurf-project/Githublocal/ME
```

2. **Set up data directory:**
```bash
mkdir -p data/backups
chmod 700 data
echo '{"submissions":[]}' > data/responses.json
chmod 600 data/responses.json
```

3. **Configure environment:**
```bash
# Add to ~/.bash_profile or ~/.zshrc
export OPENAI_API_KEY='sk-your-actual-api-key-here'
source ~/.bash_profile  # or source ~/.zshrc
```

4. **Create PHP config (create this file next):**
```bash
# Will be created in Milestone 1 - php/config.php
```

5. **Start local server:**
```bash
php -S localhost:8000
```

6. **Open in browser:**
```
http://localhost:8000/index.html
```

---

## Project Structure

```
ME/
├── README.md                    # This file ✅
├── PRD.md                       # Product Requirements Document ✅
├── PLANNING.md                  # Architecture & tech stack ✅
├── TASKS.md                     # Development task tracking ✅
│
├── questions.html               # Main questionnaire interface ✅
├── styles.css                   # All styling (responsive) ✅
├── script.js                    # Application logic ✅
│
├── MaskHero.webp               # Language selection hero image ✅
├── MaskHero2.webp              # Welcome page hero image ✅
│
├── Questions.json              # Gameshow questions data ✅
│
└── Documentation/              # Additional docs
    ├── AI-RECOMMENDATIONS.md
    ├── DEPLOYMENT-CHECKLIST.md
    ├── LANGUAGE-SELECTION-UPDATE.md
    ├── OPENAI_SETUP.md
    ├── POCKETBASE-SCHEMA-UPDATE.md
    └── PocketBase-Schema.md
```

---

## Current Implementation (v2.0)

### Completed Features ✅

#### 1. Language Selection Page
- **Hero image** with fade-in animation
- **Two language buttons** (Dutch/English) with flags
- **Bottom white rectangle** containing:
  - Gameshow name display (pill-shaped)
  - Player name input field
  - "Laten we beginnen!" button (appears after name entry)
- **Fade-in and slide-up animation** for button
- **Fully responsive** grid layout

#### 2. Welcome Page
- **Main heading:** "ONTDEK DE MYSTERIEUZE HELD IN JEZELF!"
- **Subheading** with line breaks
- **Body text** explaining the questionnaire
- **Purple gradient rectangle** with:
  - Centered title: "DEZE VRAGENLIJST is JOUW TICKET NAAR ROEM!"
  - Two equal columns (50/50):
    - Left: 4 bullet points with purple bullets
    - Right: Square hero image (1:1 aspect ratio)
  - "JA, START DE VRAGENLIJST!" button centered in left column
  - Subtle black outline border
  - Level 2 shadow effect
- **Full translation** to English

#### 3. Confidentiality Modal
- **Red header** with white text and drop shadow
- **Warning message** with line breaks
- **5 forbidden rules** as bullet list
- **Purple gradient box** containing:
  - Penalty clause (€9,750 fine)
  - Red agreement checkbox with white text
  - "Ik ga akkoord" button
- **Fully translated** for English version

#### 4. Question System
- **Chapter-based navigation** with progress tracking
- **Multiple question types:** text, textarea, radio, checkbox, rating
- **Dynamic question loading** from Questions.json
- **PocketBase integration** for data storage
- **Previous/Next navigation**
- **Summary page** with AI analysis placeholder

#### 5. Styling & UX
- **Level 2 shadows** throughout (CodePen standard)
- **Purple gradient theme** (#667eea → #764ba2)
- **Smooth animations** (fade-in, slide-up)
- **Responsive design** for mobile, tablet, desktop
- **Font:** Barlow Semi Condensed (300, 600, 700 weights)

### Technology Stack

#### Frontend
- **HTML5** - Semantic structure
- **CSS3** - Modern styling with gradients, shadows, animations
- **Vanilla JavaScript** - Dynamic content, translations, form handling
- **Responsive Grid & Flexbox** - Layout system

#### Backend
- **PocketBase** - NoSQL database for submissions
- **Questions.json** - Dynamic question configuration

#### External Resources
- **Google Fonts** - Barlow Semi Condensed
- **PocketBase SDK** - CDN-hosted JavaScript library

### File Details

#### questions.html (239 lines)
- Complete questionnaire interface
- Language selection page
- Welcome page with confidentiality modal
- Chapter-based question pages
- Summary and processing pages

#### styles.css (1,370+ lines)
- Global styles and resets
- Language selection page styles
- Welcome page styles
- Question page styles
- Modal styles
- Responsive breakpoints
- Animations and transitions

#### script.js (1,835+ lines)
- GameShowApp class with full questionnaire logic
- Translation system (Dutch/English)
- PocketBase integration
- Question rendering and validation
- Navigation and progress tracking
- Confidentiality modal handling

### Configuration

#### Questions.json Structure
```json
{
  "gameshow": {
    "id": "string",
    "name": "string",
    "description": "string",
    "confidentiality_warning": "string",
    "penalty_clause": "string",
    "forbidden_rules": ["array"]
  },
  "chapters": [
    {
      "id": "string",
      "title": "string",
      "description": "string",
      "questions": [...]
    }
  ]
}
```

## Development Workflow

### Current Development Focus
1. UI/UX refinements and styling
2. Translation completeness
3. Responsive design optimization
4. Animation polishing

### Before Each Session
1. **Read TASKS.md** - Check completed and pending tasks
2. **Test current build** - Verify all features working
3. **Review recent changes** - Understand latest updates

### During Development
1. Make incremental changes
2. Test immediately after each change
3. Update documentation as you go
4. Upload and test on production server

### After Each Session
1. Update TASKS.md with completed items
2. Update README.md with new features
3. Document any issues or discoveries

---

## API Configuration

### OpenAI Setup
1. **Create account:** https://platform.openai.com/signup
2. **Get API key:** https://platform.openai.com/api-keys
3. **Add credits:** $5-10 minimum (150 users ≈ $50-75 total cost)
4. **Set environment variable:**
   ```bash
   export OPENAI_API_KEY='sk-...'
   ```

### Cost Estimation
- **Per user:** 6 API calls (character, environment, props, 3 story prompts)
- **Cost per call:** ~$0.05-0.08 (GPT-4)
- **Total (150 users):** ~$45-75

---

## Questionnaire Structure

### Part 1: General Information (3 questions)
- Email address (required)
- Preferred name (optional)
- Department/team (optional)

### Part 2: Character Generation (13 questions)
Personality traits, interests, values, style preferences, fictional affinities

### Part 3: Environment & Props (8 questions)
Ideal settings, atmosphere, colors, signature objects, sensory details

### Part 4: Video Stories (20 questions across 3 levels)
- **Level 1 (3 questions):** Public achievements and known passions
- **Level 2 (4 questions):** Hidden talents and surprising experiences
- **Level 3 (5 questions):** Transformative moments and vulnerabilities

**Total:** 44 questions, ~45 minutes completion time

---

## AI Generation Process

### Input Processing
```
User Responses → Formatted Prompts → AI API → Generated Content → Storage
```

### Generated Outputs (per user)
1. **Character Description** (150-250 words)
   - Creative alias name
   - Vivid appearance and personality
   - Mysterious and engaging tone

2. **Environment Description** (100-150 words)
   - Signature location/setting
   - Atmospheric details
   - Sensory descriptions

3. **Props List** (3-5 items)
   - Symbolic objects
   - Character-defining items
   - Brief meanings

4. **Video Story Prompts** (3 levels)
   - Level 1: 30-60 second surface story
   - Level 2: 60-90 second hidden story
   - Level 3: 90-120 second deep story

---

## Admin Dashboard

### Features (TODO - Milestone 4)
- View all submissions
- Search and filter responses
- Export to JSON/CSV
- View generated character profiles
- Regenerate AI content (if needed)
- Analytics overview

### Access
- Protected by password authentication
- URL: `/admin-dashboard.html`
- Default credentials: Set in `php/config.php`

---

## Security Best Practices

### Must-Do
- ✅ Store API keys in environment variables (never in code)
- ✅ Use HTTPS in production
- ✅ Set data file permissions to 0600
- ✅ Sanitize all user input
- ✅ Validate email addresses
- ✅ Implement rate limiting
- ✅ Use password hashing for admin access

### File Permissions
```bash
chmod 700 data/                    # Only owner can access
chmod 600 data/responses.json      # Only owner can read/write
chmod 600 php/config.php           # Only owner can read/write
```

---

## Testing

### Test Locally Before Deployment
```bash
# 1. Test form submission
# Fill out questionnaire completely

# 2. Test AI generation
# Verify all 6 outputs generated

# 3. Test admin dashboard
# View submission, export data

# 4. Test edge cases
# - Invalid email
# - Duplicate email
# - Partial submission
# - API failure
```

### Pilot Testing
1. Recruit 5-10 users
2. Have them complete full questionnaire
3. Review generated characters
4. Gather UX feedback
5. Fix bugs before full rollout

---

## Troubleshooting

### "Permission denied" when saving data
```bash
chmod 700 data/
chmod 600 data/responses.json
```

### "OpenAI API error: Invalid API key"
```bash
# Check environment variable is set
echo $OPENAI_API_KEY

# Re-export if needed
export OPENAI_API_KEY='sk-...'
```

### "Failed to write to responses.json"
```bash
# Check file exists and is writable
ls -la data/responses.json
# Should show: -rw------- (600 permissions)
```

### AI generation takes too long
- Expected: 5-10 seconds per user (6 API calls)
- If longer: Check network connection and API status
- Monitor at: https://status.openai.com/

---

## Data Backup

### Manual Backup
```bash
cp data/responses.json data/backups/responses_$(date +%Y%m%d_%H%M%S).json
```

### Automated Backup (Cron - TODO)
```bash
# Add to crontab (daily at 2am)
0 2 * * * cd /path/to/ME && php php/backup.php
```

---

## Deployment Checklist

### Pre-Deployment
- [ ] All tests passing
- [ ] Security audit completed
- [ ] API keys in environment variables
- [ ] HTTPS configured
- [ ] Backup system active
- [ ] Error logging enabled

### Deployment
- [ ] Upload files to production server
- [ ] Set file permissions
- [ ] Configure web server
- [ ] Test all functionality
- [ ] Monitor initial submissions

### Post-Deployment
- [ ] Send invitations to pilot group (10 users)
- [ ] Monitor for errors
- [ ] Fix any issues
- [ ] Roll out to remaining users
- [ ] Collect feedback

---

## Support & Maintenance

### Daily Tasks
- Check for new submissions
- Review generated content quality
- Monitor error logs
- Respond to user issues

### Weekly Tasks
- Export data backup
- Review analytics
- Check API usage/costs
- Update documentation if needed

---

## Development Milestones

Current progress tracked in **TASKS.md**

1. ⏳ **Foundation & Setup** - In Progress
2. ⏱️ **Questionnaire Development** - Pending
3. ⏱️ **AI Integration** - Pending
4. ⏱️ **Admin Dashboard** - Pending
5. ⏱️ **Security & Optimization** - Pending
6. ⏱️ **Testing & QA** - Pending
7. ⏱️ **Documentation & Deployment** - Pending
8. ⏱️ **Post-Launch & Maintenance** - Pending

---

## Resources

### Documentation
- [OpenAI API Docs](https://platform.openai.com/docs)
- [PHP JSON Functions](https://www.php.net/manual/en/ref.json.php)
- [Fetch API Guide](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)

### Design Inspiration
- [Typeform](https://www.typeform.com/) - Multi-step forms
- [Notion](https://www.notion.so/) - Clean UI
- [Linear](https://linear.app/) - Modern design

---

## FAQ

**Q: Can users edit their responses after submission?**  
A: Not in MVP. Can be added in Phase 2 with edit window (e.g., 24 hours).

**Q: What if AI generates inappropriate content?**  
A: Manual review process for flagged content. Admin can regenerate.

**Q: How is anonymity maintained?**  
A: Character descriptions contain no PII. Email stored separately from character data.

**Q: What happens if AI API is down?**  
A: Submissions queue for later processing. Users receive confirmation email when ready.

**Q: Can this scale beyond 150 users?**  
A: Yes. For 1,000+ users, migrate to database (MySQL/PostgreSQL) and add queue system.

---

## License

Internal company use only. Not for public distribution.

---

## Contributors

Development team - Project ME

**Last Updated:** 2025-10-06  
**Version:** 2.0.0

---

## Recent Updates (2025-10-06)

### Session Summary: Welcome Page & Modal Refinement
- ✅ Updated welcome page layout with equal columns and centered title
- ✅ Added square hero image (1:1 aspect ratio) in purple box
- ✅ Moved button inside purple rectangle, centered in left column
- ✅ Implemented level 2 shadows throughout interface
- ✅ Redesigned confidentiality modal with red header and purple penalty box
- ✅ Fixed welcome page translations for English version
- ✅ Added IDs to all translatable elements for dynamic updates
- ✅ Completed Dutch and English translation dictionaries
- ✅ Updated all documentation (README, PRD, PLANNING, TASKS)
