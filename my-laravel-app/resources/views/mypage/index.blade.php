@if(auth()->check())
  <p style="color:red;">ログインID: {{ auth()->id() }}</p>
@endif


@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="mypage-container">

  <h1 class="page_title">マイページ</h1>

  {{-- 上部：アカウント情報 --}}
  <section class="mypage-card">
    <div class="mypage-card__head">
      <a class="btn btn-primary" href="{{ route('profile.edit') }}">アカウント編集</a>
    </div>

    <div class="mypage-grid">
      <div>
        <div class="kv"><span class="kv__label">ユーザ名：</span>{{ $user->name }}</div>
        <div class="kv"><span class="kv__label">Eメール：</span>{{ $user->email }}</div>
      </div>
      <div>
        <div class="kv"><span class="kv__label">名前：</span>{{ $user->name_kanji ?? '' }}</div>
        <div class="kv"><span class="kv__label">カナ：</span>{{ $user->name_kana ?? '' }}</div>
      </div>
    </div>
  </section>

  {{-- 中部：出品商品 --}}
  <section class="mypage-card">
    <div class="mypage-card__head">
      <h2 class="mypage-subtitle">＜出品商品＞</h2>
      <a href="{{ route('ec.products.create') }}" class="btn btn-primary">新規登録</a>
    </div>

    @if ($listedProducts->isEmpty())
      <p style="margin-top:12px;">出品した商品はまだありません。</p>
    @else
      <div class="table-wrap" style="margin-top:12px;">
        <table class="mypage-table">
          <thead>
            <tr>
              <th style="width:120px;">商品番号</th>
              <th>商品名</th>
              <th>商品説明</th>
              <th style="width:120px;">料金(¥)</th>
              <th style="width:110px;"></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($listedProducts as $product)
              <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->description }}</td>
                <td>{{ number_format($product->price) }}</td>
                <td>
                  <a href="{{ route('ec.products.show', $product) }}" class="btn btn-primary">詳細</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>

  {{-- 下部：購入した商品 --}}
  <section class="mypage-card">
    <div class="mypage-card__head">
      <h2 class="mypage-subtitle">＜購入履歴＞</h2>
    </div>

    @if ($orders->isEmpty())
      <p style="margin-top:12px;">購入した商品はまだありません。</p>
    @else
      <div class="table-wrap" style="margin-top:12px;">
        <table class="mypage-table">
          <thead>
            <tr>
              <th>商品名</th>
              <th>商品説明</th>
              <th style="width:120px;">料金(¥)</th>
              <th style="width:110px;">個数</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
              <tr>
                <td>{{ $order->product?->name ?? '（商品が見つかりません）' }}</td>
                <td>{{ $order->product?->description ?? '' }}</td>
                <td>{{ $order->product ? number_format($order->product->price) : '' }}</td>
                <td>{{ $order->quantity }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>

</div>
@endsection
