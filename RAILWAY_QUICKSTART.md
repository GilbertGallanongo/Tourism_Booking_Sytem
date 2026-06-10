# 🚀 Quick Start: Deploy to Railway

Your Tourism Booking System is now ready for Railway deployment!

## 30-Second Setup

### 1. **Verify Everything is Committed**
```bash
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

### 2. **Create Railway Project**
- Go to https://railway.app/dashboard
- Click **"New Project"** → **"Deploy from GitHub repo"**
- Select this repository
- Railway will auto-detect the Dockerfile

### 3. **Add Database**
- In Railway: Click **"Create"** → **"Database"** → **"PostgreSQL"**
- Railway auto-links it to your app

### 4. **Set Environment Variables**
In Railway **Project Settings** → **Variables**, add:
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    # (leave blank, will be generated)
LOG_LEVEL=info
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

Database variables are auto-populated by Railway after linking PostgreSQL.

### 5. **Deploy**
- Railway automatically deploys when you push to `main`
- Check logs at Project → Deployments

## ✅ Verification

Once deployed, verify it's working:

```bash
# Check health status
curl https://your-railway-app.up.railway.app/health

# Should return: {"status":"healthy"}
```

## 📋 What We Configured

✅ **Dockerfile** - Multi-stage optimized build  
✅ **docker-entrypoint.sh** - Auto-runs migrations  
✅ **railway.json** - Railway configuration  
✅ **.dockerignore** - Optimized build  
✅ **.env.railway** - Production environment template  
✅ **Health check** - `/health` endpoint  
✅ **GitHub Actions** - Deployment verification workflow  

## 📚 Full Documentation

- **[RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md)** - Complete deployment guide
- **[RAILWAY_CHECKLIST.md](RAILWAY_CHECKLIST.md)** - Pre-deployment verification

## 🔧 Environment Variables Reference

| Variable | Value | Notes |
|----------|-------|-------|
| `APP_ENV` | `production` | Required |
| `APP_DEBUG` | `false` | Always false in production |
| `DB_CONNECTION` | `pgsql` | PostgreSQL (auto-linked) |
| `CACHE_DRIVER` | `file` | Can upgrade to Redis later |
| `SESSION_DRIVER` | `file` | Session storage method |
| `QUEUE_CONNECTION` | `sync` | Job queue (sync for free tier) |

## 🆘 Quick Troubleshooting

**App crashes after deploy?**
```bash
# Check logs in Railway Dashboard
# Common issues:
# 1. APP_KEY not set
# 2. Database not connected
# 3. Missing env variables
```

**Database migrations failed?**
```bash
# Railway auto-runs migrations
# If they fail, check database connection
# View logs: Railway Dashboard → Logs
```

**Assets not loading?**
```bash
# Vite builds during Docker build
# Check build logs for npm errors
```

## 🎯 Next Steps

1. ✅ Push to GitHub
2. ✅ Create Railway project from this repo
3. ✅ Add PostgreSQL
4. ✅ Set environment variables
5. ✅ Monitor deployment
6. ✅ Test the application
7. 📝 Set up custom domain (optional)
8. 🔄 Set up CI/CD pipeline (optional)

## 📞 Support

- **Railway**: https://docs.railway.app
- **Laravel**: https://laravel.com/docs
- **Issues**: Check RAILWAY_CHECKLIST.md

---

**Ready to deploy?** Push this code to GitHub and Railway will automatically build and deploy! 🎉
