<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Profile;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{

     public function register()
     {
         return view('register');
     }
    public function store(RegisterRequest $request)
    {
        // バリデーション済みデータ取得
        $validated = $request->validated();

        // パスワードをハッシュ化
        $validated['password'] = Hash::make($validated['password']);

        // ユーザー作成
        $user = User::create($validated);

        // ログイン
        Auth::login($user);

        // セッション再生成
        $request->session()->regenerate();

        // 認証メール送信
        $user->sendEmailVerificationNotification();

        // ✅ registermail にリダイレクト
        return redirect('/registermail');
    }
    public function registermail()
    {
        return view('registermail');
    }
    public function index()
    {
        // 未ログインの場合 → 通常のindex表示
        if (!Auth::check()) {
            return view('index');
        }

        // ログイン済み かつ メール未認証 → registermailへ
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect('/registermail');
        }

        // ログイン済み かつ メール認証済み → 通常のindex表示
        return view('index');
    }
    public function showlogin(){
        return view('login');
    }
    public function login(LoginRequest $request)
    {
        // 認証処理（LoginRequestのauthenticateを使う）
        $request->authenticate();

        // セッション再生成（セキュリティ対策）
        $request->session()->regenerate();



        return redirect('/');
    }
    public function mypage()
    {
        return view('mypage.mypage');
    }
    public function profileedit()
    {
        return view('mypage.profileedit');
    }
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile;
        $address = $profile?->address;

        return view('mypage.profileedit', compact('user', 'address'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:20',
            'address_number' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required',
            'building' => 'nullable',
        ]);

        $user = auth()->user();

        DB::transaction(function () use ($request, $user) {

            // ✅ users 更新
            $user->update([
                'name' => $request->name,
            ]);

            // ✅ profileを取得または作成
            $profile = Profile::firstOrCreate(['user_id' => $user->id]);

            // ✅ addressを更新または作成
            if ($profile->address_id) {
                // 既存のaddressを更新
                Address::where('id', $profile->address_id)->update([
                    'address_number' => $request->address_number,
                    'address' => $request->address,
                    'building' => $request->building,
                ]);
                $address = Address::find($profile->address_id);
            } else {
                // 新しいaddressを作成
                $address = Address::create([
                    'address_number' => $request->address_number,
                    'address' => $request->address,
                    'building' => $request->building,
                ]);
                // profileのaddress_idを更新
                $profile->update(['address_id' => $address->id]);
            }
        });

        return redirect('/')->with('success', 'プロフィール更新完了');
    }
}
