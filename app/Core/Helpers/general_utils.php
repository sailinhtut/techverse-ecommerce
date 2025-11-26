<?php

use App\Setting\Models\AppSetting;
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

if (!function_exists('getSiteLogoURL')) {
    function getSiteLogoURL(): string
    {
        $template = AppSetting::where('key', 'site_logo')->first();

        if ($template && $template->value) {
            $site_logo_path = getDownloadableLink($template->value);
            return $site_logo_path;
        } else {
            return asset(config('app.app_logo'));
        }
    }
}


if (!function_exists('getParsedTemplate')) {
    function getParsedTemplate(string $templateKey): string
    {
        $template = AppSetting::where('key', $templateKey)->first()?->value ?? '';

        $siteSettings = AppSetting::whereIn('key', [
            'site_name',
            'site_description',
            'site_phone_1',
            'site_phone_2',
            'site_support_email',
            'site_contact_email',
            'site_address',
            'site_map_location_link',
            'site_primary_color',
            'site_primary_content_color'
        ])->pluck('value', 'key')->toArray();

        if ($templateKey === 'site_logo' && isset($siteSettings['site_logo']) && $siteSettings['site_logo']) {
            $site_logo_path = getDownloadableLink($siteSettings['site_logo']);
            return $site_logo_path;
        }

        $siteSettings = array_merge([
            'site_name' => config('app.name'),
            'site_description' => config('app.description'),
            'site_phone_1' => config('app.phone_1'),
            'site_phone_2' => config('app.phone_2'),
            'site_support_email' => config('app.support_email'),
            'site_contact_email' => config('app.contact_email'),
            'site_address' => config('app.address'),
            'site_map_location_link' => config('app.map_location_link'),
            'site_primary_color' => config('app.site_primary_color'),
            'site_primary_content_color' => config('app.site_primary_content_color'),
        ], $siteSettings);

        foreach ($siteSettings as $key => $value) {
            $template = str_replace("@{$key}", $value, $template);
        }

        return $template;
    }
}
