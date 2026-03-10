<?php

namespace Tests\Feature\Item;

use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemExhibitionTest extends TestCase
{
    /**
     * 商品出品画面にて必要な情報が保存できる
     */
    public function test_商品出品画面にて必要な情報が保存できること()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストプロフィール',
            'introduction' => 'テスト紹介',
        ]);

        // カテゴリーを作成
        $category = Category::create(['name' => 'スマートフォン']);

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品出品画面を開く
        $response = $this->get('/sell');

        // ステータスコード200
        $response->assertStatus(200);

        // テスト用ファイルを作成
        Storage::fake('public');
        // テスト用の実際のファイルパスから画像を作成
        $file = UploadedFile::fake()->create('test_item.jpg', 100, 'image/jpeg');

        // 各項目に適切な情報を入力
        $response = $this->post('/sell', [
            'category_ids' => [$category->id],
            'condition_id' => '良好',
            'item_name' => 'テスト出品商品',
            'brand_name' => 'テストブランド',
            'item_detail' => 'テスト商品の説明です',
            'price' => '10000',
            'item_img' => $file,
        ]);


        // 商品がデータベースに保存されている
        $this->assertDatabaseHas('items', [
            'item_name' => 'テスト出品商品',
            'brand_name' => 'テストブランド',
            'item_detail' => 'テスト商品の説明です',
            'price' => '10000',
            'category_id' => $category->id,
            'condition' => '良好',
        ]);

    }
}
