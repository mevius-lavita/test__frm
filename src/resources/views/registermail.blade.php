<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/registermail.css') }}">
    <title>メール認証</title>
</head>
メール認証先がmailtrapになっている
<body>
    <header class="auth-header">
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="header-logo">
    </header>
    <main>
        <div class="email-auth-container">
            <div class="message-box">
                <p>登録していただいたメールアドレスに認証メールを送付しました。<br />メール認証を完了してください。</p>
            </div>
            <a class="auth-button-link" href="http://localhost:8025" target="_blank"
                rel="noopener noreferrer">認証はこちらから</a>
            <div class="resend-link">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit">認証メールを再送する</button>
                </form>
            </div>
        </div>
    </main>

</body>

</html>
