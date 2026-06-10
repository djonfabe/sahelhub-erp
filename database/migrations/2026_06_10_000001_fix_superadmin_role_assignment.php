<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $user = \App\Models\User::where('type', 'superadmin')->first();

        if (!$user) {
            return;
        }

        $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        if (!$user->hasRole('superadmin')) {
            $user->assignRole($role);
        }
    }

    public function down(): void
    {
        //
    }
};
