@extends('backend.v1.layouts.default')
@section('title', __('backend.va_list'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script
        src="{{ asset('backend/v1/customes/js/virtual-account.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        virtualAccount.getList();
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('backend.va_account') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('backend.va_account_list') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('backend.va_account_list') }}</h4>
                        <div id="datatable_wrapper"
                            class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-user-virtual-account-list"
                                        data-ajax-url="{{ route('backend.virtual-account.ajax-get-list') }}"
                                        data-id-filter="form-filter-user-virtual_account" data-key="user_virtual_accounts"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center desktop col-number" data-priority="1">#
                                                </th>
                                                <th class="text-left desktop" data-priority="2">{{ __('backend.bank') }}</th>
                                                <th class="text-left desktop" data-priority="2">{{ __('backend.account_holder') }}</th>
                                                </th>
                                                <th class="text-left desktop" data-priority="2">{{ __('backend.account_number') }}
                                                </th>
                                                <th class="text-center desktop" data-priority="7">{{ __('backend.created_date') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
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