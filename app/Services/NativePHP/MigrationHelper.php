<?php

namespace App\Services\NativePHP;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MigrationHelper
{
    /**
     * SQLite-compatible column addition without ->after()
     */
    public static function addColumnIfNotExists(string $table, string $column, callable $definition): void
    {
        if (Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($definition) {
            $definition($table);
        });
    }

    /**
     * Run migrations with SQLite compatibility fixes
     */
    public static function runMigrations(): void
    {
        // Disable foreign key checks temporarily for SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        try {
            // Run migrations normally
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } finally {
            // Re-enable foreign key checks
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            }
        }
    }
}
