<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        // Hapus objek user setelah setiap tes selesai
        $this->user->delete();
        parent::tearDown();
    }

    public function testUserHasName()
    {
        $this->assertNotEmpty($this->user->name);
    }
}
