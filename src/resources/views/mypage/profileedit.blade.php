<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profileedit.css') }}">
    <title>プロフィール編集</title>
</head>

<body>
    <header class="auth-header">
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="" id="CoachTech_White1">
        <div class="toppage-header-search">
            <input type="text">
        </div>
        <nav class="toppage-header-nav">
            <li><a href="" class="list_white">ログアウト</a></li>
            <li><a href="" class="list_white">マイページ</a></li>
            <li><a href="" class="list_black">出品</a></li>
        </nav>
    </header>
    <main>
    <form class="auth-content" action="{{ route('profile.update') }}" method="POST">
    @csrf

    <h1>プロフィール設定</h1>

    <p>ユーザー名</p>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>

    <p>郵便番号</p>
    <input type="text" name="address_number" value="{{ old('address_number', $address->address_number ?? '') }}">

    <p>住所</p>
    <input type="text" name="address" value="{{ old('address', $address->address ?? '') }}" required>

    <p>建物名</p>
    <input type="text" name="building" value="{{ old('building', $address->building ?? '') }}">
<div class="register__link">
    <button type="submit">更新する</button>
</div>
</form>
</main>