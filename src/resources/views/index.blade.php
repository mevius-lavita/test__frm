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
        <form action="/search" method="get" class="toppage-header-search">
            @csrf
            <input type="text" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
            <input type="hidden" name="type" value="{{ request('type') }}">
        </form>
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
            </li>
            @endif
            <li><a href="/mypage" class="list_white">マイページ</a></li>
            <li><a href="/sell" class="list_black">出品</a></li>
        </nav>
    </header>
    <main>
        <div class="list_item">
            <nav class="toppage-list">
                <li>
                    <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
                        class="{{ request('tab') !== 'mylist' ? 'active-tab' : '' }}">
                        おすすめ
                    </a>
                </li>

                <li>
                    <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
                        class="{{ request('tab') === 'mylist' ? 'active-tab' : '' }}">
                        マイリスト
                    </a>
                </li>
            </nav>
        </div>
        <div class="products-row">
            @foreach ($items as $item)
            <div class="product-card">
                <div class="product-image-wrapper">
                    <a href="{{ route('item.show', $item->id) }}">
                        <img src="{{ Storage::url($item->item_img) }}" alt="商品画像" class="product-image">
                    </a>
                    @if (in_array($item->id, $soldItemIds ?? []))
                    <div class="sold-label">sold</div>
                    @endif
                </div>
                <h3 class="product-title">{{ $item->item_name }}</h3>
            </div>
            @endforeach
        </div>
    </main>
