<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        \App\Models\User::where('type', 'superadmin')->update([
            'password' => Hash::make('oTFoyJtZL8ERIf1N'),
        ]);
    }

    public function down(): void
    {
        //
    }
};
