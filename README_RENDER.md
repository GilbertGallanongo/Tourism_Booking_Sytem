Render deployment instructions

1) Push your repository to GitHub (branch `main`).

2) Sign in to Render and create a new Web Service.
   - Connect your GitHub repo and select this repository.
   - Environment: Docker (Render will build using the `Dockerfile`).
   - Branch: `main`.

3) Set Environment Variables in Render's dashboard (Service → Environment):
   - Add the variables from `.env.production.example` and set `APP_KEY` to a value generated locally:

```bash
php artisan key:generate --show
```

Copy the output and paste it into `APP_KEY` on Render.

4) Add a managed PostgreSQL database in Render and set DB variables accordingly.
   - Render provides a managed Postgres service (Postgres is the supported option here).
   - Do not use SQLite in production on Render because the app filesystem is ephemeral.
   - You can either set the individual `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` variables, or set the single `DB_URL` (Render's connection string). Example `DB_URL` format:

```text
postgres://username:password@host:5432/databasename
```

   - Recommended: set `DB_CONNECTION=pgsql` and `DB_SSLMODE=require` if your Render DB requires SSL.

7) Auto-seeding on free plan
   - If you're on Render's free plan and want demo data automatically created during first startup, set `RUN_SEEDS=true` in the Web Service environment.
   - The container will run `php artisan db:seed --class=DatabaseSeeder --force` (with safe retries). Seeders in this project are idempotent (`updateOrCreate`), so re-deploys won't duplicate data.
   - To disable automatic seeding, leave `RUN_SEEDS=false` (default).

5) Set the Start Command or Health Check:
   - Render will use the Dockerfile; no custom start command is needed.
   - This repo now includes a startup entrypoint that runs migrations automatically on container start.
   - If you do not want auto-migrations, set `RUN_MIGRATIONS=false` in Render environment variables.

   - To run migrations manually after deploy, use a one-off shell on Render (paid plan only):

```bash
php artisan migrate --force
```

6) Deploy and monitor build logs. If the backend fails, check `Dockerfile`, `composer.lock`, and whether the managed database credentials are correct.

Notes:
- This is the backend deployment guide for Render (Postgres).
- The `Dockerfile` already installs `pdo_pgsql` so Postgres connections will work at runtime.
- This Dockerfile uses `php:8.2-apache` and a simple multi-stage build. For higher-performance production, consider using a dedicated PHP-FPM + Nginx setup or Laravel-specific platforms like Vapor.
