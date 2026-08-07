@extends('backend.v2.layouts.default')
@section('title', __('Thống kê người dùng'))
@section('style')
@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/libs/apexcharts/apexcharts.min.js') }}?v={{ config('app.asset_version') }}">
    </script>
    <script src="{{ asset('backend/v2/customes/js/report.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script type="text/javascript">
        var urlAjaxReportTop10User = "{{ route('backend.report.ajax-get-top-user-list') }}";
        report.user();
    </script>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    {{-- <h4 class="mb-sm-0 font-size-18">Danh sách thanh toán</h4> --}}
                    <div class="page-title-left">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Thống kê</a></li>
                            <li class="breadcrumb-item active">Thống kê người dùng</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">Biểu đồ thông tin khách</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="top-10-user" data-colors='["--bs-success","--bs-primary", "--bs-danger"]'
                            class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">Danh sách thống kê</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                            <div class="row">
                                                <div class="col-sm-12" data-pattern="priority-columns">
                                                    <table id="data-table-list"
                                                        data-ajax-url="{{ route('backend.report.ajax-get-list-revenue-paymenthot') }}"
                                                        data-id-filter="table-filer" data-key="reports"
                                                        class="table  table-striped dt-responsive  wrap w-100">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center" data-priority="1">#</th>
                                                                <th class="text-left" data-priority="2">Đối tác</th>
                                                                <th class="text-center" data-priority="2">Số dư</th>
                                                                <th class="text-right" data-priority="2">Tiền vào</th>
                                                                <th class="text-center" data-priority="5">Tiền ra</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div> -->
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>
@endsection