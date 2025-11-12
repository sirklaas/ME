# Deploy to Server - Quick Guide

**Target URL:** https://ranking.pinkmilk.eu/votes/  
**Build Status:** ✅ Complete  
**Build Location:** `/Users/mac/GitHubLocal/ME/ranking/dist/`

---

## Files Ready to Deploy

```
dist/
├── index.html (1.56 KB)
└── assets/
    └── index-BAV6KAXe.js (217 KB)
```

---

## Deployment Options

### Option 1: FTP/SFTP Upload

1. **Connect to your server:**
   - Host: ranking.pinkmilk.eu
   - Use your FTP client (FileZilla, Cyberduck, etc.)

2. **Navigate to web root:**
   - Usually `/public_html/` or `/var/www/html/`

3. **Create `/votes` directory** (if it doesn't exist)

4. **Upload files:**
   - Upload `dist/index.html` → `/votes/index.html`
   - Upload `dist/assets/` folder → `/votes/assets/`

### Option 2: Command Line (SCP)

```bash
# From your local machine
cd /Users/mac/GitHubLocal/ME/ranking

# Upload to server (replace with your credentials)
scp -r dist/* user@ranking.pinkmilk.eu:/path/to/public_html/votes/
```

### Option 3: cPanel File Manager

1. Log into cPanel
2. Open File Manager
3. Navigate to public_html
4. Create `votes` folder
5. Upload files from `dist/` folder

### Option 4: Git Deploy (if server has git)

```bash
# On server
cd /path/to/public_html
git clone https://github.com/sirklaas/ME.git temp
cp -r temp/ranking/* votes/
cd votes
npm install
npm run build
cp -r dist/* .
rm -rf node_modules dist
```

---

## After Upload - Server Structure

Your server should look like this:

```
ranking.pinkmilk.eu/
├── (your existing app files)
└── votes/
    ├── index.html
    └── assets/
        └── index-BAV6KAXe.js
```

---

## Test After Deployment

1. **Presenter View:**  
   https://ranking.pinkmilk.eu/votes/

2. **Voter View:**  
   https://ranking.pinkmilk.eu/votes/?view=voter

3. **Setup View:**  
   https://ranking.pinkmilk.eu/votes/?view=setup

---

## PocketBase Setup (IMPORTANT!)

After deploying, you MUST create the `votes` collection in PocketBase:

1. **Go to:** https://ranking.pinkmilk.eu/_/

2. **Create Collection:** `votes`

3. **Add Fields:**
   - `session_id` (Text, Required)
   - `round` (Number, Required)
   - `voter_id` (Text, Required)
   - `image_id` (Number, Required)

4. **Create Unique Index:**
   - Name: `unique_vote`
   - Fields: `session_id`, `round`, `voter_id`
   - Unique: ✅ Yes

5. **Set API Rules:**
   - List: `""` (public)
   - View: `""` (public)
   - Create: `""` (public)
   - Update: `null` (deny)
   - Delete: `null` (deny)

---

## What You Need

To deploy, I need you to either:

1. **Provide FTP/SFTP credentials** (Host, Username, Password, Port)
2. **Provide SSH access** (for command line deployment)
3. **Use your own FTP client** to upload the files from `dist/` folder

**Which deployment method do you prefer?**
