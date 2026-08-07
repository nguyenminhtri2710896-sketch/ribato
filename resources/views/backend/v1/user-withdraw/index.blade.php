@extends('backend.v1.layouts.default')
@section('title', __('backend.withdraw_requests'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v1/customes/js/user-withdraw.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxUserWithdrawDetail = '{{ route('backend.user-withdraw.ajax-get-detail') }}';
        var urlAjaxAddMultibleCheck = '{{ route('backend.user-withdraw.ajax-add-multible-check') }}';
        var urlAjaxUserWithdrawExportExcel = '{{ route('backend.user-withdraw.ajax-export-excel') }}';
        var urlAjaxUserWithdrawCreateBill = '{{ route('backend.user-withdraw.ajax-create-bill') }}';


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
                            <li class="breadcrumb-item active">{{ __('backend.withdraw_requests') }}</a></li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <div class="btn-group ajax-select2  d-md-inline-block d-block  d-grid">
                               @if (auth()->user()->is_accountant!=1)
                            <button type="button" class="btn btn-success waves-effect  btn-custome waves-light mt-1"
                                data-bs-toggle="modal" data-bs-target=".bs-modal-user-withdraw-add-multible"><i
                                    class="fas fa-plus"></i>
                                {{ __('backend.create_request_bulk') }}
                            </button>
                            <button type="button" class="btn btn-success waves-effect  btn-custome waves-light mt-1"
                                data-bs-toggle="modal" data-bs-target=".bs-modal-user-withdraw-add"><i
                                    class="fas fa-plus"></i>
                                {{ __('backend.create_request') }}
                            </button>
                            @if (auth()->user()->is_admin || auth()->user()->full_access)
                                <button type="button" class="btn btn-warning waves-effect  btn-custome waves-light mt-1"
                                    data-bs-toggle="modal" data-bs-target=".bs-modal-user-withdraw-add-manual"><i
                                        class="fas fa-tools"></i>
                                    {{ __('backend.create_request_manual') }}
                                </button>
                            @endif
                            @endif
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
                                        @if (auth()->user()->is_admin)
                                            <div class="mb-2">
                                                <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.user') }}</label>
                                                <select name="user_id" style="width:100%" class="js-data-select2"
                                                    data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}">
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.gateway') }}</label>
                                                <select name="gateway_id" style="width:100%" class="js-data-select2"
                                                    data-ajax-url="{{ route('backend.gateway.ajax-select2-get-list') }}">
                                                </select>
                                            </div>
                                        @endif
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
                                        data-ajax-url="{{ route('backend.user-withdraw.ajax-get-list') }}"
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
                                                @if (auth()->user()->is_admin || auth()->user()->is_fullaccess)
                                                    <th class="text-center" data-priority="10">{{ __('backend.action') }}</th>
                                                @endif
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
    @include('backend.v1.user-withdraw.modal.add')
    @include('backend.v1.user-withdraw.modal.add-manual')
    @include('backend.v1.user-withdraw.modal.add-multible')
    @include('backend.v1.user-withdraw.modal.detail')

    <div class="modal fade bs-modal-user-withdraw-bill" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('backend.transaction_bill') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="bill-image-container">
                        <img src="" alt="Bill Image" class="img-fluid bill-image" style="max-height: 80vh;">
                    </div>
                    <div class="bill-loading d-none">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p class="mt-2">{{ __('backend.loading_bill') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('backend.close') }}</button>
                    <a href="" target="_blank" class="btn btn-primary btn-download-bill" download>{{ __('backend.download') }}</a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection