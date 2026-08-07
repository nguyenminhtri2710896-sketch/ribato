<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/v1/images/favicon.ico') }}?v={{ config('app.asset_version') }}">

    <link href="{{ asset('backend/v1/css/bootstrap.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet"
        type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('backend/v1/css/icons.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('backend/v1/css/app.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('backend/v1/libs/sweetalert2/sweetalert2.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('backend/v1/libs/toastr/build/toastr.min.css') }}?v={{ config('app.asset_version') }}">
    <link href="{{ asset('backend/v1/customes/css/custom-style.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />
    @yield('style')
    <script type="text/javascript">

    </script>
</head>

<body>
    @yield('content')

    <!-- JAVASCRIPT -->
    <script src="{{ asset('backend/v1/libs/jquery/jquery.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/parsleyjs/parsley.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v1/libs/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v1/libs/metismenu/metisMenu.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v1/libs/toastr/build/toastr.min.js') }}?v={{ config('app.asset_version') }}"></script>

    <script
        src="{{ asset('backend/v1/libs/simplebar/simplebar.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/node-waves/waves.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v1/js/pages/form-validation.init.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/sweetalert2/sweetalert2.min.js') }}?v={{ config('app.asset_version') }}">
    </script>
    <script src="{{ asset('backend/v1/js/app.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v1/js/pages/simple.ajax.uploader.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": 300,
            "hideDuration": 1000,
            "timeOut": 5000,
            "extendedTimeOut": 1000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
    <script src="{{ asset('backend/v1/customes/js/base.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        base.submitAjaxForm();
        base.basicImageUpload();
    </script>
    @yield('javascript')
</body>

</html>