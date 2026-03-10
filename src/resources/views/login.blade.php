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
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="header-logo">
    </header>
    <main>
        <div class="login-container">
            <h1>ログイン</h1>
            <form class="auth-content" action="/login" method="post" novalidate>
                @csrf

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password">
                    @error('password')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="submit-button">ログインする</button>
            </form>
            <div class="register-link">
                <a href="/register">会員登録はこちら</a>
            </div>
        </div>
    </main>
</body>

</html>
