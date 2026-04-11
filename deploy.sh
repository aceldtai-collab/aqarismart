#!/bin/bash
# Aqari Smart - One-Command Deploy to Cloudways
# Usage: ./deploy.sh

set -e

echo "═══════════════════════════════════════════════════════════"
echo "  Aqari Smart - Production Deploy"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Configuration
## SECURITY NOTE: Do NOT store plaintext passwords in the repository.
## The deploy script uses SSH. Configure an SSH key or an SSH config
## entry for non-interactive deploys. If you must use a password for
## automated deploys, set `DEPLOY_SSH_PASSWORD` and install `sshpass`
## locally (not recommended).
REMOTE_HOST="master_bggefyhrbt@159.203.2.235"
REMOTE_PATH="/home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html"
TEMP_ZIP="/tmp/aqarismart-deploy-$(date +%s).zip"

# Step 1: Build assets locally
echo "→ Building production assets..."
npm run build
if [ $? -ne 0 ]; then
    echo "✗ Asset build failed!"
    exit 1
fi
echo "✓ Assets built successfully"
echo ""

# Step 2: Create deployment archive
echo "→ Creating deployment archive..."
git archive --format=zip -o "$TEMP_ZIP" HEAD
if [ $? -ne 0 ]; then
    echo "✗ Failed to create archive!"
    exit 1
fi
echo "✓ Archive created: $TEMP_ZIP"
echo ""

# Step 3: Upload code archive
echo "→ Uploading code to server..."
scp "$TEMP_ZIP" "$REMOTE_HOST:~/deploy.zip"
if [ $? -ne 0 ]; then
    echo "✗ Upload failed!"
    exit 1
fi
echo "✓ Code uploaded"
echo ""

# Step 4: Extract code on server
echo "→ Extracting code on server..."
ssh "$REMOTE_HOST" "unzip -oq ~/deploy.zip -d $REMOTE_PATH"
if [ $? -ne 0 ]; then
    echo "✗ Extraction failed!"
    exit 1
fi
echo "✓ Code extracted"
echo ""

# Step 5: Upload built assets
echo "→ Uploading built assets..."
ssh "$REMOTE_HOST" "rm -rf $REMOTE_PATH/public/build"
scp -r public/build "$REMOTE_HOST:$REMOTE_PATH/public/"
if [ $? -ne 0 ]; then
    echo "✗ Asset upload failed!"
    exit 1
fi
echo "✓ Assets uploaded"
echo ""

# Step 6: Post-deploy commands
echo "→ Running post-deploy tasks..."

# Run migrations + seed required data
ssh "$REMOTE_HOST" "php $REMOTE_PATH/artisan migrate --force"
ssh "$REMOTE_HOST" "php $REMOTE_PATH/artisan db:seed --class=AdDurationSeeder --force"

# Clear caches
ssh "$REMOTE_HOST" "php $REMOTE_PATH/artisan optimize:clear"

# Fix storage link
ssh "$REMOTE_HOST" "if [ -d $REMOTE_PATH/public/storage ] && [ ! -L $REMOTE_PATH/public/storage ]; then rm -rf $REMOTE_PATH/public/storage; fi; php $REMOTE_PATH/artisan storage:link || true"

# Rebuild caches
ssh "$REMOTE_HOST" "php $REMOTE_PATH/artisan config:cache"
ssh "$REMOTE_HOST" "php $REMOTE_PATH/artisan route:cache"
ssh "$REMOTE_HOST" "php $REMOTE_PATH/artisan view:cache"

echo "✓ Post-deploy tasks completed"
echo ""

# Step 7: Cleanup
echo "→ Cleaning up..."
rm -f "$TEMP_ZIP"
ssh "$REMOTE_HOST" "rm -f ~/deploy.zip"
echo "✓ Cleanup done"
echo ""

echo "═══════════════════════════════════════════════════════════"
echo "  ✓ Deployment Complete!"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "Your site is now live at:"
echo "  https://aqarismart.com"
echo ""
echo "Test tenant subdomain (if demo tenant exists):"
echo "  https://demo.aqarismart.com"
echo ""
