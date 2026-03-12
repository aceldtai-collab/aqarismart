<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $propertyCategories = [
            [
                'key' => 'residential',
                'ar'  => 'سكني',
                'en'  => 'Residential',
                'subcategories' => [
                    ['key' => 'apartment',           'ar' => 'شقة',              'en' => 'Apartment'],
                    ['key' => 'furnished_apartment', 'ar' => 'شقة مفروشة',       'en' => 'Furnished Apartment'],
                    ['key' => 'floor_unit',          'ar' => 'طابق مستقل',        'en' => 'Floor Unit'],
                    ['key' => 'duplex',              'ar' => 'دوبلكس',            'en' => 'Duplex'],
                    ['key' => 'villa',               'ar' => 'فيلا',              'en' => 'Villa'],
                    ['key' => 'townhouse',           'ar' => 'تاونهاوس',          'en' => 'Townhouse'],
                    ['key' => 'studio',              'ar' => 'ستوديو',            'en' => 'Studio'],
                    ['key' => 'penthouse',           'ar' => 'بنتهاوس',           'en' => 'Penthouse'],
                    ['key' => 'rooftop_unit',        'ar' => 'رووف/سطح',          'en' => 'Rooftop Unit'],
                    ['key' => 'basement_unit',       'ar' => 'تسوية/قبو',         'en' => 'Basement Unit'],
                    ['key' => 'room',                'ar' => 'غرفة',              'en' => 'Room'],
                    ['key' => 'bed_space',           'ar' => 'سكن مشترك/سرير',    'en' => 'Bed Space'],
                    ['key' => 'chalet',              'ar' => 'شاليه/استراحة',     'en' => 'Chalet'],
                ],
            ],
            [
                'key' => 'commercial_office',
                'ar'  => 'تجاري/إداري',
                'en'  => 'Commercial / Office',
                'subcategories' => [
                    ['key' => 'office',          'ar' => 'مكتب',           'en' => 'Office'],
                    ['key' => 'retail_shop',     'ar' => 'محل تجاري',      'en' => 'Retail Shop'],
                    ['key' => 'showroom',        'ar' => 'معرض',           'en' => 'Showroom'],
                    ['key' => 'restaurant_cafe', 'ar' => 'مطعم/كافيه',     'en' => 'Restaurant/Café'],
                    ['key' => 'clinic',          'ar' => 'عيادة',          'en' => 'Clinic'],
                    ['key' => 'pharmacy',        'ar' => 'صيدلية',         'en' => 'Pharmacy'],
                    ['key' => 'salon_spa',       'ar' => 'صالون/سبا',      'en' => 'Salon/Spa'],
                    ['key' => 'warehouse',       'ar' => 'مستودع',         'en' => 'Warehouse'],
                    ['key' => 'storage',         'ar' => 'مخزن',           'en' => 'Storage'],
                    ['key' => 'workshop',        'ar' => 'ورشة',           'en' => 'Workshop'],
                    ['key' => 'kiosk',           'ar' => 'كشك/كيـوسك',     'en' => 'Kiosk'],
                ],
            ],
            [
                'key' => 'industrial_logistics',
                'ar'  => 'صناعي/لوجستي',
                'en'  => 'Industrial / Logistics',
                'subcategories' => [
                    ['key' => 'factory',       'ar' => 'مصنع',        'en' => 'Factory'],
                    ['key' => 'hangar',        'ar' => 'هنغر/عنبر',   'en' => 'Hangar'],
                    ['key' => 'storage_yard',  'ar' => 'ساحة تخزين',  'en' => 'Yard'],
                ],
            ],
            [
                'key' => 'land',
                'ar'  => 'أراضٍ',
                'en'  => 'Land',
                'subcategories' => [
                    ['key' => 'residential_land', 'ar' => 'أرض سكنية',     'en' => 'Residential Land'],
                    ['key' => 'commercial_land',  'ar' => 'أرض تجارية',     'en' => 'Commercial Land'],
                    ['key' => 'agricultural_land','ar' => 'أرض زراعية',     'en' => 'Agricultural Land'],
                    ['key' => 'industrial_land',  'ar' => 'أرض صناعية',     'en' => 'Industrial Land'],
                    ['key' => 'investment_land',  'ar' => 'أرض استثمارية',  'en' => 'Investment Land'],
                    ['key' => 'farm_land',        'ar' => 'مزرعة',          'en' => 'Farm Land'],
                ],
            ],
            [
                'key' => 'hospitality_education',
                'ar'  => 'ضيافة وتعليم',
                'en'  => 'Hospitality & Education',
                'subcategories' => [
                    ['key' => 'hotel',         'ar' => 'فندق/شقق فندقية', 'en' => 'Hotel/Serviced Apartments'],
                    ['key' => 'guest_house',   'ar' => 'بيت ضيافة',        'en' => 'Guest House'],
                    ['key' => 'school',        'ar' => 'مدرسة/روضة',       'en' => 'School/Kindergarten'],
                    ['key' => 'training_center','ar'=> 'مركز تدريب',       'en' => 'Training Center'],
                ],
            ],
            [
                'key' => 'parking_facilities',
                'ar'  => 'مواقف ومرافق',
                'en'  => 'Parking & Facilities',
                'subcategories' => [
                    ['key' => 'parking_spot',  'ar' => 'موقف سيارة',       'en' => 'Parking Spot'],
                    ['key' => 'utility_room',  'ar' => 'مخزن خدمات/غرفة مرافق', 'en' => 'Utility/Service Room'],
                ],
            ],
        ];

        DB::transaction(function () use ($propertyCategories) {
            $catOrder = 0;
            foreach ($propertyCategories as $cat) {
                $category = Category::updateOrCreate(
                    ['slug' => $cat['key']],
                    [
                        'name' => ['en' => $cat['en'], 'ar' => $cat['ar'] ?? $cat['en']],
                        'description' => ['en' => $cat['en'], 'ar' => $cat['ar'] ?? $cat['en']],
                        'is_active' => true,
                        'sort_order' => $catOrder++,
                    ]
                );

                $subOrder = 0;
                foreach ($cat['subcategories'] as $sub) {
                    Subcategory::updateOrCreate(
                        ['category_id' => $category->id, 'slug' => $sub['key']],
                        [
                            'name' => ['en' => $sub['en'], 'ar' => $sub['ar'] ?? $sub['en']],
                            'description' => ['en' => $sub['en'], 'ar' => $sub['ar'] ?? $sub['en']],
                            'is_active' => true,
                            'sort_order' => $subOrder++,
                        ]
                    );
                }
            }
        });
    }
}
