#!/bin/bash
# ═══════════════════════════════════════════════════════════
# Aqari Smart — Routine Production Deployment
# Run this ON THE CLOUDWAYS SERVER via SSH after git push
# ═══════════════════════════════════════════════════════════
set -euo pipefail

APP_DIR="/mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html"

echo "═══════════════════════════════════════════════════════"
echo " Aqari Smart — Routine Deploy"
echo " $(date '+%Y-%m-%d %H:%M:%S')"
echo "═══════════════════════════════════════════════════════"
echo ""

cd "$APP_DIR"

# ── Step 1: Maintenance mode ──
echo "[1/7] Entering maintenance mode..."
php artisan down --render="errors.503" --secret="aqari-deploy-bypass" || true
echo "  ✓ Maintenance mode ON (bypass: /aqari-deploy-bypass)"

# ── Step 2: Pull latest code ──
echo ""
echo "[2/7] Pulling latest code..."
git fetch origin main
git reset --hard origin/main

# ── Step 3: Update dependencies (if composer.lock changed) ──
echo ""
echo "[3/7] Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# ── Step 4: Run migrations + seed required data ──
echo ""
echo "[4/7] Running migrations..."
php artisan migrate --force
php artisan db:seed --class=AdDurationSeeder --force

# ── Step 5: Clear and rebuild caches ──
echo ""
echo "[5/7] Rebuilding caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Step 6: Restart queue workers ──
echo ""
echo "[6/7] Restarting queue workers..."
php artisan queue:restart

# ── Step 7: Exit maintenance mode ──
echo ""
echo "[7/7] Exiting maintenance mode..."
php artisan up

echo ""
echo "═══════════════════════════════════════════════════════"
echo " Deploy complete! $(date '+%Y-%m-%d %H:%M:%S')"
echo "═══════════════════════════════════════════════════════"
echo ""
echo "NOTE: If you updated frontend assets, upload them from local:"
echo "  scp -r public/build/* server:$APP_DIR/public/build/"
echo ""
