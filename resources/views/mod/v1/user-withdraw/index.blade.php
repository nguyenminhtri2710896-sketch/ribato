@extends('mod.v1.layouts.default')
@section('title', __('backend.withdraw_requests'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('mod/v1/customes/js/user-withdraw.js') }}?v={{ config('app.asset_version') }}"></script>
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('backend.transaction_history') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('backend.withdraw_requests') }}</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <div class="btn-group ajax-select2  d-md-inline-block d-block  d-grid">
                            <div class="btn-group ajax-select2 mt-1">
                                <button type="button"
                                    class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                        class="fas fa-search"></i> {{ __('backend.search') }} <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                    <form id="table-filer" autocomplete="off">
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.transaction_code') }}</label>
                                            <input type="text" name="trans_code" class="form-control"
                                                placeholder="{{ __('backend.transaction_code') }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.account_holder') }}</label>
                                            <input type="text" name="bank_account_name" class="form-control"
                                                placeholder="{{ __('backend.account_holder') }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRemark">{{ __('backend.transfer_remark') }}</label>
                                            <input type="text" name="remark" class="form-control"
                                                placeholder="{{ __('backend.transfer_remark') }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.status') }}</label>
                                            <select name="status_id" style="width:100%" class="base-select2">
                                                <option value="">==== {{ __('backend.all') }} ===</option>
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
                                            <label>{{ __('backend.transaction_date') }}</label>
                                            <div class="input-daterange input-group" id="created-at"
                                                data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                                data-provide="datepicker" data-date-container="#created-at">
                                                <input type="text" class="form-control" name="created_at_from"
                                                    placeholder="{{ __('backend.from_date') }}">
                                                <input type="text" class="form-control" name="created_at_to"
                                                    placeholder="{{ __('backend.to_date') }}">
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 mb-2">
                                            <button class="btn btn-info  btn-secondary btn-block"><i
                                                    class="fa fa-search"></i>
                                                {{ __('backend.search') }}</button>
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
                        <h4 class="card-title">{{ __('backend.withdraw_request_list') }}</h4>
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
                                                <th class="text-left" data-priority="2">{{ __('backend.code') }}</th>
                                                <th class="text-left" data-priority="3">{{ __('backend.bank') }}</th>
                                                <th class="text-left" data-priority="4">{{ __('backend.account') }}</th>
                                                <th class="text-left" data-priority="5">{{ __('backend.requested_amount') }}</th>
                                                <th class="text-left" data-priority="6">{{ __('backend.withdraw_fee') }}</th>
                                                <th class="text-left" data-priority="7">{{ __('backend.deducted_amount') }}</th>
                                                <th class="text-center" data-priority="8">{{ __('backend.status') }}</th>
                                                <th class="text-center" data-priority="9">{{ __('backend.created_date') }}</th>
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
                                        class="far fa-file-excel"></i> {{ __('backend.export_excel') }}</button>

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