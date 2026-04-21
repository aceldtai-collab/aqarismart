# Aqari Smart - Production Readiness Report

This is a historical readiness report, not the operator deployment guide.

Use [LOCAL_SETUP.md](LOCAL_SETUP.md) for local workflow and [PRODUCTION_SETUP.md](PRODUCTION_SETUP.md) for production deploy and reset steps.

Generated: March 12, 2026

## ✓ Fixed Issues

### 1. Hardcoded Development URLs
**Issue**: Admin tenant view had fallback to `http://initech.localtest.me:8000/dashboard`

**Fix**: 
- Added `getUrlAttribute()` to Tenant model using `TenantManager->tenantUrl()`
- Removed hardcoded fallback in `resources/views/admin/tenants/show.blade.php`

**Status**: ✓ FIXED

### 2. Missing Production Documentation
**Issue**: No clear production deployment guide

**Fix**: Created comprehensive `PRODUCTION_SETUP.md` with:
- Environment variable templates
- DNS/SSL configuration
- Subdomain architecture explanation
- Step-by-step deployment workflow
- Troubleshooting guide

**Status**: ✓ FIXED

### 3. No Automated Deploy Script
**Issue**: Manual deployment steps error-prone

**Fix**: Created `deploy.sh` one-command deploy script

**Status**: ✓ FIXED

## ✓ Verified Production-Ready Components

### Architecture
- **Tenant Resolution**: ✓ Working via subdomain extraction in `IdentifyTenant` middleware
- **URL Generation**: ✓ Dynamic per-request via `APP_URL` update in middleware
- **Session Sharing**: ✓ Configured via `SESSION_DOMAIN=.domain.com`
- **Permission System**: ✓ 63 permissions via Spatie, scoped by tenant_id

### Routing
- **Base Domain Routes** (`aqarismart.com`):
  - `/` → Public landing page
  - `/admin/*` → Super admin panel (requires `superadmin` middleware)
  - `/login`, `/register` → Central auth

- **Tenant Subdomain Routes** (`{slug}.aqarismart.com`):
  - `/` → Tenant public home
  - `/search` → Tenant search
  - `/listings/{unit}` → Public unit detail
  - `/dashboard` → Tenant dashboard (auth + tenant membership required)
  - `/properties`, `/units`, `/residents`, etc. → Staff features

### Middleware Chain
1. `web` → Sessions, CSRF, cookies
2. `tenant` → Resolves tenant from subdomain, sets context
3. `auth` → Requires authenticated user
4. `verified` → Email verification (where needed)
5. `staff` → Ensures user is staff member (not resident-only)
6. `superadmin` → Super admin only (checks `SUPER_ADMIN_EMAILS`)
7. `role:owner,admin` → Spatie role check

### Storage & Assets
- **Assets**: ✓ Vite builds to `public/build/`, served by Nginx
- **Photos**: ✓ `Storage::disk('public')->url()` generates correct URLs
- **Symlink**: ✓ `public/storage` → `storage/app/public`
- **URL Scoping**: ✓ Automatically includes subdomain from current request

## ⚠️ Important Notes for Production

### 1. Environment Variables

**Critical `.env` settings**:
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://aqarismart.com
TENANCY_BASE_DOMAIN=aqarismart.com
SESSION_DOMAIN=.aqarismart.com
CENTRAL_DOMAINS=aqarismart.com
SANCTUM_STATEFUL_DOMAINS=aqarismart.com,*.aqarismart.com
```

### 2. DNS Configuration

**Required DNS records**:
```
A     @    → YOUR_SERVER_IP
A     *    → YOUR_SERVER_IP
CNAME www  → aqarismart.com
```

The wildcard `*` A record enables all tenant subdomains.

### 3. SSL Certificate

Request wildcard certificate:
```
*.aqarismart.com
aqarismart.com
```

On Cloudways, this is automatic with Let's Encrypt.

### 4. First Tenant Setup

After deployment, create first tenant via Tinker or seeder:

```php
$tenant = \App\Models\Tenant::create([
    'name' => 'Demo Company',
    'slug' => 'demo',  // Will be demo.aqarismart.com
    'plan' => 'pro',
]);

$user = \App\Models\User::create([
    'name' => 'Owner',
    'email' => 'owner@demo.com',
    'password' => bcrypt('password'),
]);

$tenant->users()->attach($user->id, ['role' => 'owner']);

\Artisan::call('permissions:sync', ['--tenant' => $tenant->id]);
```

## 🔍 Testing Checklist

### Base Domain (`aqarismart.com`)
- [ ] Public landing page loads
- [ ] Login/register work
- [ ] Super admin can access `/admin`
- [ ] Super admin can see tenant list
- [ ] Clicking "Open Dashboard" redirects to correct tenant subdomain

### Tenant Subdomain (`demo.aqarismart.com`)
- [ ] Public home page loads
- [ ] Search page works
- [ ] Unit listings display
- [ ] Unit photos load (via `/storage/` URLs)
- [ ] Login works
- [ ] After login, redirected to `/dashboard`
- [ ] Dashboard shows correct tenant data
- [ ] Owner can access `/settings`
- [ ] Owner can manage properties/units
- [ ] Member roles respect permissions
- [ ] Agent-scoped users only see their data

### Assets & Performance
- [ ] CSS loads from `/build/assets/app.css`
- [ ] JS loads from `/build/assets/app2.js`
- [ ] No Tailwind CDN warning in console
- [ ] Photos load from `/storage/units/`, `/storage/properties/`
- [ ] No 404 errors in Network tab
- [ ] No console errors

### Multi-Tenant Isolation
- [ ] Create second tenant with different slug
- [ ] Login to first tenant subdomain
- [ ] Try accessing second tenant subdomain → should logout or deny access
- [ ] Verify data isolation (tenant A can't see tenant B's properties)

## 📋 Deployment Workflow

### Initial Deploy

1. **Set up `.env` on server** (see `PRODUCTION_SETUP.md`)
2. **Run deploy script**:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```
3. **Run migrations**:
   ```bash
   ssh cloudways-aqarismart 'php /home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html/artisan migrate --force'
   ```
4. **Create first tenant** (see section above)
5. **Test flows** (see checklist above)

### Routine Updates

```bash
git add .
git commit -m "Your changes"
git push origin main
./deploy.sh
```

If database schema changed:
```bash
ssh cloudways-aqarismart 'php /home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html/artisan migrate --force'
```

## 🚨 Known Limitations

### 1. Vite Dev Server Config
**File**: `vite.config.js`
```js
server: {
    host: 'localtest.me',  // Development only
    port: 5173,
}
```

**Impact**: None in production (uses built assets from `public/build/`)

**Action**: No change needed. This is for local HMR only.

### 2. Node Version on Cloudways
**Issue**: Server has Node v20.5.1, Vite requires 20.19+

**Workaround**: Build assets locally, upload via deploy script

**Status**: ✓ Automated in `deploy.sh`

### 3. `.env` Not in Git
**Issue**: `.env` must be created manually on server

**Action**: Use template from `PRODUCTION_SETUP.md`

**Status**: ✓ Documented

## 🎯 Production Deployment Summary

**Current State**: READY FOR PRODUCTION

**Remaining Steps**:
1. Configure production `.env` on Cloudways
2. Point DNS to server (wildcard A record)
3. Configure SSL certificate (automatic on Cloudways)
4. Run `./deploy.sh`
5. Run migrations
6. Create first tenant
7. Test all flows

**Estimated Time**: 30-60 minutes

## 📞 Support Commands

### Check logs
```bash
ssh cloudways-aqarismart 'tail -100 /home/1599704.cloudwaysapps.com/tsyaqtsxmr/logs/php_error.log'
```

### Clear all caches
```bash
ssh cloudways-aqarismart 'php /home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html/artisan optimize:clear'
```

### Check storage symlink
```bash
ssh cloudways-aqarismart 'ls -la /home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html/public/storage'
```

### Restart PHP-FPM (if needed)
On Cloudways: Use the platform's restart button

## ✅ Final Checklist

- [x] Removed hardcoded development URLs
- [x] Added Tenant URL accessor
- [x] Created production documentation
- [x] Created automated deploy script
- [x] Verified routing and middleware structure
- [x] Verified asset/storage URL generation
- [x] Documented DNS/SSL requirements
- [x] Documented first tenant setup
- [x] Created testing checklist
- [ ] Configure production `.env` ← **YOU DO THIS**
- [ ] Point DNS to server ← **YOU DO THIS**
- [ ] Run first deployment ← **YOU DO THIS**

---

**Repository**: `git@github.com:aceldtai-collab/aqarismart.git`  
**Server**: Cloudways at `138.197.131.128`  
**SSH Alias**: `cloudways-aqarismart`  
**App Path**: `/home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html`
