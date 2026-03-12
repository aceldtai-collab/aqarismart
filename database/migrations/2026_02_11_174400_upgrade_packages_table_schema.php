<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (! Schema::hasColumn('packages', 'price_monthly')) {
                $table->unsignedInteger('price_monthly')->default(0)->after('description');
            }
            if (! Schema::hasColumn('packages', 'price_yearly')) {
                $table->unsignedInteger('price_yearly')->default(0)->after('price_monthly');
            }
            if (! Schema::hasColumn('packages', 'stripe_price_monthly')) {
                $table->string('stripe_price_monthly')->nullable()->after('price_yearly');
            }
            if (! Schema::hasColumn('packages', 'stripe_price_yearly')) {
                $table->string('stripe_price_yearly')->nullable()->after('stripe_price_monthly');
            }
            if (! Schema::hasColumn('packages', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('is_active');
            }
            if (! Schema::hasColumn('packages', 'metadata')) {
                $table->json('metadata')->nullable()->after('is_default');
            }
        });

        // Migrate data from old columns to new columns
        if (Schema::hasColumn('packages', 'price') && Schema::hasColumn('packages', 'price_monthly')) {
            DB::table('packages')->get()->each(function ($pkg) {
                $priceInCents = (int) round(((float) $pkg->price) * 100);
                DB::table('packages')->where('id', $pkg->id)->update([
                    'price_monthly' => $priceInCents,
                    'price_yearly'  => $priceInCents * 12,
                ]);

                // Migrate stripe_price_id to stripe_price_monthly if present
                if (! empty($pkg->stripe_price_id) && Schema::hasColumn('packages', 'stripe_price_id')) {
                    DB::table('packages')->where('id', $pkg->id)->update([
                        'stripe_price_monthly' => $pkg->stripe_price_id,
                    ]);
                }
            });
        }

        // Drop old columns if they exist
        Schema::table('packages', function (Blueprint $table) {
            $dropCols = [];
            foreach (['price', 'billing_cycle', 'stripe_price_id', 'features', 'max_users', 'max_properties', 'max_units'] as $col) {
                if (Schema::hasColumn('packages', $col)) {
                    $dropCols[] = $col;
                }
            }
            if ($dropCols) {
                $table->dropColumn($dropCols);
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Re-add old columns
            $table->decimal('price', 10, 2)->default(0)->after('description');
            $table->string('billing_cycle')->default('monthly')->after('price');
            $table->string('stripe_price_id')->nullable()->after('billing_cycle');
            $table->json('features')->nullable()->after('stripe_price_id');
            $table->unsignedInteger('max_users')->nullable()->after('features');
            $table->unsignedInteger('max_properties')->nullable()->after('max_users');
            $table->unsignedInteger('max_units')->nullable()->after('max_properties');
        });

        // Drop new columns
        Schema::table('packages', function (Blueprint $table) {
            $dropCols = [];
            foreach (['price_monthly', 'price_yearly', 'stripe_price_monthly', 'stripe_price_yearly', 'is_default', 'metadata'] as $col) {
                if (Schema::hasColumn('packages', $col)) {
                    $dropCols[] = $col;
                }
            }
            if ($dropCols) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
