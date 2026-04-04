<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait StoresPhotos
{
    protected function storePhotos(Request $request, string $directory, array $existing = []): array
    {
        $photos = $this->retainedPhotos($request, $existing);
        $this->deleteRemovedPhotos($existing, $photos);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }
                try {
                    $path = $file->store($directory, 'public');
                    if ($path) {
                        $photos[] = $path;
                    }
                } catch (\ValueError $e) {
                    continue;
                }
            }
        }

        return $photos;
    }

    protected function retainedPhotos(Request $request, array $existing = []): array
    {
        $existingPhotos = collect($existing)
            ->map(fn ($photo) => $this->normalizePhotoForStorage($photo))
            ->filter()
            ->values()
            ->all();

        if (! $request->has('keep_photos_present') && ! $request->exists('keep_photos')) {
            return $existingPhotos;
        }

        $requestedPhotos = collect((array) $request->input('keep_photos', []))
            ->map(fn ($photo) => $this->normalizePhotoForStorage($photo))
            ->filter()
            ->values()
            ->all();

        return array_values(array_filter(
            $existingPhotos,
            fn ($photo) => in_array($photo, $requestedPhotos, true)
        ));
    }

    protected function normalizePhotoForStorage(mixed $photo): ?string
    {
        if (! is_string($photo)) {
            return null;
        }

        $photo = trim($photo);
        if ($photo === '') {
            return null;
        }

        if (Str::startsWith($photo, ['http://', 'https://'])) {
            $publicBase = rtrim((string) Storage::disk('public')->url(''), '/');
            if ($publicBase !== '' && Str::startsWith($photo, $publicBase)) {
                return ltrim(Str::after($photo, $publicBase), '/');
            }

            $path = parse_url($photo, PHP_URL_PATH);
            if (is_string($path) && Str::contains($path, '/storage/')) {
                return ltrim(Str::after($path, '/storage/'), '/');
            }

            return $photo;
        }

        if (Str::startsWith($photo, '/storage/')) {
            return ltrim(Str::after($photo, '/storage/'), '/');
        }

        return ltrim($photo, '/');
    }

    protected function deleteRemovedPhotos(array $existing = [], array $retained = []): void
    {
        $existingPhotos = collect($existing)
            ->map(fn ($photo) => $this->normalizePhotoForStorage($photo))
            ->filter()
            ->values()
            ->all();

        $retainedPhotos = collect($retained)
            ->map(fn ($photo) => $this->normalizePhotoForStorage($photo))
            ->filter()
            ->values()
            ->all();

        $removedPhotos = array_diff($existingPhotos, $retainedPhotos);

        foreach ($removedPhotos as $photo) {
            if (! is_string($photo) || $photo === '' || Str::startsWith($photo, ['http://', 'https://'])) {
                continue;
            }

            Storage::disk('public')->delete($photo);
        }
    }
}
