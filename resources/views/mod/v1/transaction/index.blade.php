@extends('mod.v1.layouts.default')
@section('title', __('backend.transaction_list'))
@section('style')
@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('mod/v1/customes/js/transaction.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxTransactionExportExcel = '{{ route('mod.transaction.ajax-export-excel') }}';
        transaction.index();
    </script>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    {{-- <h4 class="mb-sm-0 font-size-18">Danh sách thanh toán</h4> --}}
                    <div class="page-title-left">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('backend.transaction') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('backend.transaction_list') }}</li>
                        </ol>
                    </div>
                    <div class="page-title-right ajax-select2">
                        <div class="btn-group  d-md-inline-block d-block d-grid mb-0 mt-1">
                            <button type="button"
                                class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                    class="fas fa-search"></i> {{ __('backend.search') }} <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                <form id="table-filer" autocomplete="off">
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.order_code') }}</label>
                                        <input type="text" name="ref_code" class="form-control" placeholder="{{ __('backend.order_code') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormCode">{{ __('backend.transaction_code') }}</label>
                                        <input type="text" name="code" class="form-control" placeholder="{{ __('backend.transaction_code') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="bank_account_number">{{ __('backend.va_account') }}</label>
                                        <select name="bank_account_number" style="width:100%" class="js-data-select2"
                                            data-ajax-url="{{ route('mod.virtual-account.ajax-select2-get-list') }}">
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
                                    <div class="mb-2">
                                        <label>{{ __('backend.transaction_updated_date') }}</label>

                                        <div class="input-daterange input-group" id="updated-at"
                                            data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                            data-provide="datepicker" data-date-container="#updated-at">
                                            <input type="text" class="form-control" name="updated_at_from"
                                                placeholder="{{ __('backend.from_date') }}">
                                            <input type="text" class="form-control" name="updated_at_to"
                                                placeholder="{{ __('backend.to_date') }}">
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 mb-2">
                                        <button class="btn btn-info  btn-secondary btn-block"><i class="fa fa-search"></i>
                                            {{ __('backend.search') }}</button>
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
                    <div class="card-body border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">{{ __('backend.payment_list') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="datatable_wrapper"
                            class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12" data-pattern="priority-columns">
                                    <table id="data-table-list" data-ajax-url="{{ route('mod.transaction.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="transactions"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1"># </th>
                                                <th class="text-left" data-priority="2">{{ __('backend.order_code') }}</th>
                                                <th class="text-center" data-priority="3">{{ __('backend.transaction_code') }}</th>
                                                <th class="text-right" data-priority="4">{{ __('backend.amount') }}</th>
                                                <th class="text-right" data-priority="5">{{ __('backend.fee') }}</th>
                                                <th class="text-right" data-priority="6">{{ __('backend.actual_received_amount') }}</th>
                                                <th class="text-center" data-priority="7" style="width:20%">{{ __('backend.content') }}</th>
                                                <th class="text-center" data-priority="8">{{ __('backend.receiving_bank') }}</th>
                                                <th class="text-center" data-priority="9">{{ __('backend.status') }}</th>
                                                <th class="text-center" data-priority="10">{{ __('backend.transaction_type') }}</th>
                                                <th class="text-center" data-priority="11">{{ __('backend.transaction_date') }}</th>
                                                <!-- <th class="text-center" data-priority="12">Ngày nhận giao dịch</th> -->
                                                <th class="text-center" data-priority="12">{{ __('backend.updated_date') }}</th>
                                                {{-- <th class="text-center" data-priority="5">Thao tác</th> --}}
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