<?php

namespace Database\Seeders;

use App\Models\AdDuration;
use Illuminate\Database\Seeder;

class AdDurationSeeder extends Seeder
{
    public function run(): void
    {
        $durations = [
            [
                'name_en' => '1 Week',
                'name_ar' => 'أسبوع واحد',
                'days' => 7,
                'price' => 50000.00,
                'currency' => 'IQD',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_en' => '2 Weeks',
                'name_ar' => 'أسبوعين',
                'days' => 14,
                'price' => 90000.00,
                'currency' => 'IQD',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_en' => '1 Month',
                'name_ar' => 'شهر واحد',
                'days' => 30,
                'price' => 150000.00,
                'currency' => 'IQD',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($durations as $duration) {
            AdDuration::updateOrCreate(
                ['days' => $duration['days']],
                $duration
            );
        }

        $this->command->info('Ad durations seeded successfully.');
    }
}
