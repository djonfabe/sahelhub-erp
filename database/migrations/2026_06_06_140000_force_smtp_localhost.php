<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $admin = DB::table('users')->where('type', 'superadmin')->first();
        if (!$admin) return;

        // Force SMTP to local Postfix — overrides any previously set external SMTP config.
        // The VPS uses Postfix on port 25 (loopback-only, no auth, no encryption).
        $settings = [
            'email_driver'      => 'smtp',
            'email_provider'    => 'smtp',
            'email_host'        => '127.0.0.1',
            'email_port'        => '25',
            'email_encryption'  => '',
            'email_username'    => '',
            'email_password'    => '',
            'email_fromAddress' => 'info@sahelhub.com',
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key, 'created_by' => $admin->id],
                ['value' => $value, 'is_public' => false, 'updated_at' => now()]
            );
        }
    }

    public function down(): void {}
};
