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

    <main class="mypage-main">
        <section class="user-info">
            @if (!empty($profile?->profile_img))
            <img class="user-img" src="{{ Storage::url($profile->profile_img) }}" alt="プロフィール画像">
            @else
            <div class="user-img-placeholder"></div>
            @endif
            <h2>{{ $user->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="edit-profile-btn">プロフィールを編集</a>
        </section>

        <section class="list-section">
            <nav class="toppage-list">
                <li>
                    <a href="{{ route('mypage', ['page' => 'sell']) }}"
                        class="{{ request('page') === 'sell' || !request('page') ? 'active-tab' : '' }}">
                        出品した商品
                    </a>
                </li>
                <li>
                    <a href="{{ route('mypage', ['page' => 'buy']) }}"
                        class="{{ request('page') === 'buy' ? 'active-tab' : '' }}">
                        購入した商品
                    </a>
                </li>
            </nav>
        </section>

        <section class="products-grid">
            @forelse ($myItems as $myItem)
            <article class="product-card">
                <div class="image-wrap">
                    <img src="{{ Storage::url($myItem->item_img) }}" alt="商品画像" class="product-image">
                    @if (in_array($myItem->id, $soldItemIds))
                    <div class="sold-label">SOLD</div>
                    @endif
                </div>
                <h3 class="product-name">{{ $myItem->item_name }}</h3>
            </article>
            @empty
            <p class="empty-text">商品がありません</p>
            @endforelse
        </section>
    </main>
</body>

</html>
