<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $user_roles = Config::get('setup_data.user_roles', []);
        if (!empty($user_roles)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_roles')->truncate();
            DB::table('user_roles')->insert($user_roles);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->info('Role data seeded successfully.');
        } else {
            echo ("⚠️ No permission data found in config/user_roles.php");
        }

        $userPermissions = Config::get('setup_data.user_permission_types', []);
        if (!empty($userPermissions)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('permissions')->truncate();
            DB::table('permissions')->insert($userPermissions);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->info('Permission data seeded successfully.');
        } else {
            echo ("⚠️ No permission data found in config/user_permissions.php");
        }

        $this->command->info('Admin Data Seeded Successfully. [superadmin@gmail.com:superadmin]');
    }
}
