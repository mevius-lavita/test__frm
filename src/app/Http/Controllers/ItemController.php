<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Comment;
use App\Models\ItemLike;
use App\Models\ListedItem;
use App\Models\PurchasedItem;
use App\Http\Requests\Auth\ExhibitionRequest;
use App\Http\Requests\Auth\CommentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    // 商品一覧・検索
    public function index(Request $request)
    {
        $soldItemIds = PurchasedItem::pluck('item_id')->all();
        $tab = $request->tab;
        $keyword = $request->keyword;

        $items = Item::query();

        // キーワード検索
        if (!empty($keyword)) {
            $items->KeywordSearch($keyword);
        }

        if ($tab === 'mylist') {
            // マイリスト表示
            if (Auth::check() && Auth::user()->profile) {
                $profileId = Auth::user()->profile->id;
                $items->whereHas('likes', function ($query) use ($profileId) {
                    $query->where('profile_id', $profileId);
                });
            } else {
                // 未認証の場合は空の結果を返す
                $items->whereRaw('1 = 0');
            }
        } else {
            // 通常の一覧表示：自分が出品した商品を除外
            if (Auth::check() && Auth::user()->profile) {
                $profileId = Auth::user()->profile->id;
                $myListedItemIds = ListedItem::where('profile_id', $profileId)->pluck('item_id')->all();
                $items->whereNotIn('id', $myListedItemIds);
            }
        }

        $items = $items->get();

        // 未ログインの場合
        if (!Auth::check()) {
            return view('index', compact('items', 'soldItemIds'));
        }

        // メール未認証
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect('/registermail');
        }

        return view('index', compact('items', 'soldItemIds'));
    }

    public function search(Request $request)
    {
        return redirect()->route('items.index', ['keyword' => $request->keyword, 'type' => $request->type]);
    }

    // 商品詳細
    public function show(Item $item)
    {
        $item->load(['comments.profile.user', 'categories', 'likes']);

        $likeCount = $item->likes->count();
        $commentCount = $item->comments->count();
        $isLiked = false;
        $profile = null;

        if (Auth::check()) {
            $user = Auth::user();
            $profile = $user->profile ?? Profile::create(['user_id' => $user->id, 'nickname' => $user->name]);
            $profileId = $profile->id;
            $isLiked = $item->likes->contains('profile_id', $profileId);
        }

        return view('item', compact('item', 'likeCount', 'commentCount', 'isLiked', 'profile'));
    }

    // 商品出品
    public function create()
    {
        $categories = DB::table('categories')->get();
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        return view('listing', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('item_img')->store('items', 'public');

        // 現在のDB構造では1つのカテゴリーのみ保存可能なため、最初のカテゴリーを使用
        $item = Item::create([
            'item_img' => $path,
            'item_name' => $request->item_name,
            'brand_name' => $request->brand_name,
            'item_detail' => $request->item_detail,
            'price' => $request->price,
            'category_id' => $request->category_ids[0],
            'condition' => $request->condition_id,
        ]);
        $profile = Profile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['nickname' => Auth::user()->name]
        );

        ListedItem::create([
            'profile_id' => $profile->id,
            'item_id' => $item->id,
        ]);

        // 複数カテゴリーを中間テーブルに保存
        if ($request->has('category_ids')) {
            $item->categories()->attach($request->category_ids);
        }

        return redirect('/');
    }

    // コメント
    public function storeComment(CommentRequest $request, Item $item)
    {
        $user = $request->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        Comment::create([
            'comment_detail' => $request->input('comment_detail'),
            'profile_id' => $profile->id,
            'item_id' => $item->id,
        ]);

        return redirect()->route('item.show', $item->id);
    }

    // いいね
    public function storeLike(Request $request, Item $item)
    {
        $user = $request->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        $like = ItemLike::where('item_id', $item->id)
            ->where('profile_id', $profile->id)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            ItemLike::create([
                'item_id' => $item->id,
                'profile_id' => $profile->id,
            ]);
        }

        return redirect()->route('item.show', $item->id);
    }
}
