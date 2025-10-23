<?php

namespace Database\Seeders;

use App\Payment\Models\PaymentMethod;
use App\Payment\Models\PaymentMethodAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. Cash on Delivery (COD) ---
        $cod = PaymentMethod::create([
            'name' => 'Cash on Delivery',
            'type' => 'manual',
            'code' => 'cod',
            'enabled' => true,
            'priority' => 'high',
            'description' => 'Pay with cash upon delivery of your order.',
        ]);

        PaymentMethodAttribute::create([
            'payment_method_id' => $cod->id,
            'key' => 'instruction',
            'value' => 'Please prepare the exact amount of cash for the delivery personnel.',
        ]);

        // --- 2. Direct Bank Transfer ---
        $bankTransfer = PaymentMethod::create([
            'name' => 'Direct Bank Transfer',
            'type' => 'manual',
            'code' => 'direct_bank_transfer',
            'enabled' => true,
            'priority' => 'high',
            'description' => 'Transfer the payment directly to our bank accounts.',
        ]);

        // Dummy bank accounts
        $bankAccounts = [
            [
                'account_id' => '00123456789',
                'account_name' => 'Simfulex Software Co., Ltd.',
                'bank_name' => 'Global Bank',
                'branch_name' => 'Yangon Main Branch',
            ],
            [
                'account_id' => '00987654321',
                'account_name' => 'Simfulex Development Unit',
                'bank_name' => 'Unity Bank',
                'branch_name' => 'Mandalay Branch',
            ],
        ];

        foreach ($bankAccounts as $index => $account) {
            foreach ($account as $key => $value) {
                PaymentMethodAttribute::create([
                    'payment_method_id' => $bankTransfer->id,
                    'key' => "bank_account_{$index}_{$key}",
                    'value' => $value,
                ]);
            }
        }

        $this->command->info('Product Data Seeded Successfully.');
    }
}
