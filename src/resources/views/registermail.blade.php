<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/registermail.css') }}">
    <title>会員登録</title>
</head>
<body>
    <header class="auth-header">
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="" id="CoachTech_White1">
    </header>
    <main>
        <div class="text_massage">
            <p>登録していただいたメールアドレスに認証メールを送付しました。<br/> メール認証を完了してください。</p>
        </div>
        <div class="sumbit__button">
        <button onclick="location.href='{{ route('verification.notice') }}'">
    認証はこちらから
</button>
        </div>
        <div class="restart_link">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">認証メールを再送する</button>
    </form>
</div>
        
    </main>