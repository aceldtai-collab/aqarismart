# ======================================================
# Aqari Smart - Local Deploy Script (Windows PowerShell)
# Run from project root: .\scripts\deploy-local.ps1
# ======================================================
param(
    [string]$SshTarget = "cloudways-aqarismart",
    [string]$AppDir = "/mnt/BLOCKSTORAGE/home/master/applications/tsyaqtsxmr/public_html",
    [switch]$SkipBuild,
    [switch]$InitialDeploy,
    [switch]$SkipGitCheck
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host " Aqari Smart - Local Deploy to Cloudways" -ForegroundColor Cyan
Write-Host " $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: verify git state
Write-Host "[1/5] Checking git state..." -ForegroundColor Yellow
if (-not $SkipGitCheck) {
    $gitStatus = git status --porcelain
    if ($LASTEXITCODE -ne 0) { throw "git status failed" }
    if ($gitStatus) {
        Write-Host $gitStatus
        throw "Working tree is not clean. Commit or stash changes before deploying. Use -SkipGitCheck only if you intentionally want to bypass this guard."
    }

    $branch = (git rev-parse --abbrev-ref HEAD).Trim()
    if ($branch -ne "main") {
        throw "Current branch is '$branch'. Deploy from 'main' only."
    }

    Write-Host "  OK - Git tree is clean on main" -ForegroundColor Green
} else {
    Write-Host "  Skipped git check" -ForegroundColor DarkGray
}

# Step 2: build frontend assets
Write-Host ""
if (-not $SkipBuild) {
    Write-Host "[2/5] Building frontend assets..." -ForegroundColor Yellow
    npm run build
    if ($LASTEXITCODE -ne 0) { throw "npm run build failed" }
    Write-Host "  OK - Assets built" -ForegroundColor Green
} else {
    Write-Host "[2/5] Skipping build (--SkipBuild)" -ForegroundColor DarkGray
}

# Step 3: push code
Write-Host ""
Write-Host "[3/5] Pushing code to git..." -ForegroundColor Yellow
git push origin main
if ($LASTEXITCODE -ne 0) { throw "git push failed" }
Write-Host "  OK - Code pushed" -ForegroundColor Green

# Step 4: upload built assets
Write-Host ""
Write-Host "[4/5] Uploading built assets to server..." -ForegroundColor Yellow
scp -r "public/build" "${SshTarget}:${AppDir}/public/"
if ($LASTEXITCODE -ne 0) { throw "scp failed - check SSH access for '$SshTarget'" }
Write-Host "  OK - Assets uploaded" -ForegroundColor Green

# Step 5: run server-side deploy
Write-Host ""
Write-Host "[5/5] Running server-side deploy script..." -ForegroundColor Yellow
if ($InitialDeploy) {
    ssh $SshTarget "bash ${AppDir}/scripts/deploy-initial.sh"
} else {
    ssh $SshTarget "bash ${AppDir}/scripts/deploy.sh"
}
if ($LASTEXITCODE -ne 0) { throw "Remote deploy script failed" }

Write-Host ""
Write-Host "======================================================" -ForegroundColor Green
Write-Host " Deploy complete!" -ForegroundColor Green
Write-Host "======================================================" -ForegroundColor Green
Write-Host ""
