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
    <main class="profile-edit-page">
        <form class="auth-content" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h1>プロフィール設定</h1>

            <div class="profile__icon">
                <img id="profile__icon-input" class="profile__icon-input"
                    src="{{ $profile?->profile_img ? \Illuminate\Support\Facades\Storage::url($profile->profile_img) : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' }}"
                    alt="プロフィール画像">
                <input type="file" id="profile-img-input" name="profile_img" accept="image/*" style="display: none;">
                <button type="button" onclick="document.getElementById('profile-img-input').click();">画像を選択する</button>
            </div>

            <script>
                document.getElementById('profile-img-input').addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            document.getElementById('profile__icon-input').src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });

            </script>

            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="address_number">郵便番号</label>
                <input id="address_number" type="text" name="address_number"
                    value="{{ old('address_number', $profile->address_number ?? '') }}">
            </div>

            <div class="form-group">
                <label for="address">住所</label>
                <input id="address" type="text" name="address" value="{{ old('address', $profile->address ?? '') }}"
                    required>
            </div>

            <div class="form-group">
                <label for="building">建物名</label>
                <input id="building" type="text" name="building"
                    value="{{ old('building', $profile->building ?? '') }}">
            </div>

            <div class="register__link">
                <button type="submit">更新する</button>
            </div>
        </form>
    </main>
</body>

</html>
