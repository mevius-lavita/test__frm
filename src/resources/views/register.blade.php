<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <title>会員登録</title>
</head>

<body>
    <header class="auth-header">
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="" id="CoachTech_White1">
    </header>
    <main>
       <form class="auth-content" action="/register" method="post">
    @csrf
    <h1>会員登録</h1>

    <p>ユーザー名</p>
    <input type="text" name="name" value="{{ old('name') }}">
    @error('name')
        <p style="color:red; font-size:12px;">{{ $message }}</p>
    @enderror

    <p>メールアドレス</p>
    <input type="email" name="email" value="{{ old('email') }}">
    @error('email')
        <p style="color:red; font-size:12px;">{{ $message }}</p>
    @enderror

    <p>パスワード</p>
    <input type="password" name="password">
    @error('password')
        <p style="color:red; font-size:12px;">{{ $message }}</p>
    @enderror

    <p>確認用パスワード</p>
    <input type="password" name="password_confirmation">

    <div class="register__link">
        <button type="submit">登録する</button>
    </div>
</form>
        <div class="login_buttun">
            <a href="/login">ログインはこちらから</a>
        </div>
    </main>
</body>

</html>