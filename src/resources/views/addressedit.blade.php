<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/addressedit.css') }}">
    <title>送付先住所変更画面</title>
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
    <main class="address-edit-main">
        <h2 class="address-edit-title">住所の変更</h2>
        <form action="{{ route('address.update', $item->id) }}" method="post" class="address-edit-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="address_number">郵便番号</label>
                <input type="text" id="address_number" name="address_number">
                @error('address_number')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="address">住所</label>
                <input type="text" id="address" name="address">
                @error('address')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="building">建物名</label>
                <input type="text" id="building" name="building">
                @error('building')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="update-button">更新する</button>
        </form>
    </main>
</body>

</html>
