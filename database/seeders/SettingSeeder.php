<?php

namespace Database\Seeders;

use App\Setting\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $app_settings = Config::get('setup_data.app_settings', []);
        if (!empty($app_settings)) {
            AppSetting::truncate();
            AppSetting::insert($app_settings);
            $this->command->info('App settings data seeded successfully.');
        } else {
            echo ("⚠️ No app settings data found in config/setup_data.php");
        }
    }
}
