<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_roles_assigned(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);

        // Dodeljujemo ulogu korisniku u pivot tabelu
        $user->roles()->attach($role);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('developer'));
    }

    public function test_user_inherits_permissions_from_assigned_role(): void
    {
        $user = User::factory()->create();

        $permission = Permission::create([
            'name' => 'Manage Users', 
            'slug' => 'manage-users'
        ]);

        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $role->permissions()->attach($permission);

        $user->roles()->attach($role);

        // Učitavamo relaciju i proveravamo permisiju
        $user->load('roles.permissions');

        $this->assertTrue($user->hasPermission('manage-users'));
        $this->assertFalse($user->hasPermission('delete-tasks'));
    }

    public function test_gate_allows_action_based_on_user_permission(): void
    {
        $user = User::factory()->create();

        $permission = Permission::create([
            'name' => 'Manage Tasks', 
            'slug' => 'manage-tasks'
        ]);

        $role = Role::create(['name' => 'Manager', 'slug' => 'manager']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        // Definišemo pravilo na nivou Gate-a
        Gate::define('manage-tasks', function (User $user) {
            return $user->hasPermission('manage-tasks');
        });

        $this->assertTrue(Gate::forUser($user)->allows('manage-tasks'));
    }
}
