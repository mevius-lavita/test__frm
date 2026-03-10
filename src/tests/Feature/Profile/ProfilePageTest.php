<?php

namespace Tests\Feature\Profile;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\ListedItem;
use App\Models\PurchasedItem;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    /**
     * プロフィールページで必要な情報が取得できる
     */
    public function test_プロフィールページで必要な情報が取得できる()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        // プロフィールを作成（住所情報とプロフィール画像を含む）
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'プロフィール名',
            'introduction' => 'テスト紹介',
            'address_number' => '123-4567',
            'address' => 'テスト住所',
            'profile_img' => 'profile_images/test_profile.jpg',
        ]);

        // 出品した商品を作成
        $listedItem = Item::factory()->create([
            'item_name' => '出品した商品',
        ]);
        ListedItem::create([
            'profile_id' => $profile->id,
            'item_id' => $listedItem->id,
        ]);

        // 購入した商品を作成
        $purchasedItem = Item::factory()->create([
            'item_name' => '購入した商品',
        ]);
        PurchasedItem::create([
            'profile_id' => $profile->id,
            'item_id' => $purchasedItem->id,
            'payment_method' => 'コンビニ支払い',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // プロフィールページを開く（出品した商品一覧）
        $response = $this->get('/mypage');

        // ステータスコード200
        $response->assertStatus(200);

        // ユーザー名が表示されている
        $response->assertSee('テストユーザー');

        // プロフィール画像が表示されている
        $response->assertSee('profile_images/test_profile.jpg');

        // 出品した商品が表示されている
        $response->assertSee('出品した商品');

        // 購入した商品一覧ページを開く
        $response = $this->get('/mypage?page=buy');

        // ステータスコード200
        $response->assertStatus(200);

        // 購入した商品が表示されている
        $response->assertSee('購入した商品');
    }
}
