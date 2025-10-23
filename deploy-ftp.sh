#!/bin/bash

##############################################################################
# Masked Employee - FTP Deployment Script
# Uploads files to server via FTP
##############################################################################

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}"
echo "═══════════════════════════════════════════════════════════"
echo "  Masked Employee - FTP Deployment"
echo "═══════════════════════════════════════════════════════════"
echo -e "${NC}"

# FTP Configuration
FTP_HOST="103.214.6.202"
FTP_USER="dukowqeu"
FTP_PASS="tTO4rf9h*ZD8!9"
FTP_PORT="21"

# Remote directory - ADJUST THIS IF NEEDED
# Common paths: /public_html/ME, /domains/pinkmilk.eu/public_html/ME, /httpdocs/ME
REMOTE_DIR="/domains/pinkmilk.eu/public_html/ME"

echo -e "${YELLOW}Deployment target:${NC}"
echo -e "  Host: ${BLUE}${FTP_HOST}${NC}"
echo -e "  User: ${BLUE}${FTP_USER}${NC}"
echo -e "  Path: ${BLUE}${REMOTE_DIR}${NC}"
echo ""

# Ask about remote directory
read -p "Is the remote directory path correct? (y/n/edit): " dir_confirm

if [ "$dir_confirm" = "edit" ]; then
    read -p "Enter the correct remote path: " REMOTE_DIR
    echo -e "${GREEN}✓${NC} Using: $REMOTE_DIR"
elif [ "$dir_confirm" != "y" ] && [ "$dir_confirm" != "yes" ]; then
    echo -e "${RED}❌ Deployment cancelled.${NC}"
    echo "Please edit the script and set the correct REMOTE_DIR"
    exit 1
fi

# Check if lftp is installed
if ! command -v lftp &> /dev/null; then
    echo -e "${YELLOW}⚠️  lftp is not installed. Installing via Homebrew...${NC}"
    
    if command -v brew &> /dev/null; then
        brew install lftp
    else
        echo -e "${RED}❌ Homebrew not found. Please install lftp manually:${NC}"
        echo "  brew install lftp"
        echo "  or visit: https://lftp.yar.ru/"
        exit 1
    fi
fi

echo ""
read -p "Continue with deployment? (y/n): " confirm

if [ "$confirm" != "y" ] && [ "$confirm" != "yes" ]; then
    echo -e "${RED}❌ Deployment cancelled.${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}🚀 Starting FTP deployment...${NC}"
echo ""

# Essential files to upload
FILES=(
    "questions.html"
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
)

# Count files
TOTAL_FILES=${#FILES[@]}
UPLOADED=0
FAILED=0

# Create FTP command script
FTP_SCRIPT=$(mktemp)

cat > "$FTP_SCRIPT" << EOF
set ftp:ssl-allow no
set net:timeout 10
set net:reconnect-interval-base 5
set net:max-retries 3

open -u ${FTP_USER},${FTP_PASS} -p ${FTP_PORT} ${FTP_HOST}

# Create remote directory if it doesn't exist
mkdir -p ${REMOTE_DIR}
cd ${REMOTE_DIR} || exit 1

# Create subdirectories
mkdir -p generated-images
mkdir -p logs

# Upload files
EOF

# Add each file to FTP script
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "put \"$file\"" >> "$FTP_SCRIPT"
        echo -e "  ${BLUE}→${NC} Queued: $file"
    else
        echo -e "  ${YELLOW}⚠️${NC}  Missing: $file"
        ((FAILED++))
    fi
done

echo "quit" >> "$FTP_SCRIPT"

echo ""
echo -e "${BLUE}📤 Uploading files via FTP...${NC}"
echo ""

# Execute FTP upload
lftp -f "$FTP_SCRIPT"

UPLOAD_RESULT=$?

# Clean up temp script
rm "$FTP_SCRIPT"

echo ""

if [ $UPLOAD_RESULT -eq 0 ]; then
    echo -e "${GREEN}✅ Upload completed successfully!${NC}"
    
    # Set permissions via FTP
    echo ""
    echo -e "${BLUE}🔒 Setting permissions...${NC}"
    
    PERMS_SCRIPT=$(mktemp)
    cat > "$PERMS_SCRIPT" << EOF
set ftp:ssl-allow no
open -u ${FTP_USER},${FTP_PASS} -p ${FTP_PORT} ${FTP_HOST}
cd ${REMOTE_DIR}
chmod 644 *.html *.js *.css *.php *.json
chmod 755 generated-images
chmod 755 logs
quit
EOF
    
    lftp -f "$PERMS_SCRIPT" 2>/dev/null
    rm "$PERMS_SCRIPT"
    
    echo -e "  ${GREEN}✓${NC} Permissions set"
    
else
    echo -e "${RED}❌ Upload failed!${NC}"
    echo "Please check your FTP credentials and try again."
    exit 1
fi

# Summary
echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}🎉 Deployment Complete!${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${YELLOW}📋 Post-Deployment Checklist:${NC}"
echo ""
echo "1. Configure OpenAI API Key:"
echo "   - Login to FTP"
echo "   - Edit ${REMOTE_DIR}/api-keys.php"
echo "   - Add your OpenAI API key"
echo ""
echo "2. Test the application:"
echo "   - Visit: https://pinkmilk.eu/ME/questions.html"
echo ""
echo "3. Complete a test submission:"
echo "   - Fill out the questionnaire"
echo "   - Verify character generation works"
echo "   - Check PocketBase connection"
echo ""
echo -e "${GREEN}🚀 Your application is now live!${NC}"
echo ""
