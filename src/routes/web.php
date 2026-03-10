<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 通常ルート
|--------------------------------------------------------------------------
*/

// トップページ・検索
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/search', [ItemController::class, 'search'])->name('search');

// 会員登録
Route::get('/register', [RegisterController::class, 'show']);
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/registermail', [RegisterController::class, 'registermail'])->middleware('auth');

// ログイン
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| メール認証（Laravel標準）
|--------------------------------------------------------------------------
*/

// 認証案内画面（Laravel標準）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール内の認証リンク
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| 認証後のみアクセス可能
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // マイページ
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 商品出品
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell', [ItemController::class, 'store'])->name('listing.store');

    // コメント・いいね
    Route::post('/item/{item}/comments', [ItemController::class, 'storeComment'])->name('item.comments.store');
    Route::post('/item/{item}/likes', [ItemController::class, 'storeLike'])->middleware('auth')->name('item.likes.store');

    // 購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->whereNumber('item')->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'process'])->whereNumber('item')->name('purchase.confirm');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->whereNumber('item')->name('address.edit');
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->whereNumber('item')->name('address.update');
});

// 商品詳細（認証不要）
Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');

// 購入完了・キャンセル
Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

// Stripe Webhook
Route::post('/stripe/webhook', [PurchaseController::class, 'handleWebhook']);
