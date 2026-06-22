<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $iraqId = DB::table('countries')->where('iso2', 'IQ')->value('id');

        if (! $iraqId) {
            $iraqId = DB::table('countries')->insertGetId([
                'iso2' => 'IQ',
                'iso3' => 'IRQ',
                'phone_code' => '+964',
                'currency_code' => 'IQD',
                'name_en' => 'Iraq',
                'name_ar' => 'العراق',
                'lat' => 33.223191,
                'lng' => 43.679291,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! DB::table('countries')->where('iso2', 'JO')->exists()) {
            DB::table('countries')->insert([
                'iso2' => 'JO',
                'iso3' => 'JOR',
                'phone_code' => '+962',
                'currency_code' => 'JOD',
                'name_en' => 'Jordan',
                'name_ar' => 'الأردن',
                'lat' => 30.585164,
                'lng' => 36.238414,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('slug')->constrained('countries')->nullOnDelete();
                $table->index('country_id');
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (! Schema::hasColumn('units', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('tenant_id')->constrained('countries')->nullOnDelete();
                $table->index(['country_id', 'status']);
            }
        });

        Schema::table('resident_listings', function (Blueprint $table) {
            if (! Schema::hasColumn('resident_listings', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('user_id')->constrained('countries')->nullOnDelete();
                $table->index(['country_id', 'ad_status', 'status', 'deleted_at'], 'resident_listings_country_status_index');
            }
        });

        Schema::table('ad_durations', function (Blueprint $table) {
            if (! Schema::hasColumn('ad_durations', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained('countries')->nullOnDelete();
                $table->index(['country_id', 'is_active']);
            }
        });

        DB::table('tenants')->whereNull('country_id')->update(['country_id' => $iraqId]);
        $tenantCountries = DB::table('tenants')->pluck('country_id', 'id');
        DB::table('units')
            ->whereNull('country_id')
            ->orderBy('id')
            ->chunkById(500, function ($units) use ($tenantCountries, $iraqId): void {
                foreach ($units as $unit) {
                    DB::table('units')
                        ->where('id', $unit->id)
                        ->update(['country_id' => $tenantCountries[$unit->tenant_id] ?? $iraqId]);
                }
            });
        DB::table('resident_listings')->whereNull('country_id')->update(['country_id' => $iraqId]);
        DB::table('ad_durations')->whereNull('country_id')->update(['country_id' => $iraqId]);
    }

    public function down(): void
    {
        Schema::table('ad_durations', function (Blueprint $table) {
            if (Schema::hasColumn('ad_durations', 'country_id')) {
                $table->dropIndex('ad_durations_country_id_is_active_index');
                $table->dropConstrainedForeignId('country_id');
            }
        });

        Schema::table('resident_listings', function (Blueprint $table) {
            if (Schema::hasColumn('resident_listings', 'country_id')) {
                $table->dropIndex('resident_listings_country_status_index');
                $table->dropConstrainedForeignId('country_id');
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'country_id')) {
                $table->dropIndex('units_country_id_status_index');
                $table->dropConstrainedForeignId('country_id');
            }
        });

        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'country_id')) {
                $table->dropConstrainedForeignId('country_id');
            }
        });
    }
};
