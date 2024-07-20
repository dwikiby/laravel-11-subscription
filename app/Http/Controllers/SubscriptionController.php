<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function updateSubscriptionStatus(Subscription $subscription)
    {
        $subscription->update(['status' => 'success']);

        return redirect()->route('plans.show', $subscription->plan_id)->with('success', 'Payment successful and subscription updated!');
    }
}
