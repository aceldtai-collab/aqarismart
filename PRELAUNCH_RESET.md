# Aqari Smart Prelaunch Reset

This workflow is for the one-time prelaunch production reset where the database must be wiped and rebuilt from the Iraq production seed dataset.

It is intentionally separate from the normal deploy flow.

## What It Does

`php artisan app:prelaunch-reset` will:

1. Put the app into maintenance mode with the branded `503` page
2. Create a SQL backup with `mysqldump`
3. Run `php artisan migrate:fresh --seed --seeder=DatabaseSeeder --force`
4. Repair `public/storage` if needed and run `php artisan storage:link`
5. Clear and rebuild config, route, and view caches
6. Restart queue workers
7. Bring the app back online
8. Print a post-reset summary with tenant, user, agent, property, and unit counts

If the reset fails after destructive work begins, the app stays in maintenance mode for safety.

## Seeder Source

The reset uses:

- [`database/seeders/DatabaseSeeder.php`](database/seeders/DatabaseSeeder.php)
- [`database/seeders/IraqProductionSeeder.php`](database/seeders/IraqProductionSeeder.php)

That means the production reset rebuilds:

- super admins
- Iraqi tenants
- subscriptions
- agents
- Iraqi custom attributes
- Iraqi units and related portfolio data

## Required Environment

Set these before running the reset on production:

```env
APP_ENV=production
APP_DEBUG=false
SUPER_ADMIN_EMAILS=admin@aqarismart.com
SEED_SUPER_ADMIN_PASSWORD=Admin@123456
SEED_TENANT_OWNER_PASSWORD=Owner@123456
SEED_AGENT_PASSWORD=Agent@123456
PRELAUNCH_BACKUP_DIR=/mnt/BLOCKSTORAGE/home/master/backups/aqari-prelaunch
PRELAUNCH_MAINTENANCE_SECRET=aqari-prelaunch-bypass
```

`PRELAUNCH_BACKUP_DIR` is optional. If omitted, backups go to `storage/app/backups/prelaunch`.

## Safe Validation First

Run a dry run first:

```bash
php artisan app:prelaunch-reset --dry-run
```

## Production Reset Command

Interactive:

```bash
php artisan app:prelaunch-reset --force
```

Server wrapper:

```bash
bash scripts/prelaunch-reset.sh --force
```

Alternative seeder:

```bash
php artisan app:prelaunch-reset --seed-class=DatabaseSeeder --force
```

## Useful Options

```bash
php artisan app:prelaunch-reset --dry-run
php artisan app:prelaunch-reset --force --skip-backup
php artisan app:prelaunch-reset --force --skip-cache
php artisan app:prelaunch-reset --force --skip-queue-restart
php artisan app:prelaunch-reset --force --maintenance-secret=my-secret-path
```

## Recommended Prelaunch Order

1. Upload code and built assets
2. Confirm production `.env`
3. Run `php artisan app:prelaunch-reset --dry-run`
4. Take any external server snapshot you want
5. Run `php artisan app:prelaunch-reset --force`
6. Verify central and tenant routes
7. Verify super admin login on the base domain
8. Verify at least one tenant dashboard login

## Post-Reset Verification

Check:

- `https://aqarismart.com`
- `https://aqarismart.com/login`
- `https://aqarismart.com/admin`
- `https://aqarismart.com/marketplace`
- one tenant home page
- one tenant dashboard
- one public property listing
- storage image loading
- mobile marketplace API

Suggested commands:

```bash
php artisan about
php artisan db:show --counts
php artisan route:list
```

## Rollback

If the reset fails after the destructive step starts:

1. Keep maintenance mode enabled
2. Restore the SQL backup created by the command
3. Fix the root cause
4. Run the reset again
5. Bring the app online with `php artisan up` only after verification

## Related Files

- [`app/Console/Commands/PrelaunchReset.php`](app/Console/Commands/PrelaunchReset.php)
- [`scripts/prelaunch-reset.sh`](scripts/prelaunch-reset.sh)
- [`PRODUCTION_SETUP.md`](PRODUCTION_SETUP.md)
