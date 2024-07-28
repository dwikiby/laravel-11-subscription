<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Notifications\PaymentSuccess;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function updateSubscriptionStatus(Subscription $subscription)
    {
        $subscription->update(['status' => 'success']);

        // Send payment success notification
        Auth::user()->notify(new PaymentSuccess($subscription));

        return redirect()->route('plans.show', $subscription->plan_id)->with('success', 'Payment successful and subscription updated!');
    }
}
