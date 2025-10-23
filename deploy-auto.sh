#!/bin/bash

##############################################################################
# Masked Employee - Automated FTP Deployment (No Prompts)
# Uploads files automatically to server via FTP
##############################################################################

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}"
echo "═══════════════════════════════════════════════════════════"
echo "  Masked Employee - Automated FTP Deployment"
echo "═══════════════════════════════════════════════════════════"
echo -e "${NC}"

# FTP Configuration
FTP_HOST="103.214.6.202"
FTP_USER="dukowqeu"
FTP_PASS="tTO4rf9h*ZD8!9"
FTP_PORT="21"
REMOTE_DIR="/domains/pinkmilk.eu/public_html/ME"

echo -e "${YELLOW}Deployment Configuration:${NC}"
echo -e "  Host: ${BLUE}${FTP_HOST}${NC}"
echo -e "  User: ${BLUE}${FTP_USER}${NC}"
echo -e "  Path: ${BLUE}${REMOTE_DIR}${NC}"
echo ""

# Essential files to upload
FILES=(
    "questions.html"
    "index.html"
    "script.js"
    "styles.css"
    "mask_hero.webp"
    "generate-character-summary.php"
    "api-keys.php"
    "Questions.JSON"
    "gameshow-config-v2.json"
    "chapter1-introductie.json"
    "chapter2-masked-identity.json"
    "chapter3-persoonlijke-eigenschappen.json"
    "chapter4-verborgen-talenten.json"
    "chapter5-jeugd-verleden.json"
    "chapter6-fantasie-dromen.json"
    "chapter7-eigenaardigheden.json"
    "chapter8-onverwachte-voorkeuren.json"
    "test-json-loading.html"
)

echo -e "${GREEN}🚀 Starting automated deployment...${NC}"
echo ""

# Verify files exist
echo -e "${BLUE}📋 Checking files...${NC}"
MISSING=0
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "  ${GREEN}✓${NC} $file"
    else
        echo -e "  ${RED}✗${NC} Missing: $file"
        ((MISSING++))
    fi
done

if [ $MISSING -gt 0 ]; then
    echo -e "${RED}❌ $MISSING files missing. Aborting.${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}📤 Uploading to server...${NC}"
echo ""

# Create FTP command script
FTP_SCRIPT=$(mktemp)

cat > "$FTP_SCRIPT" << 'FTPEOF'
set ftp:ssl-allow no
set net:timeout 30
set net:reconnect-interval-base 5
set net:max-retries 3
set ftp:passive-mode on

open -u dukowqeu,tTO4rf9h*ZD8!9 -p 21 103.214.6.202

# Create remote directory structure
mkdir -p /domains/pinkmilk.eu/public_html/ME
cd /domains/pinkmilk.eu/public_html/ME || exit 1

# Create subdirectories
mkdir -p generated-images
mkdir -p logs

FTPEOF

# Add file uploads to script
for file in "${FILES[@]}"; do
    echo "put \"$file\"" >> "$FTP_SCRIPT"
    echo "  ${BLUE}→${NC} Uploading: $file"
done

cat >> "$FTP_SCRIPT" << 'FTPEOF'

# Set permissions
chmod 644 *.html
chmod 644 *.js
chmod 644 *.css
chmod 644 *.php
chmod 644 *.json
chmod 755 generated-images
chmod 755 logs

quit
FTPEOF

echo ""
echo -e "${YELLOW}Executing FTP transfer...${NC}"
echo ""

# Execute FTP upload with verbose output
lftp -f "$FTP_SCRIPT" 2>&1 | while IFS= read -r line; do
    if [[ $line == *"Transferring"* ]]; then
        echo -e "  ${GREEN}✓${NC} $line"
    elif [[ $line == *"error"* ]] || [[ $line == *"Error"* ]]; then
        echo -e "  ${RED}✗${NC} $line"
    elif [[ $line == *"bytes"* ]]; then
        echo -e "  ${BLUE}→${NC} $line"
    fi
done

UPLOAD_RESULT=$?

# Clean up temp script
rm "$FTP_SCRIPT"

echo ""

if [ $UPLOAD_RESULT -eq 0 ]; then
    echo -e "${GREEN}✅ Deployment successful!${NC}"
    echo ""
    echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}🎉 Files uploaded to server!${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "${YELLOW}📋 Next Steps:${NC}"
    echo ""
    echo "1. Configure OpenAI API Key:"
    echo "   - Use FTP client to edit: api-keys.php"
    echo "   - Add your OpenAI API key"
    echo ""
    echo "2. Test your site:"
    echo "   ${GREEN}https://pinkmilk.eu/ME/questions.html${NC}"
    echo ""
    echo "3. Complete test submission:"
    echo "   - Fill out questionnaire"
    echo "   - Verify character generation"
    echo "   - Check PocketBase connection"
    echo ""
    echo -e "${GREEN}🚀 Your app is now live!${NC}"
    echo ""
else
    echo -e "${RED}❌ Deployment failed!${NC}"
    echo ""
    echo "Troubleshooting:"
    echo "  1. Check FTP credentials"
    echo "  2. Verify remote directory path"
    echo "  3. Check server permissions"
    echo "  4. Try manual FTP upload"
    echo ""
    exit 1
fi
