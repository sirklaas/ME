#!/bin/bash

# Stop ME Auto-Deploy
echo "🛑 Stopping ME Auto-Deploy..."

# Kill auto-deploy processes
if pkill -f "auto-deploy.sh"; then
    echo "✅ Auto-Deploy stopped"
else
    echo "ℹ️  No Auto-Deploy process was running"
fi

# Also kill any fswatch processes for ME directory
if pkill -f "fswatch.*GitHubLocal/ME"; then
    echo "✅ File watcher stopped"
fi

echo "🏁 Auto-Deploy system stopped"
