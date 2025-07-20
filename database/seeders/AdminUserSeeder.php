<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $subadminRole = Role::firstOrCreate(['name' => 'subadmin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create permissions
        $permissions = [
            'manage-users',
            'manage-products',
            'manage-categories',
            'manage-orders',
            'view-reports',
            'manage-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions);
        $subadminRole->givePermissionTo(['manage-products', 'manage-categories', 'manage-orders', 'view-reports']);
        $userRole->givePermissionTo([]);

        // Create Admin User
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@ecoman.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');

        // Create admin profile
        UserProfile::create([
            'user_id' => $admin->id,
            'phone' => '+84901234567',
            'address' => '123 Admin Street, District 1',
            'city' => 'Ho Chi Minh City',
            'state' => 'Ho Chi Minh',
            'country' => 'Vietnam',
            'postal_code' => '70000',
            'date_of_birth' => '1985-01-15',
            'gender' => 'male',
            'bio' => 'System Administrator with full access to all features.',
            'is_active' => true,
        ]);

        // Create SubAdmin User
        $subadmin = User::create([
            'name' => 'Sub Admin',
            'email' => 'subadmin@ecoman.com',
            'password' => Hash::make('subadmin123'),
            'email_verified_at' => now(),
        ]);

        $subadmin->assignRole('subadmin');

        // Create subadmin profile
        UserProfile::create([
            'user_id' => $subadmin->id,
            'phone' => '+84901234568',
            'address' => '456 SubAdmin Avenue, District 3',
            'city' => 'Ho Chi Minh City',
            'state' => 'Ho Chi Minh',
            'country' => 'Vietnam',
            'postal_code' => '70000',
            'date_of_birth' => '1990-05-20',
            'gender' => 'female',
            'bio' => 'Sub Administrator managing products and orders.',
            'is_active' => true,
        ]);

        // Create 5 Regular Users
        $users = [
            [
                'name' => 'Nguyen Van A',
                'email' => 'user1@example.com',
                'phone' => '+84901234569',
                'city' => 'Hanoi',
                'gender' => 'male',
                'birth_year' => 1992
            ],
            [
                'name' => 'Tran Thi B',
                'email' => 'user2@example.com',
                'phone' => '+84901234570',
                'city' => 'Da Nang',
                'gender' => 'female',
                'birth_year' => 1988
            ],
            [
                'name' => 'Le Van C',
                'email' => 'user3@example.com',
                'phone' => '+84901234571',
                'city' => 'Can Tho',
                'gender' => 'male',
                'birth_year' => 1995
            ],
            [
                'name' => 'Pham Thi D',
                'email' => 'user4@example.com',
                'phone' => '+84901234572',
                'city' => 'Hai Phong',
                'gender' => 'female',
                'birth_year' => 1993
            ],
            [
                'name' => 'Hoang Van E',
                'email' => 'user5@example.com',
                'phone' => '+84901234573',
                'city' => 'Hue',
                'gender' => 'male',
                'birth_year' => 1991
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('user123'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole('user');

            // Create user profile
            UserProfile::create([
                'user_id' => $user->id,
                'phone' => $userData['phone'],
                'address' => fake()->address(),
                'city' => $userData['city'],
                'state' => fake()->state(),
                'country' => 'Vietnam',
                'postal_code' => fake()->postcode(),
                'date_of_birth' => $userData['birth_year'] . '-' . rand(1, 12) . '-' . rand(1, 28),
                'gender' => $userData['gender'],
                'bio' => fake()->paragraph(1),
                'is_active' => true,
            ]);
        }

        $this->command->info('Admin, SubAdmin and 5 Users created successfully!');
        $this->command->info('Admin: admin@ecoman.com / admin123');
        $this->command->info('SubAdmin: subadmin@ecoman.com / subadmin123');
        $this->command->info('Users: user1@example.com to user5@example.com / user123');
    }
}
