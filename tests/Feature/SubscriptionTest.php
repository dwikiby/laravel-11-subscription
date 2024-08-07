<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\MidtransService;
use App\Notifications\PaymentSuccess;
use App\Notifications\PaymentReminder;
use Database\Seeders\PlanSeeder;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->seed(PlanSeeder::class); // Seed the plans using PlanSeeder
    }


    public function test_user_can_checkout_plan()
    {
        $user = User::first();
        $plan = Plan::first();

        $subscription = Subscription::factory()->create([
            'plan_id' => $plan->id,
            'snap_token' => 'test_snap_token'
        ]);

        $midtransService = $this->mock(MidtransService::class);
        $midtransService->shouldReceive('createSubscription')->andReturn($subscription);

        $response = $this->actingAs($user)->post('/plans/' . $plan->id . '/checkout', [
            'plan_id' => $plan->id,
        ]);

        $response->assertRedirect('/plans/' . $plan->id . '?snap_token=test_snap_token&subscription_id=' . $subscription->id);
    }

    public function test_payment_reminder_is_sent()
    {
        Notification::fake();

        $user = User::first();
        $plan = Plan::first();

        $this->actingAs($user);

        $midtransService = new MidtransService();
        $subscription = $midtransService->createSubscription($plan->id);

        // Assert the PaymentReminder notification was sent
        Notification::assertSentTo(
            [$user], PaymentReminder::class
        );
    }

    public function test_subscription_status_is_success()
    {
        $user = User::first();
        $plan = Plan::first();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonths($plan->duration),
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get('/subscription/' . $subscription->id . '/success');

        $response->assertRedirect('/plans/' . $plan->id);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'success'
        ]);
    }

    public function test_payment_success_notification_is_sent()
    {
        Notification::fake();

        $user = User::first();
        $plan = Plan::first();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonths($plan->duration),
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get('/subscription/' . $subscription->id . '/success');

        $response->assertRedirect('/plans/' . $plan->id);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'success'
        ]);

        Notification::assertSentTo(
            [$user], PaymentSuccess::class
        );
    }
}
