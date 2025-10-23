# TASKS.md
## The Masked Employee - Gameshow Questionnaire

**Last Updated:** 2025-10-06  
**Current Phase:** UI/UX Refinement & Translation
**Version:** 2.0.0

---

## Milestone 1: Foundation & Setup ⏳

### Environment Setup
- [ ] Create complete file/folder structure
- [ ] Set up data directory with proper permissions
- [ ] Create php/config.php with API configuration
- [ ] Set environment variable for OpenAI API key
- [ ] Test local PHP server
- [ ] Initialize Git repository (if not exists)

### Core Data Structure
- [ ] Create Questions.json with all 4 parts
  - [ ] Part 1: General information questions (with email)
  - [ ] Part 2: Character generation questions (15-20 questions)
  - [ ] Part 3: Environment & props questions (8-12 questions)
  - [ ] Part 4: Video story questions (10-15 questions across 3 levels)
- [ ] Create responses.json schema
- [ ] Test JSON read/write operations
- [ ] Implement automated backup script

### Basic Landing Page
- [ ] Create index.html (landing/welcome page)
- [ ] Design hero section with project explanation
- [ ] Add "Start Questionnaire" CTA button
- [ ] Create css/main.css for global styles
- [ ] Make responsive for mobile/tablet/desktop

---

## Milestone 2: Questionnaire Development 📝

### Form Structure
- [ ] Create questionnaire.html with multi-step form
- [ ] Implement progress bar/indicator
- [ ] Build Part 1: General Information section
- [ ] Build Part 2: Character Generation section
- [ ] Build Part 3: Environment & Props section
- [ ] Build Part 4: Video Stories section
- [ ] Add Previous/Next navigation
- [ ] Create thank-you.html confirmation page

### Form Styling
- [ ] Create css/questionnaire.css
- [ ] Design modern, clean form UI
- [ ] Style input fields (text, textarea, radio, checkbox)
- [ ] Add hover/focus states
- [ ] Implement responsive design
- [ ] Add character counters for text fields
- [ ] Style progress indicator

### Form Logic & Validation
- [ ] Create js/questionnaire.js
- [ ] Implement multi-step navigation logic
- [ ] Add client-side validation
  - [ ] Email format validation
  - [ ] Required field checks
  - [ ] Text length validation
- [ ] Implement auto-save to localStorage
- [ ] Add "Save & Continue Later" functionality
- [ ] Handle form data collection
- [ ] Implement form submission via Fetch API
- [ ] Show loading states during submission

### Backend Form Processing
- [ ] Create php/save-response.php
- [ ] Implement server-side validation
- [ ] Sanitize and escape user input
- [ ] Check for duplicate email addresses
- [ ] Append data to responses.json
- [ ] Generate unique ID for each submission
- [ ] Add timestamp to submissions
- [ ] Return success/error JSON response
- [ ] Implement rate limiting

---

## Milestone 3: AI Integration 🤖

### API Setup
- [ ] Set up OpenAI API account
- [ ] Get API key and set in environment
- [ ] Test API connection with simple request
- [ ] Implement error handling for API failures

### Prompt Engineering
- [ ] Create js/ai-generator.js for client-side formatting
- [ ] Design character generation prompt template
- [ ] Design environment generation prompt template
- [ ] Design props generation prompt template
- [ ] Design video story prompt templates (3 levels)
- [ ] Test prompts with sample data
- [ ] Refine prompts based on output quality

### AI Generation Backend
- [ ] Create php/generate-character.php
- [ ] Implement AI API call function
- [ ] Process character generation
- [ ] Process environment generation
- [ ] Process props generation
- [ ] Process video story prompt generation (all 3 levels)
- [ ] Store generated content back to responses.json
- [ ] Add retry logic for failed API calls
- [ ] Implement fallback for AI failures

### Quality Control
- [ ] Validate AI output completeness
- [ ] Check for PII in generated content
- [ ] Verify word count ranges
- [ ] Test content appropriateness filter
- [ ] Add manual review flag for edge cases

---

## Milestone 4: Admin Dashboard 📊

### Dashboard Structure
- [ ] Create admin-dashboard.html
- [ ] Implement login/authentication
- [ ] Design dashboard layout
- [ ] Create css/admin.css for dashboard styles

### Data Display
- [ ] Create js/admin.js
- [ ] Display submission count and statistics
- [ ] List all submissions in table/card view
- [ ] Implement search functionality
- [ ] Add filter options (by date, completion status)
- [ ] Show individual submission details
- [ ] Display generated character profiles

### Data Management
- [ ] Create php/get-submissions.php to fetch all data
- [ ] Implement pagination for large datasets
- [ ] Add ability to view individual responses
- [ ] Add ability to regenerate AI content
- [ ] Add manual edit capability (optional)

### Export Functionality
- [ ] Create php/export-data.php
- [ ] Implement JSON export
- [ ] Implement CSV export
- [ ] Add date range filters for export
- [ ] Generate downloadable file
- [ ] Create export summary statistics

---

## Milestone 5: Security & Optimization 🔒

### Security Implementation
- [ ] Enforce HTTPS only
- [ ] Add CSRF token protection
- [ ] Implement secure session management
- [ ] Add input sanitization throughout
- [ ] Set secure file permissions (0600 for data files)
- [ ] Store API keys in environment (never in code)
- [ ] Add rate limiting to prevent abuse
- [ ] Implement admin password hashing
- [ ] Add security headers (CSP, X-Frame-Options)

### Performance Optimization
- [ ] Minify CSS and JavaScript
- [ ] Optimize image assets
- [ ] Implement lazy loading where appropriate
- [ ] Add caching headers for static assets
- [ ] Test page load times (<2s target)
- [ ] Optimize JSON file operations
- [ ] Add loading indicators for long operations

### Error Handling
- [ ] Add comprehensive try-catch blocks
- [ ] Create user-friendly error messages
- [ ] Implement logging for server errors
- [ ] Add fallback UI for failed operations
- [ ] Test all error scenarios

---

## Milestone 6: Testing & Quality Assurance ✅

### Unit Testing
- [ ] Test form validation functions
- [ ] Test data sanitization
- [ ] Test JSON read/write operations
- [ ] Test AI prompt formatting
- [ ] Test duplicate email detection

### Integration Testing
- [ ] Test complete form submission flow
- [ ] Test AI generation pipeline
- [ ] Test admin dashboard data retrieval
- [ ] Test export functionality
- [ ] Test backup and restore operations

### User Acceptance Testing
- [ ] Recruit 5-10 pilot users
- [ ] Have users complete full questionnaire
- [ ] Gather feedback on UX/UI
- [ ] Review generated character quality
- [ ] Collect suggestions for improvements
- [ ] Fix identified bugs

### Load Testing
- [ ] Simulate 50 concurrent submissions
- [ ] Simulate 150 concurrent submissions
- [ ] Monitor server performance
- [ ] Check data integrity under load
- [ ] Verify backup systems work under pressure
- [ ] Test AI API rate limits

### Cross-Browser Testing
- [ ] Test on Chrome
- [ ] Test on Firefox
- [ ] Test on Safari
- [ ] Test on Edge
- [ ] Test on mobile browsers (iOS Safari, Chrome Mobile)

### Accessibility Testing
- [ ] Test keyboard navigation
- [ ] Test screen reader compatibility
- [ ] Check color contrast ratios
- [ ] Verify ARIA labels
- [ ] Test with browser zoom

---

## Milestone 7: Documentation & Deployment 🚀

### Documentation
- [ ] Write README.md for setup instructions
- [ ] Document API configuration steps
- [ ] Create admin user guide
- [ ] Document backup/restore procedures
- [ ] Add code comments for maintainability
- [ ] Create troubleshooting guide

### Deployment Preparation
- [ ] Review all security settings
- [ ] Set production environment variables
- [ ] Configure production database/storage
- [ ] Set up automated backups on server
- [ ] Test on staging environment
- [ ] Create rollback plan

### Production Deployment
- [ ] Deploy files to production server
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up SSL certificate
- [ ] Test production environment thoroughly
- [ ] Monitor initial submissions
- [ ] Set up error monitoring/logging

### Launch
- [ ] Conduct final security audit
- [ ] Perform smoke tests on production
- [ ] Send invitation emails to first 10 users
- [ ] Monitor and resolve any issues
- [ ] Roll out to remaining 140 users
- [ ] Collect initial feedback

---

## Milestone 8: Post-Launch & Maintenance 🔧

### Monitoring
- [ ] Set up uptime monitoring
- [ ] Monitor API usage and costs
- [ ] Track submission completion rates
- [ ] Review generated content quality
- [ ] Check for errors/issues daily

### Iteration
- [ ] Gather user feedback
- [ ] Prioritize improvement requests
- [ ] Fix bugs as reported
- [ ] Optimize AI prompts based on results
- [ ] Update documentation as needed

### Future Enhancements (Post-MVP)
- [ ] Video recording interface
- [ ] Character reveal events/voting
- [ ] Social sharing features
- [ ] Mobile app version
- [ ] Multi-language support
- [ ] Integration with HR systems

---

## Utility Tasks (Ongoing)

### Create Utility Scripts
- [ ] Create js/utils.js with helper functions
  - [ ] Date formatting
  - [ ] String utilities
  - [ ] Validation helpers
  - [ ] Storage helpers (localStorage)
- [ ] Add email validation function
- [ ] Add character counter function
- [ ] Add form data serialization function

### Assets & Resources
- [ ] Select and download icon set
- [ ] Choose color palette
- [ ] Select fonts (Google Fonts)
- [ ] Create logo/branding (if needed)
- [ ] Optimize all image assets

---

## Completed Tasks ✅

### Core Implementation (2025-10-05 to 2025-10-06)
- [x] Built complete questions.html interface
- [x] Implemented styles.css with responsive design
- [x] Created script.js with GameShowApp class
- [x] Integrated PocketBase for data storage
- [x] Implemented multi-language support (Dutch/English)
- [x] Created dynamic question rendering system
- [x] Added chapter-based navigation
- [x] Implemented progress tracking

### Language Selection Page (2025-10-05)
- [x] Hero image with fade-in animation
- [x] Two language buttons (Dutch/English with flags)
- [x] Bottom white rectangle layout
- [x] Gameshow name display (pill-shaped)
- [x] Player name input field
- [x] "Laten we beginnen!" button with conditional display
- [x] Fade-in and slide-up animation for button
- [x] Responsive grid layout

### Welcome Page (2025-10-06)
- [x] Main heading and subheading structure
- [x] Body text with line breaks
- [x] Purple gradient rectangle with border
- [x] Equal two-column layout (50/50)
- [x] Centered title above columns
- [x] 4 bullet points with purple bullets in left column
- [x] Square hero image (1:1 aspect ratio) in right column
- [x] Button moved inside rectangle, centered in left column
- [x] Level 2 shadows applied throughout
- [x] Full English translation implementation

### Confidentiality Modal (2025-10-06)
- [x] Red header with white text and drop shadow
- [x] Warning message with line breaks
- [x] 5 forbidden rules as bullet list
- [x] Purple gradient penalty box
- [x] Penalty clause display (€9,750 fine)
- [x] Red agreement checkbox with white text
- [x] "Ik ga akkoord" button inside purple box
- [x] Full English translation
- [x] Dynamic content loading based on language

### Styling & Design System (2025-10-05 to 2025-10-06)
- [x] Level 2 shadows from CodePen standard
- [x] Purple gradient theme (#667eea → #764ba2)
- [x] Barlow Semi Condensed font integration
- [x] Smooth animations (fade-in, slide-up)
- [x] Responsive breakpoints for all devices
- [x] Hover effects and transitions

### Translation System (2025-10-06)
- [x] Created translation dictionaries for Dutch
- [x] Created translation dictionaries for English
- [x] Added IDs to all translatable elements
- [x] Implemented dynamic content updates
- [x] Fixed welcome page translations
- [x] Fixed modal translations
- [x] Tested language switching

### Documentation (2025-10-04 to 2025-10-06)
- [x] Created PRD.md with complete product requirements
- [x] Created PLANNING.md with architecture and tech stack
- [x] Created TASKS.md with all milestones and tasks
- [x] Created Questions.json with chapter structure
- [x] Created README.md with setup and usage instructions
- [x] Updated README.md with v2.0 implementation details
- [x] Updated PRD.md with current status
- [x] Updated PLANNING.md with current architecture
- [x] Updated TASKS.md with completed items

---

## Notes

- **Priority:** Focus on Milestones 1-3 first (core functionality)
- **Testing:** Test after each milestone, don't wait until end
- **Security:** Never commit API keys or sensitive data to Git
- **Backup:** Test backup/restore procedures early
- **AI Costs:** Monitor API usage to stay within budget

---

**Task Management:**
- Mark tasks as completed: `- [x]` 
- Add newly discovered tasks as you find them
- Update this file at the end of each session
- Review before starting new work session
