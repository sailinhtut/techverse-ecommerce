<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = Config::get('setup_data.frequent_questions', []);

        if (!empty($faqs)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('frequent_questions')->truncate();
            DB::table('frequent_questions')->insert($faqs);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->command->info('FAQ data seeded successfully.');
        } else {
            echo ("⚠️ No FAQ data found in config/setup_data.php");
        }
    }
}
