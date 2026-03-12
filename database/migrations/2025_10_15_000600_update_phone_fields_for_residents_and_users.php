<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 32)->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'phone_country_code')) {
                $table->string('phone_country_code', 8)->nullable()->after('phone');
            }
        });

        // Make email nullable to allow phone-first resident accounts.
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');

        // Ensure unique constraint on phone for quick lookups, allowing multiple NULL values.
        Schema::table('users', function (Blueprint $table) {
            $table->unique('phone', 'users_phone_unique');
        });

        Schema::table('residents', function (Blueprint $table) {
            if (! Schema::hasColumn('residents', 'phone_country_code')) {
                $table->string('phone_country_code', 8)->nullable()->after('phone');
            }
            $table->index(['tenant_id', 'phone'], 'residents_tenant_phone_index');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropIndex('residents_tenant_phone_index');
            $table->dropColumn('phone_country_code');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_phone_unique');
            $table->dropColumn('phone_country_code');
            $table->dropColumn('phone');
        });

        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
    }
};
