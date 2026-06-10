<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $admin = DB::table('users')->where('type', 'superadmin')->first();
        if (!$admin) return;

        $settings = [
            'email_driver'      => 'smtp',
            'email_provider'    => 'smtp',
            'email_host'        => 'mail.sahelhub.com',
            'email_port'        => '465',
            'email_encryption'  => 'ssl',
            'email_username'    => 'app@sahelhub.com',
            'email_password'    => 'bA3$*aPRzTgu$hZ',
            'email_fromAddress' => 'app@sahelhub.com',
            'email_fromName'    => 'SahelHub',
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
