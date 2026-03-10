<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_購入画面が表示される()
    {
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");
        $response->assertStatus(200);
    }

    public function test_住所が未登録の場合購入できない()
    {
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'コンビニ支払い',
        ]);

        $response->assertRedirect('/');
    }
}
