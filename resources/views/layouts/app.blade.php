<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name') . ' - Suivi Travaux')</title>

    {{-- Bootstrap CSS (local) --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    {{-- Bootstrap Icons (CDN - fichier local tronqué) --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    {{-- Dashboard styles with orange button overrides --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">

    {{-- Header styles (shared across pages) --}}
    <link rel="stylesheet" href="{{ asset('css/includes/header.css') }}">
    {{-- Footer styles (shared across pages) --}}
    <link rel="stylesheet" href="{{ asset('css/includes/footer.css') }}">

    {{-- Global Orange Theme Override --}}
    <link rel="stylesheet" href="{{ asset('css/theme-green.css') }}">

    @stack('styles')
</head>
<body>

    @include('includes.header')

    <main>

                 <br>
                 <br>
        @yield('content')

        
    </main>

    @include('includes.footer')

    {{-- Bootstrap JS (local) --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
