<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <title>商品一覧</title>
</head>

<body>
    <header class="auth-header">
        <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="" id="CoachTech_White1">
        <div class="toppage-header-search">
            <input type="text">
        </div>
        <nav class="toppage-header-nav">
            @if (Auth::check())
            <li>
                <form action="/logout" method="post">
                    @csrf
                    <button class="list_white">ログアウト</button>
                </form>
            </li>
            @endif
            @if(!Auth::check())
            <li>
                
                    <a href="/login" class="list_white">ログイン</a>
                </form>
            </li>
            @endif
            <li><a href="{{ route('profile.edit') }}" class="list_white">マイページ</a></li>
            <li><a href="" class="list_black">出品</a></li>
        </nav>
        list_white
    </header>
    <main>
        <div class="list_item">
            <nav class="toppage-list">
                <li><a href="">おすすめ</a></li>
                <li><a href="/">マイリスト</a></li>
            </nav>
        </div>
    </main>