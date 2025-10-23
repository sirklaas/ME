#!/bin/bash

# ME Project Auto-Deploy Script
# Watches for file changes and automatically uploads to ISP server

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Server configuration
SERVER="103.214.6.202"
USERNAME="dukowaeu"
PASSWORD="tTO4rf9h*ZD8!9"
REMOTE_DIR="/domains/pinkmilk.eu/public_html/ME"
LOCAL_DIR="/Users/mac/GitHubLocal/ME"

echo -e "${BLUE}🚀 ME Project Auto-Deploy Started${NC}"
echo -e "${BLUE}📁 Watching: ${LOCAL_DIR}${NC}"
echo -e "${BLUE}🌐 Server: ${SERVER}${REMOTE_DIR}${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop${NC}"
echo ""

# Function to upload a single file
upload_file() {
    local file="$1"
    local filename=$(basename "$file")
    
    if [[ -f "$file" ]]; then
        echo -e "${YELLOW}📤 Uploading: ${filename}${NC}"
        
        # Upload using curl with progress
        if curl -T "$file" "ftp://${SERVER}${REMOTE_DIR}/" --user "${USERNAME}:${PASSWORD}" --silent --show-error; then
            echo -e "${GREEN}✅ Uploaded: ${filename}${NC}"
        else
            echo -e "${RED}❌ Failed: ${filename}${NC}"
        fi
    fi
}

# Function to upload all files
upload_all_files() {
    echo -e "${BLUE}🔄 Uploading all ME project files...${NC}"
    
    cd "$LOCAL_DIR"
    
    # Upload all relevant files
    for file in *.html *.css *.js *.json *.webp *.md; do
        if [[ -f "$file" ]]; then
            upload_file "$LOCAL_DIR/$file"
        fi
    done
    
    echo -e "${GREEN}🎉 All files uploaded!${NC}"
    echo ""
}

# Initial upload of all files
upload_all_files

# Watch for changes and upload modified files
echo -e "${BLUE}👀 Watching for file changes...${NC}"
fswatch -0 "$LOCAL_DIR" | while IFS= read -r -d '' file; do
    # Only process files we care about
    if [[ "$file" =~ \.(html|css|js|json|webp|md)$ ]]; then
        echo -e "${YELLOW}🔄 File changed: $(basename "$file")${NC}"
        sleep 1  # Brief delay to ensure file write is complete
        upload_file "$file"
        echo ""
    fi
done
