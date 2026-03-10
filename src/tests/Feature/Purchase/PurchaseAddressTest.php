<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\PurchasedItem;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurchaseAddressTest extends TestCase
{
    /**
     * 登録した住所が商品購入画面に反映されている
     */
    public function test_送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
        ]);

        // 商品を作成
        $item = Item::factory()->create([
            'item_name' => 'テスト商品',
            'price' => '5000',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 送付先住所変更画面を開く
        $response = $this->get("/purchase/address/{$item->id}");

        // ステータスコード200
        $response->assertStatus(200);

        // 住所を登録
        $response = $this->put("/purchase/address/{$item->id}", [
            'address_number' => '123-4567',
            'address' => 'テスト都道府県テスト市',
            'building' => 'テストビル101号室',
        ]);

        // 商品購入画面を再度開く
        $response = $this->get("/purchase/{$item->id}");

        // ユーザーのプロフィールを再度取得
        $user = auth()->user();

        // ステータスコード200
        $response->assertStatus(200);

        // 登録した住所がプロフィールに存在
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'address_number' => '123-4567',
            'address' => 'テスト都道府県テスト市',
            'building' => 'テストビル101号室',
        ]);
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_購入した商品に送付先住所が紐づいて登録される()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
        ]);

        // 商品を作成
        $item = Item::factory()->create([
            'item_name' => 'テスト商品',
            'price' => '5000',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 送付先住所を登録
        $response = $this->put("/purchase/address/{$item->id}", [
            'address_number' => '987-6543',
            'address' => '登録テスト市区町村',
            'building' => '登録テストビル',
        ]);

        // 商品購入画面にリダイレクト
        $response->assertRedirect("/purchase/{$item->id}");

        // 商品を購入
        $response = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'コンビニ支払い',
            'address_number' => '987-6543',
            'address' => '登録テスト市区町村',
            'building' => '登録テストビル',
        ]);

        // ホームページにリダイレクト
        $response->assertRedirect('/');

        // 購入した商品が登録されている
        $this->assertDatabaseHas('purchased_items', [
            'profile_id' => $profile->id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ支払い',
        ]);

        // 購入に紐づくプロフィールに、設定した住所が反映されている
        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'user_id' => $user->id,
            'address_number' => '987-6543',
            'address' => '登録テスト市区町村',
            'building' => '登録テストビル',
        ]);
    }
}
