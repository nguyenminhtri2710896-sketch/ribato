@extends('backend.v1.layouts.default')
@section('title', __('Chi tiết người dùng'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@php 
    $intUserId = request()->input('user_id') ?? '';
@endphp
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v1/customes/js/user.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script src="{{ asset('backend/v1/customes/js/user-fee.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script
        src="{{ asset('backend/v1/customes/js/user-referal-fee.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script
        src="{{ asset('backend/v1/customes/js/user-virtual-account.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script src="{{ asset('backend/v1/customes/js/user-transaction.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxUserDetail = '{{ route('backend.user.ajax-get-detail') }}';
        var urlAjaxUserFeeDetail = '{{ route('backend.user-fee.ajax-get-detail') }}';
        var urlAjaxUserReferalFeeDetail = '{{ route('backend.user-referal-fee.ajax-get-detail') }}';
        user.getProfile({{ $intUserId}});
        userTransaction.getList();
        userFee.getList();
        userReferalFee.getList();
        userVirtualAccount.getList();

        $('.user-form-submit-update').on('user-form-submit-update-success', function () {
            user.getProfile({{ $intUserId}});
        });

    </script>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left  mb-1">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{route('backend.user.index')}}">Người dùng</a></li>
                            <li class="breadcrumb-item active">Thông tin người dùng</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4">
                <div class="card overflow-hidden  text-center  get-profile-info">
                    <div class="bg-primary-subtle">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-primary p-3 user-image-cover"
                                    style="background-image: url('/static/backend/v1/images/profile-img.png'); background-size: contain; background-repeat: no-repeat; background-position: right;">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p>It will seem like simplified</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="avatar-md profile-user-wid-custom">
                                    <img src="{{ asset('backend/v1/images/users/avatar-1.jpg') }}" alt=""
                                        class="img-thumbnail rounded-circle user-image-avatar">
                                </div>
                                <h5 class="font-size-15 text-truncate user-name"></h5>
                                <p class="text-muted mb-0 text-truncate ">
                                    <span class="badge bg-success font-size-11 m-1 user-group"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top">
                        <div class="contact-links d-flex font-size-20">
                            <div class="flex-fill">
                                <a href="javascript: void(0);" data-bs-toggle="modal" data-bs-target=".bs-modal-update"><i
                                        class="bx bx-edit"></i></a>
                            </div>
                            <div class="flex-fill">
                                <a href="javascript: void(0);" data-bs-toggle="modal"
                                    data-bs-target=".bs-modal-change-password"><i class="bx bxs-key"></i></a>
                            </div>
                            <div class="flex-fill">
                                <a href="javascript: void(0);"><i class="bx bx-info-circle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Thông tin phí</h4>
                                <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="data-table-user-fee-list"
                                                data-ajax-url="{{ route('backend.user-fee.ajax-get-list') }}"
                                                data-id-filter="form-filter-user-referal-fee" data-key="user_fees"
                                                data-params="{{json_encode(["user_fees.user_id" => $intUserId])}}"
                                                class="table  table-striped dt-responsive  wrap w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center desktop col-number" data-priority="1">#
                                                        </th>
                                                        <th class="text-left desktop" data-priority="1">Loại phí</th>
                                                        <th class="text-left desktop" data-priority="2">Phí</th>
                                                        <th class="text-left desktop" data-priority="2">Phí tối thiểu</th>
                                                        <th class="text-left desktop" data-priority="3">Trạng thái</th>
                                                        <th class="text-left desktop" data-priority="4">Chức năng</th>
                                                    </tr>
                                                </thead>

                                                <tbody class="fee-list">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Thông tin phí người giới thiệu</h4>
                                <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="data-table-user-referal-fee-list"
                                                data-ajax-url="{{ route('backend.user-referal-fee.ajax-get-list') }}"
                                                data-id-filter="form-filter-user-fee" data-key="user_referal_fees"
                                                data-params="{{json_encode(["user_referal_fees.user_id" => $intUserId])}}"
                                                class="table  table-striped dt-responsive  wrap w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center desktop col-number" data-priority="1">#
                                                        </th>
                                                        <th class="text-left desktop" data-priority="1">Loại phí</th>
                                                        <th class="text-left desktop" data-priority="2">Phí</th>
                                                        <th class="text-left desktop" data-priority="2">Phí tối thiểu</th>
                                                        <th class="text-left desktop" data-priority="3">Trạng thái</th>
                                                        <th class="text-left desktop" data-priority="4">Chức năng</th>
                                                    </tr>
                                                </thead>

                                                <tbody class="fee-list">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Hạn mức rút tiền</h4>
                                <form
                                    class="form-horizontal needs-validation frm-ajax-submit user-form-submit-withdraw-limit get-profile-info"
                                    novalidate autocomplete="off" method="POST"
                                    data-ajax-url="{{ route('backend.user.ajax-update-withdraw-limit') }}">
                                    <input type="hidden" name="user_id" value="{{ $intUserId }}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="has-validation">
                                                    <label class="mt-2 mb-1">Số tiền rút tối đa trong ngày<span
                                                            class="text-danger">(*)</span></label>
                                                    <input type="text" class="form-control format-number"
                                                        name="withdraw_limit_in_day" placeholder="Số tiền rút trong ngày"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="has-validation">
                                                    <label class="mt-2 mb-1">Số tiền rút tối đa một tài khoản trong
                                                        ngày<span class="text-danger">(*)</span></label>
                                                    <input type="text" class="form-control format-number"
                                                        name="withdraw_limit_bank_account_in_day"
                                                        placeholder="Số tiền một tài khoản rút trong ngày" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="has-validation">
                                                    <label class="mt-2 mb-1">Ngày được phép rút tiền</label>
                                                    <select name="allow_withdraw_day[]" style="width:100%"
                                                        class="base-select2 form-control" multiple>
                                                        <option value="2">Thứ 2</option>
                                                        <option value="3">Thứ 3</option>
                                                        <option value="4">Thứ 4</option>
                                                        <option value="5">Thứ 5</option>
                                                        <option value="6">Thứ 6</option>
                                                        <option value="7">Thứ 7</option>
                                                        <option value="8">Chủ nhật</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="form-check form-switch mt-3 mb-1">
                                                    <input class="form-check-input" type="checkbox" id="lockWithdrawCheck"
                                                        name="lock_withdraw" value="1">
                                                    <label class="form-check-label" for="lockWithdrawCheck">Khóa rút
                                                        tiền</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mt-3 text-right">
                                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                                    Lưu lại</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
            </div>
            <div class="col-xl-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-2">Tổng giao dịch</p>
                                        <h4 class="mb-0">xx</h4>
                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="bx bx-check-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-2">Tồng đơn hàng</p>
                                        <h4 class="mb-0">xx</h4>
                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="avatar-sm mini-stat-icon rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="bx bx-package font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-2">Tổng tiền giao dịch</p>
                                        <h4 class="mb-0">xx</h4>
                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="avatar-sm mini-stat-icon rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="bx bx-dollar font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div class="page-title-left  mb-1">
                                <ol class="breadcrumb m-0">
                                    <!-- <h4 class="card-title">Danh sách VA</h4> -->
                                </ol>
                            </div>
                            <div class="page-title-right">
                                <div class="btn-group  d-md-inline-block d-block d-grid mb-1">
                                    <button type="button"
                                        class="btn btn-success waves-effect  btn-lg-custom btn-custome waves-light"
                                        data-bs-toggle="modal" data-bs-target=".bs-modal-add-user-virtual-account"><i
                                            class="fas fa-plus"></i>
                                        Tạo VA
                                    </button>
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
                                <h4 class="card-title">Danh sách VA</h4>
                                <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="data-table-user-virtual-account-list"
                                                data-ajax-url="{{ route('backend.user-virtual-account.ajax-get-list') }}"
                                                data-id-filter="form-filter-user-virtual_account"
                                                data-key="user_virtual_accounts"
                                                data-params="{{json_encode(["user_virtual_accounts.user_id" => $intUserId])}}"
                                                class="table  table-striped dt-responsive  wrap w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center desktop col-number" data-priority="1">#
                                                        </th>
                                                        <th class="text-left desktop" data-priority="2">Cổng</th>
                                                        <th class="text-left desktop" data-priority="2">Ngân hàng</th>
                                                        <th class="text-left desktop" data-priority="2">Chủ khoản</th>
                                                        </th>
                                                        <th class="text-left desktop" data-priority="2">Số TK
                                                        </th>
                                                        <th class="text-center desktop" data-priority="3">Chức năng</th>
                                                        <th class="text-center desktop" data-priority="7">Ngày tạo</th>
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


                <div class="row">
                    <div class="col-12">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div class="page-title-left  mb-1">
                                <ol class="breadcrumb m-0">
                                    <!-- <h4 class="card-title">Lịch sử giao dịch</h4> -->
                                </ol>
                            </div>
                            <div class="page-title-right">
                                <div class="btn-group  d-md-inline-block d-block d-grid mb-1">
                                    <button type="button"
                                        class="btn btn-success waves-effect  btn-lg-custom btn-custome waves-light"
                                        data-bs-toggle="modal" data-bs-target=".bs-modal-add-money"><i
                                            class="fas fa-plus"></i>
                                        Cộng tiền
                                    </button>
                                </div>
                                <div class="btn-group  d-md-inline-block d-block d-grid mb-1">
                                    <button type="button"
                                        class="btn btn-danger waves-effect  btn-lg-custom btn-custome waves-light"
                                        data-bs-toggle="modal" data-bs-target=".bs-modal-deduct-money"><i
                                            class="fas fa-minus"></i>
                                        Trừ tiền
                                    </button>
                                </div>
                                <div class="btn-group ajax-select2 d-md-inline-block d-block  d-grid mb-1">
                                    <button type="button"
                                        class="waves-effect waves-light btn btn-info btn-lg-custom btn-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                            class="fas fa-search"></i> Tìm
                                        kiếm <i class="mdi mdi-chevron-down"></i>
                                    </button>

                                    <div
                                        class="dropdown-menu dropdown-table-filter dropdown-menu-md p-3 dropdown-menu-search">
                                        <form id="form-filter-user-transaction" autocomplete="off">
                                            <div class="mb-2">
                                                <label class="form-label">Mã giao dịch</label>
                                                <input type="text" name="user_transactions.trans_code"
                                                    class="form-control form-control-custom" placeholder="Mã giao dịch">
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Loại giao dịch</label>
                                                <select name="user_transactions.type_id" style="width:100%"
                                                    class="base-select2">
                                                    <option value="">Tắt cả</option>
                                                    @foreach (\App\Services\UserTransactionService::$arrTypeId as $intTypeId => $arrTypeId)
                                                        <option {{ request()->get('type_id') == $intTypeId ? 'selected' : '' }}
                                                            value="{{ $intTypeId }}">{{ $arrTypeId['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label>Ngày tạo giao dịch</label>
                                                <div class="input-daterange input-group" id="created-at"
                                                    data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                                    data-provide="datepicker" data-date-container="#created-at">
                                                    <input type="text" class="form-control form-control-custom"
                                                        name="user_transactions.created_at_from" placeholder="Từ ngày">
                                                    <input type="text" class="form-control form-control-custom"
                                                        name="user_transactions.created_at_to" placeholder="Đến ngày">
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
                <!-- end page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Danh sách giao dịch nạp rút</h4>
                                <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="data-table-user-transaction-list"
                                                data-ajax-url="{{ route('backend.user-transaction.ajax-get-list') }}"
                                                data-id-filter="form-filter-user-transaction" data-key="user_transactions"
                                                data-params="{{json_encode(["user_transactions.user_id" => $intUserId])}}"
                                                class="table  table-striped dt-responsive  wrap w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center desktop col-number" data-priority="1">#
                                                        </th>
                                                        <th class="text-left desktop" data-priority="2">Mã giao dịch</th>
                                                        <th class="text-left desktop" data-priority="2">Loại</th>
                                                        <th class="text-left desktop" data-priority="3">Nội dung</th>
                                                        <th class="text-left desktop" data-priority="4">Số tiền</th>
                                                        <th class="text-center desktop" data-priority="5">Tổng tiền</th>
                                                        <th class="text-center desktop" data-priority="7">Ngày tạo</th>
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
        </div>
    </div>
    @include('backend.v1.user.modal.update')
    @include('backend.v1.user.modal.add-money')
    @include('backend.v1.user.modal.deduct-money')
    @include('backend.v1.user.modal.change-password')
    @include('backend.v1.user-fee.modal.update')
    @include('backend.v1.user-referal-fee.modal.update')
    @include('backend.v1.user.modal.add-virtual-account')
@endsection