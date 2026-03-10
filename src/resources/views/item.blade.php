<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productdetails.css') }}">
    <title>商品詳細画面</title>
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
        <div class="product-detail">
            <div class="product-image-area">
                <div class="product-image-container">
                    <img src="{{ Storage::url($item->item_img) }}" alt="商品画像" class="product-image">
                    @if($item->purchasedItems()->exists())
                    <div class="sold-label">Sold</div>
                    @endif
                </div>
            </div>
            <div class="product-description-area">
                <div class="Product-title">
                    <h1>{{ $item->item_name }}</h1>
                    @if($item->brand_name && $item->brand_name !== '')
                    <p>{{ $item->brand_name }}</p>
                    @endif
                    <div class="price">
                        <span class="price-currency">¥</span><span
                            class="price-amount">{{ number_format((float) $item->price) }}</span><span
                            class="price-tax">(税込)</span>
                    </div>
                </div>
                <div class="product-actions">
                    <form action="{{ route('item.likes.store', $item->id) }}" method="post" class="like-form">
                        @csrf
                        <button type="submit" class="like-button">
                            <div class="action-item">
                                <img src="{{ asset($isLiked ? 'img/ハートロゴ_ピンク.png' : 'img/ハートロゴ_デフォルト.png') }}" alt="いいね"
                                    class="like-icon">
                                <span class="like-count">{{ $likeCount }}</span>
                            </div>
                        </button>
                    </form>
                    <div class="action-item">
                        <img src="{{ asset('img/ふきだしロゴ.png') }}" alt="コメント" class="like-icon">
                        <span class="like-count">{{ $commentCount }}</span>
                    </div>
                </div>
                <a href="{{ route('purchase.show', $item->id) }}" class="purchase-box">
                    購入手続きへ
                </a>
                <div class="product-description">
                    <h2>商品説明</h2>
                    <p>{{ $item->item_detail }}</p>
                </div>
                <div class="product-info">
                    <h2>商品の情報</h2>
                    <ul>
                        <li>
                            <span>カテゴリー</span>
                            <div class="category-badges">
                                @foreach($item->categories as $category)
                                <span class="category-badge">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </li>
                        <li>
                            <span>商品の状態</span>
                            <span>{{ $item->condition ?? '未設定' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="prodct-comments">
                    <h3>コメント({{ $commentCount }})</h3>
                    <div class="comment-user">
                        @forelse ($item->comments as $comment)
                        <div class="comment-item">
                            <img src="{{ $comment->profile->profile_img ? Storage::url($comment->profile->profile_img) : asset('img/default_profile.png') }}"
                                alt="ユーザーアイコン" class="comment-user-icon">
                            <div class="comment-content">
                                <p class="comment-username">{{ $comment->profile->user->name ?? '匿名' }}</p>
                                <p class="comment-text">{{ $comment->comment_detail }}</p>
                            </div>
                        </div>
                        @empty
                        <p>コメントはまだありません。</p>
                        @endforelse
                    </div>

                    <div class="comment-input-section">
                        <h4>商品へのコメント</h4>
                        @if ($errors->any())
                        <div class="error-message">
                            @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                            @endforeach
                        </div>
                        @endif
                        <form action="{{ route('item.comments.store', $item->id) }}" method="post">
                            @csrf
                            <textarea name="comment_detail" rows="4" placeholder="コメント内容を入力してください"></textarea>
                            <button type="submit" class="comment-submit">コメントを送信する</button>
                        </form>
                    </div>
                </div>
            </div>
    </main>
</body>

</html>
