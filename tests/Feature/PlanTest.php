<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
class PlanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class); // Seed the plans using PlanSeeder
    }


    public function test_user_can_view_pricing_plan()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/pricing');

        $response->assertStatus(200);
        $response->assertViewHas('plans');
    }

    public function test_user_can_view_plan_details()
    {
        $user = User::factory()->create();
        $plan = \App\Models\Plan::first();

        $response = $this->actingAs($user)->get('/plans/' . $plan->id);

        $response->assertStatus(200);
        $response->assertViewHas('plan');
    }

    public function test_user_can_view_checkout_button()
    {
        $user = User::factory()->create();
        $plan = \App\Models\Plan::first();

        $response = $this->actingAs($user)->get('/plans/' . $plan->id);

        $response->assertSee('Checkout');
    }
}
