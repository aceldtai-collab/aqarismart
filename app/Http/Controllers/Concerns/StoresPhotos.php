<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait StoresPhotos
{
    protected function storePhotos(Request $request, string $directory, array $existing = []): array
    {
        $photos = $existing;
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
}
