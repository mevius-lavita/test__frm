<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Profile;
use App\Models\PurchasedItem;
use App\Http\Requests\Auth\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id, 'nickname' => $user->name]);
        $paymentMethods = ['カード支払い', 'コンビニ支払い'];
        $paymentMethod = null;

        return view('purchase', compact('item', 'profile', 'paymentMethod', 'paymentMethods'));
    }

    public function editAddress(Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        return view('addressedit', compact('item', 'profile'));
    }

    public function updateAddress(Request $request, Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        $profile->update([
            'address_number' => $request->address_number,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.show', $item->id);
    }

    public function process(PurchaseRequest $request, Item $item)
    {
        $user = auth()->user();

        $profile = $user->profile ?? Profile::create([
            'user_id' => $user->id,
            'nickname' => $user->name
        ]);

        // 住所情報を保存
        $profile->update([
            'address_number' => $request->address_number,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // コンビニ支払いの場合
        if ($request->payment_method === 'コンビニ支払い') {
            PurchasedItem::create([
                'profile_id' => $profile->id,
                'item_id' => $item->id,
                'payment_method' => $request->payment_method,
            ]);

            return redirect('/')->with('success', '購入が完了しました。');
        }

        // カード支払いの場合はStripe決済
        $stripe = new \Stripe\StripeClient(config('stripe.secret'));

        // 価格からカンマを削除して整数に変換
        $price = (int) str_replace(',', '', $item->price);

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->item_name,
                    ],
                    'unit_amount' => $price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.cancel'),
            'metadata' => [
                'profile_id' => $profile->id,
                'item_id' => $item->id,
                'payment_method' => $request->payment_method,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect('/')->with('error', '決済情報の取得に失敗しました。');
        }

        $stripe = new StripeClient(config('stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect('/')->with('error', '決済が完了していません。');
        }

        $profileId = $session->metadata->profile_id ?? null;
        $itemId = $session->metadata->item_id ?? null;
        $paymentMethod = $session->metadata->payment_method ?? 'カード支払い';

        if (!$profileId || !$itemId) {
            return redirect('/')->with('error', '購入情報が不足しています。');
        }

        PurchasedItem::firstOrCreate(
            [
                'profile_id' => $profileId,
                'item_id' => $itemId,
            ],
            [
                'payment_method' => $paymentMethod,
            ]
        );

        return redirect('/')->with('success', '購入が完了しました。');
    }

    public function cancel()
    {
        return redirect('/')->with('error', '購入がキャンセルされました。');
    }

    public function handleWebhook(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            $profileId = $session->metadata->profile_id;
            $itemId = $session->metadata->item_id;
            $paymentMethod = $session->metadata->payment_method ?? null;

            // 🔥 ここでDB保存
            PurchasedItem::firstOrCreate([
                'profile_id' => $profileId,
                'item_id' => $itemId,
            ], [
                'payment_method' => $paymentMethod,
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
