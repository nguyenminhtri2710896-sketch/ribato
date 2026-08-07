@extends('backend.v2.layouts.default')
@section('title', __('Lịch sử gửi và rút'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/customes/js/user-transaction.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxUserTransactionExportExcel = '{{ route('backend.user-transaction.ajax-export-excel') }}';
        userTransaction.index();
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Lịch sử giao dịch</a></li>
                            <li class="breadcrumb-item active">Lịch sử nạp rút</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <div class="btn-group ajax-select2  d-md-inline-block d-block  d-grid">
                            <button type="button"
                                class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                    class="fas fa-search"></i> Tìm
                                kiếm <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                <form id="table-filer" autocomplete="off">
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">Mã giao dịch</label>
                                        <input type="text" name="trans_code" class="form-control"
                                            placeholder="Mã giao dịch">
                                    </div>
                                    @if (auth()->user()->is_admin)
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">Người dùng</label>
                                            <select name="user_id" style="width:100%" class="js-data-select2"
                                                data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}">
                                            </select>
                                        </div>
                                    @endif
                                    <div class="mb-2">
                                        <label>Ngày tạo giao dịch</label>
                                        <div class="input-daterange input-group" id="created-at"
                                            data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                            data-provide="datepicker" data-date-container="#created-at">
                                            <input type="text" class="form-control" name="created_at_from"
                                                placeholder="Từ ngày">
                                            <input type="text" class="form-control" name="created_at_to"
                                                placeholder="Đến ngày">
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 mb-2">
                                        <button class="btn btn-info  btn-secondary btn-block"><i class="fa fa-search"></i>
                                            Tìm</button>
                                    </div>
                                </form>
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
                        <h4 class="card-title">Danh sách giao dịch nạp rút</h4>
                        <div id="datatable_wrapper" class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-user-transaction-list"
                                        data-ajax-url="{{ route('backend.user-transaction.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="user_transactions"
                                        class="table  table-striped  dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-left" data-priority="2">Mã</th>
                                                <th class="text-left" data-priority="3">Loại</th>
                                                <th class="text-left" data-priority="3">Nội dung</th>
                                                <th class="text-left" data-priority="4">Số tiền</th>
                                                <th class="text-center" data-priority="5">Tổng tiền </th>
                                                <th class="text-center" data-priority="7">Ngày tạo</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="footer-button">
                                <button type="button"
                                    class="waves-effect waves-light btn btn-info btn-lg-custom btn-export-excel"><i
                                        class="far fa-file-excel"></i> Xuất excel</button>

                            </div>
                            <div class="footer-note">

                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>
@endsection