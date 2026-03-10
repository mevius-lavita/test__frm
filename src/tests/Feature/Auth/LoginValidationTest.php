<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    public function test_メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(
            'メールアドレスを入力してください',
            session('errors')->get('email')[0]
        );
    }
    public function test_パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals(
            'パスワードを入力してください',
            session('errors')->get('password')[0]
        );
    }

    public function test_入力情報が間違っている場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals(
            'ログイン情報が登録されていません',
            session('errors')->get('password')[0]
        );
    }

    public function test_正しい情報が入力された場合、ログイン処理が実行される()
    {
        $response = $this->get('/login');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // ホームページにリダイレクトされる
        $response->assertRedirect('/');

        // ユーザーがログイン状態である
        $this->assertAuthenticated();
    }

    public function test_ログアウトができる()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // ユーザーにログイン
        $this->actingAs($user);
        $this->assertAuthenticated();

        // ログアウト
        $response = $this->post('/logout');

        // ホームページにリダイレクトされる
        $response->assertRedirect('/');

        // ユーザーがログアウト状態である
        $this->assertGuest();
    }
}
