<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use App\Models\Profile;
use Tests\TestCase;

class ProfileEditInitialValueTest extends TestCase
{
    /**
     * 変更項目が初期値として過去設定されていること
     */
    public function test_変更項目が初期値として過去設定されていること()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        // プロフィールを作成（住所情報を含む）
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'プロフィール名前',
            'introduction' => 'プロフィール紹介文',
            'address_number' => '123-4567',
            'address' => 'テスト都道府県テスト市テスト区',
            'building' => 'テストビル123号',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // プロフィール編集ページを開く
        $response = $this->get('/mypage/profile');

        // ステータスコード200
        $response->assertStatus(200);

        // ユーザー名が初期値として表示されている
        $response->assertSee('テストユーザー');

        // 郵便番号が初期値として表示されている
        $response->assertSee('123-4567');

        // 住所が初期値として表示されている
        $response->assertSee('テスト都道府県テスト市テスト区');

        // 建物名が初期値として表示されている
        $response->assertSee('テストビル123号');
    }
}
