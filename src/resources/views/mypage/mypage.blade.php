<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
    <title>プロフィール画面</title>
</head>
<body>
    <header class="auth-header">
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="" id="CoachTech_White1">
        <div class="toppage-header-search">
            <input type="text">
        </div>
        <nav class="toppage-header-nav">
            <li><a href="" class="list_white">ログアウト</a></li>
            <li><a href=""class="list_white">マイページ</a></li>
            <li><a href="" class="list_black">出品</a></li>
        </nav>
    </header>
     <main>
        <div class="user-info">
            <div class="user-img"></div>
            <h2>ユーザー名  </h2>
            <a>プロフィールを編集</a>
        </div>
        <div class="list_item">
            <nav class="toppage-list">
                <li><a href="">出品した商品</a></li>
                <li><a href="">購入した商品</a></li>
            </nav>
        </div>
    </main>