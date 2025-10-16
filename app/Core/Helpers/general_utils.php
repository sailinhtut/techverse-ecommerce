<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


if (!function_exists('getDownloadableLink')) {
    function getDownloadableLink(?string $path): ?string
    {
        if (!$path) return null;
        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }
}
