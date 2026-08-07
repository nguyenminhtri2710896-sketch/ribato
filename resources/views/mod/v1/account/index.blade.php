@extends('mod.v1.layouts.default')
@section('title', __('Tài khoản'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('mod/v1/customes/js/account.js') }}?v={{ config('app.asset_version').time() }}"></script>
    <script type="text/javascript">
        var strUrlAjaxAccountGetInfo = '{{ route('mod.account.ajax-getInfo') }}';
        var strUrlAjaxAccountGetAuthy2factor = '{{ route('mod.account.ajax-get-authy-2factor') }}';
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
                @include('mod.v1.account.left-info')
            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body border-bottom card-custom-header">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">Cài đặt bảo mật</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox" id="SwitchAuthy2factor" {{ auth()->user()->authy_2factor ? 'checked' : '' }}>
                                    <label class="form-check-label" for="SwitchAuthy2factor">Bảo mật 2 lớp (Sử dụng cho đăng nhập và yêu cầu rút tiền)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('mod.v1.account.modal.change-password')
    @include('mod.v1.account.modal.update-profile')
    @include('mod.v1.account.modal.authy-2factor')
    @include('mod.v1.account.modal.cancel-authy-2factor')
@endsection