<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\PurchasedItem;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    public function test_「購入する」ボタンを押下すると購入が完了する()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成（住所情報を含む）
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
            'address_number' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        // 商品を作成
        $item = Item::factory()->create([
            'item_name' => 'テスト商品',
            'price' => '5000',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品購入画面を開く
        $response = $this->get("/purchase/{$item->id}");

        // コンビニ支払いで購入
        $response = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'コンビニ支払い',
            'address_number' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        // ホームページにリダイレクト
        $response->assertRedirect('/');
        // 購入データが保存されている
        $this->assertDatabaseHas('purchased_items', [
            'profile_id' => $user->profile->id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ支払い',
        ]);
    }

    public function test_「プロフィールの購入した商品一覧」に追加されている()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成（住所情報を含む）
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
            'address_number' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        // 商品を作成
        $item = Item::factory()->create([
            'item_name' => '購入テスト商品',
            'price' => '5000',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品購入画面を開く
        $response = $this->get("/purchase/{$item->id}");

        // コンビニ支払いで購入
        $response = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'コンビニ支払い',
            'address_number' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        // ホームページにリダイレクト
        $response->assertRedirect('/');

        // 購入した商品一覧ページを開く
        $response = $this->get('/mypage?page=buy');

        // ステータスコード200
        $response->assertStatus(200);

        // 購入した商品が表示されている
        $response->assertSee('購入テスト商品');
    }
}
