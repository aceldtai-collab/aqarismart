# ═══════════════════════════════════════════════════════════
# Aqari Smart — Local Deploy Script (Windows PowerShell)
# Run from project root: .\scripts\deploy-local.ps1
# ═══════════════════════════════════════════════════════════
param(
    [string]$SshHost = "cloudways-aqarismart",
    [string]$AppDir = "/home/1599704.cloudwaysapps.com/tsyaqtsxmr/public_html",
    [switch]$SkipBuild,
    [switch]$InitialDeploy
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host " Aqari Smart — Local Deploy to Cloudways" -ForegroundColor Cyan
Write-Host " $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# ── Step 1: Build frontend assets ──
if (-not $SkipBuild) {
    Write-Host "[1/4] Building frontend assets..." -ForegroundColor Yellow
    npm run build
    if ($LASTEXITCODE -ne 0) { throw "npm run build failed" }
    Write-Host "  OK - Assets built" -ForegroundColor Green
} else {
    Write-Host "[1/4] Skipping build (--SkipBuild)" -ForegroundColor DarkGray
}

# ── Step 2: Git push ──
Write-Host ""
Write-Host "[2/4] Pushing code to git..." -ForegroundColor Yellow
git add .
$hasChanges = git diff --cached --quiet 2>&1; $hasChanges = $LASTEXITCODE
if ($hasChanges -ne 0) {
    $commitMsg = Read-Host "  Enter commit message"
    git commit -m $commitMsg
}
git push origin main
Write-Host "  OK - Code pushed" -ForegroundColor Green

# ── Step 3: Upload built assets ──
Write-Host ""
Write-Host "[3/4] Uploading built assets to server..." -ForegroundColor Yellow
scp -r "public/build" "${SshHost}:${AppDir}/public/"
if ($LASTEXITCODE -ne 0) { throw "scp failed — check SSH config for host '$SshHost'" }
Write-Host "  OK - Assets uploaded" -ForegroundColor Green

# ── Step 4: Run server-side deploy ──
Write-Host ""
Write-Host "[4/4] Running server-side deploy script..." -ForegroundColor Yellow
if ($InitialDeploy) {
    ssh $SshHost "bash ${AppDir}/scripts/deploy-initial.sh"
} else {
    ssh $SshHost "bash ${AppDir}/scripts/deploy.sh"
}
Write-Host ""
Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host " Deploy complete!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host ""
