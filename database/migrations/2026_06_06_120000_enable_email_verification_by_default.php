<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')
            ->where('key', 'enableEmailVerification')
            ->where('value', 'off')
            ->update(['value' => 'on']);
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('key', 'enableEmailVerification')
            ->where('value', 'on')
            ->update(['value' => 'off']);
    }
};
