<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
  <link href="  https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />

  @yield('styles')

</head>

<body class="c-app">

  <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show c-sidebar-md" id="sidebar">
    <div class="c-sidebar-brand d-md-down-none">
      {{ config('app.name', 'Laravel') }}
    </div>

    @include('multiauth::partials.menu')

  </div>
  <div class="c-wrapper">
    <header class="c-header c-header-light c-header-fixed">
      <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
        <i class="c-icon c-icon-2xl cil-menu"></i>
      </button>
      <a class="c-header-brand d-lg-none c-header-brand-sm-up-center" href="{{ route('admin.home') }}">{{ config('app.name', 'Laravel') }}</a>
      <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
        <i class="c-icon c-icon-2xl cil-menu"></i>
      </button>
      <ul class="c-header-nav mfs-auto">
        <li class="c-header-nav-item px-3 c-d-legacy-none">
          <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
            @csrf
          </form>
          <a class="c-header-nav-btn fas fa-sign-out-alt fa-lg " href="{{ route('admin.logout') }}" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"></a>
        </li>
      </ul>
    </header>
    <div class="c-body">
      <main class="c-main">

        <div class="container-fluid">

          @yield('content')

        </div>
      </main>
    </div>

    <footer class="c-footer">
      <div><a href="https://coreui.io">CoreUI</a> © 2020 creativeLabs.</div>
      <div class="mfs-auto">Powered by&nbsp;<a href="https://coreui.io">CoreUI</a></div>
    </footer>

  </div>
  </div>

  <script src="{{ asset('js/app.js') }}" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  @yield('scripts')

</body>

</html>