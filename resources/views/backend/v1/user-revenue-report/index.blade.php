@extends('backend.v1.layouts.default')
@section('title', __('Thống kê'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v1/customes/js/user-revenue-report.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxUserRevenueReportExport = "{{ route('backend.user-revenue-report.ajax-export-excel') }}";
        userRevenueReport.index();
    </script>
@endsection
@section('content')
                                                            <div class="container-fluid">
                                                                <!-- start page title -->
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                                                            <div class="page-title-left">
                                                                                <ol class="breadcrumb m-0">
                                                                                    <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                                                                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Thống kê</a></li>
                                                                                    <li class="breadcrumb-item active">Thống kê doanh số</li>
                                                                                </ol>
                                                                            </div>
                                                                            <div class="page-title-right">
                                                                                <div class="btn-group ajax-select2  d-md-inline-block d-block  d-grid">
                                                                                    <div class="btn-group ajax-select2 mt-1">
                                                                                        <button type="button"
                                                                                            class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                                                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                                                                class="fas fa-search"></i> Tìm
                                                                                            kiếm <i class="mdi mdi-chevron-down"></i>
                                                                                        </button>
                                                                                        <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                                                                            <form id="table-filer" autocomplete="off">
                                                                                                <div class="mb-2">
                                                                                                    <label class="form-label" for="exampleDropdownFormRefCode">Người dùng</label>
                                                                                                    <select name="user_revenue_reports.user_id[]" style="width:100%" class="js-data-select2"
                                                                                                        multiple="true"
                                                                                                        data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}">
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label class="form-label" for="exampleDropdownFormRefCode">Loại</label>
                                                                                                    <select name="user_revenue_reports.type_id" style="width:100%" class="base-select2">
                                                                                                        <option value="">==== Tất cả ===</option>
                                                                                                        @php
    foreach (\App\Services\UserRevenueReportService::$arrTypeId as $id => $arrTypeId):
                                                                                                        @endphp
                                                                                                        <option value="{{ $id }}">{{ $arrTypeId["name"] }}</option>
                                                                                                        @php
    endforeach;
                                                                                                        @endphp
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label>Ngày tạo giao dịch</label>
                                                                                                    <div class="input-daterange input-group" id="report-at"
                                                                                                        data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                                                                                        data-provide="datepicker" data-date-container="#report-at">
                                                                                                        <input type="text" class="form-control" name="report_at_from"
                                                                                                            placeholder="Từ ngày">
                                                                                                        <input type="text" class="form-control" name="report_at_to"
                                                                                                            placeholder="Đến ngày">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-grid gap-2 mb-2">
                                                                                                    <button class="btn btn-info  btn-secondary btn-block"><i
                                                                                                            class="fa fa-search"></i>
                                                                                                        Tìm</button>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- end page title -->
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <h4 class="card-title">Thống kê doanh số</h4>
                                                                                <div id="datatable_wrapper"
                                                                                    class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                                                                                    <div class="row">
                                                                                        <div class="col-sm-12">
                                                                                            <table id="data-table-user-revenue-report-list"
                                                                                                data-ajax-url="{{ route('backend.user-revenue-report.ajax-get-list') }}"
                                                                                                data-id-filter="table-filer" data-key="user_revenue_reports" data-header-view="user_revenue_sum_report"
                                                                                                class="table  table-striped dt-responsive  wrap w-100">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th class="text-center" data-priority="1">#</th>
                                                                                                        <th class="text-left" data-priority="2">Khách hàng</th>
                                                                                                        <th class="text-left total_referal_fee" data-priority="3">Phí người giới thiệu<br /><span class="val text-danger"></span></th>
                                                                                                        <th class="text-left total_gateway_fee" data-priority="4">Phí cổng<br/><span class="val text-danger"></span></th>
                                                                                                        <th class="text-left total_profit" data-priority="5">Lợi nhuận<br /><span class="val text-danger"></span></th>
                                                                                                        <th class="text-left total_transaction_amount" data-priority="6">Tổng tiền giao dịch<br /><span class="val text-danger"></span></th>
                                                                                                        <th class="text-left total_transaction_fee" data-priority="7">Tổng phí giao dịch<br /><span class="val text-danger"></span></th>
                                                                                                        <th class="text-center" data-priority="8">Loại giao dịch</th>
                                                                                                        <th class="text-center" data-priority="9">Ngày chốt</th>
                                                                                                    </tr>
                                                                                                </thead>

                                                                                                <tbody>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="footer-button">
                                                                                        <button type="button"
                                                                                            class="waves-effect waves-light btn btn-info btn-lg-custom btn-export-user-revenue-report"><i
                                                                                                class="far fa-file-excel"></i> Xuất excel</button>
                                                                                    </div>
                                                                                    <div class="footer-note"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div> <!-- end col -->
                                                                </div>
                                                            </div>
@endsection