<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\VoucherUsage;
use Tests\TestCase;

class VoucherControllerTest extends TestCase
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
                        ->get('/api/v1/voucher');

        $response->assertStatus(200);
    }

    public function test_create(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $response = $this->actingAs($user, 'api')
                        ->post('/api/v1/voucher',[
                            'name' => $this->faker->words(6, true),
                            'code' => $this->faker->words(10, true),
                            'active_at' => date('Y-m-d H:i:s'),
                            'disc_amount' => 5000,
                            'status' => 'ACTIVE',
                            'expired_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
                        ]);

        $response->assertStatus(201);
    }

    public function test_view(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $product = Voucher::factory()->create([
            'name' => $this->faker->words(6, true),
            'code' => $this->faker->words(10, true),
            'active_at' => date('Y-m-d H:i:s'),
            'disc_amount' => 5000,
            'status' => 'ACTIVE',
            'expired_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ]);
        $response = $this->actingAs($user, 'api')
                        ->get('/api/v1/voucher/'.$product->id);

        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $product = Voucher::factory()->create([
            'name' => $this->faker->words(6, true),
            'code' => $this->faker->words(10, true),
            'active_at' => date('Y-m-d H:i:s'),
            'disc_amount' => 5000,
            'status' => 'ACTIVE',
            'expired_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ]);
        $response = $this->actingAs($user, 'api')
                        ->put('/api/v1/voucher/'.$product->id,
                        [
                            'name' => $this->faker->words(6, true),
                            'code' => $this->faker->words(10, true),
                            'active_at' => date('Y-m-d H:i:s'),
                            'disc_amount' => 5000,
                            'status' => 'ACTIVE',
                            'expired_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
                        ]);

        $response->assertStatus(201);
    }

    public function test_delete(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $product = Voucher::factory()->create([
            'name' => $this->faker->words(6, true),
            'code' => $this->faker->words(10, true),
            'expired_at' => date('Y-m-d h:i:s'),
            'disc_amount' => 5000,
            'status' => 'ACTIVE',
            'active_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ]);
        $response = $this->actingAs($user, 'api')
                        ->delete('/api/v1/voucher/'.$product->id);

        $response->assertStatus(204);
    }

    public function test_apply(): void
    {
        $user = User::factory()->create([
            'password' => '123'
        ]);
        $voucher = Voucher::factory()->create([
            'name' => $this->faker->words(6, true),
            'code' => $this->faker->words(10, true),
            'expired_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'disc_amount' => 5000,
            'status' => 'ACTIVE',
            'active_at' => date('Y-m-d H:i:s')
        ]);
        $product = Product::factory()->create([
            'name' => $this->faker->words(6, true),
            'description' => $this->faker->words(10, true),
            'sku' => $this->faker->words(3, true),
            'price' => 5000
        ]);
        $response = $this->actingAs($user, 'api')
                        ->post('/api/v1/voucher-apply',[
                            'product_id' => $product->id,
                            'voucher_code' => $voucher->code,
                        ]);

        $response->assertStatus(200);
    }
}
