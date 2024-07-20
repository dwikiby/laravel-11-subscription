<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        $userId = Auth::id();

        // Get a list of plan IDs that the user has successfully subscribed to
        $purchasedPlans = Subscription::where('user_id', $userId)
            ->where('status', 'success')
            ->pluck('plan_id');

        return view('pricing', [
            'plans' => $plans,
            'purchasedPlans' => $purchasedPlans
        ]);
    }

    public function show(Plan $plan, Request $request)
    {
        $existingSubscription = Subscription::where('user_id', Auth::id())
            ->where('plan_id', $plan->id)
            ->where('status', 'success')
            ->first();

        return view('checkout.index', [
            'plan' => $plan,
            'existingSubscription' => $existingSubscription,
            'snap_token' => $request->query('snap_token'),
            'subscription_id' => $request->query('subscription_id')
        ]);
    }

    public function checkout(Request $request)
    {
        $plan = Plan::find($request->plan);

        if (!$plan) {
            return redirect()->back()->with('error', 'Plan not found');
        }

        // check if user already subscribed
        $existingSubscription = Subscription::where('user_id', Auth::id())
                ->where('plan_id', $plan->id)
                ->where('status', 'success')
                ->first();
        
        if ($existingSubscription) {
            return redirect()->route('plans.show', ['plan' => $plan->id])->with('info', 'You already purchased this plan.');
        }


        // Create new subscription
        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonths($plan->duration),
            'status' => 'pending',
        ]);
        // Midtrans Checkout
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        // snap 
        $params = array(
            'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' => $plan->price,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            )
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $subscription->snap_token = $snapToken;
        $subscription->save();

        return redirect()->route('plans.show', [
            'plan' => $plan->id,
            'snap_token' => $snapToken,
            'subscription_id' => $subscription->id
        ]);    
    }
}
