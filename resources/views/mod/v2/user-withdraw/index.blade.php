@extends('mod.v2.layouts.default')
@section('title', __('Yêu cầu rút tiền'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('mod/v2/customes/js/user-withdraw.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxUserWithdrawExportExcel = '{{ route('mod.user-withdraw.ajax-export-excel') }}';
        userWithdraw.index();
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
                            <li class="breadcrumb-item active">Yêu cầu rút tiền</li>
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
                                            <label class="form-label" for="exampleDropdownFormRefCode">Mã giao dịch</label>
                                            <input type="text" name="trans_code" class="form-control"
                                                placeholder="Mã giao dịch">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">Tên chủ khoản</label>
                                            <input type="text" name="bank_account_name" class="form-control"
                                                placeholder="Tên chủ khoản">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRemark">Nội dung chuyển khoản</label>
                                            <input type="text" name="remark" class="form-control"
                                                placeholder="Nội dung chuyển khoản">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">Trạng thái</label>
                                            <select name="status_id" style="width:100%" class="base-select2">
                                                <option value="">==== Tất cả ===</option>
                                                @php
                                                    foreach (\App\Services\UserWithdrawService::$arrStatusId as $id => $arrStatusId):
                                                @endphp
                                                <option value="{{ $id }}">{{ $arrStatusId["name"] }}</option>
                                                @php
                                                    endforeach;
                                                @endphp
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
                        <h4 class="card-title">Danh sách yêu cầu rút tiền</h4>
                        <div id="datatable_wrapper"
                            class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-user-withdraw-list"
                                        data-ajax-url="{{ route('mod.user-withdraw.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="user_withdraws"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-left" data-priority="2">Mã</th>
                                                <th class="text-left" data-priority="3">Ngân hàng</th>
                                                <th class="text-left" data-priority="4">Tài khoản</th>
                                                <th class="text-left" data-priority="5">Số tiền yêu cầu</th>
                                                <th class="text-left" data-priority="6">Phí rút</th>
                                                <th class="text-left" data-priority="7">Số tiền khấu trừ</th>
                                                <th class="text-center" data-priority="8">Trạng thái</th>
                                                <th class="text-center" data-priority="9">Ngày tạo</th>
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
