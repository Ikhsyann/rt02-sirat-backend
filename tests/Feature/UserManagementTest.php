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

    public function test_only_superadmin_can_manage_user_records(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');
        $admin = $this->createUserWithRole('admin');

        $policy = new UserPolicy;

        $this->assertTrue($policy->viewAny($superadmin));
        $this->assertTrue($policy->create($superadmin));
        $this->assertTrue($policy->update($superadmin, $admin));

        $this->assertFalse($policy->viewAny($admin));
        $this->assertFalse($policy->create($admin));
        $this->assertFalse($policy->update($admin, $superadmin));
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

    public function test_superadmin_cannot_change_own_role(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');

        $this->actingAs($superadmin);

        Livewire::test(EditUser::class, [
            'record' => $superadmin->getRouteKey(),
        ])
            ->fillForm([
                'role' => 'admin',
            ])
            ->call('save')
            ->assertHasFormErrors(['role']);

        $this->assertSame(
            'superadmin',
            $superadmin->fresh()->role
        );
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

    public function test_superadmin_can_demote_another_superadmin(): void
    {
        $currentSuperadmin = $this->createUserWithRole('superadmin');
        $targetSuperadmin = $this->createUserWithRole('superadmin');

        $this->actingAs($currentSuperadmin);

        Livewire::test(EditUser::class, [
            'record' => $targetSuperadmin->getRouteKey(),
        ])
            ->fillForm([
                'name' => $targetSuperadmin->name,
                'email' => $targetSuperadmin->email,
                'role' => 'admin',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            'admin',
            $targetSuperadmin->fresh()->role
        );

        $this->assertSame(
            1,
            User::where('role', 'superadmin')->count()
        );
    }
}
