# Railway Deployment Guide

This guide will help you deploy the Tourism Booking System to Railway.

## Prerequisites

- Railway account (free tier available at [railway.app](https://railway.app))
- Docker (for local testing)
- Git repository with this code pushed to GitHub

## Quick Start

### 1. Connect Your GitHub Repository

1. Go to [Railway Dashboard](https://railway.app/dashboard)
2. Click "New Project" → "Deploy from GitHub repo"
3. Select your repository and authorize Railway
4. Railway will automatically detect the Dockerfile and start building

### 2. Configure Environment Variables

Railway will automatically create environment variables. Add the following to your Railway project:

#### Required Variables

```env
APP_NAME=TourismBooking
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    # Leave blank - will be generated automatically
APP_URL=https://your-railway-app.up.railway.app

LOG_CHANNEL=stack
LOG_LEVEL=info

# Database Configuration
DB_CONNECTION=postgres
DB_HOST=                    # Railway will populate this from linked database
DB_PORT=5432
DB_DATABASE=tourismdb
DB_USERNAME=                # Railway will populate this
DB_PASSWORD=                # Railway will populate this

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Migrations
RUN_MIGRATIONS=true         # Auto-run migrations on startup
RUN_SEEDS=false             # Set to true to seed demo data on first deploy

# Mail Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=              # Your SMTP username
MAIL_PASSWORD=              # Your SMTP password
MAIL_FROM_ADDRESS=noreply@tourismapp.com
MAIL_FROM_NAME="Tourism Booking"

# CORS
CORS_ALLOW_ORIGIN=*
```

### 3. Add PostgreSQL Database

1. In Railway Dashboard, click "Create" → "Database" → "PostgreSQL"
2. Railway will automatically link it and populate DB_* variables
3. The database will be provisioned and ready to use

### 4. Manual APP_KEY Generation (if needed)

If `APP_KEY` is not auto-generated, run in the Railway shell or locally:

```bash
php artisan key:generate
```

Then add the generated key to your Railway environment variables.

### 5. Access Your Application

Once the deployment completes (green status), visit the Railway URL displayed in your dashboard.

## Environment Variables Explained

| Variable | Purpose | Example |
|----------|---------|---------|
| `APP_ENV` | Environment type | `production` |
| `APP_DEBUG` | Show debug info | `false` (always false in production) |
| `RUN_MIGRATIONS` | Auto-run migrations on startup | `true` |
| `RUN_SEEDS` | Auto-seed database on startup | `false` (only for initial setup) |
| `CACHE_DRIVER` | Cache mechanism | `file` or `redis` |
| `SESSION_DRIVER` | Session storage | `file` or `database` |
| `QUEUE_CONNECTION` | Job queue type | `sync` or `database` |

## Monitoring & Logs

### View Logs
```bash
# Via Railway CLI
railway logs -f

# Or in Dashboard: Project → Deployments → Logs tab
```

### Database Migrations

The docker-entrypoint.sh script automatically runs migrations on startup. To check if they completed:

```bash
railway run php artisan migrate:status
```

### Cache Clearing

If you need to clear the cache:

```bash
railway run php artisan cache:clear
railway run php artisan config:cache
railway run php artisan route:cache
```

## Database Management

### Connect to Database

```bash
# Via Railway CLI
railway run php artisan tinker
```

### Backup Database

```bash
# Create a backup
railway run php artisan db:backup

# Or use pg_dump
pg_dump $DATABASE_URL > backup.sql
```

## Troubleshooting

### Build Fails

**Error**: "Dockerfile build failed"
- Check Docker syntax in Dockerfile
- Ensure all COPY paths exist
- Check available disk space in build container

**Solution**: 
```bash
# Test locally first
docker build -t tourism-booking .
```

### App Crashes After Deploy

**Error**: "503 Service Unavailable"
- Check logs: `railway logs -f`
- Common issues: APP_KEY not set, database not connected
- Verify DATABASE_URL in environment

**Solution**:
1. Check Railway logs for specific errors
2. Ensure PostgreSQL is linked
3. Verify all required env vars are set

### Migration Fails

**Error**: "php artisan migrate" fails during startup

**Solutions**:
1. Check database connection: `railway run php artisan db:connection`
2. View migrations: `railway run php artisan migrate:status`
3. Manually run: `railway run php artisan migrate --force`

### Storage/Cache Issues

Ensure storage directories are writable:

```bash
railway run php artisan storage:link
```

## Performance Optimization

### For Better Performance

1. **Use Redis** (optional):
   - Add Redis plugin in Railway
   - Set `CACHE_DRIVER=redis`
   - Set `SESSION_DRIVER=redis`

2. **Enable Configuration Caching**:
   ```bash
   railway run php artisan config:cache
   railway run php artisan route:cache
   ```

3. **Database Indexing**:
   - Ensure database tables have proper indexes
   - Check slow query logs if performance degrades

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Strong database password set
- [ ] CORS properly configured
- [ ] HTTPS enforced (automatic with Railway)
- [ ] API rate limiting enabled (if applicable)
- [ ] Sensitive files in .gitignore
- [ ] Regular backups enabled

## Custom Domain Setup

1. In Railway Dashboard → Project Settings
2. Add your custom domain (e.g., tourismapp.com)
3. Update DNS records as instructed by Railway
4. Update `APP_URL` in environment variables
5. Update `CORS_ALLOW_ORIGIN` if needed

## Scaling & Costs

- Railway free tier includes monthly credits
- Monitor resource usage in Dashboard
- Upgrade plan as needed for higher traffic
- PostgreSQL and other services are billed separately

## Additional Commands

```bash
# Clear all caches
railway run php artisan optimize:clear

# Tinker shell
railway run php artisan tinker

# Run tests
railway run php artisan test

# View database schema
railway run php artisan schema:show

# Generate API docs (if applicable)
railway run php artisan api:docs
```

## Next Steps

1. Set up a staging environment (create another Railway project)
2. Configure CI/CD for automated deployments
3. Set up monitoring and alerting
4. Regular database backups
5. Document any custom configurations

## Support

For Railway-specific issues, visit [Railway Documentation](https://docs.railway.app)

For Laravel-specific help, check [Laravel Documentation](https://laravel.com/docs)
