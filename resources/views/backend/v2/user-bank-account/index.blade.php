@extends('backend.v2.layouts.default')
@section('title', __('Danh sách tài khoản ngân hàng'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/customes/js/user-bank-account.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxUserBankAccountDetail = '{{ route('backend.user-bank-account.ajax-get-detail') }}';
        var urlAjaxUserBankAccountDelete = '{{ route('backend.user-bank-account.ajax-delete') }}';
        userBankAccount.index();
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Người dùng</a></li>
                            <li class="breadcrumb-item active">Danh sách tài khoản ngân hàng</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-success waves-effect  btn-custome waves-light"
                            data-bs-toggle="modal" data-bs-target=".bs-modal-user-bank-account-add"><i class="fas fa-plus"></i> Thêm mới
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
                                        <label class="form-label" for="exampleDropdownFormRefCode">Tài khoản ngân
                                            hàng</label>
                                        <select name="bank_account_id" style="width:100%" class="js-data-select2"
                                            data-ajax-url="{{ route('backend.bank-account.ajax-select2-get-list') }}">
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">Người dùng</label>
                                        <select name="user_id" style="width:100%" class="js-data-select2"
                                            data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}">
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
                        <h4 class="card-title">Danh sách tài khoản ngân hàng</h4>
                        <div id="datatable_wrapper" class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-user-bank-account-list"
                                        data-ajax-url="{{ route('backend.user-bank-account.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="user_bank_accounts"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-center" data-priority="2">Người dùng</th>
                                                <th class="text-center" data-priority="3">Tên chủ khoản</th>
                                                <th class="text-center" data-priority="4">Số tài khoản</th>
                                                <th class="text-center" data-priority="6">Trạng thái</th>
                                                <th class="text-center" data-priority="7">Ngày tạo</th>
                                                <th class="text-center" data-priority="8">Ngày cập nhật</th>
                                                <th class="text-center" data-priority="9">Chức năng</th>
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
    @include('backend.v2.user-bank-account.modal.add')
    @include('backend.v2.user-bank-account.modal.update')
@endsection
