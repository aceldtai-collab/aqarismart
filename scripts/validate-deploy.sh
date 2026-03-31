#!/bin/bash
# ═══════════════════════════════════════════════════════════
# Aqari Smart — Post-Deploy Validation
# Run ON THE CLOUDWAYS SERVER after deployment
# ═══════════════════════════════════════════════════════════
set -uo pipefail

APP_DIR="/home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html"
DOMAIN="aqarismart.com"
PASS=0
FAIL=0
WARN=0

check() {
    local label="$1"
    local result="$2"
    if [ "$result" = "0" ]; then
        echo "  ✓ $label"
        PASS=$((PASS + 1))
    else
        echo "  ✗ $label"
        FAIL=$((FAIL + 1))
    fi
}

warn() {
    echo "  ⚠ $1"
    WARN=$((WARN + 1))
}

echo "═══════════════════════════════════════════════════════"
echo " Post-Deploy Validation — $(date '+%Y-%m-%d %H:%M:%S')"
echo "═══════════════════════════════════════════════════════"
echo ""

cd "$APP_DIR"

# ── Infrastructure ──
echo "[Infrastructure]"
check "PHP 8.2+" "$(php -r 'echo version_compare(PHP_VERSION,"8.2.0",">=") ? 0 : 1;')"
redis-cli ping > /dev/null 2>&1; check "Redis running" "$?"
check ".env exists" "$([ -f .env ] && echo 0 || echo 1)"
check "APP_ENV=production" "$(grep -q '^APP_ENV=production' .env && echo 0 || echo 1)"
check "APP_DEBUG=false" "$(grep -q '^APP_DEBUG=false' .env && echo 0 || echo 1)"
check "SESSION_SECURE_COOKIE=true" "$(grep -q '^SESSION_SECURE_COOKIE=true' .env && echo 0 || echo 1)"
check "CACHE_STORE=redis" "$(grep -q '^CACHE_STORE=redis' .env && echo 0 || echo 1)"
check "SESSION_DRIVER=redis" "$(grep -q '^SESSION_DRIVER=redis' .env && echo 0 || echo 1)"
check "QUEUE_CONNECTION=redis" "$(grep -q '^QUEUE_CONNECTION=redis' .env && echo 0 || echo 1)"

# ── Filesystem ──
echo ""
echo "[Filesystem]"
check "storage/logs writable" "$([ -w storage/logs ] && echo 0 || echo 1)"
check "bootstrap/cache writable" "$([ -w bootstrap/cache ] && echo 0 || echo 1)"
check "public/storage is symlink" "$([ -L public/storage ] && echo 0 || echo 1)"
check "public/build/manifest.json exists" "$([ -f public/build/manifest.json ] && echo 0 || echo 1)"
check "public/build/assets/ has files" "$([ -n \"$(ls public/build/assets/ 2>/dev/null)\" ] && echo 0 || echo 1)"

# ── Laravel ──
echo ""
echo "[Laravel]"
php artisan config:cache > /dev/null 2>&1; check "config:cache succeeds" "$?"
php artisan route:cache > /dev/null 2>&1; check "route:cache succeeds" "$?"

# ── HTTP checks ──
echo ""
echo "[HTTP Endpoints]"

# Root domain
HTTP_ROOT=$(curl -s -o /dev/null -w "%{http_code}" -L "https://${DOMAIN}/" --max-time 10 2>/dev/null)
check "https://${DOMAIN}/ responds (${HTTP_ROOT})" "$([ "$HTTP_ROOT" = "200" ] || [ "$HTTP_ROOT" = "302" ] && echo 0 || echo 1)"

# Health check
HTTP_UP=$(curl -s -o /dev/null -w "%{http_code}" "https://${DOMAIN}/up" --max-time 10 2>/dev/null)
check "https://${DOMAIN}/up responds 200 (${HTTP_UP})" "$([ "$HTTP_UP" = "200" ] && echo 0 || echo 1)"

# Mobile API marketplace
HTTP_API=$(curl -s -o /dev/null -w "%{http_code}" "https://${DOMAIN}/api/mobile/marketplace" --max-time 10 2>/dev/null)
check "Mobile API /api/mobile/marketplace responds 200 (${HTTP_API})" "$([ "$HTTP_API" = "200" ] && echo 0 || echo 1)"

# Mobile API tenants
HTTP_TENANTS=$(curl -s -o /dev/null -w "%{http_code}" "https://${DOMAIN}/api/mobile/tenants" --max-time 10 2>/dev/null)
check "Mobile API /api/mobile/tenants responds 200 (${HTTP_TENANTS})" "$([ "$HTTP_TENANTS" = "200" ] && echo 0 || echo 1)"

# www redirect
HTTP_WWW=$(curl -s -o /dev/null -w "%{http_code}" -L "https://www.${DOMAIN}/" --max-time 10 2>/dev/null)
check "https://www.${DOMAIN}/ responds (${HTTP_WWW})" "$([ "$HTTP_WWW" = "200" ] || [ "$HTTP_WWW" = "301" ] || [ "$HTTP_WWW" = "302" ] && echo 0 || echo 1)"

# HTTPS enforcement
HTTP_SCHEME=$(curl -s -o /dev/null -w "%{redirect_url}" "http://${DOMAIN}/" --max-time 10 2>/dev/null)
if echo "$HTTP_SCHEME" | grep -q "^https"; then
    check "HTTP→HTTPS redirect works" "0"
else
    warn "HTTP→HTTPS redirect not detected (check Cloudways Force HTTPS setting)"
fi

# Asset loading
HTTP_MANIFEST=$(curl -s "https://${DOMAIN}/build/manifest.json" --max-time 10 2>/dev/null)
if echo "$HTTP_MANIFEST" | grep -q "app.css"; then
    check "Build manifest accessible and valid" "0"
else
    check "Build manifest accessible and valid" "1"
fi

# ── Tenant subdomain (optional) ──
echo ""
echo "[Tenant Subdomain]"
FIRST_TENANT=$(php artisan tinker --execute="echo \App\Models\Tenant::first()?->slug ?? 'NONE';" 2>/dev/null | tail -1)
if [ "$FIRST_TENANT" != "NONE" ] && [ -n "$FIRST_TENANT" ]; then
    HTTP_TENANT=$(curl -s -o /dev/null -w "%{http_code}" "https://${FIRST_TENANT}.${DOMAIN}/" --max-time 10 2>/dev/null)
    check "https://${FIRST_TENANT}.${DOMAIN}/ responds (${HTTP_TENANT})" "$([ "$HTTP_TENANT" = "200" ] && echo 0 || echo 1)"
else
    warn "No tenants exist yet — create one to test subdomain routing"
fi

# ── Summary ──
echo ""
echo "═══════════════════════════════════════════════════════"
echo " Results: ${PASS} passed, ${FAIL} failed, ${WARN} warnings"
echo "═══════════════════════════════════════════════════════"
if [ "$FAIL" -gt 0 ]; then
    echo " ⚠ Some checks failed — review above"
    exit 1
else
    echo " ✓ All checks passed!"
fi
echo ""
