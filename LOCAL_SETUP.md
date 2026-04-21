# Aqari Smart Local Setup

This file is the local operator guide.

Use it for:

- running the project on your machine
- building frontend assets
- preparing a deploy from Windows

Do not put production secrets in this file.

## Local Stack

- Windows + Laragon
- Project root: `C:\laragon\www\aqarismart`
- Local domain: `http://localtest.me:8000`

## First-Time Setup

```powershell
cd C:\laragon\www\aqarismart
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate
php artisan db:seed
```

If you want the Iraq production-style dataset locally:

```powershell
php artisan db:seed --class=IraqProductionSeeder
```

## Run Locally

Terminal 1:

```powershell
cd C:\laragon\www\aqarismart
php artisan serve --host=127.0.0.1 --port=8000
```

Terminal 2:

```powershell
cd C:\laragon\www\aqarismart
npm run dev
```

Open:

- `http://localtest.me:8000`
- `http://localtest.me:8000/marketplace`

## Local Checks Before Any Deploy

Run these before touching production:

```powershell
cd C:\laragon\www\aqarismart
php artisan test
php artisan optimize:clear
npm run build
```

If the change is scoped and you do not want to run the full test suite, at least run:

```powershell
php artisan route:list > $null
php artisan view:clear
php artisan view:cache
```

## Safe Deploy Preparation From Windows

This project includes a PowerShell helper:

- [deploy-local.ps1](/C:/laragon/www/aqarismart/scripts/deploy-local.ps1)

Routine usage:

```powershell
cd C:\laragon\www\aqarismart
.\scripts\deploy-local.ps1 -SshTarget "master_bggefyhrbt@159.203.2.235"
```

What it does:

1. verifies git status is clean
2. builds frontend assets
3. pushes `main`
4. uploads `public/build`
5. runs the server-side production deploy script

If you already built assets and only need to re-upload them:

```powershell
.\scripts\deploy-local.ps1 -SshTarget "master_bggefyhrbt@159.203.2.235" -SkipBuild
```

If this is the first production deployment on a fresh server:

```powershell
.\scripts\deploy-local.ps1 -SshTarget "master_bggefyhrbt@159.203.2.235" -InitialDeploy
```

## SSH Notes

Password auth can work, but an SSH key is better.

Optional SSH config:

```text
Host cloudways-aqarismart
    HostName 159.203.2.235
    User master_bggefyhrbt
    Port 22
    IdentityFile ~/.ssh/id_rsa
```

Then you can run:

```powershell
.\scripts\deploy-local.ps1 -SshTarget "cloudways-aqarismart"
```

## Important Rules

- Never commit `.env`
- Never store the production password in repo files
- Do not deploy from a dirty worktree
- Do not run destructive reset commands unless you explicitly want to rebuild production data
