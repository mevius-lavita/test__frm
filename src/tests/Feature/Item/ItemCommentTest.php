<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\Comment;
use Tests\TestCase;

class ItemCommentTest extends TestCase
{
    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        // 商品を作成
        $item = Item::factory()->create([
            'item_name' => 'テスト商品',
        ]);

        // コメント数初期値を確認
        $initialCommentCount = Comment::where('item_id', $item->id)->count();

        // ログインしていない状態でコメント送信を試みる
        $response = $this->post("/item/{$item->id}/comments", [
            'comment_detail' => 'テストコメント_' . uniqid(),
        ]);

        // ログインページにリダイレクトされる
        $response->assertRedirect('/login');

        // コメント数が増加していない
        $afterCommentCount = Comment::where('item_id', $item->id)->count();
        $this->assertEquals($initialCommentCount, $afterCommentCount);
    }

    public function test_ログイン済みのユーザーはコメントを送信できる()
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

        // コメントを送信
        $response = $this->post("/item/{$item->id}/comments", [
            'comment_detail' => 'テストコメント',
        ]);

        // リダイレクト先のページを取得
        $detailResponse = $this->get("/item/{$item->id}");
        // コメントが表示されている
        $detailResponse->assertSee('テストコメント');

        // コメント数が1表示されている
        $detailResponse->assertSee('コメント');
    }

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

        // リダイレクトして商品詳細ページを取得
        $detailResponse = $this->get("/item/{$item->id}");
        // バリデーションメッセージが表示されている
        $detailResponse->assertSee('コメントは必須です。');
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

        // リダイレクトして商品詳細ページを取得
        $detailResponse = $this->get("/item/{$item->id}");
        // バリデーションメッセージが表示されている
        $detailResponse->assertSee('コメントは255文字以内でなければなりません。');

    }
}
