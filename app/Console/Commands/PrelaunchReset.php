<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Package;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class PrelaunchReset extends Command
{
    use ConfirmableTrait;

    protected $signature = 'app:prelaunch-reset
        {--seed-class=DatabaseSeeder : Seeder class to run after fresh migration}
        {--skip-backup : Skip creating a SQL backup before wiping the database}
        {--skip-storage-link : Skip repairing the public storage symlink}
        {--skip-cache : Skip optimize/cache rebuild}
        {--skip-queue-restart : Skip queue restart after reset}
        {--maintenance-secret=aqari-prelaunch-bypass : Maintenance mode bypass secret}
        {--dry-run : Print the resolved reset plan without executing it}
        {--force : Force the operation to run when the app is in production}';

    protected $description = 'Reset the application for prelaunch by backing up the database, running migrate:fresh, reseeding production data, and rebuilding caches.';

    protected ?string $backupPath = null;

    public function handle(): int
    {
        $resolvedSeeder = $this->resolveSeederClass((string) $this->option('seed-class'));

        if ($resolvedSeeder === null) {
            $this->error('Seeder class not found: ' . $this->option('seed-class'));

            return self::FAILURE;
        }

        $this->renderPlan($resolvedSeeder);

        if ($this->option('dry-run')) {
            $this->info('Dry run complete. No destructive steps were executed.');

            return self::SUCCESS;
        }

        if (! $this->confirmToProceed('This will wipe the current database and reseed the prelaunch dataset.')) {
            return self::FAILURE;
        }

        $destructiveStepStarted = false;

        try {
            $this->line('');
            $this->info('Entering maintenance mode...');
            $this->runArtisan('down', [
                '--render' => 'errors.503',
                '--secret' => $this->maintenanceSecret(),
            ]);

            if (! $this->option('skip-backup')) {
                $this->line('');
                $this->info('Creating prelaunch database backup...');
                $this->backupPath = $this->createDatabaseBackup();
                $this->line('Backup saved to: ' . $this->backupPath);
            }

            $destructiveStepStarted = true;

            $this->line('');
            $this->info('Running migrate:fresh --seed...');
            $this->runArtisan('migrate:fresh', [
                '--seed' => true,
                '--seeder' => $resolvedSeeder,
                '--force' => true,
            ]);

            if (! $this->option('skip-storage-link')) {
                $this->line('');
                $this->info('Repairing storage link...');
                $this->repairStorageLink();
            }

            if (! $this->option('skip-cache')) {
                $this->line('');
                $this->info('Rebuilding caches...');
                $this->runArtisan('optimize:clear');
                $this->runArtisan('config:cache');
                $this->runArtisan('route:cache');
                $this->runArtisan('view:cache');
            }

            if (! $this->option('skip-queue-restart')) {
                $this->line('');
                $this->info('Restarting queue workers...');
                $this->runArtisan('queue:restart');
            }

            $this->line('');
            $this->info('Bringing application back online...');
            $this->runArtisan('up');

            $this->line('');
            $this->renderSummary($resolvedSeeder);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->line('');
            $this->error('Prelaunch reset failed: ' . $exception->getMessage());

            if ($this->backupPath !== null) {
                $this->warn('Database backup preserved at: ' . $this->backupPath);
            }

            if (! $destructiveStepStarted) {
                $this->warn('No destructive step started. Bringing the application back online.');

                try {
                    $this->runArtisan('up');
                } catch (Throwable) {
                    $this->warn('Failed to exit maintenance mode automatically. Run `php artisan up` after inspection.');
                }
            } else {
                $this->warn('The application remains in maintenance mode for safety. Resolve the issue or restore from backup before running `php artisan up`.');
            }

            return self::FAILURE;
        }
    }

    protected function resolveSeederClass(string $seeder): ?string
    {
        if ($seeder === '') {
            return null;
        }

        if (class_exists($seeder)) {
            return $seeder;
        }

        $fqcn = 'Database\\Seeders\\' . ltrim($seeder, '\\');

        return class_exists($fqcn) ? $fqcn : null;
    }

    protected function renderPlan(string $resolvedSeeder): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        $this->warn('Prelaunch reset plan');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Environment', app()->environment()],
                ['Connection', (string) $connection],
                ['Database', (string) $database],
                ['Seeder', $resolvedSeeder],
                ['Backup', $this->option('skip-backup') ? 'Skipped' : $this->plannedBackupPath()],
                ['Storage link repair', $this->option('skip-storage-link') ? 'Skipped' : 'Enabled'],
                ['Cache rebuild', $this->option('skip-cache') ? 'Skipped' : 'Enabled'],
                ['Queue restart', $this->option('skip-queue-restart') ? 'Skipped' : 'Enabled'],
                ['Maintenance secret', $this->maintenanceSecret()],
            ]
        );
    }

    protected function runArtisan(string $command, array $arguments = []): void
    {
        $exitCode = $this->call($command, $arguments);

        if ($exitCode !== self::SUCCESS) {
            throw new RuntimeException("Artisan command [{$command}] failed with exit code {$exitCode}.");
        }
    }

    protected function createDatabaseBackup(): string
    {
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");
        $driver = $connection['driver'] ?? null;

        if ($driver !== 'mysql') {
            throw new RuntimeException('Prelaunch SQL backup currently supports MySQL/MariaDB only. Take an external backup and rerun with --skip-backup.');
        }

        $binary = (new ExecutableFinder())->find('mysqldump');

        if ($binary === null) {
            throw new RuntimeException('`mysqldump` is not available on the server PATH. Install it or rerun with --skip-backup after taking an external backup.');
        }

        $directory = $this->backupDirectory();
        File::ensureDirectoryExists($directory);

        $path = $this->plannedBackupPath();
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new RuntimeException('Unable to create backup file at ' . $path);
        }

        $arguments = [
            $binary,
            '--default-character-set=utf8mb4',
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--triggers',
            '--routines',
            '--user=' . (string) ($connection['username'] ?? ''),
        ];

        $socket = (string) ($connection['unix_socket'] ?? '');

        if ($socket !== '') {
            $arguments[] = '--socket=' . $socket;
        } else {
            $arguments[] = '--host=' . (string) ($connection['host'] ?? '127.0.0.1');
            $arguments[] = '--port=' . (string) ($connection['port'] ?? '3306');
        }

        $arguments[] = (string) ($connection['database'] ?? '');

        $environment = [];
        $password = (string) ($connection['password'] ?? '');

        if ($password !== '') {
            $environment['MYSQL_PWD'] = $password;
        }

        $process = new Process($arguments, base_path(), $environment, null, null);

        try {
            $process->run(function (string $type, string $buffer) use ($handle): void {
                fwrite($handle, $buffer);
            });
        } finally {
            fclose($handle);
        }

        if (! $process->isSuccessful()) {
            @unlink($path);

            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'mysqldump failed.');
        }

        return $path;
    }

    protected function repairStorageLink(): void
    {
        $publicStorage = public_path('storage');

        if (is_dir($publicStorage) && ! is_link($publicStorage)) {
            $backupDirectory = public_path('storage-backup-' . now()->format('Ymd-His'));

            if (! @rename($publicStorage, $backupDirectory)) {
                throw new RuntimeException('Existing public/storage directory could not be moved aside.');
            }

            $this->warn("Moved existing public/storage directory to {$backupDirectory}");
        }

        $this->runArtisan('storage:link');
    }

    protected function backupDirectory(): string
    {
        $configured = trim((string) env('PRELAUNCH_BACKUP_DIR', ''));

        return $configured !== ''
            ? $configured
            : storage_path('app/backups/prelaunch');
    }

    protected function plannedBackupPath(): string
    {
        return $this->backupDirectory() . DIRECTORY_SEPARATOR . 'aqari-prelaunch-' . now()->format('Ymd-His') . '.sql';
    }

    protected function maintenanceSecret(): string
    {
        $option = (string) $this->option('maintenance-secret');
        $default = 'aqari-prelaunch-bypass';
        $configured = trim((string) env('PRELAUNCH_MAINTENANCE_SECRET', ''));

        if ($option !== $default) {
            return $option;
        }

        return $configured !== '' ? $configured : $default;
    }

    protected function renderSummary(string $resolvedSeeder): void
    {
        $this->info('Prelaunch reset complete.');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Seeder', $resolvedSeeder],
                ['Super admins', User::query()->whereIn('email', config('auth.super_admin_emails', []))->count()],
                ['Packages', Package::query()->count()],
                ['Tenants', Tenant::query()->count()],
                ['Active subscriptions', TenantSubscription::query()->where('status', 'active')->count()],
                ['Users', User::query()->count()],
                ['Agents', Agent::query()->count()],
                ['Properties', Property::query()->count()],
                ['Units', Unit::query()->count()],
                ['Backup file', $this->backupPath ?? 'Skipped'],
            ]
        );
    }
}
