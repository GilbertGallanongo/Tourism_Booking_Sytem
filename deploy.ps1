#!/usr/bin/env pwsh
# Railway Deployment Script for Tourism Booking System

param(
    [string]$CommitMessage = "Deploy to Railway"
)

Write-Host ""
Write-Host "=== Railway Deployment ===" -ForegroundColor Cyan
Write-Host ""

# Check git exists
$gitCheck = Get-Command git -ErrorAction SilentlyContinue
if (-not $gitCheck) {
    Write-Host "ERROR: Git not found" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path ".git")) {
    Write-Host "ERROR: Not a git repository" -ForegroundColor Red
    exit 1
}

Write-Host "Step 1: Checking Repository" -ForegroundColor Cyan
Write-Host "[OK] Git repository found" -ForegroundColor Green
Write-Host ""

Write-Host "Step 2: Preparing Git" -ForegroundColor Cyan
$status = git status --short
if ($status) {
    Write-Host "Uncommitted changes found:" -ForegroundColor Yellow
    $response = Read-Host "Commit and push? (y/n)"
    if ($response -eq 'y') {
        git add .
        git commit -m $CommitMessage
        Write-Host "[OK] Committed" -ForegroundColor Green
    }
} else {
    Write-Host "[OK] No uncommitted changes" -ForegroundColor Green
}

Write-Host "Pushing to GitHub..." -ForegroundColor Yellow
$branch = git rev-parse --abbrev-ref HEAD
git push origin $branch
if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Pushed to GitHub" -ForegroundColor Green
} else {
    Write-Host "ERROR: Push failed" -ForegroundColor Red
    exit 1
}
Write-Host ""

Write-Host "Step 3: Railway Deployment Instructions" -ForegroundColor Cyan
Write-Host ""

Write-Host "Your code is now on GitHub!" -ForegroundColor Green
Write-Host ""
Write-Host "Complete these steps in Railway Dashboard:" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Go to: https://railway.app/dashboard" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Click 'New Project' > 'Deploy from GitHub repo'" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Select 'Tourism_Booking_Sytem' repository" -ForegroundColor Cyan
Write-Host ""
Write-Host "4. Railway auto-detects Dockerfile and builds" -ForegroundColor Cyan
Write-Host ""
Write-Host "5. Add PostgreSQL Database:" -ForegroundColor Cyan
Write-Host "   - Click 'Create' > 'Database' > 'PostgreSQL'" -ForegroundColor Cyan
Write-Host "   - Wait ~1 minute for provisioning" -ForegroundColor Cyan
Write-Host ""
Write-Host "6. Set Environment Variables:" -ForegroundColor Cyan
Write-Host "   - Go to Project Settings > Variables" -ForegroundColor Cyan
Write-Host "   - Add these:" -ForegroundColor Cyan
Write-Host ""

$envVars = @"
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=info
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
RUN_MIGRATIONS=true
"@

Write-Host $envVars -ForegroundColor Yellow
Write-Host ""
Write-Host "   (DB_* variables auto-populated by Railway)" -ForegroundColor Gray
Write-Host ""
Write-Host "7. Deploy!" -ForegroundColor Cyan
Write-Host "   - Railway auto-deploys when code is pushed" -ForegroundColor Cyan
Write-Host "   - Takes 3-5 minutes" -ForegroundColor Cyan
Write-Host ""

Write-Host "Opening Railway Dashboard..." -ForegroundColor Yellow
Start-Process "https://railway.app/dashboard"
Start-Sleep -Seconds 2

Write-Host ""
Write-Host "=== SUCCESS ===" -ForegroundColor Green
Write-Host ""
Write-Host "Code pushed to GitHub. Open the Railway dashboard link above." -ForegroundColor Green
Write-Host "See RAILWAY_QUICKSTART.md for quick reference." -ForegroundColor Green
Write-Host ""
