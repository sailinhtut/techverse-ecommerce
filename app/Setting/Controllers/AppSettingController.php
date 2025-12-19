<?php

namespace App\Setting\Controllers;

use App\Setting\Models\AppSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AppSettingController
{
    public function viewAdminSettingPage(Request $request)
    {
        $settings = AppSetting::all()
            ->pluck('value', 'key')
            ->toArray();
        $site_logo = getSiteLogoURL();

        return view('pages.admin.dashboard.setting', ['settings' => $settings, 'site_logo' => $site_logo]);
    }

    public function getAppSettings(Request $request)
    {
        try {
            $settings = AppSetting::all();
            $settings = $settings->map(fn($s) => $s->jsonResponse())->all();

            return response()->json(
                [
                    'data' => $settings
                ],
                200
            );
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function setAppSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'site_logo' => 'nullable|image|max:2048',
                'settings' => 'required|array',
                'settings.*.key' => 'required|string',
                'settings.*.value' => 'nullable|string',
            ]);

            if ($request->hasFile('site_logo')) {
                $template = AppSetting::where('key', 'site_logo')->first();

                if ($template && $template->value && Storage::disk('public')->exists($template->value)) {
                    Storage::disk('public')->delete($template->value);
                }

                $path = Storage::disk('public')
                    ->putFile('site/logos',  $request->file('site_logo'));

                AppSetting::updateOrCreate(
                    ['key' => 'site_logo'],
                    ['value' => $path]
                );
            }

            foreach ($validated['settings'] as $settingData) {
                $value = $settingData['value'] ?? '';

                $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $cleanValue = preg_replace('#<(script|object)(.*?)>(.*?)</\1>#is', '', $decoded);

                AppSetting::updateOrCreate(
                    ['key' => $settingData['key']],
                    ['value' => $cleanValue]
                );
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Settings saved successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Settings saved successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function getAppSetting(Request $request, $key)
    {
        try {
            $setting = AppSetting::where('key', $key)->first();

            if (!$setting) {
                return response()->json(
                    [
                        'message' => 'Setting not found.'
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $setting->jsonResponse()
                ],
                200
            );
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteAppSetting(Request $request, $key)
    {
        try {
            $setting = AppSetting::where('key', $key)->first();

            if (!$setting) {
                return response()->json(
                    [
                        'message' => 'Setting not found.'
                    ],
                    404
                );
            }

            if ($key === 'site_logo') {
                $template = AppSetting::where('key', 'site_logo')->first();

                if ($template && $template->value && Storage::disk('public')->exists($template->value)) {
                    Storage::disk('public')->delete($template->value);
                }
            }

            $setting->delete();


            return response()->json(
                [
                    'message' => 'Setting deleted successfully.'
                ],
                200
            );
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
