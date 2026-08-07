<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/v2/images/favicon.ico') }}?v={{ config('app.asset_version') }}">
    <!-- DataTables -->
    <link
        href="{{ asset('backend/v2/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />
    <link
        href="{{ asset('backend/v2/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link
        href="{{ asset('backend/v2/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ asset('backend/v2/css/bootstrap.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet"
        type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('backend/v2/css/icons.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('backend/v2/libs/toastr/build/toastr.min.css') }}?v={{ config('app.asset_version') }}">
    <!-- App Css-->
    <link href="{{ asset('backend/v2/css/app.min.css') }}?v={{ config('app.asset_version') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('backend/v2/libs/sweetalert2/sweetalert2.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/v2//libs/select2/css/select2.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />
    <link
        href="{{ asset('backend/v2/libs/admin-resources/rwd-table/rwd-table.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />

    <link
        href="{{ asset('backend/v2/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />
    <link
        href="{{ asset('backend/v2/libs/@chenfengyuan/datepicker/datepicker.min.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />


    <link href="{{ asset('backend/v2/customes/css/custom-style.css') }}?v={{ config('app.asset_version') }}"
        rel="stylesheet" type="text/css" />
    @yield('style')
    <script type="text/javascript">
        var assetUrl = '{{ asset('') }}';
        var isMobile = {{ $isMobile == true ? 1 : 0 }};
        var isAdmin = {{ (int) auth()->user()->is_admin }};
        var isFullAccess = {{ (int) auth()->user()->full_access }};
        var routerName = '{{ request()->route()->getName() }}';
        var urlDaskboard = '{{ route('backend.index.index') }}';
        var urlSignIn = '{{ route('backend.auth.sign-in') }}';
        var urlAjaxSignOut = '{{ route('backend.auth.ajax-sign-out') }}';
        var urlAjaxAccountGetBalance = '{{ route('backend.account.ajax-get-balance') }}';
    </script>
</head>

<body data-sidebar="dark" data-layout-mode="light">
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('backend.v2.partials.header')
        <!-- ========== Left Sidebar Start ========== -->
        <!-- Left Sidebar End -->
        @include('backend.v2.partials.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                @yield('content')
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('backend.v2.partials.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- JAVASCRIPT -->
    <script src="{{ asset('backend/v2/libs/jquery/jquery.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/libs/parsleyjs/parsley.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v2/libs/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v2/libs/metismenu/metisMenu.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v2/libs/simplebar/simplebar.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/libs/node-waves/waves.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v2/libs/toastr/build/toastr.min.js') }}?v={{ config('app.asset_version') }}"></script>

    <!-- Required datatable js -->
    <script
        src="{{ asset('backend/v2/libs/datatables.net/js/jquery.dataTables.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v2/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <!-- Buttons examples -->
    <script
        src="{{ asset('backend/v2/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v2/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script src="{{ asset('backend/v2/libs/jszip/jszip.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/libs/pdfmake/build/pdfmake.min.js') }}?v={{ config('app.asset_version') }}">
    </script>
    <script
        src="{{ asset('backend/v2/libs/pdfmake/build/vfs_fonts.js') }}?v={{ config('app.asset_version') }}"></script>
    <script
        src="{{ asset('backend/v2/libs/datatables.net-buttons/js/buttons.html5.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v2/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}?v={{ config('app.asset_version') }}">
        </script>

    <!-- Responsive examples -->
    <script
        src="{{ asset('backend/v2/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v2/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v2/js/pages/form-validation.init.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/libs/sweetalert2/sweetalert2.min.js') }}?v={{ config('app.asset_version') }}">
    </script>
    <!-- App js -->
    <script src="{{ asset('backend/v2/js/app.js') }}?v={{ config('app.asset_version') }}"></script>

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
    <script
        src="{{ asset('backend/v2/libs/admin-resources/rwd-table/rwd-table.min.js') }}?v={{ config('app.asset_version') }}">
        </script>

    <script
        src="{{ asset('backend/v2/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script
        src="{{ asset('backend/v2/libs/@chenfengyuan/datepicker/datepicker.min.js') }}?v={{ config('app.asset_version') }}">
        </script>
    <script src="{{ asset('backend/v2/customes/js/js-cookie.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/customes/js/jquery-dateformat.min.js') }}?v={{ config('app.asset_version') }}">
    </script>
    <script
        src="{{ asset('backend/v2/customes/js/jquery.number.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/libs/select2/js/select2.min.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('backend/v2/customes/js/base.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        base.submitAjaxForm();
        base.ajaxSelect2();
        base.baseSelect2();
        base.getBalance();
        base.formatInputNumber();
    </script>
    @yield('javascript')
</body>

</html>