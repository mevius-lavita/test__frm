<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    /**
     * 会員登録のバリデーションエラーに対するテスト
     */

    public function test_名前が入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertEquals(
            'お名前を入力してください',
            session('errors')->get('name')[0]
        );
    }

    public function test_メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(
            'メールアドレスを入力してください',
            session('errors')->get('email')[0]
        );
    }

    public function test_パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals(
            'パスワードを入力してください',
            session('errors')->get('password')[0]
        );
    }

    public function test_パスワードが7文字以下の場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals(
            'パスワードは8文字以上で入力してください',
            session('errors')->get('password')[0]
        );
    }

    public function test_パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals(
            'パスワードと一致しません',
            session('errors')->get('password')[0]
        );
    }

    /**
     * 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
     */
    public function test_全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. 全ての必要項目を正しく入力する
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 3. 登録ボタンを押す
        // 4. メール認証誘導画面に遷移する
        $response->assertRedirect('/registermail');

        // 会員情報がデータベースに登録されている
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'newuser@example.com',
        ]);

        // メール認証誘導画面にアクセスできる
        $response = $this->get('/registermail');
        $response->assertStatus(200);
    }

}
