<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\ItemLike;
use Tests\TestCase;

class ItemLikeTest extends TestCase
{
    /**
     * いいねアイコンを押下することによって、いいねした商品として登録され、いいね合計値が増加表示される
     */
    public function test_いいねアイコンを押下することによって、いいねした商品として登録することができる()
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
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品詳細ページにアクセス
        $response = $this->get("/item/{$item->id}");

        // いいねアイコンを押下
        $response = $this->post("/item/{$item->id}/likes");

        // いいねが登録されている
        $this->assertTrue(
            ItemLike::where('item_id', $item->id)
                ->where('profile_id', $profile->id)
                ->exists()
        );

        // 商品詳細ページを再度開く
        $response = $this->get("/item/{$item->id}");

        // いいね数が1表示されている
        $response->assertSee('1');
    }

    /**
     * いいねが解除され、いいね合計値が減少表示される
     */
    public function test_再度いいねアイコンを押下することによって、いいねを解除することができる。()
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
        ]);

        // 既にいいねを登録
        ItemLike::create([
            'item_id' => $item->id,
            'profile_id' => $profile->id,
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 最初のいいね数確認（1）
        $response = $this->get("/item/{$item->id}");

        $response->assertSee('1');

        // いいねアイコンを再度押下（削除）
        $response = $this->post("/item/{$item->id}/likes");

        // 商品詳細ページを再度開く
        $response = $this->get("/item/{$item->id}");

        // いいね数が0表示されている
        $response->assertDontSee('span class="like-count">1</span>');
    }

    /**
     * いいねした商品のアイコンの色が変化する
     */
    public function test_追加済みのアイコンは色が変化する()
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
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 最初の商品詳細ページを開く（いいねなし）
        $response = $this->get("/item/{$item->id}");

        // ステータスコード200
        $response->assertStatus(200);

        // デフォルトハート画像が表示されている
        $response->assertSee('ハートロゴ_デフォルト');

        // いいねアイコンを押下
        $response = $this->post("/item/{$item->id}/likes");

        // 商品詳細ページを再度開く
        $response = $this->get("/item/{$item->id}");

        // ステータスコード200
        $response->assertStatus(200);

        // ピンク色ハート画像が表示されている
        $response->assertSee('ハートロゴ_ピンク');
    }
}
