<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>ログイン</title>
</head>

<body>
    <header class="auth-header">
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="" id="CoachTech_White1">
    </header>
    <main>
        <form class="auth-content" action="/login" method="post">
            @csrf
            <h1>ログイン</h1>
            @error('email')
            <p style="color: red; font-size:10px">
                {{$errors->first('email')}}
            </p>
            @enderror
            <p>メールアドレス</p>
            <input type="email" name="email" value="{{ old('email') }}" />
            @error('password')
            <p style="color: red; font-size:10px">
                {{$errors->first('password')}}
            </p>
            @enderror
            <p>パスワード</p>
            <input type="password" name="password" />
            <div class="login__link">
                <button type="submit">ログインする</button>
            </div>
        </form>
        <div class="register_buttun">
            <a href="/register">会員登録はこちらから</a>
        </div>
    </main>
</body>

</html>