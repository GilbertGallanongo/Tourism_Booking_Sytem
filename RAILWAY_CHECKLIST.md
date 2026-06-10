# Railway Deployment Checklist

Use this checklist to ensure your application is ready for Railway deployment.

## Pre-Deployment Checklist

### 1. Code Quality & Configuration

- [ ] Git repository is clean and pushed to GitHub
- [ ] No sensitive information in `.env` files
- [ ] `.env.example` contains all required variables (with dummy values)
- [ ] `.env.railway` template is created with production settings
- [ ] All credentials/secrets removed from code
- [ ] `.gitignore` includes all sensitive files
- [ ] No hardcoded database connections or API keys
- [ ] APP_KEY is not hardcoded

### 2. Dependencies & Lock Files

- [ ] `composer.lock` is committed (for reproducible builds)
- [ ] `package-lock.json` or `package.json` is committed
- [ ] All dependencies are compatible with PHP 8.2+
- [ ] No local/dev dependencies in production build
- [ ] Run `composer install` locally to verify no errors
- [ ] Run `npm install` locally to verify no errors

### 3. Docker Configuration

- [ ] `Dockerfile` exists and is valid
- [ ] `Dockerfile` uses multi-stage build for optimization
- [ ] `.dockerignore` excludes unnecessary files
- [ ] `docker-entrypoint.sh` is executable and correct
- [ ] Build is tested locally: `docker build -t test .`
- [ ] `railway.json` is created and configured
- [ ] Health check endpoint exists at `/health`

### 4. Database Setup

- [ ] Database migrations are created (`database/migrations/`)
- [ ] Migration files are properly formatted
- [ ] Initial migration creates necessary tables
- [ ] Foreign keys are properly configured
- [ ] Indexes are added to improve performance
- [ ] Test locally: `php artisan migrate` works
- [ ] `RUN_MIGRATIONS=true` in deployment settings

### 5. Frontend Assets

- [ ] `vite.config.js` is properly configured
- [ ] Assets build successfully: `npm run build`
- [ ] Built assets output to `public/build/`
- [ ] Asset manifest is generated
- [ ] CSS/JS files are not in git (build generated)

### 6. Environment Variables

Required variables set in Railway:
- [ ] `APP_NAME` - Application name
- [ ] `APP_ENV` - Set to `production`
- [ ] `APP_DEBUG` - Set to `false`
- [ ] `APP_KEY` - Generate with `php artisan key:generate`
- [ ] `APP_URL` - Your Railway domain URL
- [ ] `DB_CONNECTION` - Set to `pgsql`
- [ ] `DB_HOST` - Database host (auto-linked by Railway)
- [ ] `DB_DATABASE` - Database name
- [ ] `DB_USERNAME` - Database user (auto-linked)
- [ ] `DB_PASSWORD` - Database password (auto-linked)
- [ ] `CACHE_DRIVER` - Set to `file`
- [ ] `SESSION_DRIVER` - Set to `file`
- [ ] `QUEUE_CONNECTION` - Set to `sync`
- [ ] `LOG_CHANNEL` - Set to `stack`

Optional but recommended:
- [ ] `MAIL_MAILER` - SMTP configuration
- [ ] `REDIS_HOST` - If using Redis cache
- [ ] `AWS_BUCKET` - If using S3 storage

### 7. Storage & Permissions

- [ ] Storage directory has write permissions
- [ ] `storage/logs/` directory exists
- [ ] `storage/framework/` directory exists
- [ ] `bootstrap/cache/` directory exists
- [ ] Permissions are set correctly in Dockerfile
- [ ] No sensitive files in storage directory

### 8. Routes & Health

- [ ] Health check route exists at `/health`
- [ ] Main routes are tested locally
- [ ] API routes are properly configured
- [ ] CORS is properly configured
- [ ] Authentication routes work
- [ ] Database routes/queries tested

### 9. Logging & Monitoring

- [ ] Log channel is configured
- [ ] Log level is set appropriately
- [ ] Application logs are written to stdout
- [ ] Error handling is in place
- [ ] Exception handling is configured

### 10. Security

- [ ] `APP_DEBUG=false` in production
- [ ] Sensitive data is in environment variables
- [ ] Database password is strong
- [ ] CORS_ALLOW_ORIGIN is configured correctly
- [ ] No SQL injection vulnerabilities
- [ ] Input validation is in place
- [ ] XSS protection is enabled
- [ ] CSRF tokens are implemented

### 11. Performance

- [ ] Database queries are optimized
- [ ] Indexes are created on foreign keys
- [ ] N+1 queries are minimized
- [ ] Large queries use pagination
- [ ] Images are optimized
- [ ] CSS/JS are minified (Vite does this)

### 12. Testing

- [ ] Unit tests pass: `php artisan test --unit`
- [ ] Feature tests pass: `php artisan test --feature`
- [ ] Database migrations can be rolled back and re-run
- [ ] Application starts without errors locally
- [ ] Routes return expected responses

## Deployment Steps

### 1. Initial Setup in Railway

```bash
# 1. Go to https://railway.app/dashboard
# 2. Click "New Project"
# 3. Select "Deploy from GitHub repo"
# 4. Authorize and select your repository
```

### 2. Add Services

```bash
# Railway will auto-detect Dockerfile
# 1. Add PostgreSQL database service
# 2. Link database to your app service
```

### 3. Configure Environment

```bash
# In Railway Dashboard:
# Project Settings → Variables
# Add all variables from .env.railway template
```

### 4. Deploy

```bash
# Railway automatically deploys on push to main branch
# Monitor logs in: Project → Deployments → Logs
```

## Post-Deployment Verification

- [ ] Application loads without errors
- [ ] Health check passes: `curl https://your-app.up.railway.app/health`
- [ ] Database migrations ran successfully
- [ ] Homepage loads and displays content
- [ ] Authentication works
- [ ] Database queries work correctly
- [ ] Logs show no errors
- [ ] Assets are loading (CSS/JS)
- [ ] All API endpoints respond correctly

## Troubleshooting

If deployment fails, check:

1. **Build Logs**: Railway Dashboard → Deployments → Logs
   - Look for npm/composer errors
   - Check Dockerfile syntax

2. **Runtime Logs**: Click on deployment to see runtime errors
   - Database connection issues
   - Missing environment variables
   - Application errors

3. **Common Issues**:
   - `APP_KEY` not generated → Set in env variables
   - Database not connecting → Check `DB_*` variables
   - Assets not loading → Check Vite build output
   - Migrations failing → Check database connection, run manually

## Rollback

If something goes wrong:

```bash
# Railway Dashboard → Deployments
# Click on previous successful deployment
# Click "Redeploy"
```

## Monitoring & Maintenance

- [ ] Set up log alerts for errors
- [ ] Monitor CPU/Memory usage
- [ ] Plan database backups
- [ ] Monitor database growth
- [ ] Set up custom domain (optional)
- [ ] Configure SSL certificate (automatic with Railway)
- [ ] Plan scaling strategy as traffic grows

## Next Steps

1. **Staging Environment**: Create separate Railway project for staging
2. **CI/CD Pipeline**: Set up automatic tests before deployment
3. **Monitoring**: Integrate error tracking (e.g., Sentry)
4. **Backups**: Set up automated database backups
5. **Documentation**: Document deployment procedures
6. **Team Access**: Grant team members Railway access

---

**Need Help?**
- Railway Docs: https://docs.railway.app
- Laravel Docs: https://laravel.com/docs
- Laravel Deployment: https://laravel.com/docs/deployment
