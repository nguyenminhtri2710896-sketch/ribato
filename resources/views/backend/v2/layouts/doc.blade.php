<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('backend/v2/images/favicon.ico') }}?v={{ config('app.asset_version') }}">
    <link href="{{ asset('backend/v2/css/bootstrap.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/v2/css/icons.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/v2/css/app.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/v2/customes/css/custom-style.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet" type="text/css" />
    @yield('style')
</head>
<body style="background-color: #f8f9fa;">
    <div class="py-4">
        @yield('content')
    </div>
    <script src="{{ asset('backend/v2/libs/jquery/jquery.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/libs/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        function switchTab(element, tabId) {
            const container = element.closest('.code-tab-container');
            container.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            element.classList.add('active');
            container.querySelectorAll('.tab-pane-custom').forEach(pane => {
                pane.classList.add('d-none');
            });
            container.querySelector('#' + tabId).classList.remove('d-none');
        }
    </script>
    @yield('javascript')
</body>
</html>
