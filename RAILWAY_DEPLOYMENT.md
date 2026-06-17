# Railway Deployment Guide - Tourism Booking System

This guide walks you through deploying your Laravel 12 Tourism Booking System to Railway, step by step.

---

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Step 1: Prepare Your Repository](#step-1-prepare-your-repository)
3. [Step 2: Create Railway Account](#step-2-create-railway-account)
4. [Step 3: Connect Repository](#step-3-connect-repository)
5. [Step 4: Configure Database](#step-4-configure-database)
6. [Step 5: Set Environment Variables](#step-5-set-environment-variables)
7. [Step 6: Deploy](#step-6-deploy)
8. [Step 7: Run Migrations](#step-7-run-migrations)
9. [Step 8: Verify Deployment](#step-8-verify-deployment)
10. [Troubleshooting](#troubleshooting)

---

## Prerequisites

Before starting, ensure you have:
- A GitHub account with your repository
- Railway account (create at https://railway.app)
- Git installed and configured locally
- The project pushed to GitHub

---

## Step 1: Prepare Your Repository

### 1.1 Ensure .env.example is properly configured
Your `.env.example` should contain all configuration templates. Check it exists in your repo root.

### 1.2 Add deployment files to your repository
This includes:
- `Procfile` - tells Railway how to run your app
- `.railwayignore` - files to ignore in Railway

These files are created in the next steps.

### 1.3 Update .gitignore (if needed)
Ensure these are NOT committed:
```
.env
.env.*.php
node_modules/
vendor/
storage/logs/*
bootstrap/cache/*
public/hot
```

### 1.4 Verify Git is clean
```bash
git status
```
All changes should be committed before proceeding.

---

## Step 2: Create Railway Account

1. Go to **https://railway.app**
2. Click **"Start Your Project"**
3. Sign up using GitHub (recommended for easy integration)
4. Authorize Railway to access your GitHub account
5. Complete the setup

---

## Step 3: Connect Repository

### 3.1 Create New Railway Project
1. Go to https://railway.app/dashboard
2. Click **"New Project"**
3. Select **"Deploy from GitHub"**
4. Authorize Railway if prompted
5. Search for your repository: `Tourism_Booking_Sytem`
6. Select it and click **"Deploy Now"**

### 3.2 Wait for Initial Build (may fail - this is normal!)
Railway will attempt a build. It may fail because we haven't configured the database yet. This is expected.

---

## Step 4: Configure Database

### 4.1 Add PostgreSQL Database Plugin

**In your Railway project:**
1. Click **"+ Add"** button (top right)
2. Select **"Database"** → **"PostgreSQL"**
3. Wait for PostgreSQL to be provisioned (2-3 minutes)

### 4.2 Connect Database to Your App

1. Click on **PostgreSQL** service in your Railway project
2. Click **"Connect"** button
3. Select your Laravel app from the dropdown
4. This automatically sets database environment variables

---

## Step 5: Set Environment Variables

### 5.1 Set Variables in Railway Dashboard

In your Railway project, click on your Laravel app service, then go to **"Variables"** tab.

Add the following environment variables:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=<paste your generated Laravel key here>
APP_URL=https://tourism-booking-system-production-b0bb.up.railway.app

DB_CONNECTION=pgsql
DATABASE_URL=${{ Postgres.DATABASE_PUBLIC_URL }}
DB_URL=${{ Postgres.DATABASE_PUBLIC_URL }}
# Use the internal host only if it is resolvable in your runtime environment.
DB_HOST=${{ Postgres.PGHOST }}
DB_PORT=${{ Postgres.PGPORT }}
DB_DATABASE=${{ Postgres.PGDATABASE }}
DB_USERNAME=${{ Postgres.PGUSER }}
DB_PASSWORD=${{ Postgres.PGPASSWORD }}
DB_SSLMODE=require

FILESYSTEM_DISK=public
# Recommended when using Railway Volumes for uploaded images:
# PUBLIC_STORAGE_PATH=${{RAILWAY_VOLUME_MOUNT_PATH}}/storage/app/public
QUEUE_CONNECTION=sync
CACHE_STORE=database
SESSION_DRIVER=database

SANCTUM_STATEFUL_DOMAINS=tourism-booking-system-production-b0bb.up.railway.app
SESSION_DOMAIN=.railway.app

LOG_CHANNEL=single
```

### 5.2 Generate APP_KEY

1. Go back to your laptop terminal
2. Run: `php artisan key:generate`
3. Copy the key from your `.env` file (it looks like: `base64:xxxxx...`)
4. Paste it into Railway's `APP_KEY` variable

### 5.3 Set Your Domain

If you have a custom domain, update `APP_URL` and `SANCTUM_STATEFUL_DOMAINS` accordingly.

---

## Step 6: Deploy

### 6.1 Trigger Manual Deployment

1. In Railway dashboard, go to your **Laravel app**
2. Click **"Deployments"** tab
3. Click **"Redeploy"** button on the latest deployment
4. Wait for the build to complete (5-10 minutes)

**What happens during build:**
- Downloads composer dependencies
- Builds frontend assets with Vite
- Compiles optimizations
- Prepares the application

---

## Step 7: Run Migrations

### 7.1 Execute Migrations on Railway

Once deployment is successful:

1. Click on your **Laravel app** in Railway
2. Go to the **"Logs"** tab to verify the app is running
3. Click **"Command Palette"** (top right) or use railway CLI

**Option A: Using Railway CLI (Recommended)**
```bash
npm install -g @railway/cli
railway link
railway run php artisan migrate --force
railway run php artisan db:seed --force
```

**Option B: Using Heroku-like exec commands**
In Railway dashboard:
1. Click your Laravel app
2. Look for a terminal or exec option
3. Run: `php artisan migrate --force`
4. Run: `php artisan db:seed --force` (if you want sample data)

### 7.2 Create API Token (Optional)
If your API requires tokens:
```bash
railway run php artisan tinker
# Then in tinker:
$user = App\Models\User::first();
$token = $user->createToken('api-token')->plainTextToken;
```

---

## Step 8: Verify Deployment

### 8.1 Check Application Health

1. Go to Railway dashboard → Your app → **"Settings"**
2. Find your **"Public Domain"** (looks like: `your-app-xxxx.railway.app`)
3. Click the domain link to open your app
4. You should see your Tourism Booking System home page

### 8.2 Test Key Features

- **Home page loads** ✓
- **Login works** (test with admin@tourph.com / password123)
- **Packages display** ✓
- **API endpoints work** (test `/api/packages`)

### 8.3 Check Logs for Errors

In Railway dashboard:
1. Click your **Laravel app**
2. Click **"Logs"** tab
3. Look for any ERROR or CRITICAL messages
4. Address any issues (see Troubleshooting section)

---

## Troubleshooting

### Build Fails: "Missing composer.lock"
**Solution:** Commit `composer.lock` to Git:
```bash
git add composer.lock
git commit -m "Add composer lock file"
git push
```

### Build Fails: "PHP Version Mismatch"
**Solution:** Railway requires `composer.json` to specify PHP ^8.2. Verify in your `composer.json`:
```json
"require": {
    "php": "^8.2",
    ...
}
```

### App Crashes: "No APP_KEY Set"
**Solution:** Generate and set the APP_KEY as described in Step 5.2

### Database Connection Error
**Solution:** 
1. Verify PostgreSQL is connected in Step 4.2
2. Check environment variables are correct
3. Run migrations as described in Step 7.1

### Migrations Won't Run
**Solution:**
```bash
# Force migrations in production
railway run php artisan migrate --force --step
```

### Static Assets (CSS/JS) Not Loading
**Solution:**
1. Ensure Vite build ran successfully during deployment
2. Check `APP_URL` is correct in environment variables
3. Run: `railway run php artisan storage:link`

### File Upload Issues
**Solution:**
Railway uses ephemeral storage. For persistent file uploads:
1. Use **Railway Volumes** (Pro feature) or
2. Switch to cloud storage (S3, etc.)

---

## Next Steps

### Security Checklist
- [ ] Set `APP_DEBUG=false` (already done)
- [ ] Enable HTTPS (Railway provides automatic SSL)
- [ ] Set strong `APP_KEY`
- [ ] Use environment variables for sensitive data
- [ ] Enable CSRF protection (enabled by default)

### Performance Optimization
- [ ] Enable caching: `php artisan config:cache`
- [ ] Optimize autoloader: `composer install --optimize-autoloader`
- [ ] Clear logs periodically to save disk space

### Monitoring
- In Railway dashboard, monitor:
  - Deploy logs
  - Application logs
  - Memory usage
  - CPU usage

---

## Useful Railway CLI Commands

```bash
# Install Railway CLI
npm install -g @railway/cli

# Link to your Railway project
railway link

# Run a command on Railway
railway run php artisan migrate

# View logs
railway logs

# Open Railway dashboard
railway open
```

---

## Support & Resources

- **Railway Docs:** https://docs.railway.app
- **Laravel Deployment:** https://laravel.com/docs/12/deployment
- **PostgreSQL with Laravel:** https://laravel.com/docs/12/database

---

**Good luck with your deployment! 🚀**
