@extends('backend.v2.layouts.default')
@section('title', __('Tài khoản'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/customes/js/account.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var strUrlAjaxAccountGetInfo = '{{ route('backend.account.ajax-getInfo') }}';
        var strUrlAjaxAccountGetAuthy2factor = '{{ route('backend.account.ajax-get-authy-2factor') }}';
        account.index();
    </script>

@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Profile</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Contacts</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4">
                @include('backend.v2.account.left-info')
            </div>

            <div class="col-xl-8">

                

                <div class="card">
                    <div class="card-body border-bottom card-custom-header">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">Lịch sử đăng nhập</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-list"
                                        data-ajax-url="{{ route('backend.account.ajax-get-list-signin-logs') }}"
                                        data-key="user_signin_logs" class="table dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-center" data-priority="2">IP</th>
                                                <th class="text-center" data-priority="3">Thông tin thiết bị</th>
                                                <th class="text-center" data-priority="4">Ngày đăng nhập</th>
                                                <th class="text-center" data-priority="5">ID</th>
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
            </div>
        </div>
    </div>
    @include('backend.v2.account.modal.change-password')
    @include('backend.v2.account.modal.update-profile')
    @include('backend.v2.account.modal.update-public-key')
    @include('backend.v2.account.modal.update-webhook-url')
    @include('backend.v2.account.modal.authy-2factor')
    @include('backend.v2.account.modal.cancel-authy-2factor')
    @include('backend.v2.account.modal.otp-withdraw')
@endsection