<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcategory;
use App\Models\AttributeField;

class AttributeFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $apt = Subcategory::firstWhere('slug', 'apartment');  // ensure seeded before
        $land = Subcategory::firstWhere('slug', 'land');
        $villa = Subcategory::firstWhere('slug', 'villa');
        $office = Subcategory::firstWhere('slug', 'office');

        $fields = [
            // Apartment
            // Apartment – Extended Properties

            [$apt, 'living_rooms', ['en' => 'Living Rooms', 'ar' => 'غرف المعيشة'], 'bool', false, true, true, true, null, 0, null, null, 'Basics', 90],
            [$apt, 'storage_room', ['en' => 'Storage Room', 'ar' => 'مخزن'], 'bool', false, true, true, false, null, null, null, null, 'Extras', 120],

            [$apt, 'living_rooms', ['en' => 'Living Rooms', 'ar' => 'غرف نوم ماستر'], 'int', false, true, true, true, null, 0, null, null, 'Basics', 90],

            [$apt, 'bedroom', ['en' => 'Master Bedroom', 'ar' => 'غرفة نوم '], 'int', false, true, true, false, 0, null, null, null, 'Basics', 100],

            [$apt, 'dining_room', ['en' => 'Dining Room', 'ar' => 'صالون/غرفة طعام'], 'bool', false, true, true, false, null, null, null, null, 'Basics', 110],

            [$apt, 'storage_room', ['en' => 'Storage Room', 'ar' => 'مخزن'], 'bool', false, true, true, false, null, null, null, null, 'Extras', 120],

            [$apt, 'kitchen_type', ['en' => 'Kitchen Type', 'ar' => 'نوع المطبخ'], 'enum', false, true, true, false, [
                'open'   => 'open',
                'closed' => 'closed',
            ], null, null, null, 'Basics', 130],

            [$apt, 'air_conditioning', ['en' => 'Air Conditioning', 'ar' => 'تكييف'], 'enum', false, true, true, false, [
                'none'    => 'none',
                'split'   => 'split',
                'central' => 'central',
            ], null, null, null, 'Comfort', 140],

            [$apt, 'elevator', ['en' => 'Elevator', 'ar' => 'مصعد'], 'bool', false, true, true, false, null, null, null, null, 'Building', 150],

            [$apt, 'building_security', ['en' => 'Building Security', 'ar' => 'حارس بناية'], 'bool', false, true, true, false, null, null, null, null, 'Building', 160],

            [$apt, 'maintenance_service', ['en' => 'Maintenance Service', 'ar' => 'خدمات صيانة'], 'bool', false, true, true, false, null, null, null, null, 'Building', 170],

            [$apt, 'electricity', ['en' => 'Electricity', 'ar' => 'كهرباء'], 'bool', false, true, true, false, null, null, null, null, 'Utilities', 180],

            [$apt, 'water', ['en' => 'Water Supply', 'ar' => 'مياه'], 'bool', false, true, true, false, null, null, null, null, 'Utilities', 190],

            [$apt, 'gas', ['en' => 'Gas', 'ar' => 'غاز'], 'bool', false, true, true, false, null, null, null, null, 'Utilities', 200],

            // Land
            [$land, 'zone', ['en' => 'Zone', 'ar' => 'المنطقة'], 'enum', true, true, true, false, ['residential' => 'residential', 'commercial' => 'commercial', 'agricultural' => 'agricultural'], null, null, null, 'Zoning', 10],
            [$land, 'area_m2', ['en' => 'Area (m2)', 'ar' => 'المساحة (م2)'], 'int', true, true, true, true, null, 100, null, 'm2', 'Basics', 20],
            [$land, 'frontage_m', ['en' => 'Frontage (m)', 'ar' => 'الواجهة (م)'], 'int', false, true, true, false, null, null, 100, 'm', 'Basics', 30],
            [$land, 'corner', ['en' => 'Corner Plot', 'ar' => 'قطعة زاوية'], 'bool', false, true, true, false, null, null, null, null, 'Topology', 40],
            [$land, 'utilities', ['en' => 'Utilities', 'ar' => 'المرافق'], 'multi_enum', false, true, true, false, ['water' => 'water', 'electricity' => 'electricity', 'sewage' => 'sewage', 'gas' => 'gas', 'fiber' => 'fiber'], null, null, null, 'Infra', 50],

            // Villa
            [$villa, 'pool', ['en' => 'Pool', 'ar' => 'حمام سباحة'], 'bool', false, true, true, false, null, null, null, null, 'Luxury', 10],
            [$villa, 'garden', ['en' => 'Garden', 'ar' => 'حديقة'], 'bool', false, true, true, false, null, null, null, null, 'Outdoor', 20],
            [$villa, 'garage_size', ['en' => 'Garage Size', 'ar' => 'حجم الكراج'], 'int', false, true, true, false, null, null, null, null, 'Parking', 30],
            [$villa, 'view', ['en' => 'View', 'ar' => 'الإطلالة'], 'enum', false, true, true, false, ['none' => 'none', 'garden' => 'garden', 'sea' => 'sea', 'mountain' => 'mountain'], null, null, null, 'Features', 40],

            // Office
            [$office, 'office_size', ['en' => 'Office Size', 'ar' => 'حجم المكتب'], 'decimal', true, true, true, true, null, null, 1000, 'm2', 'Basics', 10],
            [$office, 'elevator', ['en' => 'Elevator', 'ar' => 'مصعد'], 'bool', false, true, true, false, null, null, null, null, 'Building', 20],
            [$office, 'ac', ['en' => 'Air Conditioning', 'ar' => 'تكييف'], 'enum', false, true, true, false, ['central' => 'central', 'split' => 'split', 'none' => 'none'], null, null, null, 'Comfort', 30],
            [$office, 'access_type', ['en' => 'Access Type', 'ar' => 'نوع الوصول'], 'enum', false, true, true, false, ['24h' => '24h', 'business_hours' => 'business_hours'], null, null, null, 'Access', 40],
        ];

        foreach ($fields as $field) {
            $subcat = $field[0];
            if (!$subcat) continue;  // Skip if subcategory not found (not seeded yet)

            $key = $field[1];
            $label = $field[2];
            $type = $field[3];
            $required = $field[4];
            $searchable = $field[5];
            $facetable = $field[6];
            $promoted = $field[7];
            $options = $field[8];
            $min = $field[9];
            $max = $field[10];
            $unit = $field[11];
            $group = $field[12];
            $sort = $field[13];

            AttributeField::updateOrCreate(
                ['subcategory_id' => $subcat->id, 'key' => $key],
                [
                    'label' => $label['en'],
                    'label_translations' => $label,
                    'type' => $type,
                    'required' => $required,
                    'searchable' => $searchable,
                    'facetable' => $facetable,
                    'promoted' => $promoted,
                    'unit' => $unit,
                    'group' => $group,
                    'sort' => $sort,
                    'options' => $options,
                    'min' => $min,
                    'max' => $max,
                ]
            );
        }
    }
}
