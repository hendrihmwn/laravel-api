<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Product;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test get.
     */
    public function test_get(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $response = $this->actingAs($user, 'api')
                        ->get('/api/v1/product');

        $response->assertStatus(200);
    }

    public function test_create(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $response = $this->actingAs($user, 'api')
                        ->post('/api/v1/product',[
                            'name' => $this->faker->words(6, true),
                            'description' => $this->faker->words(10, true),
                            'sku' => $this->faker->words(3, true),
                            'price' => 5000
                        ]);

        $response->assertStatus(201);
    }

    public function test_view(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $product = Product::factory()->create([
            'name' => $this->faker->words(6, true),
            'description' => $this->faker->words(10, true),
            'sku' => $this->faker->words(3, true),
            'price' => 5000
        ]);
        $response = $this->actingAs($user, 'api')
                        ->get('/api/v1/product/'.$product->id);

        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $product = Product::factory()->create([
            'name' => $this->faker->words(6, true),
            'description' => $this->faker->words(10, true),
            'sku' => $this->faker->words(3, true),
            'price' => 5000
        ]);
        $response = $this->actingAs($user, 'api')
                        ->put('/api/v1/product/'.$product->id,
                        [
                            'name' => $this->faker->words(6, true),
                            'description' => $this->faker->words(10, true),
                            'sku' => $this->faker->words(3, true),
                            'price' => 5000
                        ]);

        $response->assertStatus(201);
    }

    public function test_delete(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $product = Product::factory()->create([
            'name' => $this->faker->words(6, true),
            'description' => $this->faker->words(10, true),
            'sku' => $this->faker->words(3, true),
            'price' => 5000
        ]);
        $response = $this->actingAs($user, 'api')
                        ->delete('/api/v1/product/'.$product->id);

        $response->assertStatus(204);
    }
}
