<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Database\Seeder;

class UnitPhotosSeeder extends Seeder
{
    public function run(): void
    {
        // Sample royalty-free real estate images from picsum.photos (no auth required)
        $pool = [
            'https://picsum.photos/seed/room1/500/400',
            'https://picsum.photos/seed/room2/500/400',
            'https://picsum.photos/seed/room3/500/400',
            'https://picsum.photos/seed/room4/500/400',
            'https://picsum.photos/seed/room5/500/400',
            'https://picsum.photos/seed/room6/500/400',
            'https://picsum.photos/seed/room7/500/400',
            'https://picsum.photos/seed/room8/500/400',
            'https://picsum.photos/seed/room9/500/400',
            'https://picsum.photos/seed/room10/500/400',
            'https://picsum.photos/seed/room11/500/400',
            'https://picsum.photos/seed/room12/500/400',
            'https://picsum.photos/seed/room13/500/400',
        ];

        foreach (Unit::cursor() as $unit) {
            // Skip units that already have media
            if (is_array($unit->photos) && count($unit->photos) > 0) {
                continue;
            }

            // Pick 3 random images from pool
            $chosen = collect($pool)->shuffle()->take(3)->values()->all();
            $unit->photos = $chosen;
            $unit->save();
        }

        // Also seed property photos if empty
        foreach (Property::cursor() as $property) {
            if (is_array($property->photos) && count($property->photos) > 0) {
                continue;
            }
            $chosen = collect($pool)->shuffle()->take(3)->values()->all();
            $property->photos = $chosen;
            $property->save();
        }
    }
}
