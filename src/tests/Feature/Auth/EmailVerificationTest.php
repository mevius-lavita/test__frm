<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録後、認証メールが送信される
     */
    public function test_会員登録後_認証メールが送信される()
    {
        // 1. 会員登録をする
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // ユーザーがデータベースに作成されている
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        // 2. 認証メールを送信する
        // 登録したメールアドレス宛に認証メールが送信されている
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    /**
     * メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     */
    public function test_メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する()
    {
        // 1. メール認証導線画面を表示する
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->get('/registermail');

        $response->assertStatus(200);

        // 2. 「認証はこちらから」ボタンを押下
        // 3. メール認証サイトを表示する
        // メール認証サイトに遷移する
        $response->assertSee('認証はこちらから');
        $response->assertSee('href="https://mailtrap.io/inboxes"', false);
    }

    /**
     * メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する
     */
    public function test_メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する()
    {
        // 1. メール認証を完了する
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        // 2. プロフィール設定画面を表示する
        // プロフィール設定画面に遷移する
        $response->assertRedirect('/mypage/profile');
    }
}
