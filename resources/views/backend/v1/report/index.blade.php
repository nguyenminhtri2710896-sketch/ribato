@extends('backend.v1.layouts.default')
@section('title', __('Thống kê người dùng'))
@section('style')
@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v1/libs/apexcharts/apexcharts.min.js') }}?v={{ config('app.asset_version') }}">
    </script>
    <script src="{{ asset('backend/v1/customes/js/report.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script type="text/javascript">
        var urlAjaxReportRevenueByDay = "{{ route('backend.report.ajax-revenues-by-day') }}";
        var urlAjaxReportRevenueByMonth = "{{ route('backend.report.ajax-revenues-by-month') }}";
        var urlAjaxReportProfitChart = "{{ route('backend.report.ajax-profit-chart') }}";
        report.index();
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
                            <li class="breadcrumb-item active">Thống kê tổng hợp</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <div class="d-sm-flex align-items-center justify-content-between  ajax-select2">
                            <h5 class="mb-0 card-title flex-grow-1">Biểu đồ lợi nhuận hệ thống</h5>
                            <form id="profit-chart-filter" class="row gy-2 gx-2 align-items-end text-sm-end">
                                <div class="col-md-6 col-lg-5">
                                    <div class="input-daterange input-group profit-date-range" data-date-format="dd/mm/yyyy"
                                        data-date-autoclose="true" data-provide="datepicker"
                                        data-date-container=".profit-date-range">
                                        <input type="text" class="form-control" name="from_date" placeholder="Từ ngày">
                                        <input type="text" class="form-control" name="to_date" placeholder="Đến ngày">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                        <select name="user_id[]" class="form-control js-data-select2 select-user_id" multiple
                                            data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                            data-text-placeholder="Chọn người dùng">
                                        </select>
                                    </div> 
                                <div class="col-md-2 col-lg-3 text-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                         <div class="profit-chart-summary text-center mb-3">
                            <span class="text-muted text-uppercase small">Tổng lợi nhuận toàn hệ thống</span>
                            <h3 class="mt-1 mb-0 profit-chart-total text-success">0<sup>đ</sup></h3>
                        </div>
                        <div class="profit-chart-summary text-center mb-3">
                            <span class="text-muted text-uppercase small">Tổng lợi nhuận <span class=" profit-chart-range"></span></span>
                            <h3 class="mt-1 mb-1 profit-chart text-success">0<sup>đ</sup></h3>
                            
                        </div>
                    
                        <div id="profit-chart" data-colors='["--bs-primary","--bs-success"]' class="apex-charts"
                            dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">Biểu đồ doanh số theo tháng</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenues-by-month" data-colors='["--bs-success","--bs-primary", "--bs-danger"]'
                            class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>
@endsection