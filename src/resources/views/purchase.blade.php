<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}?v={{ time() }}">
    <title>商品購入画面</title>
</head>

<body>
    @php
    $currentPaymentMethod = old('payment_method');
    @endphp
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
        <form action="{{ route('purchase.confirm', $item->id) }}" method="post" class="purchase-page">
            @csrf
            <div class="purchase-left">
                <div class="product-section">
                    <div class="product-item">
                        <img src="{{ Storage::url($item->item_img) }}" alt="商品画像" class="product-image">
                        <div class="product-details">
                            <h2>{{ $item->item_name }}</h2>
                            <div class="price-display">
                                <span class="price-currency">¥</span>
                                <span class="price-amount">{{ number_format((float) $item->price) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payment-section">
                    <h3>支払い方法
                        <input type="hidden" name="address_number" value="{{ $profile->address_number ?? '' }}">
                        <input type="hidden" name="address" value="{{ $profile->address ?? '' }}">
                        <input type="hidden" name="building" value="{{ $profile->building ?? '' }}">
                    </h3>
                    <div class="select-wrapper" id="payment_select_wrapper">
                        <div class="select-display" id="select_display">{{ $currentPaymentMethod ?? '選択してください' }}</div>
                        <select name="payment_method" id="payment_method_select">
                            <option value="" disabled {{ $currentPaymentMethod ? '' : 'selected' }}>選択してください</option>
                            @foreach ($paymentMethods as $method)
                            <option value="{{ $method }}" {{ $currentPaymentMethod === $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                        <div class="payment-dropdown" id="payment_dropdown">
                            @foreach ($paymentMethods as $index => $method)
                            <div class="payment-option {{ $currentPaymentMethod === $method ? 'selected' : '' }}"
                                data-value="{{ $method }}">{{ $currentPaymentMethod === $method ? '✓ ' : '' }}{{ $method }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="address-section">
                    <div class="address-header">
                        <h3>配送先</h3>
                        <a href="{{ route('address.edit', $item->id) }}" class="change-link">変更する</a>
                    </div>
                    <p class="address-postcode">〒{{ $profile->address_number ?? 'XXX-YYYY' }}</p>
                    <p class="address-text">{{ $profile->address ?? 'ここには住所と建物が入ります' }} {{ $profile->building ?? '' }}
                    </p>
                </div>
            </div>

            <div class="purchase-right">
                <div class="confirm-surface">
                    <div class="confirm-row">
                        <span class="confirm-label">商品代金</span>
                        <span class="confirm-value">¥{{ number_format((float) $item->price) }}</span>
                    </div>
                    <div class="confirm-row">
                        <span class="confirm-label">支払い方法</span>
                        <span class="confirm-value" id="payment_method_display">{{ $currentPaymentMethod ?? 'コンビニ支払い' }}</span>
                    </div>
                </div>
                <button type="submit" class="purchase-button">購入する</button>
            </div>
        </form>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentSelect = document.getElementById('payment_method_select');
            const selectDisplay = document.getElementById('select_display');
            const paymentDisplay = document.getElementById('payment_method_display');
            const paymentDropdown = document.getElementById('payment_dropdown');
            const paymentSelectWrapper = document.getElementById('payment_select_wrapper');
            const paymentOptions = document.querySelectorAll('.payment-option');

            // Update display text
            function updateDisplay(value) {
                const displayValue = value || '選択してください';
                selectDisplay.textContent = displayValue;
                paymentDisplay.textContent = value || 'コンビニ支払い';
            }

            // Toggle dropdown visibility
            selectDisplay.addEventListener('click', function (e) {
                e.stopPropagation();
                paymentDropdown.classList.toggle('active');
            });

            // Handle dropdown option selection
            paymentOptions.forEach(option => {
                option.addEventListener('click', function () {
                    const value = this.getAttribute('data-value');
                    paymentSelect.value = value;
                    updateDisplay(value);

                    paymentOptions.forEach(opt => {
                        opt.classList.remove('selected');
                        opt.textContent = opt.getAttribute('data-value');
                    });

                    this.classList.add('selected');
                    this.textContent = `✓ ${value}`;
                    paymentDropdown.classList.remove('active');
                });
            });

            // Handle standard select change
            paymentSelect.addEventListener('change', function () {
                const value = this.value;
                updateDisplay(value);
                paymentDropdown.classList.remove('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!paymentSelectWrapper.contains(e.target)) {
                    paymentDropdown.classList.remove('active');
                }
            });
        });

    </script>
</body>

</html>
