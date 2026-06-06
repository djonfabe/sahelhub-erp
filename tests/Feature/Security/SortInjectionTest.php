<?php

namespace Tests\Feature\Security;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SortInjectionTest extends TestCase
{
    use RefreshDatabase;

    private function companyWith(array $perms): User
    {
        $user = User::factory()->company()->create();
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
    // Allowlisted columns are accepted
    // -------------------------------------------------------------------------

    #[DataProvider('validSortColumns')]
    public function test_valid_sort_column_returns_ok(string $column): void
    {
        $user = $this->companyWith(['manage-helpdesk-tickets', 'manage-own-helpdesk-tickets']);
        HelpdeskTicket::factory(3)->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('helpdesk-tickets.index', ['sort' => $column, 'direction' => 'asc']));

        $response->assertOk();
    }

    public static function validSortColumns(): array
    {
        return [
            ['created_at'],
            ['title'],
            ['status'],
            ['priority'],
            ['ticket_id'],
        ];
    }

    // -------------------------------------------------------------------------
    // Columns outside the allowlist are silently ignored (no SQL error)
    // -------------------------------------------------------------------------

    #[DataProvider('maliciousSortValues')]
    public function test_malicious_sort_column_does_not_cause_error(string $column): void
    {
        $user = $this->companyWith(['manage-helpdesk-tickets', 'manage-own-helpdesk-tickets']);

        $response = $this->actingAs($user)
            ->get(route('helpdesk-tickets.index', ['sort' => $column, 'direction' => 'asc']));

        // Must return 200 — the injected column is ignored and falls back to default ordering
        $response->assertOk();
    }

    public static function maliciousSortValues(): array
    {
        return [
            ['password'],
            ['(SELECT 1)'],
            ['created_at; DROP TABLE helpdesk_tickets--'],
            ["'; DELETE FROM users; --"],
            ['../../etc/passwd'],
            ['1 UNION SELECT password FROM users'],
        ];
    }

    // -------------------------------------------------------------------------
    // Sort direction is constrained to asc / desc only
    // -------------------------------------------------------------------------

    public function test_invalid_direction_defaults_to_asc(): void
    {
        $user = $this->companyWith(['manage-helpdesk-tickets', 'manage-own-helpdesk-tickets']);
        HelpdeskTicket::factory(2)->create(['created_by' => $user->id]);

        // An injected direction like 'DESC; DROP TABLE...' must not cause an error
        $response = $this->actingAs($user)
            ->get(route('helpdesk-tickets.index', [
                'sort'      => 'created_at',
                'direction' => 'DESC; DROP TABLE helpdesk_tickets--',
            ]));

        $response->assertOk();
        // Table still exists — no injection succeeded
        $this->assertDatabaseCount('helpdesk_tickets', 2);
    }
}
