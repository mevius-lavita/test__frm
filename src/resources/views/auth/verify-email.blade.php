<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>メール認証</title>
</head>

<body>
    @php
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    $verificationUrl = URL::temporarySignedRoute(
    'verification.verify',
    now()->addMinutes(60),
    [
    'id' => $user->id,
    'hash' => sha1($user->email),
    ]
    );
    @endphp
    <p>認証リンク</p>
<a href="{{ $verificationUrl }}">
    メール認証する
</a>
    @if (session('message'))
    <p>{{ session('message') }}</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">認証メールを再送する</button>
    </form>
</body>

</html>