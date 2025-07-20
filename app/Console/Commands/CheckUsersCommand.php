<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check created users and their profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== USERS AND PROFILES ===');
        
        $users = User::with(['profile', 'roles'])->get();
        
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ');
            $phone = $user->profile ? $user->profile->phone : 'N/A';
            $city = $user->profile ? $user->profile->city : 'N/A';
            
            $this->line("Name: {$user->name}");
            $this->line("Email: {$user->email}");
            $this->line("Role: {$roles}");
            $this->line("Phone: {$phone}");
            $this->line("City: {$city}");
            $this->line("---");
        }
        
        $this->info("Total Users: " . $users->count());
        $this->info("Admin: admin@ecoman.com / admin123");
        $this->info("SubAdmin: subadmin@ecoman.com / subadmin123");
        $this->info("Users: user1@example.com to user5@example.com / user123");
    }
}
