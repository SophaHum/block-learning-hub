<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Run the role and permission seeder first
        $this->call(RolesAndPermissionsSeeder::class);

        //Create admin user
        $admin = User::factory()->create([
            'name' => "Admin User",
            'email' => "admin@gamil.com",
            'password' => Hash::make('password')
        ]);
        $admin->assignRole('admin');

        // Create editor user
        $editor = User::factory()->create([
            'name' => "Editor User",
            'email' => "editor@gamil.com",
            'password' => Hash::make('password')
        ]);
        $editor->assignRole('editor');

        //Create author user
        $author = User::factory()->create([
            'name' => "Author User",
            'email' => "author@gamil.com",
            'password' => Hash::make('password')
        ]);
        $author->assignRole('author');

        //Create regular user
        $user = User::factory()->create([
            'name' => "Regular User",
            'email' => "user@gamil.com",
            'password' => Hash::make('password')
        ]);
        $user->assignRole('user');

        //Seeder for categories
        $this->call(CategorySeeder::class);
    }
}
