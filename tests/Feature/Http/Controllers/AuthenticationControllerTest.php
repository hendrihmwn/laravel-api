<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationControllerTest extends TestCase
{
    use WithFaker;

    public function test_register(): void
    {
        $response = $this->post('/api/v1/register',[   
            'email' => $this->faker->unique()->email,
            'password' => '123',
            'name' => $this->faker->words(6, true),
            'confirm_password' => '123'  
        ]);

        $response->assertStatus(200);
    }

    public function test_login(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $response = $this->post('/api/v1/login',[   
            'email' => $user->email,
            'password' => '123', 
        ]);

        $response->assertStatus(200);
    }
}
