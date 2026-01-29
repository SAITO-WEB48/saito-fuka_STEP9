<?php

namespace App\Http\Controllers\Ec;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends Controller
{
    // 一覧表示 　 (一覧index)
public function index(Request $request)
{
    $query = Product::query();

    // ログイン中なら「自分以外」の商品だけ表示
    if (Auth::check()) {
        $query->where('user_id', '!=', Auth::id());
    }

    // ここから検索条件を追加する
    if ($request->filled('keyword')) {
        $query->where('name', 'like', '%' . $request->keyword . '%');
    }

    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

        $products = $query->get();

        return view('ec.products.index', compact('products'));
    }

    // 詳細表示（これをクラス内に入れる） (詳細show)
    public function show(Product $product)
    {
        return view('ec.products.show', compact('product'));
    }

    // 商品追加画面 (新規画面create)
    public function create()
    {
        return view('ec.products.create');
    }

    // 商品保存  (登録store)
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('products', 'public');
    }

        Product::create($validated);

        return redirect()
            ->route('ec.products.index')
            ->with('success', '商品を追加しました');
    }

// お気に入り登録
public function favorite(Request $request, Product $product)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    DB::table('favorites')->updateOrInsert(
        [
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ],
        [
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    return response()->json(['favorited' => true]);
}

// お気に入り解除
public function unfavorite(Request $request, Product $product)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    DB::table('favorites')
        ->where('user_id', Auth::id())
        ->where('product_id', $product->id)
        ->delete();

    return response()->json(['favorited' => false]);
}

// 削除(destroy)
public function destroy(Product $product)
{
    $product->delete();

    return redirect()
        ->route('ec.products.index')
        ->with('success', '商品を削除しました');
}

//編集画面(edit)
public function edit(\App\Models\Product $product)
{
    return view('ec.products.edit', compact('product'));
}

//更新(update)
public function update(UpdateProductRequest $request, Product $product)
{
    $validated = $request->validated();

   // 新しい画像がアップロードされた時だけ差し替え
    if ($request->hasFile('image')) {
        // 既存画像があれば削除
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // 保存してパスを入れる（保存先 storage/app/public/products/...）
        $path = $request->file('image')->store('products', 'public');
        $validated['image'] = $path;
    }

    // 画像が来てない場合は既存のまま
    $product->update([
        'name'        => $validated['name'],
        'price'       => $validated['price'],
        'stock'       => $validated['stock'],
        'description' => $validated['description'] ?? null,
        'image'       => $validated['image'] ?? $product->image,
    ]);

    return redirect()
        ->route('ec.products.show', $product)
        ->with('success', '商品を更新しました');
}
}






