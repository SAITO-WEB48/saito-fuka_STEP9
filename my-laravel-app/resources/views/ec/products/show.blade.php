@extends('layouts.app')

@section('title', '商品詳細')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="products-container">
  <div class="page_head">
    <h1 class="page_title">商品詳細</h1>
    <a href="{{ route('ec.products.index') }}" class="search-btn">一覧へ戻る</a>
  </div>

  @php
    $isOwner = auth()->check() && auth()->id() === $product->user_id;
    $isFavorited = auth()->check() ? $product->isFavoritedBy(auth()->user()) : false;
  @endphp

  <div class="table-wrap" style="padding:16px;">
    <table class="products-table">
      <tbody>
        <tr>
          <th style="width:160px;">商品番号</th>
          <td>{{ $product->id }}</td>
        </tr>
        <tr>
          <th>商品名</th>
          <td>{{ $product->name }}</td>
        </tr>
        <tr>
          <th>商品説明</th>
          <td>{{ $product->description }}</td>
        </tr>

        <tr>
          <th>画像</th>
          <td>
            <div class="product-media">
              @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="product-img">
              @else
                <div class="product-img placeholder"></div>
              @endif

              {{-- 他人の商品だけお気に入り表示 --}}
              @if(!$isOwner)
                <button
                  id="favorite-btn"
                  data-product-id="{{ $product->id }}"
                  data-favorited="{{ $isFavorited ? '1' : '0' }}"
                  class="favorite-btn"
                  aria-label="お気に入り"
                  type="button"
                >
                  <span id="favorite-heart" style="color: {{ $isFavorited ? 'red' : '#999' }};">
                    ♥
                  </span>
                </button>
              @endif
            </div>
          </td>
        </tr>

        {{-- 他人の商品だけ購入個数を表示 --}}
        @if(!$isOwner)
        <tr>
          <th>購入個数</th>
          <td>
            <form method="POST" action="{{ route('ec.purchase.store', $product) }}" class="buy-inline">
              @csrf

              <input
                type="number"
                name="quantity"
                value="{{ old('quantity', 1) }}"
                min="1"
                max="{{ $product->stock }}"
                class="qty-input"
                {{ $product->stock <= 0 ? 'disabled' : '' }}
              >

              @error('quantity')
                <div class="error-text">{{ $message }}</div>
              @enderror
            </form>
          </td>
        </tr>
        @endif

        <tr>
          <th>料金</th>
          <td>¥{{ number_format($product->price) }}</td>
        </tr>
        <tr>
          <th>在庫</th>
          <td>{{ $product->stock }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="product-actions">

    {{-- 自分の商品：編集・削除だけ --}}
    @if($isOwner)
      <a href="{{ route('ec.products.edit', $product) }}" class="btn-back">編集</a>

      <form method="POST"
            action="{{ route('ec.products.destroy', $product) }}"
            onsubmit="return confirm('この商品を削除します。よろしいですか？');"
            style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">削除</button>
      </form>

    {{-- 他人の商品：購入ボタン --}}
    @else
      @if($product->stock > 0)
        <a href="{{ route('ec.purchase.create', $product) }}" class="btn-cart">
          カートに追加する
        </a>
      @else
        <button class="btn-cart" disabled>在庫切れ</button>
      @endif
    @endif

    <a href="{{ url()->previous() }}" class="btn-back">戻る</a>
  </div>
</div>

<script src="{{ asset('js/favorite.js') }}"></script>
@endsection
