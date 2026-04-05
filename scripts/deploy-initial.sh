#!/bin/bash
# ═══════════════════════════════════════════════════════════
# Aqari Smart — Initial Production Deployment
# Run this ON THE CLOUDWAYS SERVER via SSH
# ═══════════════════════════════════════════════════════════
set -euo pipefail

APP_DIR="/mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html"
echo "═══════════════════════════════════════════════════════"
echo " Aqari Smart — Initial Production Deploy"
echo "═══════════════════════════════════════════════════════"
echo ""

cd "$APP_DIR"

# ── Step 1: Verify prerequisites ──
echo "[1/10] Checking prerequisites..."
php -v | head -1
redis-cli ping || { echo "ERROR: Redis is not running. Enable it in Cloudways panel."; exit 1; }
echo "  ✓ PHP and Redis OK"

# ── Step 2: Clone or verify git repo ──
echo ""
echo "[2/10] Checking git repository..."
if [ -d ".git" ]; then
    echo "  ✓ Git repo already exists"
    git pull origin main
else
    echo "  Cloning repository..."
    git clone git@github.com:aceldtai-collab/aqarismart.git .
fi

# ── Step 3: Install PHP dependencies ──
echo ""
echo "[3/10] Installing Composer dependencies (no-dev)..."
composer install --no-dev --optimize-autoloader --no-interaction

# ── Step 4: Verify .env exists ──
echo ""
echo "[4/10] Checking .env file..."
if [ ! -f ".env" ]; then
    echo "ERROR: .env file not found!"
    echo "Copy .env.example and fill in production values:"
    echo "  cp .env.example .env"
    echo "  nano .env"
    echo ""
    echo "Required values to set:"
    echo "  APP_ENV=production"
    echo "  APP_DEBUG=false"
    echo "  APP_URL=https://aqarismart.com"
    echo "  APP_KEY= (generate with: php artisan key:generate)"
    echo "  TENANCY_BASE_DOMAIN=aqarismart.com"
    echo "  SESSION_DOMAIN=.aqarismart.com"
    echo "  SESSION_SECURE_COOKIE=true"
    echo "  DB_DATABASE, DB_USERNAME, DB_PASSWORD (from Cloudways panel)"
    echo "  CACHE_STORE=redis"
    echo "  SESSION_DRIVER=redis"
    echo "  QUEUE_CONNECTION=redis"
    echo ""
    echo "After creating .env, re-run this script."
    exit 1
fi

# Verify critical env values
APP_ENV=$(grep "^APP_ENV=" .env | cut -d'=' -f2)
APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d'=' -f2)
if [ "$APP_ENV" != "production" ]; then
    echo "WARNING: APP_ENV is '$APP_ENV', expected 'production'"
fi
if [ "$APP_DEBUG" != "false" ]; then
    echo "WARNING: APP_DEBUG is '$APP_DEBUG', expected 'false'"
fi
echo "  ✓ .env file exists"

# ── Step 5: Generate app key (if empty) ──
echo ""
echo "[5/10] Checking APP_KEY..."
APP_KEY=$(grep "^APP_KEY=" .env | cut -d'=' -f2)
if [ -z "$APP_KEY" ]; then
    echo "  Generating application key..."
    php artisan key:generate --force
else
    echo "  ✓ APP_KEY already set"
fi

# ── Step 6: Database backup + migrations ──
echo ""
echo "[6/10] Running database migrations..."
DB_NAME=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
DB_USER=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)
echo "  Creating pre-migration backup..."
mysqldump -u "$DB_USER" -p "$DB_NAME" > "/tmp/aqari-pre-deploy-$(date +%Y%m%d-%H%M%S).sql" 2>/dev/null || echo "  (backup skipped — enter DB password manually if needed)"
echo "  Running migrations..."
php artisan migrate --force

# ── Step 7: Seed required data ──
echo ""
echo "[7/10] Seeding required data..."
php artisan db:seed --class=DatabaseSeeder --force
echo "  ✓ Core seeders complete"

# ── Step 8: Storage symlink ──
echo ""
echo "[8/10] Setting up storage..."
# Remove if it's a real directory (not a symlink)
if [ -d "public/storage" ] && [ ! -L "public/storage" ]; then
    rm -rf public/storage
fi
php artisan storage:link
echo "  ✓ Storage symlink created"

# ── Step 9: Permissions ──
echo ""
echo "[9/10] Setting file permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || echo "  (skipped — Cloudways manages permissions via application user)"

# ── Step 10: Cache optimization ──
echo ""
echo "[10/10] Caching configuration..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "  ✓ All caches built"

# ── Summary ──
echo ""
echo "═══════════════════════════════════════════════════════"
echo " Initial deployment complete!"
echo "═══════════════════════════════════════════════════════"
echo ""
echo "REMAINING MANUAL STEPS:"
echo ""
echo "1. Upload built assets from local machine:"
echo "   scp -r public/build/* server:$APP_DIR/public/build/"
echo ""
echo "2. Set up cron job in Cloudways panel:"
echo "   * * * * * cd $APP_DIR && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "3. Set up Supervisor queue worker in Cloudways panel:"
echo "   php $APP_DIR/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600"
echo ""
echo "4. Create first tenant (run: php artisan tinker):"
echo "   \$tenant = \\App\\Models\\Tenant::create(['name'=>'Agency Name','slug'=>'demo','plan'=>'pro']);"
echo "   \$user = \\App\\Models\\User::create(['name'=>'Owner','email'=>'owner@example.com','password'=>bcrypt('password')]);"
echo "   \$tenant->users()->attach(\$user->id, ['role'=>'owner']);"
echo "   \\Artisan::call('permissions:sync', ['--tenant' => \$tenant->id]);"
echo ""
echo "5. Verify at:"
echo "   https://aqarismart.com"
echo "   https://demo.aqarismart.com"
echo "   https://aqarismart.com/api/mobile/marketplace"
echo ""
