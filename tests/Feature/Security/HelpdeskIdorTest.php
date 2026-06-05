<?php

namespace Tests\Feature\Security;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class HelpdeskIdorTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

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

    private function superadminWith(array $perms): User
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
    // Ticket — show
    // -------------------------------------------------------------------------

    public function test_company_cannot_view_another_companys_ticket(): void
    {
        $owner = $this->companyWith(['view-helpdesk-tickets']);
        $other = $this->companyWith(['view-helpdesk-tickets']);

        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);

        $response = $this->actingAs($other)->get(route('helpdesk-tickets.show', $ticket));

        $response->assertSessionHas('error');
    }

    public function test_company_can_view_own_ticket(): void
    {
        $owner = $this->companyWith(['view-helpdesk-tickets']);
        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);

        $response = $this->actingAs($owner)->get(route('helpdesk-tickets.show', $ticket));

        $response->assertOk();
    }

    public function test_superadmin_can_view_any_ticket(): void
    {
        $owner  = $this->companyWith([]);
        $admin  = $this->superadminWith(['view-helpdesk-tickets']);
        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);

        $response = $this->actingAs($admin)->get(route('helpdesk-tickets.show', $ticket));

        $response->assertOk();
    }

    // -------------------------------------------------------------------------
    // Ticket — update
    // -------------------------------------------------------------------------

    public function test_company_cannot_update_another_companys_ticket(): void
    {
        $category = HelpdeskCategory::factory()->create();
        $owner    = $this->companyWith(['edit-helpdesk-tickets']);
        $other    = $this->companyWith(['edit-helpdesk-tickets']);

        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id, 'category_id' => $category->id]);

        $response = $this->actingAs($other)->put(route('helpdesk-tickets.update', $ticket), [
            'title'       => 'Injected',
            'description' => 'Injected',
            'status'      => 'open',
            'priority'    => 'medium',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('helpdesk_tickets', ['title' => 'Injected']);
    }

    public function test_company_can_update_own_ticket(): void
    {
        $category = HelpdeskCategory::factory()->create();
        $owner    = $this->companyWith(['edit-helpdesk-tickets']);
        $ticket   = HelpdeskTicket::factory()->create(['created_by' => $owner->id, 'category_id' => $category->id]);

        $response = $this->actingAs($owner)->put(route('helpdesk-tickets.update', $ticket), [
            'title'       => 'Updated Title',
            'description' => 'Updated description',
            'status'      => 'in_progress',
            'priority'    => 'high',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('helpdesk_tickets', ['id' => $ticket->id, 'title' => 'Updated Title']);
    }

    // -------------------------------------------------------------------------
    // Ticket — destroy
    // -------------------------------------------------------------------------

    public function test_company_cannot_delete_another_companys_ticket(): void
    {
        $owner = $this->companyWith(['delete-helpdesk-tickets']);
        $other = $this->companyWith(['delete-helpdesk-tickets']);

        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);

        $response = $this->actingAs($other)->delete(route('helpdesk-tickets.destroy', $ticket));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('helpdesk_tickets', ['id' => $ticket->id]);
    }

    public function test_company_can_delete_own_ticket(): void
    {
        $owner  = $this->companyWith(['delete-helpdesk-tickets']);
        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($owner)->delete(route('helpdesk-tickets.destroy', $ticket));

        $this->assertDatabaseMissing('helpdesk_tickets', ['id' => $ticket->id]);
    }

    public function test_superadmin_can_delete_any_ticket(): void
    {
        $owner  = $this->companyWith([]);
        $admin  = $this->superadminWith(['delete-helpdesk-tickets']);
        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($admin)->delete(route('helpdesk-tickets.destroy', $ticket));

        $this->assertDatabaseMissing('helpdesk_tickets', ['id' => $ticket->id]);
    }

    // -------------------------------------------------------------------------
    // Reply — destroy
    // -------------------------------------------------------------------------

    public function test_company_cannot_delete_reply_on_another_companys_ticket(): void
    {
        $owner = $this->companyWith(['delete-helpdesk-replies']);
        $other = $this->companyWith(['delete-helpdesk-replies']);

        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);
        $reply  = HelpdeskReply::factory()->create(['ticket_id' => $ticket->id, 'created_by' => $owner->id]);

        $response = $this->actingAs($other)
            ->deleteJson(route('helpdesk-replies.destroy', $reply->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('helpdesk_replies', ['id' => $reply->id]);
    }

    public function test_company_can_delete_reply_on_own_ticket(): void
    {
        $owner  = $this->companyWith(['delete-helpdesk-replies']);
        $ticket = HelpdeskTicket::factory()->create(['created_by' => $owner->id]);
        $reply  = HelpdeskReply::factory()->create(['ticket_id' => $ticket->id, 'created_by' => $owner->id]);

        $response = $this->actingAs($owner)
            ->deleteJson(route('helpdesk-replies.destroy', $reply->id));

        $response->assertOk();
        $this->assertDatabaseMissing('helpdesk_replies', ['id' => $reply->id]);
    }

    public function test_returns_404_for_nonexistent_reply(): void
    {
        $user = $this->companyWith(['delete-helpdesk-replies']);

        $response = $this->actingAs($user)->deleteJson(route('helpdesk-replies.destroy', 99999));

        $response->assertStatus(404);
    }
}
