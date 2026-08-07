@extends('backend.v2.layouts.default')
@section('title', __('Danh sách tài khoản'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script
        src="{{ asset('backend/v2/customes/js/gateway-account.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script type="text/javascript">
        gatewayAccount.index();
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Cổng thanh toán</a></li>
                            <li class="breadcrumb-item active">Danh sách tài khoản</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <div class="btn-group">
                            <button type="button"
                                class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                    class="fas fa-search"></i> Tìm
                                kiếm <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                <form id="table-filer" autocomplete="off">
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormCode">Tên tài khoản</label>
                                        <input type="text" name="gateway_accounts.name" class="form-control"
                                            placeholder="Tên tài khoản">
                                    </div>
                                    <div class="mb-2">
                                        <label>Ngày tạo</label>
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
                        <button type="button" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#modal-add"><i class="fas fa-plus"></i> Thêm mới</button>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Danh sách tài khoản</h4>
                        <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-list"
                                        data-ajax-url="{{ route('backend.gateway-account.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="gateway_accounts"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-center" data-priority="2">Cổng</th>
                                                <th class="text-center" data-priority="3">Tài khoản</th>
                                                <th class="text-center" data-priority="4">Số dư</th>
                                                <th class="text-center" data-priority="4">Số dư tạm giữ</th>
                                                <th class="text-center" data-priority="4">Trạng thái</th>
                                                <th class="text-center" data-priority="5">Ngày tạo</th>
                                                <th class="text-center" data-priority="6">Ngày cập nhật</th>
                                                <th class="text-center" data-priority="6">Chức năng</th>
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

    <!-- Modal Edit -->
    <div id="modal-edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0">Sửa thông tin tài khoản cổng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Chọn cổng thanh toán</label>
                            <select class="form-select js-data-select2" name="gateway_id" id="edit-gateway_id" data-ajax-url="{{ route('backend.gateway.ajax-select2-get-list') }}" style="width: 100%">
                                <option value="">Chọn cổng</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên hiển thị</label>
                            <input type="text" class="form-control" name="name" id="edit-name" placeholder="Tên hiển thị">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit-username"
                                placeholder="Username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                            <input type="password" class="form-control" name="password" id="edit-password"
                                placeholder="Mật khẩu mới">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status_id" id="edit-status_id">
                                <option value="1">Chờ kích hoạt</option>
                                <option value="2">Kích hoạt</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã rút tiền mới (bỏ trống nếu không đổi)</label>
                            <input type="password" class="form-control" name="payout_pin" id="edit-payout_pin"
                                placeholder="Mã rút tiền mới">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tenant</label>
                            <input type="text" class="form-control" name="tenant" id="edit-tenant" placeholder="Tenant">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="button" class="btn btn-warning waves-effect waves-light"
                                onclick="gatewayAccount.generateKeyEdit()">Generate Key</button>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Private Key</label>
                            <textarea class="form-control" name="private_key" id="edit-private_key" rows="5"
                                placeholder="Private Key"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Public Key</label>
                            <textarea class="form-control" name="public_key" id="edit-public_key" rows="5"
                                placeholder="Public Key"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        onclick="gatewayAccount.update()">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add -->
    <div id="modal-add" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0">Thêm mới tài khoản cổng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên tài khoản</label>
                            <input type="text" class="form-control" name="name" placeholder="Tên tài khoản (định danh)">
                        </div>
                        <div class="col-md-6 mb-3 ajax-select2">
                            <label class="form-label">Chọn cổng</label>
                            <select class="form-select js-data-select2" name="gateway_id" id="gateway-id-select"
                                data-ajax-url="{{ route('backend.gateway.ajax-select2-get-list') }}" style="width: 100%">
                                <option value="">Chọn cổng</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" name="password" placeholder="Mật khẩu">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status_id">
                                <option value="1">Chờ kích hoạt</option>
                                <option value="2">Kích hoạt</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã rút tiền (Payout pin)</label>
                            <input type="password" class="form-control" name="payout_pin" placeholder="Mã rút tiền">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tenant</label>
                            <input type="text" class="form-control" name="tenant" placeholder="Tenant">
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Private Key & Public Key</label>
                                <button type="button" class="btn btn-sm btn-info"
                                    onclick="gatewayAccount.generateKey()">Generate Key</button>
                            </div>
                            <textarea class="form-control mb-2" name="private_key" placeholder="Private Key" rows="3"
                                readonly></textarea>
                            <textarea class="form-control" name="public_key" placeholder="Public Key" rows="3"
                                readonly></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        onclick="gatewayAccount.add()">Lưu</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection