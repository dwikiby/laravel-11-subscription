<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $plans = Plan::all();
        $userId = Auth::id();

        // Get a list of plan IDs that the user has successfully subscribed to
        // $purchasedPlans = Subscription::where('user_id', $userId)
        //     ->where('status', 'success')
        //     ->pluck('plan_id');

        $purchasedPlans = Subscription::ofUserWithStatus($userId, 'success')
            ->pluck('plan_id');

        return view('pricing', [
            'plans' => $plans,
            'purchasedPlans' => $purchasedPlans
        ]);
    }

    public function show(Plan $plan, Request $request)
    {
        // $existingSubscription = Subscription::where('user_id', Auth::id())
        //     ->where('plan_id', $plan->id)
        //     ->where('status', 'success')
        //     ->first();

        $existingSubscription = Subscription::ofUserWithStatus(Auth::id(), 'success')
            ->where('plan_id', $plan->id)
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
        try {
            $subscription = $this->midtransService->createSubscription($request->plan_id);

            return redirect()->route('plans.show', [
                'plan' => $subscription->plan_id,
                'snap_token' => $subscription->snap_token,
                'subscription_id' => $subscription->id
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
