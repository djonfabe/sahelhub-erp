<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update brand color settings for all superadmin users
        $superadmins = DB::table('users')->where('type', 'superadmin')->pluck('id');

        foreach ($superadmins as $userId) {
            DB::table('settings')
                ->where('created_by', $userId)
                ->where('key', 'themeColor')
                ->update(['value' => 'custom']);

            DB::table('settings')
                ->where('created_by', $userId)
                ->where('key', 'customColor')
                ->update(['value' => '#13443B']);
        }

        // Update landing page colors config if it exists
        if (DB::getSchemaBuilder()->hasTable('landing_page_settings')) {
            $setting = DB::table('landing_page_settings')->first();
            if ($setting) {
                $config = json_decode($setting->config_sections, true) ?? [];
                $config['colors'] = [
                    'primary'   => '#13443B',
                    'secondary' => '#E0762A',
                    'accent'    => '#F4B23E',
                ];
                DB::table('landing_page_settings')
                    ->where('id', $setting->id)
                    ->update(['config_sections' => json_encode($config)]);
            }
        }
    }

    public function down(): void
    {
        $superadmins = DB::table('users')->where('type', 'superadmin')->pluck('id');

        foreach ($superadmins as $userId) {
            DB::table('settings')
                ->where('created_by', $userId)
                ->where('key', 'themeColor')
                ->update(['value' => 'green']);

            DB::table('settings')
                ->where('created_by', $userId)
                ->where('key', 'customColor')
                ->update(['value' => '#10b77f']);
        }
    }
};
