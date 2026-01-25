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
    // 購入画面表示
    public function create(Product $product)
    {
        return view('ec.purchase.create', compact('product'));
    }

    // 購入処理（POST）
    public function store(Request $request, Product $product)
{
    $validated = $request->validate([
        'quantity' => ['required', 'integer', 'min:1'],
    ]);

    $qty = (int) $validated['quantity'];

    try {
        DB::transaction(function () use ($product, $qty) {

            $fresh = Product::whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($fresh->stock <= 0) {
                throw new \RuntimeException('在庫切れのため購入できません');
            }

            if ($fresh->stock < $qty) {
                throw new \RuntimeException('在庫が不足しています');
            }

            $fresh->decrement('stock', $qty);

            Order::create([
                'user_id' => Auth::id(),
                'product_id' => $fresh->id,
                'quantity' => $qty,
            ]);
        });
    } catch (\Throwable $e) {
        return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
    }

    return redirect()
        ->route('ec.mypage') 
        ->with('success', '購入しました');
}
}
