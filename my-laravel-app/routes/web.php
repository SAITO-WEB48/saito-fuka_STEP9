<?php

use Illuminate\Support\Facades\Route;

//Blog
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;

//EC
use App\Http\Controllers\Ec\ProductController;
use App\Http\Controllers\Ec\ProductController as EcProductController;
use App\Http\Controllers\Ec\PurchaseController;

/*
|--------------------------------------------------------------------------
| 1) ECサイト（/ec/...）
|--------------------------------------------------------------------------
| - prefix('ec') でURLのまとまりを明確にする
| - auth が必要なもの/不要なものを分ける
|--------------------------------------------------------------------------
*/

Route::get('/check-ec', function () {
    return view('index'); // resources/views/index.blade.php（確認用）
});

/*
|--------------------------------------------------------------------------
| EC：ログイン不要
|--------------------------------------------------------------------------
*/
//お問合せ(EC)
Route::get('/ec/contact', [ContactController::class, 'showForm'])
    ->name('ec.contact.create');

Route::post('/ec/contact',[ContactController::class, 'submitForm'])
    ->name('ec.contact.store');

/*
|--------------------------------------------------------------------------
| EC：ログイン必須（個人情報 / Auth::user()を使う)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    //プロフィール編集
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    //ECマイページ
    Route::middleware('auth')
        ->get('/ec/mypage', [\App\Http\Controllers\Ec\MypageController::class, 'index'])
        ->name('ec.mypage');

    // 商品一覧
    Route::get('/ec/products', [ProductController::class, 'index'])
        ->name('ec.products.index');

    // 商品新規登録画面
    Route::get('/ec/products/create', [ProductController::class, 'create'])
        ->name('ec.products.create');

    // 商品登録（保存）
    Route::post('/ec/products', [ProductController::class, 'store'])
        ->name('ec.products.store');

    // 商品詳細
    Route::get('/ec/products/{product}', [ProductController::class, 'show'])
        ->name('ec.products.show');

    // 商品削除
    Route::delete('/ec/products/{product}', [ProductController::class, 'destroy'])
        ->name('ec.products.destroy');

    // お気に入り
    Route::post('/ec/products/{product}/favorite', [EcProductController::class, 'favorite'])
        ->name('ec.products.favorite');

    Route::delete('/ec/products/{product}/favorite', [EcProductController::class, 'unfavorite'])
        ->name('ec.products.unfavorite');

    // 購入
    Route::get('/ec/products/{product}/purchase', [PurchaseController::class, 'create'])
        ->name('ec.purchase.create');

    Route::post('/ec/products/{product}/purchase', [PurchaseController::class, 'store'])
        ->name('ec.purchase.store');

    // 編集画面
    Route::get('/ec/products/{product}/edit', [\App\Http\Controllers\Ec\ProductController::class, 'edit'])
        ->name('ec.products.edit');

    // 更新
    Route::put('/ec/products/{product}', [\App\Http\Controllers\Ec\ProductController::class, 'update'])
        ->name('ec.products.update');
});

/*
|--------------------------------------------------------------------------
| ②ブログ(/index,/blog/...)
|--------------------------------------------------------------------------
*/

//トップ
Route::get('/', function () {
    return redirect('/index');
})->name('home');

// 一覧 / 詳細（ログイン不要）
Route::get('/index', [BlogController::class, 'index'])->name('index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('detail');

// ログイン必須（ブログ作成・編集など）
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [BlogController::class, 'mypage'])->name('mypage');

    Route::get('/create', [BlogController::class, 'create'])->name('create');
    Route::post('/store', [BlogController::class, 'store'])->name('store');

    Route::get('/blogs/{id}/edit', [BlogController::class, 'edit'])->name('edit');
    Route::put('/blogs/{id}', [BlogController::class, 'update'])->name('update');
    Route::delete('/blog/{id}', [BlogController::class, 'destroy'])->name('destroy');

    Route::get('/search', [BlogController::class, 'search'])->name('search');
});

// お問い合わせ（ブログ側）
Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');

// いいね（ブログ）
Route::post('/blogs/{blog}/like', [LikeController::class, 'likeBlog'])->middleware('auth');
Route::delete('/blogs/{blog}/like', [LikeController::class, 'unlileBlog'])->middleware('auth');

// 認証ルート
require __DIR__ . '/auth.php';




