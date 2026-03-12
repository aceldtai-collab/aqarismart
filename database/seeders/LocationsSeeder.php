<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        // Jordan (JO)
        $jo = Country::updateOrCreate(
            ['iso2' => 'JO'],
            [
                'iso3' => 'JOR',
                'phone_code' => '+962',
                'currency_code' => 'JOD',
                'name_en' => 'Jordan',
                'name_ar' => 'الأردن',
                'is_active' => true,
            ]
        );

        $jo_am = State::updateOrCreate(
            ['country_id' => $jo->id, 'code' => 'AM'],
            ['name_en' => 'Amman', 'name_ar' => 'عمّان', 'is_active' => true]
        );
        $jo_ir = State::updateOrCreate(
            ['country_id' => $jo->id, 'code' => 'IR'],
            ['name_en' => 'Irbid', 'name_ar' => 'إربد', 'is_active' => true]
        );

        City::updateOrCreate(['country_id' => $jo->id, 'state_id' => $jo_am->id, 'name_en' => 'Amman'], ['name_ar' => 'عمّان', 'is_active' => true]);
        City::updateOrCreate(['country_id' => $jo->id, 'state_id' => $jo_ir->id, 'name_en' => 'Irbid'], ['name_ar' => 'إربد', 'is_active' => true]);
        City::updateOrCreate(['country_id' => $jo->id, 'state_id' => $jo_am->id, 'name_en' => 'Zarqa'], ['name_ar' => 'الزرقاء', 'is_active' => true]);

        // Saudi Arabia (SA)
        $sa = Country::updateOrCreate(
            ['iso2' => 'SA'],
            [
                'iso3' => 'SAU',
                'phone_code' => '+966',
                'currency_code' => 'SAR',
                'name_en' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'is_active' => true,
            ]
        );

        $sa_ri = State::updateOrCreate(
            ['country_id' => $sa->id, 'code' => '01'],
            ['name_en' => 'Riyadh', 'name_ar' => 'منطقة الرياض', 'is_active' => true]
        );
        $sa_mk = State::updateOrCreate(
            ['country_id' => $sa->id, 'code' => '02'],
            ['name_en' => 'Makkah', 'name_ar' => 'منطقة مكة المكرمة', 'is_active' => true]
        );

        City::updateOrCreate(['country_id' => $sa->id, 'state_id' => $sa_ri->id, 'name_en' => 'Riyadh'], ['name_ar' => 'الرياض', 'is_active' => true]);
        City::updateOrCreate(['country_id' => $sa->id, 'state_id' => $sa_mk->id, 'name_en' => 'Jeddah'], ['name_ar' => 'جدة', 'is_active' => true]);
        City::updateOrCreate(['country_id' => $sa->id, 'state_id' => $sa_mk->id, 'name_en' => 'Makkah'], ['name_ar' => 'مكة المكرمة', 'is_active' => true]);
    }
}

