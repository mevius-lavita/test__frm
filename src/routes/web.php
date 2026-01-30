<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 通常ルート
|--------------------------------------------------------------------------
*/

Route::get('/', [TestController::class, 'index']);

Route::get('/register', [TestController::class, 'register']);
Route::post('/register', [TestController::class, 'store']);

Route::get('/login', [TestController::class, 'showlogin'])->name('login');
Route::post('/login', [TestController::class, 'login']);

Route::get('/registermail', [TestController::class, 'registermail'])
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| 認証後のみアクセス可能
|--------------------------------------------------------------------------
*/


Route::get('/mypage', [TestController::class, 'mypage'])
    ->middleware(['auth', 'verified']);


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
    $request->fulfill(); // メール認証完了

    return redirect('/mypage/profile'); // ←ここを変更
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage/profile', [TestController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [TestController::class, 'update'])->name('profile.update');
});