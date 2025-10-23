# PLANNING.md
## The Masked Employee - Gameshow Questionnaire

**Project:** ME (Masked Employee)  
**Created:** 2025-10-04  
**Last Updated:** 2025-10-06  
**Status:** Active Development - v2.0

---

## Implementation Update (v2.0)

### Current Architecture
The system has evolved into a modern, single-page application with multi-language support and dynamic content loading.

**Key Components:**
1. **questions.html** - Main interface with all pages
2. **styles.css** - Complete styling system with design tokens
3. **script.js** - GameShowApp class managing state and interactions
4. **Questions.json** - Dynamic question configuration
5. **PocketBase** - Backend data storage

### Technology Stack (Implemented)
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** PocketBase (NoSQL database)
- **Design:** Purple gradient theme, level 2 shadows, Barlow Semi Condensed font
- **Features:** Multi-language (Dutch/English), responsive design, smooth animations

---

## 1. Vision

### Product Vision
Create an engaging, privacy-focused platform that transforms employee profiles into mysterious "fantasy characters" through AI generation, fostering authentic storytelling and deeper connections through gamified anonymity.

### Core Value Proposition
- **For Employees:** Safe, creative way to share deeper stories while maintaining privacy
- **For Organizations:** Innovative employee engagement tool that builds culture and connection
- **For HR/Leadership:** Insights into team dynamics without compromising individual privacy

### Guiding Principles
1. **Privacy First:** Anonymity is paramount; character generation protects identity
2. **Progressive Disclosure:** Stories reveal depth gradually, building engagement
3. **AI-Augmented Creativity:** Technology enhances human storytelling, doesn't replace it
4. **Scalable Simplicity:** Easy to use for 1 person or 1,000 people
5. **Data Security:** Enterprise-grade protection for sensitive personal information

---

## 2. Architecture Overview

### System Components
```
User Interface → Application Layer → Integration Layer → Data Layer
```

**User Interface Layer:**
- Multi-step questionnaire form (responsive)
- Admin dashboard (view, export, analytics)

**Application Layer:**
- Form validator & processor
- AI prompt generator
- Data manager (CRUD operations)
- Response formatter

**Integration Layer:**
- AI API client (OpenAI/Claude)
- Email service (optional)

**Data Layer:**
- JSON file storage
- Automated backups

### File Structure (Current)
```
Githublocal/ME/
├── PRD.md, PLANNING.md, TASKS.md, README.md ✅
├── questions.html ✅ (All-in-one interface)
├── styles.css ✅ (Complete styling, 1370+ lines)
├── script.js ✅ (GameShowApp logic, 1835+ lines)
├── Questions.json ✅ (Chapter-based question structure)
├── MaskHero.webp, MaskHero2.webp ✅ (Hero images)
└── Documentation/ (AI-RECOMMENDATIONS.md, DEPLOYMENT-CHECKLIST.md, etc.)
```

---

## 3. Technology Stack

### Frontend
- **HTML5** - Semantic structure
- **CSS3** - Modern responsive styling
- **Vanilla JavaScript** - Lightweight interactivity
- **Fetch API** - AJAX requests

### Backend
- **PHP 7.4+** - Server logic
- **JSON Files** - Data storage (simple, portable)
- **cURL** - AI API communication

### AI Integration
- **Primary:** OpenAI GPT-4 API
- **Alternative:** Claude 3 or Local LLM
- **Cost:** ~$45-75 for 150 users (6 API calls each)

### Infrastructure
- Apache/Nginx web server
- HTTPS with Let's Encrypt
- Cron-based automated backups
- Git version control

### Security
- HTTPS only, input sanitization
- File permissions (0600 for data)
- Admin password protection
- API keys in environment variables

---

## 4. Required Tools & Resources

### Development Tools
- [x] Code editor (VS Code/PHPStorm)
- [x] Local PHP server
- [x] Web browser with DevTools
- [x] Git
- [ ] API testing tool (Postman)

### External Services
- [ ] OpenAI API account + key ($5-10 credit)
- [ ] Email service (optional - SendGrid/Mailgun)

### Design Resources
- Color palette generator (coolors.co)
- Icons (Lucide, Heroicons)
- Fonts (Google Fonts - Inter, Plus Jakarta Sans)

---

## 5. Development Setup

### Environment Setup
```bash
# Check PHP version
php -v  # Requires 7.4+

# Create directory structure
mkdir -p Githublocal/ME/data/backups

# Set secure permissions
chmod 700 Githublocal/ME/data
echo '{"submissions":[]}' > Githublocal/ME/data/responses.json
chmod 600 Githublocal/ME/data/responses.json

# Set environment variable for API key
export OPENAI_API_KEY='sk-your-key-here'
```

### Configuration (php/config.php)
```php
<?php
define('AI_API_KEY', getenv('OPENAI_API_KEY'));
define('AI_API_URL', 'https://api.openai.com/v1/chat/completions');
define('AI_MODEL', 'gpt-4');
define('DATA_DIR', __DIR__ . '/../data/');
define('RESPONSES_FILE', DATA_DIR . 'responses.json');
define('ADMIN_PASSWORD_HASH', password_hash('CHANGE_THIS', PASSWORD_BCRYPT));
?>
```

---

## 6. AI Prompt Strategy

### Character Generation
```
System: You are a creative character designer for "Masked Employee" personas.
Create a unique fantasy character (150-250 words) with creative alias, vivid 
description, mysterious tone. No PII.
```

### Environment Generation
```
Generate signature environment (100-150 words) with sensory details, mood, 
and emotional tone reflecting character preferences.
```

### Props Generation
```
Generate 3-5 signature props with symbolic meaning and brief descriptions.
```

### Video Story Prompts (3 Levels)
```
Level 1: Surface story (30-60s) - achievements, known facts
Level 2: Hidden depths (60-90s) - surprising facts, hidden talents
Level 3: Deep secrets (90-120s) - transformative experiences, vulnerabilities
```

---

## 7. Scalability & Performance

### Current Scale (150 users)
- Storage: ~1-2 MB JSON
- AI Cost: $45-75 total
- Processing: ~10 sec per submission, ~25 min total

### Future Scale (1,000+ users)
- Migrate to database (SQLite/MySQL)
- Implement queue system for async AI generation
- Add caching and CDN
- Batch processing optimization

---

## 8. Testing Strategy

### Test Phases
1. **Unit Testing** - Validation, sanitization, JSON operations
2. **Integration Testing** - Full form submission → AI generation flow
3. **UAT** - 5-10 employee pilot group
4. **Load Testing** - Simulate 150 concurrent users

### Critical Test Cases
- ✅ Full questionnaire submission
- ✅ Validation and error handling
- ✅ Duplicate email prevention
- ✅ AI generation success/failure
- ✅ Admin dashboard access
- ✅ Data export functionality
- ✅ Backup/restore operations

---

## 9. Development Phases

### Phase 1: Foundation (Week 1-2)
- Project structure, Questions.JSON
- Basic form interface, data storage

### Phase 2: Form Development (Week 2-3)
- Complete all question sections
- Validation, UX features, save functionality

### Phase 3: AI Integration (Week 3-4)
- AI API setup, prompt templates
- Generation logic, quality testing

### Phase 4: Admin & Polish (Week 4-5)
- Admin dashboard, export, security
- Final testing

### Phase 5: Deployment (Week 5)
- Production deployment
- Small group testing, bug fixes
- Full rollout to 150 users

---

**Last Updated:** 2025-10-06  
**Current Phase:** UI/UX Refinement & Testing  
**Next Review:** Before production deployment
