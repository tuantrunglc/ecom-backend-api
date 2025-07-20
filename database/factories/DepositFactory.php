<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deposit>
 */
class DepositFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = \App\Models\User::factory()->create()->id;
        
        return [
            'reference_code' => 'DEP_' . $userId . '_' . time() . '_' . $this->faker->unique()->numberBetween(1000, 9999),
            'user_id' => $userId,
            'amount' => $this->faker->numberBetween(100000, 5000000), // 100k to 5M VND
            'description' => $this->faker->randomElement([
                'Nạp tiền mua sản phẩm',
                'Nạp tiền vào ví',
                'Thanh toán đơn hàng',
                'Nạp tiền khuyến mãi',
                'Nạp tiền từ ngân hàng'
            ]),
            'bank_account' => $this->faker->numerify('##########'),
            'proof_image' => $this->faker->imageUrl(400, 300, 'business', true, 'Proof'),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'admin_note' => $this->faker->optional(0.7)->sentence(),
            'processed_by' => null,
            'processed_at' => null,
        ];
    }

    /**
     * Indicate that the deposit is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'admin_note' => null,
            'processed_by' => null,
            'processed_at' => null,
        ]);
    }

    /**
     * Indicate that the deposit is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'admin_note' => 'Đã xác nhận chuyển khoản thành công',
            'processed_by' => \App\Models\User::factory()->create()->id,
            'processed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the deposit is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'admin_note' => 'Không tìm thấy giao dịch chuyển khoản',
            'processed_by' => \App\Models\User::factory()->create()->id,
            'processed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
