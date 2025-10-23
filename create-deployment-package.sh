#!/bin/bash

##############################################################################
# Create Deployment Package
# Creates a ZIP file with only the essential files for deployment
##############################################################################

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}"
echo "═══════════════════════════════════════════════════════════"
echo "  Create Deployment Package for Masked Employee"
echo "═══════════════════════════════════════════════════════════"
echo -e "${NC}"

# Package name with timestamp
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
PACKAGE_NAME="ME-deployment-${TIMESTAMP}.zip"

echo -e "${BLUE}📦 Creating deployment package...${NC}"
echo ""

# Essential files to include
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

# Check if files exist and create list
EXISTING_FILES=()
MISSING_FILES=()

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        EXISTING_FILES+=("$file")
        echo -e "  ${GREEN}✓${NC} $file"
    else
        MISSING_FILES+=("$file")
        echo -e "  ${YELLOW}⚠️  Missing: $file${NC}"
    fi
done

echo ""

# Create ZIP file
if [ ${#EXISTING_FILES[@]} -gt 0 ]; then
    echo -e "${BLUE}Creating ZIP file: ${PACKAGE_NAME}${NC}"
    
    # Remove old package if exists
    [ -f "$PACKAGE_NAME" ] && rm "$PACKAGE_NAME"
    
    # Create ZIP
    zip -q "$PACKAGE_NAME" "${EXISTING_FILES[@]}"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Package created successfully!${NC}"
        echo ""
        echo -e "${BLUE}Package details:${NC}"
        echo -e "  Name: ${GREEN}$PACKAGE_NAME${NC}"
        echo -e "  Size: $(ls -lh "$PACKAGE_NAME" | awk '{print $5}')"
        echo -e "  Files: ${#EXISTING_FILES[@]}"
        echo ""
        
        if [ ${#MISSING_FILES[@]} -gt 0 ]; then
            echo -e "${YELLOW}⚠️  Warning: ${#MISSING_FILES[@]} files were not found and excluded${NC}"
            echo ""
        fi
        
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo -e "${GREEN}Next Steps:${NC}"
        echo ""
        echo "1. Upload $PACKAGE_NAME to your server"
        echo "2. Extract on server: unzip $PACKAGE_NAME"
        echo "3. Create directories: mkdir generated-images logs"
        echo "4. Set permissions: chmod 755 generated-images logs"
        echo "5. Edit api-keys.php with your OpenAI API key"
        echo "6. Test at: https://yourdomain.com/ME/questions.html"
        echo ""
        echo -e "${YELLOW}📋 See DEPLOYMENT-CHECKLIST.md for full instructions${NC}"
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    else
        echo -e "${RED}❌ Failed to create package${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ No files found to package!${NC}"
    exit 1
fi
