<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>@yield('title')</title>

  {{-- CSS / JS --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<header class="layout_header">
  <nav class="layout_header__nav">
    <a class="layout_header__link" href="{{ route('ec.products.index') }}">Home</a>
    <a class="layout_header__link" href="{{ route('ec.mypage') }}">マイページ</a>
    <a class="layout_header__link" href="{{ route('ec.contact.create') }}">お問い合わせ</a>

    <div class="layout_header__auth">
      @auth
        <span class="layout_header__user">
          ようこそ、{{ auth()->user()->name }}さん
        </span>

        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="logout-btn">ログアウト</button>
        </form>
      @else
        <a class="layout_header__login" href="{{ route('login') }}">ログイン</a>
      @endauth
    </div>
  </nav>
</header>

<main class="layout_main">
  @yield('content')
</main>

<footer class="layout_footer">
  <small class="layout_footer__text">TNG SHOP</small>
</footer>

</body>
</html>
