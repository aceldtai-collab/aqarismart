<?php

namespace App\Models\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait HasPhotoAttributes
{
    public function getPhotosAttribute($value): array
    {
        $raw = $value;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = json_last_error() === JSON_ERROR_NONE ? $decoded : [$raw];
        }
        if ($raw === null) {
            $raw = [];
        }
        if (! is_array($raw)) {
            $raw = [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (is_string($item)) {
                $item = trim($item);
                if ($item !== '') {
                    if (strpos($item, 'http') !== 0 && ! empty($item)) {
                        $item = Storage::disk('public')->url($item);
                    }
                    $out[] = $item;
                }
                continue;
            }
            if (is_array($item)) {
                $path = Arr::get($item, 'path') ?? Arr::get($item, 'url');
                if (is_string($path) && $path !== '') {
                    if (strpos($path, 'http') !== 0 && ! empty($path)) {
                        $path = Storage::disk('public')->url($path);
                    }
                    $out[] = $path;
                }
            }
        }
        return array_values($out);
    }

    public function setPhotosAttribute($value): void
    {
        $normalized = [];
        $items = $value;
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = json_last_error() === JSON_ERROR_NONE ? $decoded : [$items];
        }
        if (! is_array($items)) {
            $items = [];
        }
        foreach ($items as $item) {
            if (is_string($item)) {
                $item = trim($item);
                if ($item !== '') {
                    $normalized[] = $item;
                }
                continue;
            }
            if (is_array($item)) {
                $path = Arr::get($item, 'path') ?? Arr::get($item, 'url');
                if (is_string($path) && $path !== '') {
                    $normalized[] = $path;
                }
            }
        }
        $this->attributes['photos'] = $normalized ? json_encode(array_values($normalized)) : null;
    }
}
