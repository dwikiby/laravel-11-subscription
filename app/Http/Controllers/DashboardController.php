<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
                                    ->where('status', 'success')
                                    ->first();
        $status = $subscription ? 'Paid User' : 'Free User';
        $planName = $subscription ? Plan::find($subscription->plan_id)->name : 'None';

        return view('dashboard', ['status' => $status, 'planName' => $planName]);
    }
}
