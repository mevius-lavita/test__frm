<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{

    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile;
        $address = $profile?->address;

        return view('mypage.profileedit', compact('user', 'address'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:20',
            'address_number' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required',
            'building' => 'nullable',
        ]);

        $user = auth()->user();

        DB::transaction(function () use ($request, $user) {

            // ✅ users 更新
            $user->update([
                'name' => $request->name,
            ]);

            // ✅ profileを取得または作成
            $profile = Profile::firstOrCreate(['user_id' => $user->id]);

            // ✅ addressを取得または作成
            $address = Address::updateOrCreate(
                ['id' => $profile->address_id],
                [
                    'address_number' => $request->address_number,
                    'address' => $request->address,
                    'building' => $request->building ?? '',
                ]
            );

            // ✅ profileのaddress_idを更新
            $profile->update(['address_id' => $address->id]);
        });

        return redirect()->route('profile.edit')->with('success', 'プロフィール更新完了');
    }
}
