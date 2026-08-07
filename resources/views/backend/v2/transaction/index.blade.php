@extends('backend.v2.layouts.default')
@section('title', __('Danh sách giao dịch'))
@section('style')
@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/customes/js/transaction.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxTransactionCreateQrPayment = '{{ route('backend.transaction.ajax-create-qr-payment') }}';
        var urlAjaxAccountCreateQrPayment = '{{ route('backend.account.ajax-create-qr-payment') }}';
        var urlAjaxTransactionExportExcel = '{{ route('backend.transaction.ajax-export-excel') }}';
        transaction.index();
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Giao dịch</a></li>
                            <li class="breadcrumb-item active">Danh sách giao dịch</li>
                        </ol>
                    </div>
                    <div class="page-title-right ajax-select2">
                        <!-- <div class="btn-group  d-md-inline-block d-block d-grid mb-0  mt-1">
                            <button type="button" class="btn btn-lg-custom btn-success dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-plus-circle"></i>
                                Tạo QR
                                <i class="mdi mdi-chevron-down"></i></button>
                            <div class="dropdown-menu action-dropdown-menu-mobile" style="">
                                <a class="dropdown-item  btn-create-qr-transaction" href="#"><i
                                        class="fas fa-plus-circle"></i> Tạo QR giao dịch</a>
                                <a class="dropdown-item  btn-create-qr-user" href="#"><i class="fas fa-plus-circle"></i>Tạo
                                    QR cá nhân</a>
                            </div>
                        </div> -->
                        <div class="btn-group  d-md-inline-block d-block d-grid mb-0 mt-1">
                            <button type="button"
                                class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                    class="fas fa-search"></i> Tìm
                                kiếm <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                <form id="table-filer" autocomplete="off">
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">Mã đơn</label>
                                        <input type="text" name="ref_code" class="form-control" placeholder="Mã đơn">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormCode">Mã Giao dịch</label>
                                        <input type="text" name="code" class="form-control" placeholder="Mã giao dịch">
                                    </div>
                                    @if (auth()->user()->is_admin)
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">Tên VA</label>
                                            <input type="text" name="bank_account_name" class="form-control"
                                                placeholder="Tên VA">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">Người dùng</label>
                                            <select name="list_user_id[]" style="width:100%" class="js-data-select2"
                                                multiple="true"
                                                data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}">
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">Nội dung</label>
                                            <input type="text" name="content" class="form-control"
                                                placeholder="Nội dung chuyển khoản">
                                        </div>
                                    @endif
                                    <div class="mb-2">
                                        <label class="form-label" for="bank_account_number">Tài khoản VA</label>
                                        <select name="bank_account_number" style="width:100%" class="js-data-select2"
                                            data-ajax-url="{{ route('backend.virtual-account.ajax-select2-get-list') }}">
                                        </select>
                                    </div>
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
                                    <div class="mb-2">
                                        <label>Ngày cập nhật giao dịch</label>

                                        <div class="input-daterange input-group" id="updated-at"
                                            data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                            data-provide="datepicker" data-date-container="#updated-at">
                                            <input type="text" class="form-control" name="updated_at_from"
                                                placeholder="Từ ngày">
                                            <input type="text" class="form-control" name="updated_at_to"
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
                    <div class="card-body border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">Danh sách thanh toán</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="datatable_wrapper"
                            class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12" data-pattern="priority-columns">
                                    <table id="data-table-list"
                                        data-ajax-url="{{ route('backend.transaction.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="transactions"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1"># </th>
                                                <th class="text-left" data-priority="2">Mã đơn</th>
                                                <th class="text-center" data-priority="3">Mã giao dịch</th>
                                                <th class="text-right" data-priority="4">Số tiền</th>
                                                <th class="text-right" data-priority="5">Phí</th>
                                                <th class="text-right" data-priority="6">Số tiền thực nhận</th>
                                                <th class="text-center" data-priority="7" style="width:20%">Nội dung</th>
                                                <th class="text-center" data-priority="8">Bank nhận</th>
                                                <th class="text-center" data-priority="9">Trạng thái</th>
                                                <th class="text-center" data-priority="10">Loại giao dịch</th>
                                                <th class="text-center" data-priority="11">Ngày tạo giao dịch</th>
                                                <!-- <th class="text-center" data-priority="12">Ngày nhận giao dịch</th> -->
                                                <th class="text-center" data-priority="12">Ngày cập nhật</th>
                                                {{-- <th class="text-center" data-priority="5">Thao tác</th> --}}
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
    @include('backend.v2.transaction.modal.transaction-qrcode')
    @include('backend.v2.transaction.modal.user-qrcode')

@endsection