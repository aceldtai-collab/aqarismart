# Aqari Smart - Production Deployment Guide

## Production Environment Variables

Create a `.env` file on your production server with these critical settings:

```bash
# ═══════════════════════════════════════════════════════════
# PRODUCTION ENVIRONMENT CONFIGURATION
# ═══════════════════════════════════════════════════════════

APP_NAME="Aqari Smart"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://aqarismart.com

# Your actual production domain (without subdomain)
TENANCY_BASE_DOMAIN=aqarismart.com

# Central domains (base domain only for production)
CENTRAL_DOMAINS=aqarismart.com

# Session domain with leading dot for subdomain sharing
SESSION_DOMAIN=.aqarismart.com

# Sanctum stateful domains (include all subdomains pattern)
SANCTUM_STATEFUL_DOMAINS=aqarismart.com,*.aqarismart.com

# ═══════════════════════════════════════════════════════════
# DATABASE
# ═══════════════════════════════════════════════════════════

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# ═══════════════════════════════════════════════════════════
# SUPER ADMIN ACCESS
# ═══════════════════════════════════════════════════════════

SUPER_ADMIN_EMAILS=admin@aqarismart.com

# ═══════════════════════════════════════════════════════════
# MAIL CONFIGURATION
# ═══════════════════════════════════════════════════════════

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@aqarismart.com
MAIL_FROM_NAME="${APP_NAME}"

# ═══════════════════════════════════════════════════════════
# STORAGE & FILESYSTEM
# ═══════════════════════════════════════════════════════════

FILESYSTEM_DISK=public
# For production with S3/DO Spaces, use:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=
# AWS_BUCKET=
# AWS_URL=

# ═══════════════════════════════════════════════════════════
# CACHE & QUEUE
# ═══════════════════════════════════════════════════════════

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ═══════════════════════════════════════════════════════════
# OPTIONAL: STRIPE FOR BILLING
# ═══════════════════════════════════════════════════════════

# STRIPE_KEY=pk_live_...
# STRIPE_SECRET=sk_live_...
# STRIPE_WEBHOOK_SECRET=whsec_...
```

## How Tenant Subdomains Work

### Architecture

1. **Base Domain**: `aqarismart.com` (central admin, public landing)
2. **Tenant Subdomains**: `{tenant-slug}.aqarismart.com`
   - Example: `acme.aqarismart.com` for tenant with slug `acme`

### Request Flow

```
User visits: acme.aqarismart.com
    ↓
Nginx/Cloudways routes to: public_html/public/index.php
    ↓
Laravel IdentifyTenant middleware:
    - Extracts subdomain "acme"
    - Finds Tenant with slug="acme"
    - Sets tenant context
    - Updates APP_URL to https://acme.aqarismart.com
    - Sets Spatie permission team_id to tenant->id
    ↓
Routes/Controllers execute with tenant context
```

### DNS Configuration

For production, configure these DNS records:

```
A     @                   -> YOUR_SERVER_IP
A     *                   -> YOUR_SERVER_IP
CNAME www                 -> aqarismart.com
```

The wildcard `*` A record enables all subdomains (`tenant1.aqarismart.com`, `tenant2.aqarismart.com`, etc.)

### SSL Certificate

Request a wildcard SSL certificate:
```
*.aqarismart.com
aqarismart.com
```

On Cloudways, this is automatic with Let's Encrypt.

## Asset & Storage URLs

### Assets (CSS/JS)

- **Development**: Vite dev server at `http://localtest.me:5173` (HMR)
- **Production**: Built assets in `public/build/`
  - Files served directly by Nginx
  - URLs: `/build/assets/app.css`, `/build/assets/app2.js`

### Storage (Photos/Files)

- **Disk**: `public` (symlinked to `storage/app/public`)
- **URL Generation**: `Storage::disk('public')->url($path)`
  - Automatically uses current APP_URL (includes subdomain)
  - Example: `https://acme.aqarismart.com/storage/units/photo.jpg`

### Symlink Setup

On production, ensure storage symlink exists:
```bash
php artisan storage:link
```

## Pre-Deployment Checklist

### 1. Code Preparation

- [ ] All commits pushed to `main` branch
- [ ] Run `npm run build` locally (or will build on server)
- [ ] Verify no hardcoded `localhost` or `localtest.me` in PHP/Blade files
- [ ] Check `.env` is in `.gitignore` (never commit `.env`)

### 2. Server Setup

- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] MariaDB/MySQL configured
- [ ] Redis installed (for cache/sessions)
- [ ] Nginx configured with webroot pointing to `public_html/public`
- [ ] SSL certificate installed (wildcard `*.aqarismart.com`)
- [ ] DNS configured (wildcard A record)

### 3. Laravel Setup

```bash
# In public_html directory:
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Create storage symlink
rm -rf public/storage  # if exists as directory
php artisan storage:link

# Seed initial data (if needed)
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=LocationsSeeder
# php artisan db:seed --class=DemoPmsSeeder  # Only for demo tenants

# Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Permissions

```bash
# Set ownership (adjust user/group for your server)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 5. Create First Tenant

Via database or Tinker:
```bash
php artisan tinker
```

```php
$tenant = \App\Models\Tenant::create([
    'name' => 'Demo Company',
    'slug' => 'demo',  // Will be demo.aqarismart.com
    'plan' => 'pro',
]);

// Create owner user
$user = \App\Models\User::create([
    'name' => 'Owner Name',
    'email' => 'owner@demo.com',
    'password' => bcrypt('secure-password'),
]);

// Attach user to tenant
$tenant->users()->attach($user->id, ['role' => 'owner']);

// Sync permissions
\Artisan::call('permissions:sync', ['--tenant' => $tenant->id]);
```

### 6. Test Flows

- [ ] Visit `https://aqarismart.com` → Public landing page loads
- [ ] Visit `https://demo.aqarismart.com` → Tenant home page loads
- [ ] Login at `https://demo.aqarismart.com/login`
- [ ] Check dashboard loads: `https://demo.aqarismart.com/dashboard`
- [ ] Check admin panel: `https://aqarismart.com/admin` (super admin only)
- [ ] Upload a unit photo, verify it displays correctly
- [ ] Check asset loading (CSS/JS from `/build/`)

## Deployment Workflow

### Initial Deploy

1. **Clone/Upload Code**
   ```bash
   cd /path/to/public_html
   git clone git@github.com:aceldtai-collab/aqarismart.git .
   ```

2. **Build Assets Locally** (if server has no Node/npm)
   ```bash
   # On your local machine:
   npm run build
   
   # Upload public/build to server:
   scp -r public/build user@server:/path/to/public_html/public/
   ```

3. **Install Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

4. **Run Migrations & Setup**
   ```bash
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```

### Routine Deploys

1. **On Local Machine**
   ```bash
   git add .
   git commit -m "Your changes"
   git push origin main
   
   # Build fresh assets
   npm run build
   ```

2. **On Server**
   ```bash
   cd /path/to/public_html
   
   # Pull latest code
   git pull origin main
   
   # Update dependencies (if composer.json changed)
   composer install --no-dev --optimize-autoloader
   
   # Run migrations (if any)
   php artisan migrate --force
   
   # Upload fresh build from local, OR build on server if Node installed
   # Option A: Upload from local (recommended for Cloudways)
   # (scp public/build from local machine)
   
   # Option B: Build on server (if Node 20+ available)
   # npm ci
   # npm run build
   
   # Clear caches
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Cloudways-Specific Notes

**Server Path**: `/home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html`

**Webroot**: Already set to `public_html/public`

**No NVM**: Node v20.5.1 is system-wide but outdated for Vite build. **Build assets locally**, then upload.

**Deploy Script** (saved for you):
```bash
#!/bin/bash
# Local deploy to Cloudways

echo "Building assets..."
npm run build

echo "Creating deploy snapshot..."
git archive --format=zip -o /tmp/aqari-deploy.zip HEAD

echo "Uploading code..."
scp /tmp/aqari-deploy.zip cloudways-aqarismart:tmp/

echo "Extracting and activating..."
ssh cloudways-aqarismart 'unzip -oq tmp/aqari-deploy.zip -d public_html'

echo "Uploading built assets..."
scp -r public/build cloudways-aqarismart:public_html/public/

echo "Running post-deploy commands..."
ssh cloudways-aqarismart 'php public_html/artisan optimize:clear && php public_html/artisan storage:link || true && php public_html/artisan config:cache && php public_html/artisan route:cache && php public_html/artisan view:cache'

echo "Deploy complete!"
```

## Troubleshooting

### Issue: Subdomain not resolving

- Check DNS wildcard record exists: `*  A  YOUR_SERVER_IP`
- Verify `TENANCY_BASE_DOMAIN` in `.env`
- Check Nginx is NOT doing subdomain-specific vhosts

### Issue: Assets 404

- Verify `public/build/` exists and has files
- Check `public/build/manifest.json` exists
- Run `php artisan optimize:clear`

### Issue: Photos not loading

- Check `public/storage` is a symlink (not a directory)
- Run `php artisan storage:link`
- Verify `storage/app/public` directory exists and is writable

### Issue: Session/login issues across subdomains

- Set `SESSION_DOMAIN=.aqarismart.com` (with leading dot)
- Use Redis for session driver
- Check `SANCTUM_STATEFUL_DOMAINS` includes `*.aqarismart.com`

### Issue: Permission denied errors

- Check super admin email in `SUPER_ADMIN_EMAILS`
- Run `php artisan permissions:sync --tenant=TENANT_ID`
- Verify user is attached to tenant: `$tenant->users()->whereKey($userId)->exists()`

## Architecture Summary

```
aqarismart.com                    → Public landing + Super Admin panel
demo.aqarismart.com/              → Tenant public home
demo.aqarismart.com/search        → Tenant search
demo.aqarismart.com/dashboard     → Tenant dashboard (auth required)
demo.aqarismart.com/properties    → Tenant properties (staff only)
demo.aqarismart.com/settings      → Tenant settings (owner/admin)
```

**Middleware Layers**:
- `IdentifyTenant` → Resolves tenant from subdomain, sets context
- `auth` → Requires authenticated user
- `tenant` → Ensures user belongs to current tenant
- `role:owner,admin` → Spatie permission role check

**Permission System**:
- 63 permissions managed via `PackageService` and Spatie
- Super admins bypass all checks
- Owner/admin roles get implicit permission grants
- Member roles require explicit permissions

**Storage**:
- Photos stored in `storage/app/public/{units,properties,agents}/`
- Accessed via symlink: `public/storage` → `storage/app/public`
- URLs generated with current tenant subdomain

---

**Last Updated**: March 2026  
**Repository**: `git@github.com:aceldtai-collab/aqarismart.git`
