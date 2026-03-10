<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use Tests\TestCase;

class ItemCommentValidationTest extends TestCase
{
    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_コメントが入力されていない場合バリデーションメッセージが表示される()
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

        // コメントを送信（空欄）
        $response = $this->post("/item/{$item->id}/comments", [
            'comment_detail' => '',
        ]);

        // バリデーションエラーがある
        $response->assertSessionHasErrors('comment_detail');
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_コメントが255字以上の場合バリデーションメッセージが表示される()
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

        // 256文字のコメントを送信
        $longComment = str_repeat('あ', 256);
        $response = $this->post("/item/{$item->id}/comments", [
            'comment_detail' => $longComment,
        ]);

        // バリデーションエラーがある
        $response->assertSessionHasErrors('comment_detail');
    }
}
