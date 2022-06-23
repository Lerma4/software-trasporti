<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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
            <img src="{{ asset('images/logo/logo.png') }}" class="main-logo" alt="logo">
            {{ config('app.name', 'Laravel') }}
        </div>

        @include('multiauth::partials.menu')

    </div>
    <div class="c-wrapper">
        <header class="c-header c-header-light c-header-fixed">
            <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
                data-class="c-sidebar-show">
                <i class="c-icon c-icon-2xl cil-menu"></i>
            </button>
            <a class="c-header-brand d-lg-none c-header-brand-sm-up-center" href="{{ route('admin.home') }}">
                <img src="{{ asset('images/logo/logo.png') }}"
                    class="main-logo-responsive c-header-brand d-lg-none c-header-brand-sm-up-center" alt="logo">
            </a>
            <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
                data-class="c-sidebar-lg-show" responsive="true">
                <i class="c-icon c-icon-2xl cil-menu"></i>
            </button>
            <ul class="c-header-nav mfs-auto">
                <li class="c-header-nav-item dropdown">
                    <div class="btn-group dropleft">
                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-bell"></i>
                            @php
                            // notifiche patenti
                            $licenses_count = 0;
                            foreach ($licenses_notifications as $lic) {
                            if ($lic->user->companyId == auth('admin')->user()->companyId) $licenses_count++;
                            }
                            // notifiche scadenze mezzi
                            $expirations_count = 0;
                            foreach ($expirations_notifications as $exp) {
                            if ($exp->truck->companyId == auth('admin')->user()->companyId) $expirations_count++;
                            }
                            // notifiche manutenzioni
                            $maint_count = 0;
                            foreach ($maint_notifications as $maint) {
                            if ($maint->companyId == auth('admin')->user()->companyId) $maint_count++;
                            }
                            // notifiche incidenti
                            $crash_count = 0;
                            foreach ($crash_notifications as $crash) {
                            if ($crash->companyId == auth('admin')->user()->companyId) $crash_count++;
                            }
                            // notifiche segnalazioni
                            $reports_count = 0;
                            foreach ($reports_notifications as $report) {
                            if ($report->companyId == auth('admin')->user()->companyId) $reports_count++;
                            }

                            $notifications = $licenses_count + $expirations_count + $maint_count + $crash_count +
                            $reports_count;

                            @endphp
                            @if ($notifications > 0)
                            <span class="badge badge-warning">{{ $notifications }}</span>
                            @endif
                        </button>
                        <div class="dropdown-menu">
                            @if ($notifications = 0)
                            <p>@lang('No notifications')</p>
                            @endif
                            @if ($licenses_count > 0)
                            <a class="dropdown-item" href="{{ route('admin.users') }}">{{ $licenses_count }}
                                @lang('expiring licence/s')</a>
                            @endif
                            @if ($expirations_count > 0)
                            <a class="dropdown-item" href="{{ route('admin.trucks') }}">{{ $expirations_count }}
                                @lang('upcoming truck/s deadline/s')</a>
                            @endif
                            @if ($maint_count > 0)
                            <a class="dropdown-item" href="{{ route('admin.maint') }}">{{ $maint_count }}
                                @lang('upcoming maintenance/s')</a>
                            @endif
                            @if ($crash_count > 0)
                            <a class="dropdown-item" href="{{ route('admin.crash') }}">{{ $crash_count }}
                                @lang('new incident/s')</a>
                            @endif
                            @if ($reports_count > 0)
                            <a class="dropdown-item" href="{{ route('admin.reports') }}">{{ $reports_count }}
                                @lang('new report/s')</a>
                            @endif
                        </div>
                    </div>
                </li>
                <li class="c-header-nav-item px-3 c-d-legacy-none">
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    <a class="c-header-nav-btn fas fa-sign-out-alt fa-lg " href="{{ route('admin.logout') }}"
                        title="Logout"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"></a>
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
            <div><a href="#">{{ config('app.name', 'Laravel') }}</a> © {{ \Carbon\Carbon::today()->format('Y') }}
            </div>
            <div class="mfs-auto">v. {{ config('app.version') }}</a></div>
        </footer>

    </div>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    @yield('scripts')

</body>

</html>