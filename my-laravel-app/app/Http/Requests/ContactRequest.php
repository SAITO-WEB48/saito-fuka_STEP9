<?php

namespace App\Http\Controllers\Ec;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 一覧表示（検索あり）
    public function index(Request $request)
    {
        $query = Product::query();

        // ログイン中なら「自分以外」の商品だけ表示
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        // 検索条件
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

    // 詳細
    public function show(Product $product)
    {
        return view('ec.products.show', compact('product'));
    }

    // 新規作成画面
    public function create()
    {
        return view('ec.products.create');
    }

    // 保存（新規登録）
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'max:255'],
            'description' => ['nullable', 'max:1000'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        $validated['user_id'] = Auth::id();

        // 画像保存（あれば）
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('ec.products.index')
            ->with('success', '商品を追加しました');
    }

    // 編集画面（出品者のみ）
    public function edit(Product $product)
    {
        abort_unless(Auth::check() && Auth::id() === $product->user_id, 403);

        return view('ec.products.edit', compact('product'));
    }

    // 更新（出品者のみ）
    public function update(Request $request, Product $product)
    {
        abort_unless(Auth::check() && Auth::id() === $product->user_id, 403);

        $validated = $request->validate([
            'name'        => ['required', 'max:255'],
            'description' => ['nullable', 'max:1000'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        // 新しい画像がアップロードされた時だけ差し替え
        if ($request->hasFile('image')) {
            // 既存画像があれば削除
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // 保存してパスを入れる
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            // 画像が来てない場合は既存のまま
            $validated['image'] = $product->image;
        }

        $product->update([
            'name'        => $validated['name'],
            'price'       => $validated['price'],
            'stock'       => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'image'       => $validated['image'],
        ]);

        return redirect()
            ->route('ec.products.show', $product)
            ->with('success', '商品を更新しました');
    }

    // 削除（出品者のみ）
    public function destroy(Product $product)
    {
        abort_unless(Auth::check() && Auth::id() === $product->user_id, 403);

        // 画像があれば消す（任意だけどおすすめ）
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('ec.products.index')
            ->with('success', '商品を削除しました');
    }

    // お気に入り登録（JSON）
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

    // お気に入り解除（JSON）
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
}
