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
        $categories = [
            [
                'slug' => 'residential',
                'name' => ['en' => 'Residential', 'ar' => 'سكني'],
                'description' => [
                    'en' => 'Homes, apartments, villas, and family residences.',
                    'ar' => 'وحدات سكنية تشمل الشقق والفلل والمساكن العائلية.',
                ],
                'subcategories' => [
                    ['slug' => 'apartment', 'name' => ['en' => 'Apartment', 'ar' => 'شقة']],
                    ['slug' => 'furnished_apartment', 'name' => ['en' => 'Furnished Apartment', 'ar' => 'شقة مفروشة']],
                    ['slug' => 'floor_unit', 'name' => ['en' => 'Floor Unit', 'ar' => 'طابق مستقل']],
                    ['slug' => 'duplex', 'name' => ['en' => 'Duplex', 'ar' => 'دوبلكس']],
                    ['slug' => 'villa', 'name' => ['en' => 'Villa', 'ar' => 'فيلا']],
                    ['slug' => 'townhouse', 'name' => ['en' => 'Townhouse', 'ar' => 'تاون هاوس']],
                    ['slug' => 'studio', 'name' => ['en' => 'Studio', 'ar' => 'ستوديو']],
                    ['slug' => 'penthouse', 'name' => ['en' => 'Penthouse', 'ar' => 'بنتهاوس']],
                    ['slug' => 'rooftop_unit', 'name' => ['en' => 'Rooftop Unit', 'ar' => 'وحدة روف']],
                    ['slug' => 'basement_unit', 'name' => ['en' => 'Basement Unit', 'ar' => 'وحدة تسوية']],
                    ['slug' => 'room', 'name' => ['en' => 'Room', 'ar' => 'غرفة']],
                    ['slug' => 'bed_space', 'name' => ['en' => 'Bed Space', 'ar' => 'سرير مشترك']],
                    ['slug' => 'chalet', 'name' => ['en' => 'Chalet', 'ar' => 'شاليه']],
                ],
            ],
            [
                'slug' => 'commercial_office',
                'name' => ['en' => 'Commercial / Office', 'ar' => 'تجاري وإداري'],
                'description' => [
                    'en' => 'Offices, shops, showrooms, and business spaces.',
                    'ar' => 'مكاتب ومحلات ومعارض ومساحات أعمال.',
                ],
                'subcategories' => [
                    ['slug' => 'office', 'name' => ['en' => 'Office', 'ar' => 'مكتب']],
                    ['slug' => 'retail_shop', 'name' => ['en' => 'Retail Shop', 'ar' => 'محل تجاري']],
                    ['slug' => 'showroom', 'name' => ['en' => 'Showroom', 'ar' => 'معرض']],
                    ['slug' => 'restaurant_cafe', 'name' => ['en' => 'Restaurant / Cafe', 'ar' => 'مطعم أو مقهى']],
                    ['slug' => 'clinic', 'name' => ['en' => 'Clinic', 'ar' => 'عيادة']],
                    ['slug' => 'pharmacy', 'name' => ['en' => 'Pharmacy', 'ar' => 'صيدلية']],
                    ['slug' => 'salon_spa', 'name' => ['en' => 'Salon / Spa', 'ar' => 'صالون أو سبا']],
                    ['slug' => 'warehouse', 'name' => ['en' => 'Warehouse', 'ar' => 'مستودع']],
                    ['slug' => 'storage', 'name' => ['en' => 'Storage', 'ar' => 'مخزن']],
                    ['slug' => 'workshop', 'name' => ['en' => 'Workshop', 'ar' => 'ورشة']],
                    ['slug' => 'kiosk', 'name' => ['en' => 'Kiosk', 'ar' => 'كشك']],
                ],
            ],
            [
                'slug' => 'industrial_logistics',
                'name' => ['en' => 'Industrial / Logistics', 'ar' => 'صناعي ولوجستي'],
                'description' => [
                    'en' => 'Factories, industrial yards, and logistics facilities.',
                    'ar' => 'مصانع وساحات صناعية ومرافق لوجستية.',
                ],
                'subcategories' => [
                    ['slug' => 'factory', 'name' => ['en' => 'Factory', 'ar' => 'مصنع']],
                    ['slug' => 'hangar', 'name' => ['en' => 'Hangar', 'ar' => 'هنغر']],
                    ['slug' => 'storage_yard', 'name' => ['en' => 'Storage Yard', 'ar' => 'ساحة تخزين']],
                ],
            ],
            [
                'slug' => 'land',
                'name' => ['en' => 'Land', 'ar' => 'أراضٍ'],
                'description' => [
                    'en' => 'Residential, commercial, agricultural, and investment land.',
                    'ar' => 'أراضٍ سكنية وتجارية وزراعية واستثمارية.',
                ],
                'subcategories' => [
                    ['slug' => 'residential_land', 'name' => ['en' => 'Residential Land', 'ar' => 'أرض سكنية']],
                    ['slug' => 'commercial_land', 'name' => ['en' => 'Commercial Land', 'ar' => 'أرض تجارية']],
                    ['slug' => 'agricultural_land', 'name' => ['en' => 'Agricultural Land', 'ar' => 'أرض زراعية']],
                    ['slug' => 'industrial_land', 'name' => ['en' => 'Industrial Land', 'ar' => 'أرض صناعية']],
                    ['slug' => 'investment_land', 'name' => ['en' => 'Investment Land', 'ar' => 'أرض استثمارية']],
                    ['slug' => 'farm_land', 'name' => ['en' => 'Farm Land', 'ar' => 'مزرعة']],
                ],
            ],
            [
                'slug' => 'hospitality_education',
                'name' => ['en' => 'Hospitality & Education', 'ar' => 'ضيافة وتعليم'],
                'description' => [
                    'en' => 'Hotels, guest houses, schools, and training facilities.',
                    'ar' => 'فنادق وبيوت ضيافة ومدارس ومراكز تدريب.',
                ],
                'subcategories' => [
                    ['slug' => 'hotel', 'name' => ['en' => 'Hotel / Serviced Apartments', 'ar' => 'فندق أو شقق فندقية']],
                    ['slug' => 'guest_house', 'name' => ['en' => 'Guest House', 'ar' => 'بيت ضيافة']],
                    ['slug' => 'school', 'name' => ['en' => 'School / Kindergarten', 'ar' => 'مدرسة أو روضة']],
                    ['slug' => 'training_center', 'name' => ['en' => 'Training Center', 'ar' => 'مركز تدريب']],
                ],
            ],
            [
                'slug' => 'parking_facilities',
                'name' => ['en' => 'Parking & Facilities', 'ar' => 'مواقف ومرافق'],
                'description' => [
                    'en' => 'Parking spaces and service rooms.',
                    'ar' => 'مواقف سيارات وغرف خدمات ومرافق مساندة.',
                ],
                'subcategories' => [
                    ['slug' => 'parking_spot', 'name' => ['en' => 'Parking Spot', 'ar' => 'موقف سيارة']],
                    ['slug' => 'utility_room', 'name' => ['en' => 'Utility / Service Room', 'ar' => 'غرفة خدمات']],
                ],
            ],
        ];

        DB::transaction(function () use ($categories): void {
            foreach ($categories as $categorySort => $payload) {
                $subcategories = $payload['subcategories'];
                unset($payload['subcategories']);

                $category = Category::updateOrCreate(
                    ['slug' => $payload['slug']],
                    [
                        'name' => $payload['name'],
                        'description' => $payload['description'],
                        'is_active' => true,
                        'sort_order' => $categorySort + 1,
                    ]
                );

                foreach ($subcategories as $subSort => $subcategoryPayload) {
                    Subcategory::updateOrCreate(
                        ['category_id' => $category->id, 'slug' => $subcategoryPayload['slug']],
                        [
                            'name' => $subcategoryPayload['name'],
                            'description' => $subcategoryPayload['name'],
                            'is_active' => true,
                            'sort_order' => $subSort + 1,
                        ]
                    );
                }
            }
        });
    }
}
