<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_root_displays_public_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('projectmanager-landing');
    }

    public function test_guests_are_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
