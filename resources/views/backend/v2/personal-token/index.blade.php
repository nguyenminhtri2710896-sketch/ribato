@extends('backend.v2.layouts.default')
@section('title', 'Quản lý Token cá nhân')
@section('style')
    <!-- INSET STYLE SCRIPT -->
@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v1/customes/js/personal-token.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script type="text/javascript">
        var urlAjaxPersonalTokenList = '{{ route('backend.personal-token.ajax-get-list') }}';
        var urlAjaxPersonalTokenAdd = '{{ route('backend.personal-token.ajax-add') }}';
        var urlAjaxPersonalTokenDelete = '{{ route('backend.personal-token.ajax-delete') }}';
        personalToken.index();
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
                            <li class="breadcrumb-item"><a href="{{ route('backend.account.index') }}">{{ __('backend.profile') }}</a></li>
                            <li class="breadcrumb-item active">Quản lý Token cá nhân</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-success waves-effect btn-custome waves-light"
                            data-bs-toggle="modal" data-bs-target=".bs-modal-add"><i class="fas fa-plus"></i> Tạo Token mới
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Danh sách Token cá nhân</h4>
                        <p class="text-muted">Tại đây bạn có thể tạo và quản lý các token cá nhân để kết nối với hệ thống API.</p>
                        <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-list"
                                         data-ajax-url="{{ route('backend.personal-token.ajax-get-list') }}"
                                         data-key="user_tokens"
                                         class="table table-striped dt-responsive wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">#</th>
                                                <th>Tên Token</th>
                                                <th>Mã Token (Secret Key)</th>
                                                <th class="text-center">Quyền Hạn</th>
                                                <th class="text-center">Ngày Hết Hạn</th>
                                                <th class="text-center">Ngày Tạo</th>
                                                <th class="text-center" style="width: 100px;">Hành Động</th>
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
    @include('backend.v2.personal-token.modal.add')
@endsection
