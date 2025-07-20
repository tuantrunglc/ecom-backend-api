<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deposit;
use App\Models\User;
use Carbon\Carbon;

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users (excluding admin)
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->take(5)->get();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $statuses = ['pending', 'approved', 'rejected'];
        $descriptions = [
            'Nạp tiền mua sản phẩm',
            'Nạp tiền vào ví',
            'Thanh toán đơn hàng',
            'Nạp tiền khuyến mãi',
            'Nạp tiền từ ngân hàng'
        ];

        foreach ($users as $user) {
            // Create 3-5 deposits per user
            $depositCount = rand(3, 5);
            
            for ($i = 0; $i < $depositCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $amount = rand(100000, 5000000); // 100k to 5M VND
                $createdAt = Carbon::now()->subDays(rand(1, 30));
                
                $deposit = Deposit::create([
                    'reference_code' => 'DEP_' . $user->id . '_' . time() . '_' . $i,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'description' => $descriptions[array_rand($descriptions)],
                    'bank_account' => '1234567890' . rand(10, 99),
                    'proof_image' => 'https://via.placeholder.com/400x300/0066cc/ffffff?text=Proof+' . ($i + 1),
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // If approved or rejected, add admin processing info
                if ($status !== 'pending') {
                    $adminUser = User::role('admin')->first();
                    if ($adminUser) {
                        $deposit->update([
                            'admin_note' => $status === 'approved' 
                                ? 'Đã xác nhận chuyển khoản thành công' 
                                : 'Không tìm thấy giao dịch chuyển khoản',
                            'processed_by' => $adminUser->id,
                            'processed_at' => $createdAt->addHours(rand(1, 24)),
                        ]);

                        // If approved, update user wallet balance
                        if ($status === 'approved') {
                            $user->increment('wallet_balance', $amount);
                        }
                    }
                }
            }
        }

        $this->command->info('Deposit seeder completed successfully!');
    }
}
