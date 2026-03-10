<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listing.css') }}">
    <title>商品出品画面</title>
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

    <main class="listing-main">
        <form action="{{ route('listing.store') }}" method="post" enctype="multipart/form-data" class="listing-form">
            @csrf

            <h1 class="listing-title">商品の出品</h1>

            <section class="image-section">
                <h2 class="field-label">商品画像</h2>
                <div class="image-upload-box">
                    <img id="item_img_preview" alt="" class="item-img-preview">
                    <input type="file" name="item_img" accept="image/*" id="item_img_input" hidden>
                    <button type="button" id="item_img_button" class="item-img-button">画像を選択する</button>
                </div>
                @error('item_img')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </section>

            <section class="detail-section">
                <h2 class="section-title">商品の詳細</h2>

                <div class="category-block">
                    <h3 class="field-label">カテゴリー</h3>
                    <div id="selected_categories_container">
                        @foreach (old('category_ids', []) as $selectedCategoryId)
                        <input type="hidden" name="category_ids[]" value="{{ $selectedCategoryId }}">
                        @endforeach
                    </div>
                    <div class="category-list">
                        @foreach ($categories as $category)
                        <button type="button"
                            class="category-btn {{ in_array($category->id, old('category_ids', [])) ? 'selected' : '' }}"
                            data-category-id="{{ $category->id }}">{{ $category->name }}</button>
                        @endforeach
                    </div>
                    @error('category_ids')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="condition-block">
                    <h3 class="field-label">商品の状態</h3>
                    <input type="hidden" name="condition_id" id="condition_id" value="{{ old('condition_id') }}"
                        required>
                    <div class="condition-wrapper" id="condition_wrapper">
                        <div class="condition-display" id="condition_display">{{ old('condition_id') ?: '選択してください' }}
                        </div>
                        <div class="condition-dropdown" id="condition_dropdown">
                            @foreach ($conditions as $index => $condition)
                            <div class="condition-option {{ $index === 0 ? 'default-active' : '' }} {{ old('condition_id') === $condition ? 'selected' : '' }}"
                                data-value="{{ $condition }}">
                                {{ old('condition_id') === $condition ? '✓ ' : '' }}{{ $condition }}</div>
                            @endforeach
                        </div>
                    </div>
                    @error('condition_id')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </section>

            <section class="name-desc-section">
                <h2 class="section-title">商品名と説明</h2>

                <div class="form-block">
                    <h3 class="field-label">商品名</h3>
                    <input type="text" name="item_name" value="{{ old('item_name') }}" required>
                    @error('item_name')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-block">
                    <h3 class="field-label">ブランド名</h3>
                    <input type="text" name="brand_name" value="{{ old('brand_name') }}">
                    @error('brand_name')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-block">
                    <h3 class="field-label">商品の説明</h3>
                    <textarea name="item_detail" rows="4" required>{{ old('item_detail') }}</textarea>
                    @error('item_detail')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-block">
                    <h3 class="field-label">販売価格</h3>
                    <div class="price-input-wrap">
                        <span class="yen-mark">¥</span>
                        <input type="text" name="price" value="{{ old('price') }}" required>
                    </div>
                    @error('price')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </section>

            <button type="submit" class="exhibited-product-button">出品する</button>
        </form>
    </main>

    <script>
        const itemImgInput = document.getElementById('item_img_input');
        const itemImgButton = document.getElementById('item_img_button');
        const itemImgPreview = document.getElementById('item_img_preview');

        itemImgButton.addEventListener('click', () => itemImgInput.click());

        itemImgInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file)
                return;
            const reader = new FileReader();
            reader.onload = (event) => {
                itemImgPreview.src = event.target.result;
                itemImgPreview.style.display = 'block';
                itemImgButton.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });

        const categoryButtons = document.querySelectorAll('.category-btn');
        const selectedCategoriesContainer = document.getElementById('selected_categories_container');
        const selectedCategories = new Set(
            Array.from(selectedCategoriesContainer.querySelectorAll('input[name="category_ids[]"]')).map(input =>
                input.value)
        );

        categoryButtons.forEach(button => {
            const categoryId = button.dataset.categoryId;
            if (selectedCategories.has(categoryId)) {
                button.classList.add('selected');
            }

            button.addEventListener('click', () => {
                if (selectedCategories.has(categoryId)) {
                    selectedCategories.delete(categoryId);
                    button.classList.remove('selected');
                } else {
                    selectedCategories.add(categoryId);
                    button.classList.add('selected');
                }
                updateHiddenInputs();
            });
        });

        function updateHiddenInputs() {
            selectedCategoriesContainer.innerHTML = '';
            selectedCategories.forEach(categoryId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'category_ids[]';
                input.value = categoryId;
                selectedCategoriesContainer.appendChild(input);
            });
        }

        const conditionIdInput = document.getElementById('condition_id');
        const conditionDisplay = document.getElementById('condition_display');
        const conditionDropdown = document.getElementById('condition_dropdown');
        const conditionWrapper = document.getElementById('condition_wrapper');
        const conditionOptions = document.querySelectorAll('.condition-option');

        conditionDisplay.addEventListener('click', function (e) {
            e.stopPropagation();
            conditionDropdown.classList.toggle('active');
        });

        conditionOptions.forEach(option => {
            option.addEventListener('click', function () {
                const value = this.getAttribute('data-value');
                conditionIdInput.value = value;
                conditionDisplay.textContent = value;

                conditionOptions.forEach(opt => {
                    opt.classList.remove('selected');
                    opt.textContent = opt.getAttribute('data-value');
                });

                this.classList.add('selected');
                this.textContent = `✓ ${value}`;
                conditionDropdown.classList.remove('active');
            });
        });

        document.addEventListener('click', function (e) {
            if (!conditionWrapper.contains(e.target)) {
                conditionDropdown.classList.remove('active');
            }
        });

    </script>
</body>

</html>
