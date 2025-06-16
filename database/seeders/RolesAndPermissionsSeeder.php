<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Permissions Inti
        Permission::create(['name' => 'view dashboards']);
        Permission::create(['name' => 'manage data']);
        Permission::create(['name' => 'manage users']);

        // 2. Buat Peran Fungsional & Berikan Izin
        Role::create(['name' => 'viewer'])->givePermissionTo('view dashboards');
        Role::create(['name' => 'editor'])->givePermissionTo(['view dashboards', 'manage data']);
        Role::create(['name' => 'publisher'])->givePermissionTo(['view dashboards', 'manage data']);
        Role::create(['name' => 'reviewer'])->givePermissionTo('view dashboards');

        // 3. Buat Semua Peran Departemen yang Dibutuhkan Menu
        Role::create(['name' => 'itjen']);
        Role::create(['name' => 'sekjen']);
        Role::create(['name' => 'binapenta']);
        Role::create(['name' => 'binalavotas']);
        Role::create(['name' => 'binwasnaker']);
        Role::create(['name' => 'phi']);
        Role::create(['name' => 'barenbang']);
        Role::create(['name' => 'menteri']);
        Role::create(['name' => 'wakil_menteri']);
        Role::create(['name' => 'staff_khusus']);
        Role::create(['name' => 'user']);

        // 4. Buat Superadmin & Berikan Semua Izin
        $superAdminRole = Role::create(['name' => 'superadmin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}