<?php

namespace Tests\Feature;

use App\Models\AssociationAccount;
use App\Models\Barangay;
use App\Models\BarangayAccount;
use App\Models\FarmerRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssociationAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $association;
    protected User $otherAssociation;
    protected User $brgy;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'x']);
        $assocRole = Role::firstOrCreate(['name' => 'association'], ['description' => 'x']);
        $brgyRole = Role::firstOrCreate(['name' => 'barangay_user'], ['description' => 'x']);
        $barangay = Barangay::firstOrCreate(['name' => 'Test Brgy']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);

        $this->association = User::factory()->create(['role_id' => $assocRole->id]);
        AssociationAccount::create([
            'user_id' => $this->association->id,
            'association_name' => 'Test Rice Farmers Association',
            'approval_status' => 'approved',
        ]);

        $this->otherAssociation = User::factory()->create(['role_id' => $assocRole->id]);
        AssociationAccount::create([
            'user_id' => $this->otherAssociation->id,
            'association_name' => 'Other Association',
            'approval_status' => 'approved',
        ]);

        $this->brgy = User::factory()->create(['role_id' => $brgyRole->id]);
        BarangayAccount::create(['user_id' => $this->brgy->id, 'barangay_id' => $barangay->id, 'approval_status' => 'approved']);
    }

    public function test_association_can_submit_request_without_a_farmer(): void
    {
        $this->actingAs($this->association)->get('/requests/create')->assertOk()->assertDontSee('Select Farmer');

        $this->actingAs($this->association)->post('/requests', [
            'request_type' => 'seeds_distribution',
            'item_service' => 'Rice seeds',
            'quantity' => 50,
            'quantity_unit' => 'kg',
        ])->assertRedirect(route('requests.index'));

        $req = FarmerRequest::first();
        $this->assertNotNull($req);
        $this->assertNull($req->farmer_id);
        $this->assertEquals($this->association->id, $req->submitted_by);
        $this->assertEquals('Test Rice Farmers Association', $req->requester_name);
    }

    public function test_association_only_sees_own_requests(): void
    {
        FarmerRequest::create([
            'request_number' => 'REQ-2026-00001', 'request_type' => 'seeds_distribution',
            'item_service' => 'Seeds', 'status' => 'pending', 'submitted_by' => $this->association->id,
        ]);
        FarmerRequest::create([
            'request_number' => 'REQ-2026-00002', 'request_type' => 'fertilizer_request',
            'item_service' => 'Fertilizer', 'status' => 'pending', 'submitted_by' => $this->otherAssociation->id,
        ]);

        $resp = $this->actingAs($this->association)->get('/requests');
        $resp->assertOk()->assertSee('Seeds')->assertDontSee('Fertilizer');
    }

    public function test_association_cannot_view_another_associations_request_directly(): void
    {
        $req = FarmerRequest::create([
            'request_number' => 'REQ-2026-00003', 'request_type' => 'fertilizer_request',
            'item_service' => 'Fertilizer', 'status' => 'pending', 'submitted_by' => $this->otherAssociation->id,
        ]);

        $this->actingAs($this->association)->get("/requests/{$req->id}")->assertForbidden();
    }

    public function test_association_has_no_access_to_farmers(): void
    {
        $this->actingAs($this->association)->get('/farmers')->assertForbidden();
    }

    public function test_admin_can_create_approved_association_account(): void
    {
        $resp = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Coop Officer', 'email' => 'coop@example.com',
            'password' => 'Password@123', 'password_confirmation' => 'Password@123',
            'role_id' => Role::where('name', 'association')->first()->id,
            'association_name' => 'Coop Association',
        ]);

        $resp->assertRedirect(route('users.index'));
        $user = User::where('email', 'coop@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('active', $user->status);
        $this->assertEquals('Coop Association', $user->associationAccount->association_name);
    }

    public function test_association_self_registration_requires_admin_approval(): void
    {
        $resp = $this->post('/register', [
            'name' => 'New Assoc Rep', 'email' => 'newassoc@example.com',
            'password' => 'Password@123', 'password_confirmation' => 'Password@123',
            'account_type' => 'association', 'association_name' => 'New Farmers Group',
        ]);

        $resp->assertRedirect(route('login'));
        $user = User::where('email', 'newassoc@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('inactive', $user->status);
        $this->assertEquals('pending', $user->associationAccount->approval_status);

        // Cannot log in yet.
        $this->post('/login', [
            'email' => 'newassoc@example.com', 'password' => 'Password@123', 'role_hint' => 'association',
        ])->assertSessionHasErrors('email');

        // Admin approves, then login works.
        $this->actingAs($this->admin)->post("/users/{$user->id}/approve")->assertRedirect(route('users.index'));
        $this->assertEquals('approved', $user->fresh()->associationAccount->approval_status);

        $this->post('/login', [
            'email' => 'newassoc@example.com', 'password' => 'Password@123', 'role_hint' => 'association',
        ])->assertRedirect(route('dashboard'));
    }
}
