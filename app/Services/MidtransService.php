<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use App\Notifications\PaymentReminder;

class MidtransService
{
    public function __construct()
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = false;
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;
    }

    public function createSubscription($planId)
    {
        $plan = Plan::find($planId);

        if (!$plan) {
            throw new \Exception('Plan not found');
        }

        // Check if user already subscribed
        $existingSubscription = Subscription::where('user_id', Auth::id())
            ->where('plan_id', $plan->id)
            ->where('status', 'success')
            ->first();

        if ($existingSubscription) {
            throw new \Exception('You already purchased this plan.');
        }

        // Create new subscription
        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonths($plan->duration),
            'status' => 'pending',
        ]);

        // Prepare snap token
        $params = [
            'transaction_details' => [
                'order_id' => rand(),
                'gross_amount' => $plan->price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ]
        ];

        $snapToken = Snap::getSnapToken($params);
        $subscription->snap_token = $snapToken;
        $subscription->save();

        Auth::user()->notify(new PaymentReminder($subscription));
        return $subscription;
    }
}
