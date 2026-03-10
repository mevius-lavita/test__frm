<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ItemLike;
use App\Models\PurchasedItem;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    private function createCategories(): void
    {
        $categories = [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }

    private function productionDummyItemData(): array
    {
        return [
            [
                'item_img' => 'Armani+Mens+Clock.jpg',
                'item_name' => '腕時計',
                'brand_name' => 'Rolex',
                'item_detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => '15000',
                'category_name' => 'ファッション',
                'condition' => '良好',
            ],
            [
                'item_img' => 'HDD+Hard+Disk.jpg',
                'item_name' => 'HDD',
                'brand_name' => '西芝',
                'item_detail' => '高速で信頼性の高いハードディスク',
                'price' => '5000',
                'category_name' => '家電',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'item_img' => 'iLoveIMG+d.jpg',
                'item_name' => '玉ねぎ3束',
                'brand_name' => 'なし',
                'item_detail' => '新鮮な玉ねぎ3束のセット',
                'price' => '300',
                'category_name' => 'レディース',
                'condition' => 'やや傷や汚れあり',
            ],
        ];
    }

    private function createItemFromProductionDummy(array $dummy): Item
    {
        $category = Category::where('name', $dummy['category_name'])->firstOrFail();

        return Item::create([
            'item_img' => $dummy['item_img'],
            'item_name' => $dummy['item_name'],
            'brand_name' => $dummy['brand_name'],
            'item_detail' => $dummy['item_detail'],
            'price' => $dummy['price'],
            'category_id' => $category->id,
            'condition' => $dummy['condition'],
        ]);
    }

    /**
     * 必要な情報が商品詳細ページに表示される
     */
    public function test_必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）()
    {
        $this->createCategories();
        $item = $this->createItemFromProductionDummy($this->productionDummyItemData()[0]);

        // ユーザーを作成
        $user = User::factory()->create(['name' => 'テストユーザー']);

        // プロフィールを作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'nickname' => 'テストプロフィール',
            'profile_img' => 'profiles/test-user.png',
        ]);

        // カテゴリを関連付け（複数）
        $categories = Category::whereIn('name', ['ファッション', '家電'])->get();
        $item->categories()->attach($categories->pluck('id')->toArray());

        // コメントを追加
        Comment::create([
            'item_id' => $item->id,
            'profile_id' => $profile->id,
            'comment_detail' => 'テストコメント',
        ]);

        // いいねを追加
        ItemLike::create([
            'item_id' => $item->id,
            'profile_id' => $profile->id,
        ]);

        // 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");

        // ステータスコード200
        $response->assertStatus(200);

        // 必要な情報が表示される
        $response->assertSee($item->item_name);         // 商品名
        $response->assertSee($item->brand_name);        // ブランド名
        $response->assertSee(number_format((float) $item->price)); // 価格
        $response->assertSee($item->item_detail);       // 商品説明
        $response->assertSee('ファッション');           // カテゴリ
        $response->assertSee('家電');                   // カテゴリ
        $response->assertSee($item->condition);         // 商品の状態
        $response->assertSee('テストユーザー');         // コメントしたユーザー情報（ユーザー名）
        $response->assertSee('profiles/test-user.png'); // コメントしたユーザー画像
        $response->assertSee('テストコメント');         // コメント内容
        $response->assertSee($item->item_img);          // 商品画像
        $response->assertSee('1');                      // いいね数
        $response->assertSee('1');                      // コメント数
    }

    /**
     * 複数選択されたカテゴリが表示される
     */
    public function test_複数選択されたカテゴリが表示されているか()
    {
        $this->createCategories();
        $item = $this->createItemFromProductionDummy($this->productionDummyItemData()[1]);

        // 複数のカテゴリを関連付け
        $categories = Category::whereIn('name', ['ファッション', '家電'])->get();
        $item->categories()->attach($categories->pluck('id')->toArray());

        // 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");

        // ステータスコード200
        $response->assertStatus(200);

        // 複数のカテゴリが表示される
        $response->assertSee('ファッション');
        $response->assertSee('家電');
    }
}
