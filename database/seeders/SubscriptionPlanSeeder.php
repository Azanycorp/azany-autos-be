<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'features' => [
                    'Add up to 10 vehicles',
                    'Add up to 5 locations',
                    'Add up to 5 tags',
                    'Add up to 5 slots',
                ],
            ],
            [
                'name' => 'Starter Plan',
                'slug' => 'starter',
                'price' => 20,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'features' => [
                    'Add up to 20 vehicles',
                    'Add up to 10 locations',
                    'Add up to 10 tags',
                    'Add up to 10 slots',
                ],
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'price' => 30,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'features' => [
                    'Add up to 30 vehicles',
                    'Add up to 15 locations',
                    'Add up to 15 tags',
                    'Add up to 15 slots',
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'price' => 40,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'features' => [
                    'Add up to 50 vehicles',
                    'Add up to 20 locations',
                    'Add up to 20 tags',
                    'Add up to 20 slots',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $subscriptionPlan = SubscriptionPlan::firstOrCreate(
                ['slug' => $plan['slug']],
                collect($plan)->except('features')->toArray()
            );

            foreach ($plan['features'] as $feature) {
                $subscriptionPlan->features()->firstOrCreate(
                    ['feature' => $feature],
                    ['has_feature' => true]
                );
            }
        }
    }
}
