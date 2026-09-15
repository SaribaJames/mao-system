<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\BarangayAccount;
use App\Models\Farmer;
use App\Models\FarmerViewRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerAccessSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $brgy;
    protected Farmer $farmer;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'x']);
        $staffRole = Role::firstOrCreate(['name' => 'staff'], ['description' => 'x']);
        $brgyRole = Role::firstOrCreate(['name' => 'barangay_user'], ['description' => 'x']);
        $barangay = Barangay::firstOrCreate(['name' => 'Test Brgy']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->staff = User::factory()->create(['role_id' => $staffRole->id]);
        $this->brgy = User::factory()->create(['role_id' => $brgyRole->id]);
        BarangayAccount::create(['user_id' => $this->brgy->id, 'barangay_id' => $barangay->id, 'approval_status' => 'approved']);

        $this->farmer = Farmer::create([
            'surname' => 'Dela Cruz', 'first_name' => 'Juan', 'sex' => 'male', 'date_of_birth' => '1990-01-01',
            'barangay_id' => $barangay->id, 'registration_status' => 'approved', 'registered_by' => $this->admin->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_full_access(): void
    {
        $this->actingAs($this->admin)->get('/farmers')->assertOk();
        $this->actingAs($this->admin)->get("/farmers/{$this->farmer->id}")->assertOk()->assertSee('Part I');
        $this->actingAs($this->admin)->get('/farmers/create')->assertOk();
        $this->actingAs($this->admin)->get("/farmers/{$this->farmer->id}/edit")->assertOk();
        $this->actingAs($this->admin)->get('/farmers/view-requests')->assertOk();
    }

    public function test_staff_restricted_by_default(): void
    {
        $this->actingAs($this->staff)->get('/farmers')->assertOk();
        $resp = $this->actingAs($this->staff)->get("/farmers/{$this->farmer->id}");
        $resp->assertOk()->assertDontSee('Part I')->assertSee('Request to View');
        $this->actingAs($this->staff)->get('/farmers/create')->assertForbidden();
        $this->actingAs($this->staff)->get("/farmers/{$this->farmer->id}/edit")->assertForbidden();
        $this->actingAs($this->staff)->get('/farmers/my-view-requests')->assertOk();
        $this->actingAs($this->staff)->get('/farmers/view-requests')->assertForbidden();
    }

    public function test_brgy_restricted_and_blank_form(): void
    {
        $this->actingAs($this->brgy)->get('/farmers')->assertOk();
        $resp = $this->actingAs($this->brgy)->get("/farmers/{$this->farmer->id}");
        $resp->assertOk()->assertDontSee('Part I')->assertSee('Request to View');
        $this->actingAs($this->brgy)->get('/farmers/create')->assertForbidden();
        $this->actingAs($this->brgy)->get('/farmers/blank-form')->assertOk();
    }

    public function test_removed_routes_are_gone(): void
    {
        $this->actingAs($this->admin)->get("/farmers/{$this->farmer->id}/print")->assertNotFound();
        $this->actingAs($this->admin)->get('/farmers/pending')->assertNotFound();
    }

    public function test_full_request_approve_deny_lifecycle(): void
    {
        // Staff requests access
        $this->actingAs($this->staff)->post("/farmers/{$this->farmer->id}/request-view")->assertRedirect();
        $vr = FarmerViewRequest::first();
        $this->assertNotNull($vr);
        $this->assertEquals('pending', $vr->status);

        // Still restricted while pending
        $this->actingAs($this->staff)->get("/farmers/{$this->farmer->id}")
            ->assertOk()->assertDontSee('Part I')->assertSee('pending admin review');

        // Admin approves
        $this->actingAs($this->admin)->post("/farmers/view-requests/{$vr->id}/approve")->assertRedirect();
        $vr->refresh();
        $this->assertEquals('approved', $vr->status);
        $this->assertNotNull($vr->expires_at);
        $this->assertTrue($vr->expires_at->between(now()->addMinutes(9), now()->addMinutes(11)));

        // Staff now sees full profile
        $this->actingAs($this->staff)->get("/farmers/{$this->farmer->id}")
            ->assertOk()->assertSee('Part I');

        // Expire it manually and confirm it locks again
        $vr->update(['expires_at' => now()->subMinute()]);
        $this->actingAs($this->staff)->get("/farmers/{$this->farmer->id}")
            ->assertOk()->assertDontSee('Part I');
    }

    public function test_deny_flow(): void
    {
        $this->actingAs($this->brgy)->post("/farmers/{$this->farmer->id}/request-view")->assertRedirect();
        $vr = FarmerViewRequest::first();
        $this->actingAs($this->admin)->post("/farmers/view-requests/{$vr->id}/deny")->assertRedirect();
        $vr->refresh();
        $this->assertEquals('denied', $vr->status);
        $this->actingAs($this->brgy)->get('/farmers/my-view-requests')->assertOk()->assertSee('Denied');
    }

    public function test_only_admin_can_register(): void
    {
        $barangay = Barangay::first();
        $payload = [
            'surname' => 'Santos', 'first_name' => 'Maria', 'sex' => 'female',
            'date_of_birth' => '1985-05-05', 'barangay_id' => $barangay->id,
        ];
        $this->actingAs($this->staff)->post('/farmers', $payload)->assertForbidden();
        $this->actingAs($this->brgy)->post('/farmers', $payload)->assertForbidden();
        $this->actingAs($this->admin)->post('/farmers', $payload)->assertRedirect(route('farmers.index'));
        $this->assertDatabaseHas('farmers', ['surname' => 'Santos', 'registration_status' => 'approved']);
    }
}
