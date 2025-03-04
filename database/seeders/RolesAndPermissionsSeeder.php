<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions
        Permission::findOrCreate('view posts');
        Permission::findOrCreate('create posts');
        Permission::findOrCreate('edit posts');
        Permission::findOrCreate('delete posts');
        Permission::findOrCreate('publish posts');

        // Categories permissions
        Permission::findOrCreate('view categories');
        Permission::findOrCreate('create categories');
        Permission::findOrCreate('edit categories');
        Permission::findOrCreate('delete categories');

        // Users permissions
        Permission::findOrCreate('view users');
        Permission::findOrCreate('create users');
        Permission::findOrCreate('edit users');
        Permission::findOrCreate('delete users');

        // Roles permissions
        Permission::findOrCreate('view roles');
        Permission::findOrCreate('create roles');
        Permission::findOrCreate('edit roles');
        Permission::findOrCreate('delete roles');

        //Menus permissions
        Permission::findOrCreate('view menus');
        Permission::findOrCreate('create menus');
        Permission::findOrCreate('edit menus');
        Permission::findOrCreate('delete menus');
        
        // Additional menu access permissions
        Permission::findOrCreate('access dashboard menu');
        Permission::findOrCreate('access posts menu');

        // Create roles and assign existing permissions

        //Add the super admin role
        $superAdmin = Role::findOrCreate('super admin');

        //Add the admin role has all permissions
        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo([
            'view posts','create posts','edit posts','delete posts','publish posts',
            'view categories','create categories','edit categories','delete categories',
            'view users','create users','edit users','delete users',
            'view roles','create roles','edit roles','delete roles',
            'view menus','create menus','edit menus','delete menus',
            'access dashboard menu','access posts menu'
        ]);

        //Editor role
        $editor = Role::findOrCreate('editor');
        $editor->givePermissionTo([
            'view posts','create posts','edit posts','delete posts','publish posts',
            'view categories','create categories','edit categories','delete categories',
        ]);

        //Author role
        $author = Role::findOrCreate('author');
        $author->givePermissionTo([
            'view posts','create posts','edit posts','delete posts',
            'view categories',
            'access dashboard menu','access posts menu'
        ]);

        //Regular user role (can only publish content)
        $user = Role::findOrCreate('user');
        $user->givePermissionTo(['view posts']);

    }
}
