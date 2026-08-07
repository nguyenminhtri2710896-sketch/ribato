@extends('backend.v2.layouts.default')
@section('title', __('Quản lý mã QR'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/customes/js/user-id-qrcode.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxUserWithdrawDetail = '{{ route('backend.user-id-qrcode.ajax-get-detail') }}';
        var urlAjaxUserIdQrcodeDelete = '{{ route('backend.user-id-qrcode.ajax-delete') }}';
        userIdQrcode.index();
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
                            <li class="breadcrumb-item active">Quản lý mã Qr</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-success waves-effect  btn-custome waves-light"
                            data-bs-toggle="modal" data-bs-target=".bs-modal-user-id-qrcode-add"><i class="fas fa-plus"></i>
                            Tạo mới
                        </button>
                        <div class="btn-group ajax-select2">
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
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">Tên chủ khoản</label>
                                        <input type="text" name="bank_account_name" class="form-control"
                                            placeholder="Tên chủ khoản">
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
                        <h4 class="card-title">Danh sách Quản lý mã Qr</h4>
                        <div id="datatable_wrapper" class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-user-id-qrcode-list"
                                        data-ajax-url="{{ route('backend.user-id-qrcode.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="user_id_qrcodes"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-left" data-priority="2">Tên</th>
                                                <th class="text-left" data-priority="3">Thông tin</th>
                                                <th class="text-left" data-priority="4">Hình QR</th>
                                                <th class="text-left" data-priority="5">Ngày tạo</th>
                                                <th class="text-center" data-priority="10">Chức năng</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>
    @include('backend.v2.user-id-qrcode.modal.add')
@endsection