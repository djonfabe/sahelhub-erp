<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $setting = DB::table('landing_page_settings')->first();
        if (!$setting) return;

        $config = json_decode($setting->config_sections, true);
        if (!isset($config['sections']['hero'])) return;

        $current = $config['sections']['hero']['primary_button_text'] ?? '';

        // Only fix if it still contains the wrong text
        if (stripos($current, 'lire') !== false || stripos($current, 'trial') !== false || stripos($current, 'essai') !== false) {
            $config['sections']['hero']['primary_button_text'] = "S'inscrire";
            $config['sections']['hero']['primary_button_link'] = route('register');

            DB::table('landing_page_settings')
                ->where('id', $setting->id)
                ->update(['config_sections' => json_encode($config)]);
        }
    }

    public function down(): void {}
};
