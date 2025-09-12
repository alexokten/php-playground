#!/bin/bash

# File watcher that triggers API endpoints on PHP file changes
# Reads URLs from urls.txt and triggers them when files change

URLS_FILE="urls.txt"
WATCH_EXTENSIONS=".*\.php$"
EXCLUDE_PATHS="vendor/.*"
DELAY_BETWEEN_CHECKS=0.5
DELAY_AFTER_TRIGGER=1

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 Starting PHP file watcher...${NC}"
echo -e "${BLUE}📁 Watching: $(pwd)${NC}"
echo -e "${BLUE}📝 URLs file: $URLS_FILE${NC}"

# Check if urls.txt exists
if [ ! -f "$URLS_FILE" ]; then
    echo -e "${YELLOW}⚠️  $URLS_FILE not found. Please create it with your API endpoints.${NC}"
    exit 1
fi

echo -e "${BLUE}🎯 Loaded endpoints:${NC}"
while IFS= read -r url; do
    [[ "$url" =~ ^#.*$ ]] && continue
    [[ -z "$url" ]] && continue
    echo -e "   → $url"
done < "$URLS_FILE"

echo -e "\n${GREEN}✅ Ready! Watching for PHP file changes...${NC}\n"

# Use fswatch if available, otherwise fall back to find
if command -v fswatch >/dev/null 2>&1; then
    echo -e "${BLUE}Using fswatch for file monitoring${NC}\n"
    fswatch -o . --include="$WATCH_EXTENSIONS" --exclude="$EXCLUDE_PATHS" | while read num; do
        echo -e "${YELLOW}📁 Files changed, triggering endpoints...${NC}"
        while IFS= read -r url; do
            [[ "$url" =~ ^#.*$ ]] && continue
            [[ -z "$url" ]] && continue
            echo -e "   → $url"
            curl -s --connect-timeout 2 --max-time 5 "$url" > /dev/null &
        done < "$URLS_FILE"
        wait
        echo -e "${GREEN}✅ All endpoints triggered${NC}\n"
        sleep $DELAY_AFTER_TRIGGER
    done
else
    echo -e "${BLUE}Using find for file monitoring${NC}\n"
    while true; do
        if find . -name "*.php" -not -path "./$EXCLUDE_PATHS" -newermt "2 seconds ago" | grep -q .; then
            echo -e "${YELLOW}📁 PHP files changed, triggering endpoints...${NC}"
            while IFS= read -r url; do
                [[ "$url" =~ ^#.*$ ]] && continue
                [[ -z "$url" ]] && continue
                echo -e "   → $url"
                curl -s --connect-timeout 2 --max-time 5 "$url" > /dev/null &
            done < "$URLS_FILE"
            wait
            echo -e "${GREEN}✅ All endpoints triggered${NC}\n"
            sleep $DELAY_AFTER_TRIGGER
        fi
        sleep $DELAY_BETWEEN_CHECKS
    done
fi
