<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        $iraq = Country::updateOrCreate(
            ['iso2' => 'IQ'],
            [
                'iso3' => 'IRQ',
                'phone_code' => '+964',
                'currency_code' => 'IQD',
                'name_en' => 'Iraq',
                'name_ar' => 'العراق',
                'lat' => 33.223191,
                'lng' => 43.679291,
                'is_active' => true,
            ]
        );

        $governorates = [
            [
                'code' => 'BGD',
                'name_en' => 'Baghdad',
                'name_ar' => 'بغداد',
                'cities' => [
                    ['name_en' => 'Baghdad', 'name_ar' => 'بغداد', 'lat' => 33.315241, 'lng' => 44.366066],
                    ['name_en' => 'Kadhimiya', 'name_ar' => 'الكاظمية', 'lat' => 33.378453, 'lng' => 44.337378],
                    ['name_en' => 'Adhamiya', 'name_ar' => 'الأعظمية', 'lat' => 33.376832, 'lng' => 44.381540],
                    ['name_en' => 'Sadr City', 'name_ar' => 'مدينة الصدر', 'lat' => 33.389244, 'lng' => 44.465936],
                ],
            ],
            [
                'code' => 'BSR',
                'name_en' => 'Basra',
                'name_ar' => 'البصرة',
                'cities' => [
                    ['name_en' => 'Basra', 'name_ar' => 'البصرة', 'lat' => 30.508523, 'lng' => 47.780396],
                    ['name_en' => 'Umm Qasr', 'name_ar' => 'أم قصر', 'lat' => 30.036211, 'lng' => 47.919886],
                    ['name_en' => 'Zubair', 'name_ar' => 'الزبير', 'lat' => 30.392950, 'lng' => 47.701554],
                    ['name_en' => 'Qurna', 'name_ar' => 'القرنة', 'lat' => 31.015408, 'lng' => 47.433384],
                ],
            ],
            [
                'code' => 'ARB',
                'name_en' => 'Erbil',
                'name_ar' => 'أربيل',
                'cities' => [
                    ['name_en' => 'Erbil', 'name_ar' => 'أربيل', 'lat' => 36.191113, 'lng' => 44.009167],
                    ['name_en' => 'Ankawa', 'name_ar' => 'عنكاوا', 'lat' => 36.234240, 'lng' => 43.995350],
                    ['name_en' => 'Shaqlawa', 'name_ar' => 'شقلاوة', 'lat' => 36.403258, 'lng' => 44.325028],
                    ['name_en' => 'Soran', 'name_ar' => 'سوران', 'lat' => 36.653184, 'lng' => 44.544880],
                ],
            ],
            [
                'code' => 'NJF',
                'name_en' => 'Najaf',
                'name_ar' => 'النجف',
                'cities' => [
                    ['name_en' => 'Najaf', 'name_ar' => 'النجف', 'lat' => 31.998540, 'lng' => 44.328958],
                    ['name_en' => 'Kufa', 'name_ar' => 'الكوفة', 'lat' => 32.037746, 'lng' => 44.400879],
                ],
            ],
            [
                'code' => 'KRB',
                'name_en' => 'Karbala',
                'name_ar' => 'كربلاء',
                'cities' => [
                    ['name_en' => 'Karbala', 'name_ar' => 'كربلاء', 'lat' => 32.616027, 'lng' => 44.024887],
                    ['name_en' => 'Ain Al-Tamr', 'name_ar' => 'عين التمر', 'lat' => 32.496221, 'lng' => 43.600731],
                ],
            ],
            [
                'code' => 'NIN',
                'name_en' => 'Nineveh',
                'name_ar' => 'نينوى',
                'cities' => [
                    ['name_en' => 'Mosul', 'name_ar' => 'الموصل', 'lat' => 36.345550, 'lng' => 43.157501],
                    ['name_en' => 'Bartella', 'name_ar' => 'برطلة', 'lat' => 36.357904, 'lng' => 43.377235],
                    ['name_en' => 'Sinjar', 'name_ar' => 'سنجار', 'lat' => 36.320057, 'lng' => 41.874039],
                ],
            ],
            [
                'code' => 'SUL',
                'name_en' => 'Sulaymaniyah',
                'name_ar' => 'السليمانية',
                'cities' => [
                    ['name_en' => 'Sulaymaniyah', 'name_ar' => 'السليمانية', 'lat' => 35.564495, 'lng' => 45.430859],
                    ['name_en' => 'Chamchamal', 'name_ar' => 'جمجمال', 'lat' => 35.536666, 'lng' => 44.831944],
                    ['name_en' => 'Ranya', 'name_ar' => 'رانية', 'lat' => 36.255287, 'lng' => 44.882562],
                ],
            ],
            [
                'code' => 'DOK',
                'name_en' => 'Duhok',
                'name_ar' => 'دهوك',
                'cities' => [
                    ['name_en' => 'Duhok', 'name_ar' => 'دهوك', 'lat' => 36.866466, 'lng' => 42.988266],
                    ['name_en' => 'Zakho', 'name_ar' => 'زاخو', 'lat' => 37.144833, 'lng' => 42.687229],
                    ['name_en' => 'Amedi', 'name_ar' => 'العمادية', 'lat' => 37.091389, 'lng' => 43.487500],
                ],
            ],
            [
                'code' => 'KIR',
                'name_en' => 'Kirkuk',
                'name_ar' => 'كركوك',
                'cities' => [
                    ['name_en' => 'Kirkuk', 'name_ar' => 'كركوك', 'lat' => 35.468056, 'lng' => 44.392221],
                    ['name_en' => 'Altun Kupri', 'name_ar' => 'التون كوبري', 'lat' => 35.761625, 'lng' => 44.145443],
                ],
            ],
            [
                'code' => 'ANB',
                'name_en' => 'Anbar',
                'name_ar' => 'الأنبار',
                'cities' => [
                    ['name_en' => 'Ramadi', 'name_ar' => 'الرمادي', 'lat' => 33.425514, 'lng' => 43.299427],
                    ['name_en' => 'Fallujah', 'name_ar' => 'الفلوجة', 'lat' => 33.350000, 'lng' => 43.783333],
                    ['name_en' => 'Haditha', 'name_ar' => 'حديثة', 'lat' => 34.136944, 'lng' => 42.376389],
                ],
            ],
            [
                'code' => 'WAS',
                'name_en' => 'Wasit',
                'name_ar' => 'واسط',
                'cities' => [
                    ['name_en' => 'Kut', 'name_ar' => 'الكوت', 'lat' => 32.512807, 'lng' => 45.818171],
                    ['name_en' => 'Numaniyah', 'name_ar' => 'النعمانية', 'lat' => 32.489528, 'lng' => 45.573114],
                ],
            ],
            [
                'code' => 'BBL',
                'name_en' => 'Babylon',
                'name_ar' => 'بابل',
                'cities' => [
                    ['name_en' => 'Hillah', 'name_ar' => 'الحلة', 'lat' => 32.463667, 'lng' => 44.419632],
                    ['name_en' => 'Hashimiyah', 'name_ar' => 'الهاشمية', 'lat' => 32.335636, 'lng' => 44.393517],
                ],
            ],
            [
                'code' => 'DHI',
                'name_en' => 'Dhi Qar',
                'name_ar' => 'ذي قار',
                'cities' => [
                    ['name_en' => 'Nasiriyah', 'name_ar' => 'الناصرية', 'lat' => 31.046111, 'lng' => 46.257778],
                    ['name_en' => 'Suq Al-Shuyukh', 'name_ar' => 'سوق الشيوخ', 'lat' => 30.891667, 'lng' => 46.396389],
                ],
            ],
            [
                'code' => 'DIY',
                'name_en' => 'Diyala',
                'name_ar' => 'ديالى',
                'cities' => [
                    ['name_en' => 'Baqubah', 'name_ar' => 'بعقوبة', 'lat' => 33.744444, 'lng' => 44.641667],
                    ['name_en' => 'Khanaqin', 'name_ar' => 'خانقين', 'lat' => 34.350000, 'lng' => 45.383333],
                ],
            ],
            [
                'code' => 'SDL',
                'name_en' => 'Salah Al-Din',
                'name_ar' => 'صلاح الدين',
                'cities' => [
                    ['name_en' => 'Tikrit', 'name_ar' => 'تكريت', 'lat' => 34.607778, 'lng' => 43.678611],
                    ['name_en' => 'Samarra', 'name_ar' => 'سامراء', 'lat' => 34.198333, 'lng' => 43.874444],
                ],
            ],
            [
                'code' => 'QAD',
                'name_en' => 'Al-Qadisiyyah',
                'name_ar' => 'القادسية',
                'cities' => [
                    ['name_en' => 'Diwaniyah', 'name_ar' => 'الديوانية', 'lat' => 31.992500, 'lng' => 44.925000],
                    ['name_en' => 'Afak', 'name_ar' => 'عفك', 'lat' => 32.064444, 'lng' => 45.247222],
                ],
            ],
            [
                'code' => 'MYS',
                'name_en' => 'Maysan',
                'name_ar' => 'ميسان',
                'cities' => [
                    ['name_en' => 'Amarah', 'name_ar' => 'العمارة', 'lat' => 31.840556, 'lng' => 47.141389],
                    ['name_en' => 'Ali Al-Gharbi', 'name_ar' => 'علي الغربي', 'lat' => 32.466667, 'lng' => 46.683333],
                ],
            ],
            [
                'code' => 'MTH',
                'name_en' => 'Muthanna',
                'name_ar' => 'المثنى',
                'cities' => [
                    ['name_en' => 'Samawah', 'name_ar' => 'السماوة', 'lat' => 31.331667, 'lng' => 45.280278],
                    ['name_en' => 'Rumaitha', 'name_ar' => 'الرميثة', 'lat' => 31.528611, 'lng' => 45.203333],
                ],
            ],
            [
                'code' => 'HAL',
                'name_en' => 'Halabja',
                'name_ar' => 'حلبجة',
                'cities' => [
                    ['name_en' => 'Halabja', 'name_ar' => 'حلبجة', 'lat' => 35.177778, 'lng' => 45.986111],
                ],
            ],
        ];

        foreach ($governorates as $governorate) {
            $cities = $governorate['cities'];
            unset($governorate['cities']);

            $state = State::updateOrCreate(
                ['country_id' => $iraq->id, 'code' => $governorate['code']],
                [
                    'name_en' => $governorate['name_en'],
                    'name_ar' => $governorate['name_ar'],
                    'is_active' => true,
                ]
            );

            foreach ($cities as $city) {
                City::updateOrCreate(
                    [
                        'country_id' => $iraq->id,
                        'state_id' => $state->id,
                        'name_en' => $city['name_en'],
                    ],
                    [
                        'name_ar' => $city['name_ar'],
                        'lat' => $city['lat'],
                        'lng' => $city['lng'],
                        'timezone' => 'Asia/Baghdad',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
