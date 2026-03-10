<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Item;
use App\Models\PurchasedItem;
use App\Http\Requests\Auth\ProfileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id, 'nickname' => $user->name]);
        $page = $request->page;

        if ($page === 'buy') {
            $myPurchasedItemIds = $profile->purchasedItems()->pluck('item_id')->all();
            $myItems = Item::whereIn('id', $myPurchasedItemIds)->get();
        } elseif ($page === 'sell') {
            $myListedItemIds = $profile->listedItems()->pluck('item_id')->all();
            $myItems = Item::whereIn('id', $myListedItemIds)->get();
        } else {
            $myListedItemIds = $profile->listedItems()->pluck('item_id')->all();
            $myItems = Item::whereIn('id', $myListedItemIds)->get();
        }

        $soldItemIds = PurchasedItem::pluck('item_id')->all();

        return view('mypage.mypage', compact('myItems', 'soldItemIds', 'page', 'user', 'profile'));
    }

    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('mypage.profileedit', compact('user', 'profile'));
    }

    public function update(ProfileRequest $request)
    {
        $user = auth()->user();

        DB::transaction(function () use ($request, $user) {
            $user->update(['name' => $request->name]);

            $profile = Profile::firstOrCreate(['user_id' => $user->id]);

            if ($request->hasFile('profile_img')) {
                if ($profile->profile_img && Storage::disk('public')->exists($profile->profile_img)) {
                    Storage::disk('public')->delete($profile->profile_img);
                }
                $path = $request->file('profile_img')->store('profiles', 'public');
                $profile->update(['profile_img' => $path]);
            }

            $profile->update([
                'address_number' => $request->address_number,
                'address' => $request->address,
                'building' => $request->building ?? '',
            ]);
        });

        return redirect('/')->with('success', 'プロフィール更新完了');
    }
}
