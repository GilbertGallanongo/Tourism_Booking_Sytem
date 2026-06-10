#!/bin/bash

# Tourism Booking System - Railway Deployment Script (Linux/macOS)
# Usage: bash deploy.sh

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

PROJECT_NAME="tourism-booking-system"
BRANCH="main"
COMMIT_MSG="Deploy to Railway"

# Functions
print_status() {
    local message=$1
    local status=${2:-info}
    
    case $status in
        success)
            echo -e "${GREEN}[$(date +'%H:%M:%S')] ✅ $message${NC}"
            ;;
        error)
            echo -e "${RED}[$(date +'%H:%M:%S')] ❌ $message${NC}"
            ;;
        warning)
            echo -e "${YELLOW}[$(date +'%H:%M:%S')] ⚠️  $message${NC}"
            ;;
        *)
            echo -e "${CYAN}[$(date +'%H:%M:%S')] ℹ️  $message${NC}"
            ;;
    esac
}

print_step() {
    local number=$1
    local title=$2
    echo ""
    print_status "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    print_status "Step $number: $title"
    print_status "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
}

command_exists() {
    command -v "$1" &> /dev/null
}

# Main script
echo ""
print_status "🚀 Tourism Booking System - Railway Deployment Script"
print_status "================================================"
echo ""

# Check prerequisites
print_step 1 "Checking Prerequisites"

if ! command_exists git; then
    print_status "Git is not installed" error
    exit 1
fi
print_status "Git found" success

WORK_DIR=$(pwd)
print_status "Working directory: $WORK_DIR" info

if [ ! -d ".git" ]; then
    print_status "Not a git repository" error
    exit 1
fi
print_status "Git repository detected" success

# Check Railway CLI
if command_exists railway; then
    print_status "Railway CLI found" success
    RAILWAY_AVAILABLE=true
else
    print_status "Railway CLI not installed (optional, will provide manual steps)" warning
    RAILWAY_AVAILABLE=false
fi

# Git Operations
print_step 2 "Git Operations"

print_status "Checking git status..." info
if [ -n "$(git status --short)" ]; then
    echo "Uncommitted changes found:"
    git status --short
    echo ""
    read -p "Stage and commit all changes? (y/n): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Adding all files..." info
        git add .
        
        print_status "Committing changes..." info
        git commit -m "$COMMIT_MSG"
        print_status "Changes committed" success
    else
        print_status "Skipping commit" warning
    fi
else
    print_status "No uncommitted changes" success
fi

echo ""
print_status "Pushing to GitHub..." info
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
git push origin "$CURRENT_BRANCH"
print_status "Code pushed to GitHub" success

# Railway Deployment
print_step 3 "Railway Deployment"

if [ "$RAILWAY_AVAILABLE" = true ]; then
    print_status "Using Railway CLI for automated deployment..." info
    echo ""
    
    print_status "Logging in to Railway..." info
    railway login
    
    print_status "Logged into Railway" success
    echo ""
    
    print_status "Creating project in Railway..." info
    railway init --name "$PROJECT_NAME"
else
    print_status "📋 Manual Deployment Steps:" info
    echo ""
    echo -e "${CYAN}1️⃣  Go to: https://railway.app/dashboard${NC}"
    echo ""
    echo -e "${CYAN}2️⃣  Click 'New Project' → 'Deploy from GitHub repo'${NC}"
    echo ""
    echo -e "${CYAN}3️⃣  Select 'Tourism_Booking_Sytem' repository${NC}"
    echo ""
    echo -e "${CYAN}4️⃣  Railway auto-detects Dockerfile and starts building${NC}"
    echo ""
    echo -e "${CYAN}5️⃣  Add PostgreSQL Database:${NC}"
    echo -e "${CYAN}    • Click 'Create' → 'Database' → 'PostgreSQL'${NC}"
    echo -e "${CYAN}    • Wait for provisioning (~1 min)${NC}"
    echo ""
    echo -e "${CYAN}6️⃣  Set Environment Variables:${NC}"
    echo -e "${CYAN}    • Go to Project Settings → Variables${NC}"
    echo -e "${CYAN}    • Add these variables:${NC}"
    echo ""
    
    cat << 'EOF' | sed "s/^/${YELLOW}/; s/$/\033[0m/"
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=info
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
RUN_MIGRATIONS=true
EOF
    
    echo ""
    echo -e "${CYAN}    • Database variables are auto-populated by Railway${NC}"
    echo ""
    echo -e "${CYAN}7️⃣  Deploy:${NC}"
    echo -e "${CYAN}    • Railway auto-deploys when you push to main${NC}"
    echo -e "${CYAN}    • Wait 3-5 minutes for deployment${NC}"
    echo ""
fi

# Verification
print_step 4 "Opening Railway Dashboard"

if command_exists xdg-open; then
    xdg-open "https://railway.app/dashboard" 2>/dev/null &
elif command_exists open; then
    open "https://railway.app/dashboard" 2>/dev/null &
else
    print_status "Please open: https://railway.app/dashboard" warning
fi

echo ""
print_status "Code has been pushed to GitHub" success
print_status "Railway dashboard is opening in your browser..." info
echo ""

# Post-Deployment Steps
print_step 5 "Next Steps"

print_status "1. Monitor deployment progress in Railway Dashboard" info
print_status "2. Wait for build to complete (usually 3-5 minutes)" info
print_status "3. Once deployed, test the health endpoint:" info
echo -e "${YELLOW}   curl https://your-railway-app.up.railway.app/health${NC}"
print_status "4. Check application logs for any errors" info
print_status "5. Verify database migrations ran successfully" info

echo ""
print_status "📚 For troubleshooting, see RAILWAY_DEPLOYMENT.md" info
print_status "📋 For detailed steps, see RAILWAY_CHECKLIST.md" info
echo ""

# Final status
echo ""
print_status "🎉 Deployment process initiated successfully!" success
print_status "Check your Railway dashboard to monitor the deployment." info
echo ""
