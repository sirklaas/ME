# Deployment Guide - Live Audience Voting App

**Target:** GitHub repository in `ranking` directory  
**Live URL:** https://ranking.pinkmilk.eu  
**PocketBase:** https://ranking.pinkmilk.eu (same domain)

---

## Files to Upload

### Core Application Files
```
ranking/
├── index.html                      # Main HTML entry point
├── index.tsx                       # React app entry
├── App.tsx                         # Main app router
├── vite.config.ts                  # Vite configuration
├── tsconfig.json                   # TypeScript config
├── package.json                    # Dependencies
├── package-lock.json               # Lock file
│
├── components/                     # React components
│   ├── PresenterView.tsx          # Main presenter display
│   ├── VoterView.tsx              # Voter interface
│   ├── SetupView.tsx              # Configuration panel
│   ├── ImageCard.tsx              # Image display component
│   ├── TimerBar.tsx               # Countdown timer
│   ├── ControlButton.tsx          # Reusable button
│   └── VoterDots.tsx              # Vote progress animation
│
├── pocketbase.ts                   # PocketBase integration
├── types.ts                        # TypeScript types
├── constants.ts                    # App constants
│
├── PRD.md                          # Product requirements
├── POCKETBASE-INTEGRATION.md       # PocketBase docs
└── DEPLOYMENT-GUIDE.md             # This file
```

---

## GitHub Upload Steps

### Option 1: Using Git Command Line

```bash
# Navigate to the voting app directory
cd /Users/mac/GitHubLocal/ME/me-voting-app

# Initialize git (if not already done)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit: Live Audience Voting App with PocketBase"

# Add remote (replace with your repo URL)
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git

# Push to ranking directory
git subtree push --prefix=. origin main:ranking
```

### Option 2: Manual Upload via GitHub Web

1. Go to your GitHub repository
2. Create new directory: `ranking`
3. Upload all files from `/Users/mac/GitHubLocal/ME/me-voting-app/`
4. Commit changes

---

## Build for Production

Before uploading, build the production version:

```bash
cd /Users/mac/GitHubLocal/ME/me-voting-app
npm run build
```

This creates a `dist/` folder with optimized files.

**Upload the `dist/` folder contents to:**
- `https://ranking.pinkmilk.eu/`

---

## PocketBase Setup on ranking.pinkmilk.eu

### 1. Create Collection

**Collection Name:** `votes`

**Fields:**
```
session_id    | Text   | Required | Max: 50
round         | Number | Required | Min: 1, Max: 3
voter_id      | Text   | Required | Max: 100
image_id      | Number | Required |
```

### 2. Create Unique Index

**Index Name:** `unique_vote`  
**Fields:** `session_id`, `round`, `voter_id`  
**Unique:** Yes

### 3. Set API Rules

```
List Rule:   "" (allow public)
View Rule:   "" (allow public)
Create Rule: "" (allow public)
Update Rule: null (deny all)
Delete Rule: null (deny all)
```

---

## Environment Configuration

### No .env file needed!

The app uses:
- PocketBase URL: `https://ranking.pinkmilk.eu` (hardcoded in `pocketbase.ts`)
- No API keys required
- All client-side only

---

## Post-Deployment Checklist

### After uploading to ranking.pinkmilk.eu:

- [ ] Verify app loads at https://ranking.pinkmilk.eu
- [ ] Test presenter view (default URL)
- [ ] Test voter view (?view=voter)
- [ ] Test setup view (?view=setup)
- [ ] Create test session
- [ ] Submit test votes
- [ ] Verify votes appear in PocketBase
- [ ] Check results display correctly
- [ ] Test on mobile devices
- [ ] Test with multiple voters

---

## Testing URLs

Once deployed:

- **Presenter View:** https://ranking.pinkmilk.eu/
- **Voter View:** https://ranking.pinkmilk.eu/?view=voter
- **Setup View:** https://ranking.pinkmilk.eu/?view=setup
- **PocketBase Admin:** https://ranking.pinkmilk.eu/_/

---

## File Structure on Server

```
ranking.pinkmilk.eu/
├── index.html
├── assets/
│   ├── index-[hash].js       # Compiled React app
│   └── index-[hash].css      # Compiled styles
└── (PocketBase running on same domain)
```

---

## Quick Start After Deployment

1. **Admin Setup:**
   - Go to https://ranking.pinkmilk.eu/?view=setup
   - Upload 4 images
   - Add titles
   - Import player list (optional)
   - Click "Save Configuration"
   - Click "Create New Session"

2. **Share with Audience:**
   - Share voter URL: https://ranking.pinkmilk.eu/?view=voter
   - Or create QR code for easy access

3. **Run Voting:**
   - Open presenter view: https://ranking.pinkmilk.eu/
   - Click "Start Round 1 Vote"
   - Wait for votes
   - Click "Show Results"
   - Continue through rounds 2 & 3

---

## Troubleshooting

### App doesn't load
- Check if files uploaded correctly
- Verify index.html is in root
- Check browser console for errors

### Votes not saving
- Verify PocketBase collection exists
- Check collection name is exactly `votes`
- Verify API rules allow public create
- Check browser console for PocketBase errors

### Results show random percentages
- This means no votes were received
- Check voters are using correct session ID
- Verify PocketBase connection working

---

## Maintenance

### Clear Old Votes

```javascript
// In PocketBase admin panel
// Filter by session_id and delete old sessions
```

### Monitor Usage

- Check PocketBase admin for vote counts
- Review session IDs
- Export data for analytics

---

## Support

**Documentation:**
- PRD.md - Complete product overview
- POCKETBASE-INTEGRATION.md - Technical details
- This file - Deployment guide

**PocketBase Admin:**
- https://ranking.pinkmilk.eu/_/

---

**Ready to deploy!** 🚀
