@extends('layouts.app')

@section('title', '商品購入')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="products-container">
  <div class="page_head">
    <h1 class="page_title">商品購入</h1>
    <a href="{{ route('ec.products.show', $product) }}" class="search-btn">商品詳細へ戻る</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('ec.purchase.store', $product) }}">
    @csrf

    <div class="table-wrap" style="padding:16px;">
      <table class="products-table">
        <tbody>
          <tr>
            <th style="width:160px;">商品名</th>
            <td>{{ $product->name }}</td>
          </tr>
          <tr>
            <th>価格</th>
            <td>¥{{ number_format($product->price) }}</td>
          </tr>
          <tr>
            <th>在庫</th>
            <td>{{ $product->stock }}</td>
          </tr>
          <tr>
            <th>購入個数</th>
            <td>
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
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="product-actions">
      @if($product->stock > 0)
        <button type="submit" class="btn-cart">購入確定</button>
      @else
        <button type="button" class="btn-cart" disabled>在庫切れ</button>
      @endif

      <a href="{{ route('ec.products.show', $product) }}" class="btn-back">戻る</a>
    </div>
  </form>
</div>
@endsection



    
