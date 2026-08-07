@extends('backend.v1.layouts.default')
@section('title', __('Chi tiết giao dịch cổng'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@php 
    $intGatewayAccountId = request()->input('gateway_account_id') ?? '';
@endphp
@section('javascript')
    <!-- <script
        src="{{ asset('backend/v1/customes/js/gateway-account.js') }}?v={{ config('app.asset_version') }}"></script> -->
    <script
        src="{{ asset('backend/v1/customes/js/gateway-account-transaction.js') }}?v={{ config('app.asset_version').time() }}"></script>
    <script type="text/javascript">
        gatewayAccountTransaction.getList();
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
                            <li class="breadcrumb-item"><a href="{{route('backend.user.index')}}">Cổng</a></li>
                            <li class="breadcrumb-item active">Danh sách giao dịch tài khoản</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
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
                                        <form id="form-filter-gateway-account-transaction" autocomplete="off">
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
                                            <table id="data-table-gateway-account-transaction-list"
                                                data-ajax-url="{{ route('backend.gateway-account-transaction.ajax-get-list') }}"
                                                data-id-filter="form-filter-gateway-account-transaction"
                                                data-key="gateway_account_transactions"
                                                data-params="{{json_encode(["gateway_account_transactions.gateway_account_id" => $intGatewayAccountId])}}"
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
    @include('backend.v1.gateway-account.modal.add-money')
    @include('backend.v1.gateway-account.modal.deduct-money')
@endsection