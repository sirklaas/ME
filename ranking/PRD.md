# Product Requirements Document (PRD)
## Live Audience Voting Application

**Version:** 1.0  
**Last Updated:** November 11, 2025  
**Product Owner:** ME Team

---

## 1. Executive Summary

### 1.1 Product Overview
Live Audience Voting is a real-time, multi-round elimination voting application designed for live events, presentations, and interactive sessions. The application enables audiences to vote on visual options (images) through their mobile devices while a presenter displays results on a main screen.

### 1.2 Product Vision
Create an engaging, game-show-style voting experience that transforms passive audiences into active participants through a simple, intuitive interface with dramatic visual feedback and real-time synchronization.

### 1.3 Target Users
- **Event Organizers**: Conference hosts, workshop facilitators, team building coordinators
- **Presenters**: Speakers who want to engage their audience interactively
- **Voters**: Audience members participating via their mobile devices
- **Setup Administrators**: Technical staff configuring the voting session

---

## 2. Product Goals & Success Metrics

### 2.1 Primary Goals
1. Enable seamless real-time voting for live audiences of up to 150+ participants
2. Provide an engaging, game-show-style experience with visual drama
3. Ensure zero-setup voting for participants (no app downloads or registration)
4. Support customizable content and branding

### 2.2 Success Metrics
- **Engagement Rate**: >80% of audience members cast votes when prompted
- **Response Time**: Votes registered within 1 second of user action
- **System Reliability**: 99.9% uptime during voting sessions
- **User Satisfaction**: Presenter NPS score >8/10
- **Setup Time**: <5 minutes from configuration to first vote

---

## 3. User Personas

### 3.1 Primary Personas

#### Persona 1: The Event Presenter
- **Name**: Sarah, Conference Speaker
- **Age**: 35-45
- **Tech Savviness**: Moderate
- **Goals**: 
  - Engage audience during presentation
  - Create memorable interactive moments
  - Maintain control over pacing and timing
- **Pain Points**: 
  - Complex setup processes
  - Technical failures during live events
  - Difficulty reading audience engagement

#### Persona 2: The Audience Voter
- **Name**: Alex, Conference Attendee
- **Age**: 25-55
- **Tech Savviness**: Varies (Low to High)
- **Goals**: 
  - Participate without hassle
  - See results immediately
  - Have fun and feel heard
- **Pain Points**: 
  - App downloads and registration
  - Confusing interfaces
  - Slow or unresponsive systems

#### Persona 3: The Setup Administrator
- **Name**: Jordan, Event Coordinator
- **Age**: 28-40
- **Tech Savviness**: High
- **Goals**: 
  - Quickly configure voting sessions
  - Customize content for specific events
  - Ensure smooth technical execution
- **Pain Points**: 
  - Time-consuming configuration
  - Limited customization options
  - Lack of testing capabilities

---

## 4. Core Features & Requirements

### 4.1 Three-View Architecture

#### 4.1.1 Presenter View (Main Display)
**Purpose**: Control voting rounds and display results to the audience

**Key Features**:
- **Round Management**
  - 3-round elimination format (4→3→2→1 winner)
  - Start/Stop voting controls
  - Manual result reveal trigger
  - Reset functionality
  
- **Visual Display**
  - 2x2 grid layout for 4 images
  - Real-time voting progress indicator
  - Animated result bars (2.5s animation)
  - Dramatic elimination/winner announcements
  
- **Voting Timer**
  - 20-second countdown per round
  - Visual progress bar
  - Auto-reveal results when timer expires
  
- **Voter Engagement Indicators**
  - Animated dots representing voters
  - Real-time vote count display
  - Visual feedback as votes come in
  
- **Audio Feedback**
  - Vote start sound effect
  - Timer tick sounds
  - Results reveal sound effect

**Technical Requirements**:
- URL: `/?view=presenter` (default)
- LocalStorage sync for state management
- Responsive design (desktop optimized)
- Sound effects from CDN sources

#### 4.1.2 Voter View (Participant Interface)
**Purpose**: Enable audience members to cast votes from their devices

**Key Features**:
- **Voting Interface**
  - Large, tappable image cards
  - One vote per round restriction
  - Visual confirmation of vote (green ring + checkmark)
  - Disabled state when voting closed
  
- **Status Messaging**
  - Round number display
  - "Waiting for round to start" message
  - "Thank you for voting" confirmation
  - "See main screen for results" prompt
  
- **Responsive Layout**
  - Adaptive grid (2x2 or 2x1+2 for 3 items)
  - Mobile-first design
  - Touch-optimized interactions

**Technical Requirements**:
- URL: `/?view=voter`
- LocalStorage polling for state updates
- No authentication required
- Vote reset between rounds
- Graceful degradation for slow connections

#### 4.1.3 Setup View (Configuration Panel)
**Purpose**: Configure voting sessions before going live

**Key Features**:
- **Player Management**
  - Excel file import (.xlsx, .xls)
  - Player list display (scrollable)
  - Player count indicator
  - First column parsing for names
  
- **Image Configuration**
  - 4 image slots (fixed)
  - Image upload (file picker)
  - Title/label editing
  - Live preview of images
  - Default placeholder images
  
- **Configuration Persistence**
  - Save to localStorage
  - Success/error feedback
  - Auto-load on page refresh
  
- **Link Management**
  - Display presenter URL
  - Display voter URL
  - Copy-friendly formatting

**Technical Requirements**:
- URL: `/?view=setup`
- XLSX library integration (SheetJS)
- Image file handling (FileReader API)
- LocalStorage configuration storage
- Validation for 4-image requirement

### 4.2 Game Flow & Logic

#### 4.2.1 Round Structure
```
Round 1: 4 options → Eliminate 1 loser → 3 remain
Round 2: 3 options → Eliminate 1 loser → 2 remain
Round 3: 2 options → Declare 1 winner
```

#### 4.2.2 Voting Cycle
1. **IDLE State**: Waiting to start round
2. **VOTING State**: 20-second timer active, votes being collected
3. **SHOWING_RESULTS State**: Results displayed with animations
4. **Elimination/Winner Announcement**: After 2.6s suspense delay
5. **Transition**: Next round or reset

#### 4.2.3 Result Calculation
- **Current Implementation**: Random percentage distribution
- **Algorithm**: 
  - Generate random votes for each option
  - Normalize to 100% total
  - Distribute remainder to ensure exact 100%
- **Future Enhancement**: Actual vote counting from voter devices

### 4.3 Synchronization & State Management

#### 4.3.1 State Persistence
**votingState** (LocalStorage):
```typescript
{
  appState: 'IDLE' | 'VOTING' | 'SHOWING_RESULTS',
  round: number (1-3),
  activeImageIds: number[]
}
```

**votingSetupData** (LocalStorage):
```typescript
{
  images: ImageItem[] (4 items),
  players: string[]
}
```

#### 4.3.2 Cross-View Communication
- **Method**: LocalStorage + StorageEvent listeners
- **Update Frequency**: Real-time on state changes
- **Conflict Resolution**: Presenter view is source of truth
- **Fallback**: Default values if localStorage unavailable

### 4.4 Visual Design & Animations

#### 4.4.1 Color Palette
- **Background**: Gray-900 (#111827)
- **Primary**: Blue-400 to Emerald-400 gradient
- **Accent**: Emerald-500 (results), Amber-300 (winner), Red-500 (eliminated)
- **Text**: White with various opacity levels

#### 4.4.2 Key Animations
- **Result Bars**: 2.5s ease-out height animation
- **Percentage Counter**: 2.5s incremental counting
- **Voter Dots**: Fade-in + floating animation, disappear as votes received
- **Vote Confirmation**: Scale + ring animation
- **Elimination/Winner**: Fade-in overlay with 500ms transition

#### 4.4.3 Responsive Breakpoints
- **Mobile**: <768px (1 column, stacked layout)
- **Tablet**: 768px-1024px (2 column grid)
- **Desktop**: >1024px (optimized 2x2 grid)

---

## 5. Technical Architecture

### 5.1 Technology Stack
- **Frontend Framework**: React 19.2.0
- **Build Tool**: Vite 6.2.0
- **Language**: TypeScript 5.8.2
- **Styling**: Tailwind CSS (CDN)
- **External Libraries**: 
  - SheetJS (XLSX parsing)
  - Picsum Photos (default images)
  - CDN-hosted sound effects

### 5.2 Component Architecture
```
App.tsx (Router)
├── PresenterView
│   ├── ImageCard (x4)
│   ├── TimerBar
│   ├── ControlButton (x2-3)
│   └── VoterDots
├── VoterView
│   └── Image voting cards
└── SetupView
    └── Configuration forms
```

### 5.3 Data Flow
```
Setup View → localStorage (votingSetupData)
                ↓
Presenter View → localStorage (votingState) → Voter View
                ↓
            Sound Effects
            Visual Updates
```

### 5.4 Browser Compatibility
- **Minimum Requirements**:
  - LocalStorage support
  - ES6+ JavaScript
  - CSS Grid & Flexbox
  - FileReader API
  - Audio API
- **Target Browsers**: 
  - Chrome 90+
  - Safari 14+
  - Firefox 88+
  - Edge 90+

---

## 6. User Flows

### 6.1 Setup Flow
1. Administrator opens `/?view=setup`
2. Uploads Excel file with player names
3. Reviews imported player list
4. Uploads 4 custom images (or uses defaults)
5. Edits image titles
6. Clicks "Save Configuration"
7. Receives success confirmation
8. Shares voter URL with audience

### 6.2 Presenter Flow
1. Presenter opens main URL (presenter view)
2. Shares voter URL with audience (displayed on screen)
3. Clicks "Start Round 1 Vote"
4. Monitors timer and vote count
5. Clicks "Show Results" (or waits for auto-reveal)
6. Watches animated results
7. Sees elimination announcement
8. Clicks "Start Round 2 Vote"
9. Repeats for Round 3
10. Winner announced
11. Clicks "Reset Game" for new session

### 6.3 Voter Flow
1. Voter scans QR code or types voter URL
2. Sees "Round will begin shortly" message
3. Round starts, 4 images appear
4. Taps preferred image
5. Sees green checkmark confirmation
6. Waits for results on main screen
7. Next round begins with remaining options
8. Repeats until winner declared

---

## 7. Non-Functional Requirements

### 7.1 Performance
- **Page Load**: <2 seconds on 3G connection
- **State Sync**: <500ms latency between views
- **Animation Frame Rate**: 60fps for all animations
- **Image Loading**: Progressive loading with placeholders
- **Memory Usage**: <50MB per browser tab

### 7.2 Scalability
- **Concurrent Voters**: Support up to 150 simultaneous voters
- **LocalStorage Limit**: <5MB total storage usage
- **Image Size**: Max 2MB per image (recommended)
- **Player List**: Up to 500 names in Excel import

### 7.3 Reliability
- **Data Persistence**: LocalStorage survives page refresh
- **Error Handling**: Graceful degradation for missing data
- **Fallback Values**: Default images and empty player list
- **State Recovery**: Auto-reset on configuration changes

### 7.4 Usability
- **Setup Time**: <5 minutes for complete configuration
- **Vote Time**: <3 seconds from tap to confirmation
- **Learning Curve**: Zero training for voters, <10 minutes for presenters
- **Accessibility**: Keyboard navigation, high contrast ratios

### 7.5 Security & Privacy
- **Data Storage**: Client-side only (no server)
- **Personal Data**: Optional player names (not required for voting)
- **Network**: No external data transmission during voting
- **Session Isolation**: Each browser instance independent

---

## 8. Constraints & Limitations

### 8.1 Technical Constraints
- **No Backend**: Purely client-side application
- **No Real Vote Counting**: Results are randomly generated
- **Browser-Specific**: Requires modern browser with localStorage
- **Single Device Presenter**: One presenter view controls all voters
- **Fixed Image Count**: Always 4 images (not configurable)

### 8.2 Design Constraints
- **3-Round Format**: Fixed elimination structure
- **20-Second Timer**: Hardcoded voting duration
- **2x2 Grid**: Fixed layout for 4 images
- **Sound Effects**: Dependent on CDN availability

### 8.3 Operational Constraints
- **Internet Required**: For CDN resources (Tailwind, XLSX, sounds)
- **Same Browser**: Setup and presenter must use same browser instance
- **Manual Sync**: Voter devices must manually refresh if out of sync
- **No Vote Verification**: Cannot verify actual vote counts

---

## 9. Future Enhancements

### 9.1 Phase 2 Features (Priority: High)
- **Real Vote Counting**: WebSocket or polling for actual vote aggregation
- **QR Code Generation**: Auto-generate QR codes for voter URL
- **Custom Timer**: Configurable voting duration
- **Vote Analytics**: Export results and participation data
- **Multi-Language**: Support for multiple languages

### 9.2 Phase 3 Features (Priority: Medium)
- **Custom Rounds**: Configurable number of rounds and options
- **Video Support**: Allow video clips instead of static images
- **Live Chat**: Audience Q&A during voting
- **Presenter Notes**: Private notes visible only to presenter
- **Theme Customization**: Custom color schemes and branding

### 9.3 Phase 4 Features (Priority: Low)
- **Backend Integration**: Optional server for vote persistence
- **User Accounts**: Save configurations across sessions
- **Templates**: Pre-built voting templates for common use cases
- **Advanced Analytics**: Demographic breakdowns, timing analysis
- **API Integration**: Connect to external data sources

---

## 10. Open Questions & Risks

### 10.1 Open Questions
1. **Vote Accuracy**: How important is real vote counting vs. simulated results?
2. **Scalability**: What's the maximum realistic audience size?
3. **Offline Mode**: Should the app work without internet?
4. **Data Export**: Do users need to export results?
5. **Mobile Presenter**: Should presenter view work on tablets?

### 10.2 Identified Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| LocalStorage quota exceeded | High | Low | Implement size limits and warnings |
| Browser compatibility issues | Medium | Medium | Provide browser compatibility checker |
| Sound effects fail to load | Low | Medium | Graceful degradation, optional sounds |
| Voter URL too long/complex | Medium | Low | Implement URL shortening or QR codes |
| State desync between views | High | Medium | Add manual refresh button, better error handling |
| Image upload size crashes browser | High | Low | Implement file size validation |

---

## 11. Success Criteria & Launch Readiness

### 11.1 Launch Checklist
- [ ] All three views functional and tested
- [ ] Cross-browser compatibility verified
- [ ] Mobile responsiveness confirmed
- [ ] Sound effects loading reliably
- [ ] Excel import working with sample data
- [ ] State synchronization tested with multiple devices
- [ ] Error handling for edge cases implemented
- [ ] User documentation created
- [ ] Demo video produced
- [ ] Beta testing with 3+ live events completed

### 11.2 Post-Launch Monitoring
- **Week 1**: Monitor for critical bugs, gather initial user feedback
- **Week 2-4**: Analyze usage patterns, identify common pain points
- **Month 2**: Evaluate success metrics, plan Phase 2 features
- **Quarterly**: Review analytics, user satisfaction, feature requests

---

## 12. Appendix

### 12.1 Glossary
- **Round**: A single voting cycle with a fixed set of options
- **Elimination**: Removal of the lowest-voted option after a round
- **Voter Dots**: Visual representation of pending votes on presenter screen
- **Active Images**: Images still in competition (not eliminated)
- **State Sync**: Process of keeping presenter and voter views aligned

### 12.2 References
- **Tailwind CSS**: https://tailwindcss.com
- **SheetJS**: https://sheetjs.com
- **Picsum Photos**: https://picsum.photos
- **React Documentation**: https://react.dev

### 12.3 Version History
- **v1.0** (Nov 11, 2025): Initial PRD creation based on current implementation

---

**Document Status**: ✅ Approved for Development  
**Next Review Date**: December 11, 2025
