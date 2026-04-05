<?php

namespace Database\Seeders;

use App\Models\AttributeField;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class AttributeFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'apartment' => [
                $this->field('floor_number', ['en' => 'Floor Number', 'ar' => 'رقم الطابق'], 'int', 'Building', 10, ['min' => 0, 'max' => 80, 'promoted' => true]),
                $this->field('parking_spaces', ['en' => 'Parking Spaces', 'ar' => 'مواقف السيارات'], 'int', 'Parking', 20, ['min' => 0, 'max' => 8, 'promoted' => true]),
                $this->field('generator_amps', ['en' => 'Generator Share (Amps)', 'ar' => 'حصة المولد (أمبير)'], 'int', 'Utilities', 30, ['min' => 0, 'max' => 40, 'unit' => 'A']),
                $this->field('furnished', ['en' => 'Furnished', 'ar' => 'مفروشة'], 'bool', 'Basics', 40),
                $this->field('elevator', ['en' => 'Elevator', 'ar' => 'مصعد'], 'bool', 'Building', 50, ['promoted' => true]),
                $this->field('balconies', ['en' => 'Balconies', 'ar' => 'الشرفات'], 'int', 'Outdoor', 60, ['min' => 0, 'max' => 6]),
                $this->field('central_ac', ['en' => 'Central AC', 'ar' => 'تكييف مركزي'], 'bool', 'Utilities', 70),
                $this->field('master_bedroom', ['en' => 'Master Bedroom', 'ar' => 'غرفة ماستر'], 'bool', 'Layout', 80),
                $this->field('building_age_years', ['en' => 'Building Age', 'ar' => 'عمر البناء'], 'int', 'Building', 90, ['min' => 0, 'max' => 80, 'unit' => 'years']),
                $this->field('service_charge_iqd', ['en' => 'Monthly Service Charge', 'ar' => 'رسوم الخدمة الشهرية'], 'int', 'Costs', 100, ['min' => 0, 'max' => 5000000, 'unit' => 'IQD']),
            ],
            'furnished_apartment' => [
                $this->field('floor_number', ['en' => 'Floor Number', 'ar' => 'رقم الطابق'], 'int', 'Building', 10, ['min' => 0, 'max' => 80, 'promoted' => true]),
                $this->field('parking_spaces', ['en' => 'Parking Spaces', 'ar' => 'مواقف السيارات'], 'int', 'Parking', 20, ['min' => 0, 'max' => 6]),
                $this->field('generator_amps', ['en' => 'Generator Share (Amps)', 'ar' => 'حصة المولد (أمبير)'], 'int', 'Utilities', 30, ['min' => 0, 'max' => 40, 'unit' => 'A']),
                $this->field('elevator', ['en' => 'Elevator', 'ar' => 'مصعد'], 'bool', 'Building', 40),
                $this->field('balconies', ['en' => 'Balconies', 'ar' => 'الشرفات'], 'int', 'Outdoor', 50, ['min' => 0, 'max' => 6]),
                $this->field('central_ac', ['en' => 'Central AC', 'ar' => 'تكييف مركزي'], 'bool', 'Utilities', 60),
                $this->field('kitchen_equipped', ['en' => 'Equipped Kitchen', 'ar' => 'مطبخ مجهز'], 'bool', 'Interiors', 70, ['promoted' => true]),
                $this->field('linen_ready', ['en' => 'Linen Ready', 'ar' => 'مجهزة بالمفروشات اللينة'], 'bool', 'Interiors', 80),
                $this->field('housekeeping_ready', ['en' => 'Housekeeping Ready', 'ar' => 'جاهزة لخدمة التنظيف'], 'bool', 'Operations', 90),
                $this->field('furniture_package_notes', ['en' => 'Furniture Package Notes', 'ar' => 'ملاحظات باقة الأثاث'], 'string', 'Interiors', 100),
            ],
            'duplex' => [
                $this->field('parking_spaces', ['en' => 'Parking Spaces', 'ar' => 'مواقف السيارات'], 'int', 'Parking', 10, ['min' => 0, 'max' => 8, 'promoted' => true]),
                $this->field('private_entrance', ['en' => 'Private Entrance', 'ar' => 'مدخل خاص'], 'bool', 'Layout', 20, ['promoted' => true]),
                $this->field('family_lounge', ['en' => 'Family Lounge', 'ar' => 'صالة عائلية'], 'bool', 'Layout', 30),
                $this->field('split_levels', ['en' => 'Split Levels', 'ar' => 'طابقان داخليان'], 'bool', 'Layout', 40),
                $this->field('terrace_area_m2', ['en' => 'Terrace Area', 'ar' => 'مساحة التراس'], 'int', 'Outdoor', 50, ['min' => 0, 'max' => 600, 'unit' => 'm2']),
                $this->field('maid_room', ['en' => 'Maid Room', 'ar' => 'غرفة خادمة'], 'bool', 'Layout', 60),
                $this->field('storage_room', ['en' => 'Storage Room', 'ar' => 'غرفة خزن'], 'bool', 'Layout', 70),
                $this->field('rooftop_access', ['en' => 'Rooftop Access', 'ar' => 'وصول إلى السطح'], 'bool', 'Outdoor', 80),
                $this->field('generator_amps', ['en' => 'Generator Share (Amps)', 'ar' => 'حصة المولد (أمبير)'], 'int', 'Utilities', 90, ['min' => 0, 'max' => 50, 'unit' => 'A']),
                $this->field('central_ac', ['en' => 'Central AC', 'ar' => 'تكييف مركزي'], 'bool', 'Utilities', 100),
            ],
            'villa' => [
                $this->field('garden_area_m2', ['en' => 'Garden Area', 'ar' => 'مساحة الحديقة'], 'int', 'Outdoor', 10, ['min' => 0, 'max' => 5000, 'unit' => 'm2', 'promoted' => true]),
                $this->field('parking_spaces', ['en' => 'Parking Spaces', 'ar' => 'مواقف السيارات'], 'int', 'Parking', 20, ['min' => 0, 'max' => 10, 'promoted' => true]),
                $this->field('maid_room', ['en' => 'Maid Room', 'ar' => 'غرفة خادمة'], 'bool', 'Layout', 30),
                $this->field('driver_room', ['en' => 'Driver Room', 'ar' => 'غرفة سائق'], 'bool', 'Layout', 40),
                $this->field('private_pool', ['en' => 'Private Pool', 'ar' => 'مسبح خاص'], 'bool', 'Luxury', 50),
                $this->field('generator_amps', ['en' => 'Generator Share (Amps)', 'ar' => 'حصة المولد (أمبير)'], 'int', 'Utilities', 60, ['min' => 0, 'max' => 60, 'unit' => 'A']),
                $this->field('central_ac', ['en' => 'Central AC', 'ar' => 'تكييف مركزي'], 'bool', 'Utilities', 70),
                $this->field('basement_room', ['en' => 'Basement Room', 'ar' => 'غرفة تسوية'], 'bool', 'Layout', 80),
                $this->field('security_room', ['en' => 'Security Room', 'ar' => 'غرفة حراسة'], 'bool', 'Layout', 90),
                $this->field('solar_water_heater', ['en' => 'Solar Water Heater', 'ar' => 'سخان شمسي'], 'bool', 'Utilities', 100),
            ],
            'office' => [
                $this->field('meeting_rooms', ['en' => 'Meeting Rooms', 'ar' => 'غرف الاجتماعات'], 'int', 'Layout', 10, ['min' => 0, 'max' => 20, 'promoted' => true]),
                $this->field('parking_spaces', ['en' => 'Parking Spaces', 'ar' => 'مواقف السيارات'], 'int', 'Parking', 20, ['min' => 0, 'max' => 25, 'promoted' => true]),
                $this->field('reception_area', ['en' => 'Reception Area', 'ar' => 'منطقة استقبال'], 'bool', 'Layout', 30),
                $this->field('generator_amps', ['en' => 'Generator Share (Amps)', 'ar' => 'حصة المولد (أمبير)'], 'int', 'Utilities', 40, ['min' => 0, 'max' => 100, 'unit' => 'A']),
                $this->field('fiber_internet', ['en' => 'Fiber Internet', 'ar' => 'إنترنت ألياف ضوئية'], 'bool', 'Utilities', 50),
                $this->field('fit_out_ready', ['en' => 'Fit-out Ready', 'ar' => 'جاهز للتشطيب'], 'bool', 'Condition', 60),
                $this->field('pantry', ['en' => 'Pantry', 'ar' => 'بانتري'], 'bool', 'Layout', 70),
                $this->field('server_room', ['en' => 'Server Room', 'ar' => 'غرفة خوادم'], 'bool', 'Infrastructure', 80),
                $this->field('floor_number', ['en' => 'Floor Number', 'ar' => 'رقم الطابق'], 'int', 'Building', 90, ['min' => 0, 'max' => 80]),
                $this->field('security_access', ['en' => 'Security Access System', 'ar' => 'نظام دخول أمني'], 'bool', 'Security', 100),
            ],
            'showroom' => [
                $this->field('display_frontage_m', ['en' => 'Display Frontage', 'ar' => 'واجهة العرض'], 'decimal', 'Visibility', 10, ['min' => 0, 'max' => 300, 'unit' => 'm', 'promoted' => true]),
                $this->field('parking_spaces', ['en' => 'Customer Parking', 'ar' => 'مواقف العملاء'], 'int', 'Parking', 20, ['min' => 0, 'max' => 60]),
                $this->field('storage_room', ['en' => 'Storage Room', 'ar' => 'غرفة خزن'], 'bool', 'Layout', 30),
                $this->field('ceiling_height_m', ['en' => 'Ceiling Height', 'ar' => 'ارتفاع السقف'], 'decimal', 'Structure', 40, ['min' => 0, 'max' => 25, 'unit' => 'm']),
                $this->field('signage_ready', ['en' => 'Signage Ready', 'ar' => 'جاهز للوحات'], 'bool', 'Visibility', 50),
                $this->field('delivery_access', ['en' => 'Delivery Access', 'ar' => 'وصول للشحن'], 'bool', 'Operations', 60),
                $this->field('corner_unit', ['en' => 'Corner Unit', 'ar' => 'زاوية'], 'bool', 'Visibility', 70),
                $this->field('generator_amps', ['en' => 'Generator Share (Amps)', 'ar' => 'حصة المولد (أمبير)'], 'int', 'Utilities', 80, ['min' => 0, 'max' => 80, 'unit' => 'A']),
                $this->field('glass_facade', ['en' => 'Glass Facade', 'ar' => 'واجهة زجاجية'], 'bool', 'Visibility', 90),
                $this->field('service_lane', ['en' => 'Service Lane Access', 'ar' => 'وصول ممر خدمة'], 'bool', 'Operations', 100),
            ],
            'retail_shop' => [
                $this->field('frontage_m', ['en' => 'Street Frontage', 'ar' => 'عرض الواجهة'], 'decimal', 'Visibility', 10, ['min' => 0, 'max' => 200, 'unit' => 'm', 'promoted' => true]),
                $this->field('storage_room', ['en' => 'Storage Room', 'ar' => 'غرفة خزن'], 'bool', 'Layout', 20),
                $this->field('corner_unit', ['en' => 'Corner Unit', 'ar' => 'زاوية'], 'bool', 'Visibility', 30),
                $this->field('parking_spaces', ['en' => 'Customer Parking', 'ar' => 'مواقف الزبائن'], 'int', 'Parking', 40, ['min' => 0, 'max' => 50]),
                $this->field('license_ready', ['en' => 'License Ready', 'ar' => 'جاهز للترخيص'], 'bool', 'Condition', 50),
                $this->field('mezzanine_area_m2', ['en' => 'Mezzanine Area', 'ar' => 'مساحة الميزانين'], 'int', 'Layout', 60, ['min' => 0, 'max' => 1000, 'unit' => 'm2']),
                $this->field('outdoor_signage', ['en' => 'Outdoor Signage', 'ar' => 'لوحات خارجية'], 'bool', 'Visibility', 70),
                $this->field('delivery_access', ['en' => 'Delivery Access', 'ar' => 'وصول التوريد'], 'bool', 'Operations', 80),
                $this->field('service_lane', ['en' => 'Service Lane', 'ar' => 'ممر خدمة'], 'bool', 'Operations', 90),
                $this->field('footfall_notes', ['en' => 'Footfall Notes', 'ar' => 'ملاحظات الحركة التجارية'], 'string', 'Visibility', 100),
            ],
            'warehouse' => [
                $this->field('ceiling_height_m', ['en' => 'Ceiling Height', 'ar' => 'ارتفاع السقف'], 'decimal', 'Structure', 10, ['min' => 0, 'max' => 30, 'unit' => 'm', 'promoted' => true]),
                $this->field('loading_doors', ['en' => 'Loading Doors', 'ar' => 'أبواب التحميل'], 'int', 'Operations', 20, ['min' => 0, 'max' => 20, 'promoted' => true]),
                $this->field('yard_area_m2', ['en' => 'Yard Area', 'ar' => 'مساحة الساحة'], 'int', 'Outdoor', 30, ['min' => 0, 'max' => 100000, 'unit' => 'm2']),
                $this->field('three_phase_power', ['en' => 'Three-phase Power', 'ar' => 'كهرباء ثلاثية الطور'], 'bool', 'Utilities', 40),
                $this->field('sprinkler_system', ['en' => 'Sprinkler System', 'ar' => 'نظام رش آلي'], 'bool', 'Safety', 50),
                $this->field('truck_access', ['en' => 'Truck Access', 'ar' => 'وصول الشاحنات'], 'bool', 'Operations', 60),
                $this->field('dock_levelers', ['en' => 'Dock Levelers', 'ar' => 'مستويات التحميل'], 'int', 'Operations', 70, ['min' => 0, 'max' => 12]),
                $this->field('office_built_in', ['en' => 'Built-in Office', 'ar' => 'مكتب داخلي'], 'bool', 'Layout', 80),
                $this->field('cold_storage_ready', ['en' => 'Cold Storage Ready', 'ar' => 'مهيأ للتبريد'], 'bool', 'Condition', 90),
                $this->field('floor_load_ton', ['en' => 'Floor Load Capacity', 'ar' => 'حمولة الأرضية'], 'decimal', 'Structure', 100, ['min' => 0, 'max' => 50, 'unit' => 'ton']),
            ],
            'guest_house' => [
                $this->field('suite_count', ['en' => 'Suite Count', 'ar' => 'عدد الأجنحة'], 'int', 'Layout', 10, ['min' => 0, 'max' => 40, 'promoted' => true]),
                $this->field('parking_spaces', ['en' => 'Parking Spaces', 'ar' => 'مواقف السيارات'], 'int', 'Parking', 20, ['min' => 0, 'max' => 20]),
                $this->field('courtyard', ['en' => 'Courtyard', 'ar' => 'فناء داخلي'], 'bool', 'Outdoor', 30),
                $this->field('furnished', ['en' => 'Fully Furnished', 'ar' => 'مفروش بالكامل'], 'bool', 'Interiors', 40),
                $this->field('generator_amps', ['en' => 'Generator Share (Amps)', 'ar' => 'حصة المولد (أمبير)'], 'int', 'Utilities', 50, ['min' => 0, 'max' => 60, 'unit' => 'A']),
                $this->field('staff_room', ['en' => 'Staff Room', 'ar' => 'غرفة طاقم'], 'bool', 'Layout', 60),
                $this->field('reception_area', ['en' => 'Reception Area', 'ar' => 'منطقة استقبال'], 'bool', 'Layout', 70),
                $this->field('laundry_room', ['en' => 'Laundry Room', 'ar' => 'غرفة غسيل'], 'bool', 'Services', 80),
                $this->field('family_hall', ['en' => 'Family Hall', 'ar' => 'قاعة عائلية'], 'bool', 'Layout', 90),
                $this->field('rooftop_seating', ['en' => 'Rooftop Seating', 'ar' => 'جلسة سطح'], 'bool', 'Outdoor', 100),
            ],
            'hotel' => [
                $this->field('keys_count', ['en' => 'Keys Count', 'ar' => 'عدد الغرف'], 'int', 'Operations', 10, ['min' => 1, 'max' => 300, 'promoted' => true]),
                $this->field('parking_spaces', ['en' => 'Parking Spaces', 'ar' => 'مواقف السيارات'], 'int', 'Parking', 20, ['min' => 0, 'max' => 100]),
                $this->field('backup_power', ['en' => 'Backup Power', 'ar' => 'طاقة احتياطية'], 'bool', 'Utilities', 30),
                $this->field('restaurant_space', ['en' => 'Restaurant Space', 'ar' => 'مساحة مطعم'], 'bool', 'Services', 40),
                $this->field('conference_room', ['en' => 'Conference Room', 'ar' => 'قاعة اجتماعات'], 'bool', 'Services', 50),
                $this->field('laundry_facility', ['en' => 'Laundry Facility', 'ar' => 'مرفق غسيل'], 'bool', 'Services', 60),
                $this->field('generator_amps', ['en' => 'Generator Capacity (Amps)', 'ar' => 'قدرة المولد (أمبير)'], 'int', 'Utilities', 70, ['min' => 0, 'max' => 400, 'unit' => 'A']),
                $this->field('staff_housing', ['en' => 'Staff Housing', 'ar' => 'سكن الموظفين'], 'bool', 'Operations', 80),
                $this->field('elevators_count', ['en' => 'Elevators Count', 'ar' => 'عدد المصاعد'], 'int', 'Building', 90, ['min' => 0, 'max' => 20]),
                $this->field('service_kitchens', ['en' => 'Service Kitchens', 'ar' => 'مطابخ خدمية'], 'bool', 'Services', 100),
            ],
            'residential_land' => [
                $this->field('plot_frontage_m', ['en' => 'Plot Frontage', 'ar' => 'واجهة القطعة'], 'decimal', 'Land', 10, ['min' => 0, 'max' => 500, 'unit' => 'm', 'promoted' => true]),
                $this->field('street_width_m', ['en' => 'Street Width', 'ar' => 'عرض الشارع'], 'decimal', 'Land', 20, ['min' => 0, 'max' => 200, 'unit' => 'm']),
                $this->field('corner_plot', ['en' => 'Corner Plot', 'ar' => 'قطعة زاوية'], 'bool', 'Land', 30),
                $this->field('serviced_land', ['en' => 'Serviced Land', 'ar' => 'أرض مخدومة'], 'bool', 'Utilities', 40),
                $this->field('permit_ready', ['en' => 'Permit Ready', 'ar' => 'جاهزة للترخيص'], 'bool', 'Paperwork', 50),
                $this->field('sewer_connection', ['en' => 'Sewer Connection', 'ar' => 'ربط مجاري'], 'bool', 'Utilities', 60),
                $this->field('electricity_connection', ['en' => 'Electricity Connection', 'ar' => 'ربط كهرباء'], 'bool', 'Utilities', 70),
                $this->field('water_connection', ['en' => 'Water Connection', 'ar' => 'ربط ماء'], 'bool', 'Utilities', 80),
                $this->field('land_levelled', ['en' => 'Levelled Land', 'ar' => 'أرض مسوّاة'], 'bool', 'Condition', 90),
                $this->field('nearby_services', ['en' => 'Nearby Services', 'ar' => 'خدمات قريبة'], 'bool', 'Location', 100),
            ],
            'commercial_land' => [
                $this->field('plot_frontage_m', ['en' => 'Plot Frontage', 'ar' => 'واجهة القطعة'], 'decimal', 'Land', 10, ['min' => 0, 'max' => 800, 'unit' => 'm', 'promoted' => true]),
                $this->field('street_width_m', ['en' => 'Street Width', 'ar' => 'عرض الشارع'], 'decimal', 'Land', 20, ['min' => 0, 'max' => 300, 'unit' => 'm']),
                $this->field('corner_plot', ['en' => 'Corner Plot', 'ar' => 'قطعة زاوية'], 'bool', 'Land', 30),
                $this->field('serviced_land', ['en' => 'Serviced Land', 'ar' => 'أرض مخدومة'], 'bool', 'Utilities', 40),
                $this->field('permit_ready', ['en' => 'Permit Ready', 'ar' => 'جاهزة للترخيص'], 'bool', 'Paperwork', 50),
                $this->field('heavy_traffic_access', ['en' => 'Heavy Traffic Access', 'ar' => 'وصول مروري عالٍ'], 'bool', 'Location', 60),
                $this->field('electricity_connection', ['en' => 'Electricity Connection', 'ar' => 'ربط كهرباء'], 'bool', 'Utilities', 70),
                $this->field('water_connection', ['en' => 'Water Connection', 'ar' => 'ربط ماء'], 'bool', 'Utilities', 80),
                $this->field('warehouse_ready', ['en' => 'Warehouse Ready', 'ar' => 'مناسب للمخازن'], 'bool', 'Use Case', 90),
                $this->field('billboard_exposure', ['en' => 'Billboard Exposure', 'ar' => 'واجهة إعلانية'], 'bool', 'Visibility', 100),
            ],
        ];

        foreach ($definitions as $subcategorySlug => $fields) {
            $subcategory = Subcategory::query()->firstWhere('slug', $subcategorySlug);

            if (! $subcategory) {
                continue;
            }

            foreach ($fields as $field) {
                AttributeField::updateOrCreate(
                    [
                        'tenant_id' => null,
                        'subcategory_id' => $subcategory->id,
                        'key' => $field['key'],
                    ],
                    [
                        'label' => $field['label']['en'],
                        'label_translations' => $field['label'],
                        'type' => $field['type'],
                        'required' => $field['required'],
                        'searchable' => $field['searchable'],
                        'facetable' => $field['facetable'],
                        'promoted' => $field['promoted'],
                        'options' => $field['options'],
                        'unit' => $field['unit'],
                        'min' => $field['min'],
                        'max' => $field['max'],
                        'group' => $field['group'],
                        'sort' => $field['sort'],
                    ]
                );
            }
        }
    }

    /**
     * @param  array{min?: int, max?: int, unit?: string, required?: bool, searchable?: bool, facetable?: bool, promoted?: bool, options?: array}  $extra
     * @return array<string, mixed>
     */
    protected function field(string $key, array $label, string $type, string $group, int $sort, array $extra = []): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'type' => $type,
            'required' => $extra['required'] ?? false,
            'searchable' => $extra['searchable'] ?? true,
            'facetable' => $extra['facetable'] ?? true,
            'promoted' => $extra['promoted'] ?? false,
            'options' => $extra['options'] ?? null,
            'unit' => $extra['unit'] ?? null,
            'min' => $extra['min'] ?? null,
            'max' => $extra['max'] ?? null,
            'group' => $group,
            'sort' => $sort,
        ];
    }
}
