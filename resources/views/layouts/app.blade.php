<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if($currentWebsite)
                        {{ ucfirst($currentWebsite->uuid) }}
                    @else
                        {{ config('app.name', 'Laravel') }}
                    @endif
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            {{-- Si está identificado, no es una website y SÍ es admin --}}
                            @if(!$currentWebsite)
                                @if(auth()->user()->isAdmin())
                                    <li class="nav-item {{ request()->route()->getName() === "tenants.index" ? "active" : "" }}">
                                        <a class="nav-link" href="{{ route('tenants.index') }}">{{ __('Inquilinos') }}</a>
                                    </li>
                                    <li class="nav-item {{ request()->route()->getName() === "plans.create" ? "active" : "" }}">
                                        <a class="nav-link" href="{{ route('plans.create') }}">{{ __('Crear un nuevo plan') }}</a>
                                    </li>
                                @endif
                                {{--/ Si está identificado, no es un host y SÍ es admin --}}

                                {{-- Si está identificado, no es un host y no es admin --}}
                                @if(!auth()->user()->isAdmin())
                                    <li class="nav-item {{ request()->route()->getName() === "billing.credit_card_form" ? "active" : "" }}">
                                        <a class="nav-link" href="{{ route('billing.credit_card_form') }}">{{ __('Mi tarjeta') }}</a>
                                    </li>
                                @endif
                                {{--/ Si está identificado, no es un host y no es admin --}}

                                {{-- Si está identificado y no es un host --}}

                                <li class="nav-item {{ request()->route()->getName() === "plans.index" ? "active" : "" }}">
                                    <a class="nav-link" href="{{ route('plans.index') }}">{{ __('Planes disponibles') }}</a>
                                </li>
                            @else
                                <li class="nav-item {{ request()->route()->getName() === "tenants.products.index" ? "active" : "" }}">
                                    <a class="nav-link" href="{{ route('tenants.products.index') }}">{{ __('Productos') }}</a>
                                </li>
                            @endif
                            {{--/ Si está identificado y no es un host --}}
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @component('components.alert')@endcomponent
            @yield('content')
        </main>
    </div>
</body>
</html>
