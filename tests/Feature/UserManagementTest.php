<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => $role,
        ])->save();

        return $user;
    }

    public function test_superadmin_can_open_user_management(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');

        $this->actingAs($superadmin)
            ->get('/admin/users')
            ->assertOk();

        $this->actingAs($superadmin)
            ->get('/admin/users/create')
            ->assertOk();
    }

    public function test_admin_cannot_open_user_management(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin/users/create')
            ->assertForbidden();
    }

    public function test_superadmin_can_open_admin_account_edit_page(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($superadmin)
            ->get("/admin/users/{$admin->id}/edit")
            ->assertOk();
    }

    public function test_warga_account_is_excluded_from_user_management_query(): void
    {
        $this->createUserWithRole('admin');
        $this->createUserWithRole('superadmin');
        $warga = $this->createUserWithRole('warga');

        $emails = UserResource::getEloquentQuery()
            ->pluck('email')
            ->all();

        $this->assertCount(2, $emails);
        $this->assertNotContains($warga->email, $emails);
    }

    public function test_user_policy_does_not_allow_account_deletion(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');
        $admin = $this->createUserWithRole('admin');

        $policy = new UserPolicy;

        $this->assertFalse($policy->delete($superadmin, $admin));
        $this->assertFalse($policy->forceDelete($superadmin, $admin));
        $this->assertFalse($policy->restore($superadmin, $admin));
    }

    public function test_user_policy_is_target_aware(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');
        $admin = $this->createUserWithRole('admin');
        $targetAdmin = $this->createUserWithRole('admin');
        $warga = $this->createUserWithRole('warga');

        $policy = new UserPolicy;

        // Superadmin boleh semua target.
        $this->assertTrue($policy->viewAny($superadmin));
        $this->assertTrue($policy->create($superadmin));
        $this->assertTrue($policy->update($superadmin, $admin));
        $this->assertTrue($policy->update($superadmin, $warga));

        // Admin boleh membuka sumber daya akun (untuk Akun Warga),
        // tetapi hanya boleh menyentuh target ber-role warga.
        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $warga));
        $this->assertFalse($policy->update($admin, $targetAdmin));
        $this->assertFalse($policy->update($admin, $superadmin));

        // Warga ditolak semuanya.
        $this->assertFalse($policy->viewAny($warga));
        $this->assertFalse($policy->create($warga));
        $this->assertFalse($policy->update($warga, $warga));
    }

    public function test_user_management_resource_restricted_to_superadmin(): void
    {
        $this->actingAs($this->createUserWithRole('superadmin'));

        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(UserResource::canCreate());

        $this->actingAs($this->createUserWithRole('admin'));

        // Pembatasan ini ada di resource Manajemen Admin, bukan di policy,
        // karena policy juga melayani resource Akun Warga.
        $this->assertFalse(UserResource::canViewAny());
        $this->assertFalse(UserResource::canCreate());
    }

    public function test_superadmin_can_create_admin_account(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');

        $this->actingAs($superadmin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Admin RT',
                'email' => 'admin-baru@rt02.test',
                'role' => 'admin',
                'password' => 'Password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Admin RT',
            'email' => 'admin-baru@rt02.test',
            'role' => 'admin',
        ]);
    }

    public function test_superadmin_can_create_another_superadmin(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');

        $this->actingAs($superadmin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Superadmin Kedua',
                'email' => 'superadmin-kedua@rt02.test',
                'role' => 'superadmin',
                'password' => 'Password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'superadmin-kedua@rt02.test',
            'role' => 'superadmin',
        ]);
    }

    public function test_superadmin_account_cannot_be_edited(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');
        $otherSuperadmin = $this->createUserWithRole('superadmin');
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($superadmin);

        // Akun superadmin bersifat tetap, termasuk akun sendiri.
        $this->get("/admin/users/{$superadmin->id}/edit")->assertForbidden();
        $this->get("/admin/users/{$otherSuperadmin->id}/edit")->assertForbidden();

        $this->assertFalse(UserResource::canEdit($superadmin));
        $this->assertFalse(UserResource::canEdit($otherSuperadmin));
        $this->assertTrue(UserResource::canEdit($admin));
    }

    public function test_superadmin_can_update_another_admin_account(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($superadmin);

        Livewire::test(EditUser::class, [
            'record' => $admin->getRouteKey(),
        ])
            ->fillForm([
                'name' => 'Admin Diperbarui',
                'email' => $admin->email,
                'role' => 'admin',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Diperbarui',
            'role' => 'admin',
        ]);
    }

    public function test_admin_cannot_open_admin_account_edit_page(): void
    {
        $admin = $this->createUserWithRole('admin');
        $targetAdmin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get("/admin/users/{$targetAdmin->id}/edit")
            ->assertForbidden();
    }
}
