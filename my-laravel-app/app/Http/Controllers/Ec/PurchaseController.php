<?php

namespace App\Http\Controllers\Ec;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function create(Product $product)
    {
        return view('ec.purchase.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        // ログイン必須（念のため保険）
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $qty = (int) $validated['quantity'];

        DB::transaction(function () use ($product, $qty) {

            // 最新在庫をロックして取得
            $fresh = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            if ($fresh->stock < $qty) {
                abort(422, '在庫が足りません');
            }

            // 注文作成
            Order::create([
                'user_id' => Auth::id(),
                'product_id' => $fresh->id,
                'quantity' => $qty,
            ]);

            // 在庫を減らす
            $fresh->decrement('stock', $qty);
        });

        return redirect()
            ->route('ec.mypage')  // ここは routes に合わせてね
            ->with('success', '購入が完了しました');
    }
}
