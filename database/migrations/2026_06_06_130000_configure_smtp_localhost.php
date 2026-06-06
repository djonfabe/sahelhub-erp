<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $admin = DB::table('users')->where('type', 'superadmin')->first();
        if (!$admin) return;

        // Only apply defaults if email_host has never been configured
        $existing = DB::table('settings')
            ->where('created_by', $admin->id)
            ->where('key', 'email_host')
            ->value('value');

        if (!empty($existing)) return;

        $defaults = [
            'email_driver'      => 'smtp',
            'email_provider'    => 'smtp',
            'email_host'        => '127.0.0.1',
            'email_port'        => '25',
            'email_encryption'  => '',
            'email_username'    => '',
            'email_password'    => '',
            'email_fromAddress' => 'info@sahelhub.com',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key, 'created_by' => $admin->id],
                ['value' => $value, 'is_public' => false, 'updated_at' => now()]
            );
        }
    }

    public function down(): void {}
};
