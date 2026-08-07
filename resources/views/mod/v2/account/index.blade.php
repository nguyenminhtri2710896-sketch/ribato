@extends('mod.v2.layouts.default')
@section('title', __('Tài khoản'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('mod/v2/customes/js/account.js') }}?v={{ config('app.asset_version') }}"></script>
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
                @include('mod.v2.account.left-info')
            </div>

            <div class="col-xl-8">

                

            </div>
        </div>
    </div>
    @include('mod.v2.account.modal.change-password')
    @include('mod.v2.account.modal.update-profile')
    @include('mod.v2.account.modal.authy-2factor')
    @include('mod.v2.account.modal.cancel-authy-2factor')
@endsection