<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'              => 'تجريبي',
                'slug'              => 'trial',
                'type'              => 'trial',
                'price'             => 0,
                'billing_cycle'     => 'monthly',
                'max_campaigns'     => 2,
                'max_ad_accounts'   => 1,
                'max_monthly_spend' => 100,
                'features'          => ['basic_analytics', 'email_support'],
            ],
            [
                'name'              => 'أساسي',
                'slug'              => 'basic',
                'type'              => 'basic',
                'price'             => 29,
                'billing_cycle'     => 'monthly',
                'max_campaigns'     => 10,
                'max_ad_accounts'   => 2,
                'max_monthly_spend' => 500,
                'features'          => ['basic_analytics', 'email_support', 'reports'],
            ],
            [
                'name'              => 'احترافي',
                'slug'              => 'pro',
                'type'              => 'pro',
                'price'             => 79,
                'billing_cycle'     => 'monthly',
                'max_campaigns'     => 50,
                'max_ad_accounts'   => 5,
                'max_monthly_spend' => 2000,
                'features'          => ['advanced_analytics', 'priority_support', 'reports', 'webhooks'],
            ],
            [
                'name'              => 'وكالة',
                'slug'              => 'agency',
                'type'              => 'agency',
                'price'             => 199,
                'billing_cycle'     => 'monthly',
                'max_campaigns'     => 999,
                'max_ad_accounts'   => 50,
                'max_monthly_spend' => 99999,
                'features'          => ['advanced_analytics', 'dedicated_support', 'reports', 'webhooks', 'white_label', 'api_access'],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
