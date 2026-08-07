@extends('backend.v2.layouts.default')
@section('title', __('Danh sách người dùng'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/customes/js/sub-user.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script type="text/javascript">
        var urlAjaxSubUserDetail = '{{ route('backend.sub-user.ajax-get-detail') }}';
        var urlAjaxSubUserDelete = '{{ route('backend.sub-user.ajax-delete') }}';
        subUser.index();
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
                            <li class="breadcrumb-item active">Danh sách người dùng</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-success waves-effect  btn-custome waves-light"
                            data-bs-toggle="modal" data-bs-target=".bs-modal-add"><i class="fas fa-plus"></i> Thêm mới
                        </button>
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
                                        <label class="form-label" for="exampleDropdownFormRefCode">Email</label>
                                        <input type="text" name="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormCode">Số điện thoại</label>
                                        <input type="text" name="phone" class="form-control" placeholder="Số điện thoại">
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
                        <h4 class="card-title">Danh sách người dùng</h4>
                        <div>Đường dẫn truy cập cho người dùng phụ <a target="_blank"
                                href="{{ env('APP_URL_SUBUSER') }}">{{ env('APP_URL_SUBUSER') }}</a></div>
                        <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-list"
                                        data-ajax-url="{{ route('backend.sub-user.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="sub_users"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-center" data-priority="2">Họ Tên</th>
                                                <th class="text-center" data-priority="3">Số điện thoại</th>
                                                <th class="text-center" data-priority="4">Email</th>
                                                <th class="text-center" data-priority="4">Trạng thái</th>
                                                <th class="text-center" data-priority="5">Ngày tạo</th>
                                                <th class="text-center" data-priority="5">Ngày cập nhật</th>
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
    @include('backend.v2.sub-user.modal.add')
    @include('backend.v2.sub-user.modal.update')
@endsection