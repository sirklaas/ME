#!/bin/bash

# Start ME Auto-Deploy in background
echo "🚀 Starting ME Auto-Deploy..."

# Kill any existing auto-deploy processes
pkill -f "auto-deploy.sh"

# Start the auto-deploy script in background
nohup /Users/mac/GitHubLocal/ME/auto-deploy.sh > /Users/mac/GitHubLocal/ME/deploy.log 2>&1 &

echo "✅ Auto-Deploy started in background"
echo "📋 Process ID: $!"
echo "📄 Log file: /Users/mac/GitHubLocal/ME/deploy.log"
echo ""
echo "To stop: run ./stop-auto-deploy.sh"
echo "To view logs: tail -f /Users/mac/GitHubLocal/ME/deploy.log"
