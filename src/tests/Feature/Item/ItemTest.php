<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\ListedItem;
use App\Models\ItemLike;
use App\Models\PurchasedItem;
use Tests\TestCase;

class ItemTest extends TestCase
{
    public function test_全商品を取得できる()
    {
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->item_name);
        }
    }

    public function test_自分が出品した商品は表示されない()
    {
        // Seederデータを実行
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
        ]);

        // 自分が出品した商品を作成
        $myItem = Item::factory()->create([
            'item_name' => 'my-product-' . uniqid()
        ]);
        ListedItem::create([
            'profile_id' => $profile->id,
            'item_id' => $myItem->id,
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品ページを開く
        $response = $this->get('/');

        // ステータスコード200
        $response->assertStatus(200);

        // 自分の商品が表示されない
        $response->assertDontSee($myItem->item_name);

        // Seederの商品が表示される（自分の商品を除く）
        $seededItems = Item::where('id', '!=', $myItem->id)->get();
        foreach ($seededItems as $item) {
            $response->assertSee($item->item_name);
        }
    }

    public function test_いいねした商品だけが表示される()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
        ]);

        // いいねした商品を作成
        $likedItem = Item::factory()->create([
            'item_name' => 'いいねした商品'
        ]);
        ItemLike::create([
            'profile_id' => $profile->id,
            'item_id' => $likedItem->id,
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // マイリストページを開く
        $response = $this->get('/?tab=mylist');

        // ステータスコード200
        $response->assertStatus(200);

        // いいねした商品が表示される
        $response->assertSee('いいねした商品');

    }

    public function test_未認証の場合は何も表示されない()
    {
        // 商品を作成
        $item = Item::factory()->create([
            'item_name' => 'テスト商品'
        ]);

        // マイリストページを開く（未認証）
        $response = $this->get('/?tab=mylist');

        // ステータスコード200
        $response->assertStatus(200);

        // 何も表示されない
        $response->assertDontSee('テスト商品');
    }

    public function test_商品名で部分一致検索ができる()
    {
        // 検索キーワードを含む商品を作成
        $matchingItem = Item::factory()->create([
            'item_name' => 'スマートフォン Apple'
        ]);

        // 検索を実行
        $response = $this->get('/?keyword=スマートフォン');

        // ステータスコード200
        $response->assertStatus(200);

        // マッチする商品が表示される
        $response->assertSee('スマートフォン Apple');

    }

    /**
     * 検索状態がマイリストでも保持されている
     */
    public function test_検索状態がマイリストでも保持されている()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
        ]);

        // 検索キーワードを含む商品を作成
        $matchingItem = Item::factory()->create([
            'item_name' => 'スマートフォン Apple'
        ]);

        // いいねを登録
        ItemLike::create([
            'profile_id' => $profile->id,
            'item_id' => $matchingItem->id,
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // キーワード付きでマイリストページを開く
        $response = $this->get('/?keyword=スマートフォン&tab=mylist');

        // ステータスコード200
        $response->assertStatus(200);

        // 検索キーワードにマッチしてかつ、いいねしている商品が表示される
        $response->assertSee('スマートフォン Apple');

    }

    public function test_購入済み商品はsoldと表示される()
    {
        // 商品を作成
        $item = Item::factory()->create();

        // 購入者のユーザーを作成
        $purchaserUser = User::factory()->create();
        $purchaserProfile = Profile::create([
            'user_id' => $purchaserUser->id,
            'name' => '購入者プロフィール',
            'introduction' => '購入者紹介',
        ]);

        // 商品を購入済みにする
        PurchasedItem::create([
            'profile_id' => $purchaserProfile->id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ支払い',
        ]);

        // 商品一覧ページを開く
        $response = $this->get('/');

        // 購入済み商品を表示する
        $response = $this->get("/item/{$item->id}");

        // ステータスコード200
        $response->assertStatus(200);

        // 「sold」ラベルが表示される
        $response->assertSee('sold');
    }

    public function test_購入済み商品は「Sold」と表示される()
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
        $purchasedItem = Item::factory()->create([
            'item_name' => '購入済み商品'
        ]);
        
        // 商品にいいねを登録
        ItemLike::create([
            'profile_id' => $profile->id,
            'item_id' => $purchasedItem->id,
        ]);
        
        // 商品を購入済みにする
        PurchasedItem::create([
            'profile_id' => $profile->id,
            'item_id' => $purchasedItem->id,
            'payment_method' => 'コンビニ支払い',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // マイリストページを開く
        $response = $this->get('/?tab=mylist');

        // ステータスコード200
        $response->assertStatus(200);

        // 購入済み商品に「sold」ラベルが表示される
        $response->assertSee('sold');
        $response->assertSee('購入済み商品');
    }

}
