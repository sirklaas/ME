#!/bin/bash

##############################################################################
# Masked Employee - Deploy to Server
# This script uploads only the essential files needed for production
##############################################################################

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "═══════════════════════════════════════════════════════════"
echo "  Masked Employee - Server Deployment Script"
echo "═══════════════════════════════════════════════════════════"
echo -e "${NC}"

# Configuration - FTP Credentials
FTP_HOST="103.214.6.202"
FTP_USER="dukowqeu"
FTP_PASS="tTO4rf9h*ZD8!9"
FTP_PORT="21"
REMOTE_DIR="/ME"  # Adjust this path if needed (e.g., /public_html/ME or /domains/pinkmilk.eu/public_html/ME)

echo -e "${YELLOW}⚠️  Please configure this script first!${NC}"
echo "Edit the following variables at the top of deploy-to-server.sh:"
echo "  - SERVER_USER"
echo "  - SERVER_HOST"
echo "  - SERVER_PATH"
echo ""
read -p "Have you configured the server details? (yes/no): " configured

if [ "$configured" != "yes" ]; then
    echo -e "${RED}❌ Deployment cancelled. Please configure the script first.${NC}"
    exit 1
fi

# Ask for deployment confirmation
echo ""
echo -e "${YELLOW}This will upload files to:${NC}"
echo -e "${BLUE}${SERVER_USER}@${SERVER_HOST}:${SERVER_PATH}${NC}"
echo ""
read -p "Continue with deployment? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo -e "${RED}❌ Deployment cancelled.${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}🚀 Starting deployment...${NC}"
echo ""

# Create file list
ESSENTIAL_FILES=(
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

# Optional files to ask about
OPTIONAL_FILES=(
    "test-freepik-only.php"
    "test-ai-generation.php"
    "freepik-api.php"
    "openai-api.php"
    "generate-character.php"
    "prompt-builder.php"
)

# Function to upload files
upload_files() {
    local files=("$@")
    local success=0
    local failed=0
    
    for file in "${files[@]}"; do
        if [ -f "$file" ]; then
            echo -n "  Uploading $file... "
            
            # Using SCP - change to rsync or other method if preferred
            if scp "$file" "${SERVER_USER}@${SERVER_HOST}:${SERVER_PATH}/" 2>/dev/null; then
                echo -e "${GREEN}✓${NC}"
                ((success++))
            else
                echo -e "${RED}✗${NC}"
                ((failed++))
            fi
        else
            echo -e "  ${YELLOW}⚠️  $file not found, skipping${NC}"
        fi
    done
    
    return $failed
}

# Upload essential files
echo -e "${BLUE}📦 Uploading essential files...${NC}"
upload_files "${ESSENTIAL_FILES[@]}"
essential_result=$?

# Ask about optional files
echo ""
read -p "Upload optional testing/utility files? (yes/no): " upload_optional

if [ "$upload_optional" = "yes" ]; then
    echo ""
    echo -e "${BLUE}📦 Uploading optional files...${NC}"
    upload_files "${OPTIONAL_FILES[@]}"
fi

# Create directories on server
echo ""
echo -e "${BLUE}📁 Creating directories on server...${NC}"
ssh "${SERVER_USER}@${SERVER_HOST}" "mkdir -p ${SERVER_PATH}/generated-images ${SERVER_PATH}/logs && chmod 755 ${SERVER_PATH}/generated-images ${SERVER_PATH}/logs" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "  ${GREEN}✓${NC} Directories created"
else
    echo -e "  ${YELLOW}⚠️  Could not create directories (may already exist)${NC}"
fi

# Set permissions
echo ""
echo -e "${BLUE}🔒 Setting file permissions...${NC}"
ssh "${SERVER_USER}@${SERVER_HOST}" "cd ${SERVER_PATH} && chmod 644 *.html *.js *.css *.php *.json 2>/dev/null && chmod 755 generated-images logs 2>/dev/null"

if [ $? -eq 0 ]; then
    echo -e "  ${GREEN}✓${NC} Permissions set"
else
    echo -e "  ${YELLOW}⚠️  Could not set permissions${NC}"
fi

# Summary
echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Deployment Complete!${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANT: Post-Deployment Steps${NC}"
echo ""
echo "1. Configure API Keys:"
echo "   SSH into your server and edit api-keys.php"
echo "   Add your OpenAI API key"
echo ""
echo "2. Test the application:"
echo "   Visit: https://${SERVER_HOST}/ME/questions.html"
echo ""
echo "3. Verify PocketBase connection"
echo "   Complete a test submission"
echo ""
echo "4. Security checklist:"
echo "   - Ensure HTTPS is enabled"
echo "   - Check file permissions"
echo "   - Verify API keys are not exposed"
echo ""
echo -e "${GREEN}Happy deploying! 🚀${NC}"
echo ""
