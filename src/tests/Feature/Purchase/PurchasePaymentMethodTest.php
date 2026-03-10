<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\PurchasedItem;
use Tests\TestCase;

class PurchasePaymentMethodTest extends TestCase
{
    /**
     * 支払い方法選択画面で選択した支払い方法が正しく反映される
     */
    public function test_小計画面で変更が反映される()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // プロフィールを作成（住所情報を含む）
        Profile::create([
            'user_id' => $user->id,
            'address_number' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        // 商品を作成
        $item = Item::factory()->create([
            'item_name' => 'テスト商品',
            'price' => '5000',
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 支払い方法選択画面を開く
        $response = $this->get("/purchase/{$item->id}");

        // ステータスコード200
        $response->assertStatus(200);

        // 小計エリアの支払い方法表示（初期値）が存在する
        $response->assertSee('支払い方法');
        $response->assertSee('コンビニ支払い');

        // 支払い方法の選択肢が表示されている
        $response->assertSee('カード支払い');
        $response->assertSee('コンビニ支払い');

        // 支払い方法を「コンビニ支払い」に変更して購入
        $response = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'コンビニ支払い',
            'address_number' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        // 購入完了後にホームへリダイレクト
        $response->assertRedirect('/');

        // 選択した支払い方法で購入データが保存されている
        $this->assertDatabaseHas('purchased_items', [
            'profile_id' => $user->profile->id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ支払い',
        ]);

        // 購入レコードが1件作成されている
        $this->assertEquals(
            1,
            PurchasedItem::where('profile_id', $user->profile->id)
                ->where('item_id', $item->id)
                ->count()
        );
    }
}
