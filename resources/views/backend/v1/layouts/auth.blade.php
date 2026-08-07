<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>Đăng nhập</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Đăng nhập" name="description" />
    <meta content=""Đăng nhập" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/v1/images/favicon.ico') }}?v={{ config('app.asset_version') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('backend/v1/css/bootstrap.min.css') }}?v={{ config('app.asset_version') }}"
        id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('backend/v1/css/icons.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('backend/v1/libs/toastr/build/toastr.min.css') }}?v={{ config('app.asset_version') }}">
    <!-- App Css-->
    <link href="{{ asset('backend/v1/css/app.min.css') }}?v={{ config('app.asset_version') }}" id="app-style"
        rel="stylesheet" type="text/css" />
    @yield('style')

    <script type="text/javascript">
        var assetUrl = '{{ asset('') }}';
        var isMobile = {{ $isMobile == true ? 1 : 0 }};
        var routerName = '{{ request()->route()->getName() }}'
        var urlDaskboard = '{{ route('backend.index.index') }}';
        var urlSignIn = '{{ route('backend.auth.sign-in') }}';
    </script>
</head>

<body>
    @yield('content')
    <!-- JAVASCRIPT -->
    <script src="{{ asset('backend/v1/libs/jquery/jquery.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ config('app.asset_version') }}">
    </script>
    <script src="{{ asset('backend/v1/libs/metismenu/metisMenu.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/simplebar/simplebar.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/node-waves/waves.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/parsleyjs/parsley.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/js/pages/form-validation.init.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/libs/toastr/build/toastr.min.js') }}?v={{ config('app.asset_version') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/v1/js/app.js') }}?v={{ config('app.asset_version') }}"></script>
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
    <script src="{{ asset('backend/v1/customes/js/js-cookie.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v1/customes/js/base.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        base.submitAjaxForm();
    </script>
    @yield('javascript')
</body>

</html>
