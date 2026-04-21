# Aqari Smart Production Setup

This file is the production operator guide.

Use it for:

- first production deployment
- routine production deployment
- one-time destructive prelaunch reset
- post-deploy verification

Local workflow lives in [LOCAL_SETUP.md](LOCAL_SETUP.md).

## Production Host

- host: `159.203.2.235`
- ssh user: `master_bggefyhrbt`
- application path: `/mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html`
- webroot: `/mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html/public`
- base domain: `aqarismart.com`

Do not store the production password in repo files.

## Required Environment

Your production `.env` must include the real database and Redis credentials from Cloudways, plus these app settings:

```env
APP_NAME="Aqari Smart"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://aqarismart.com

TENANCY_BASE_DOMAIN=aqarismart.com
CENTRAL_DOMAINS=aqarismart.com

SESSION_DOMAIN=.aqarismart.com
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=aqarismart.com,*.aqarismart.com

FILESYSTEM_DISK=public

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

SUPER_ADMIN_EMAILS=admin@aqarismart.com
SEED_SUPER_ADMIN_PASSWORD=Admin@123456
SEED_TENANT_OWNER_PASSWORD=Owner@123456
SEED_AGENT_PASSWORD=Agent@123456

PRELAUNCH_BACKUP_DIR=/mnt/BLOCKSTORAGE/home/master/backups/aqari-prelaunch
PRELAUNCH_MAINTENANCE_SECRET=aqari-prelaunch-bypass
```

## Routine Deploy

This is the normal production path.

### Local Machine

From `C:\laragon\www\aqarismart`:

```powershell
.\scripts\deploy-local.ps1 -SshTarget "master_bggefyhrbt@159.203.2.235"
```

What it does:

1. checks that git is clean
2. builds frontend assets locally
3. pushes `main`
4. uploads `public/build`
5. runs the production deploy script on the server

### Server-Side Routine Script

If you need to run it manually on the server:

```bash
ssh master_bggefyhrbt@159.203.2.235
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
bash scripts/deploy.sh
```

That script:

1. enables maintenance mode
2. fetches and resets to `origin/main`
3. runs `composer install --no-dev`
4. runs `php artisan migrate --force`
5. seeds ad durations
6. rebuilds caches
7. restarts the queue
8. brings the app back up

## First Production Deploy

Use this only on a fresh server or a clean new application path.

Local:

```powershell
.\scripts\deploy-local.ps1 -SshTarget "master_bggefyhrbt@159.203.2.235" -InitialDeploy
```

Manual server path:

```bash
ssh master_bggefyhrbt@159.203.2.235
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
bash scripts/deploy-initial.sh
```

## Prelaunch Reset

This is destructive. It rebuilds the database with the Iraq production seed data.

Dry run first:

```bash
ssh master_bggefyhrbt@159.203.2.235
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
php artisan app:prelaunch-reset --dry-run
```

Actual reset:

```bash
ssh master_bggefyhrbt@159.203.2.235
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
php artisan app:prelaunch-reset --force
```

Wrapper script:

```bash
ssh master_bggefyhrbt@159.203.2.235
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
bash scripts/prelaunch-reset.sh --force
```

What the reset does:

1. puts the app into maintenance mode
2. creates a SQL backup
3. runs `migrate:fresh --seed`
4. repairs `public/storage`
5. rebuilds caches
6. restarts queue workers
7. brings the app online again

## Exact Commands For This Server

### 1. SSH Into Production

```bash
ssh master_bggefyhrbt@159.203.2.235
```

### 2. Go To The App

```bash
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
```

### 3. Pull And Deploy Manually

```bash
git fetch origin main
git reset --hard origin/main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan db:seed --class=AdDurationSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

### 4. Upload Built Frontend Assets From Local

Run this on your Windows machine after `npm run build`:

```powershell
scp -r .\public\build master_bggefyhrbt@159.203.2.235:/mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html/public/
```

## Post-Deploy Validation

Run on the server:

```bash
ssh master_bggefyhrbt@159.203.2.235
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
bash scripts/validate-deploy.sh
```

Manual checks:

- `https://aqarismart.com`
- `https://aqarismart.com/login`
- `https://aqarismart.com/admin`
- `https://aqarismart.com/marketplace`
- `https://aqarismart.com/api/mobile/marketplace`
- one tenant home page
- one tenant dashboard
- one public property page

## DNS And SSL

Required DNS:

```text
A      @      -> 159.203.2.235
A      *      -> 159.203.2.235
CNAME  www    -> aqarismart.com
```

Required certificate coverage:

- `aqarismart.com`
- `*.aqarismart.com`

## Troubleshooting

### Assets 404

- confirm `public/build/manifest.json` exists
- re-upload `public/build`
- run `php artisan optimize:clear`

### Photos or logos do not load

- check `public/storage` is a symlink
- run `php artisan storage:link`
- confirm `storage/app/public` is writable

### Tenant subdomains do not resolve

- check wildcard DNS
- confirm `TENANCY_BASE_DOMAIN=aqarismart.com`
- make sure Cloudways app serves wildcard subdomains to this app

### Login/session issues across subdomains

- confirm `SESSION_DOMAIN=.aqarismart.com`
- confirm `SESSION_SECURE_COOKIE=true`
- confirm Redis-backed sessions are working

### Production 500 after deploy

Run:

```bash
cd /mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html
tail -n 200 storage/logs/laravel.log
php artisan optimize:clear
php artisan view:clear
```

Then re-check the failing route.

## Related Scripts

- [deploy-local.ps1](/C:/laragon/www/aqarismart/scripts/deploy-local.ps1)
- [deploy.sh](/C:/laragon/www/aqarismart/scripts/deploy.sh)
- [deploy-initial.sh](/C:/laragon/www/aqarismart/scripts/deploy-initial.sh)
- [prelaunch-reset.sh](/C:/laragon/www/aqarismart/scripts/prelaunch-reset.sh)
- [validate-deploy.sh](/C:/laragon/www/aqarismart/scripts/validate-deploy.sh)
