<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Category;
use App\Models\Product;
use Spatie\Permission\Models\Role;

class DataOverviewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:overview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show overview of all data in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ECOMAN SYSTEM DATA OVERVIEW ===');
        $this->newLine();

        // Users Overview
        $this->info('👥 USERS:');
        $totalUsers = User::count();
        $adminCount = User::role('admin')->count();
        $subadminCount = User::role('subadmin')->count();
        $userCount = User::role('user')->count();
        
        $this->line("Total Users: {$totalUsers}");
        $this->line("- Admins: {$adminCount}");
        $this->line("- SubAdmins: {$subadminCount}");
        $this->line("- Regular Users: {$userCount}");
        $this->line("- User Profiles: " . UserProfile::count());
        $this->newLine();

        // Categories Overview
        $this->info('📂 CATEGORIES:');
        $totalCategories = Category::count();
        $parentCategories = Category::whereNull('parent_id')->count();
        $childCategories = Category::whereNotNull('parent_id')->count();
        
        $this->line("Total Categories: {$totalCategories}");
        $this->line("- Parent Categories: {$parentCategories}");
        $this->line("- Child Categories: {$childCategories}");
        $this->newLine();

        // Products Overview
        $this->info('🛍️ PRODUCTS:');
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $featuredProducts = Product::where('featured', true)->count();
        $inStockProducts = Product::where('in_stock', true)->count();
        
        $this->line("Total Products: {$totalProducts}");
        $this->line("- Active Products: {$activeProducts}");
        $this->line("- Featured Products: {$featuredProducts}");
        $this->line("- In Stock Products: {$inStockProducts}");
        $this->newLine();

        // API Endpoints Overview
        $this->info('🔗 AVAILABLE API ENDPOINTS:');
        $this->line('Authentication:');
        $this->line('  POST /api/auth/register');
        $this->line('  POST /api/auth/login');
        $this->line('  POST /api/auth/logout (protected)');
        $this->line('  GET  /api/auth/me (protected)');
        $this->newLine();
        
        $this->line('Public Endpoints:');
        $this->line('  GET  /api/products');
        $this->line('  GET  /api/products/{id}');
        $this->line('  GET  /api/categories');
        $this->line('  GET  /api/categories/{id}');
        $this->newLine();
        
        $this->line('Admin Endpoints (protected):');
        $this->line('  POST /api/admin/products');
        $this->line('  PUT  /api/admin/products/{id}');
        $this->line('  DELETE /api/admin/products/{id}');
        $this->line('  GET  /api/admin/users');
        $this->line('  POST /api/admin/users');
        $this->newLine();

        // Login Credentials
        $this->info('🔐 LOGIN CREDENTIALS:');
        $this->line('Admin: admin@ecoman.com / admin123');
        $this->line('SubAdmin: subadmin@ecoman.com / subadmin123');
        $this->line('Users: user1@example.com to user5@example.com / user123');
        $this->newLine();

        $this->info('✅ System is ready for testing!');
    }
}
