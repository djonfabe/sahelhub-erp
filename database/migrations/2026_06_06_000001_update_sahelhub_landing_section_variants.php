<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('landing_page_settings')) {
            return;
        }

        $setting = DB::table('landing_page_settings')->first();
        if (!$setting) {
            return;
        }

        $config = json_decode($setting->config_sections, true) ?? [];

        if (!isset($config['sections'])) {
            $config['sections'] = [];
        }

        $variants = [
            'header'   => 'header3',
            'hero'     => 'hero5',
            'stats'    => 'stats2',
            'features' => 'features3',
            'modules'  => 'modules5',
            'benefits' => 'benefits2',
            'gallery'  => 'gallery2',
            'cta'      => 'cta4',
            'footer'   => 'footer5',
        ];

        foreach ($variants as $section => $variant) {
            if (!isset($config['sections'][$section])) {
                $config['sections'][$section] = [];
            }
            $config['sections'][$section]['variant'] = $variant;
        }

        DB::table('landing_page_settings')
            ->where('id', $setting->id)
            ->update(['config_sections' => json_encode($config)]);
    }

    public function down(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('landing_page_settings')) {
            return;
        }

        $setting = DB::table('landing_page_settings')->first();
        if (!$setting) {
            return;
        }

        $config = json_decode($setting->config_sections, true) ?? [];

        $defaults = [
            'header'   => 'header1',
            'hero'     => 'hero1',
            'stats'    => 'stats1',
            'features' => 'features1',
            'modules'  => 'modules1',
            'benefits' => 'benefits1',
            'gallery'  => 'gallery1',
            'cta'      => 'cta1',
            'footer'   => 'footer1',
        ];

        foreach ($defaults as $section => $variant) {
            if (isset($config['sections'][$section])) {
                $config['sections'][$section]['variant'] = $variant;
            }
        }

        DB::table('landing_page_settings')
            ->where('id', $setting->id)
            ->update(['config_sections' => json_encode($config)]);
    }
};
