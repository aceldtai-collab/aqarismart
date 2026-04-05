<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoPmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('DemoPmsSeeder is deprecated. Running IraqProductionSeeder instead.');

        $this->call(IraqProductionSeeder::class);
    }
}
