@extends('backend.v1.layouts.default')
@section('title', __('backend.bank_account'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v1/customes/js/bank-account.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxBankAccountDetail = '{{ route('backend.bank-account.ajax-get-detail') }}';
        var urlAjaxBankAccountDelete = '{{ route('backend.bank-account.ajax-delete') }}';
        bankAccount.index();
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('backend.users') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('backend.bank_account') }}</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-success waves-effect  btn-custome waves-light"
                            data-bs-toggle="modal" data-bs-target=".bs-modal-add"><i class="fas fa-plus"></i> {{ __('backend.add_new') }}
                        </button>
                        <div class="btn-group ajax-select2">
                            <button type="button"
                                class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                    class="fas fa-search"></i> {{ __('backend.search') }} <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                <form id="table-filer" autocomplete="off">
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.account_name') }}</label>
                                        <input type="text" name="bank_account_name" class="form-control"
                                            placeholder="{{ __('backend.account_name') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.account_number') }}</label>
                                        <input type="text" name="bank_account_number" class="form-control"
                                            placeholder="{{ __('backend.account_number') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.bank') }}</label>
                                        <select name="bank_id" style="width:100%" class="js-data-select2"
                                            data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}">
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
                    <div class="card-body">
                        <h4 class="card-title">{{ __('backend.bank_account') }}</h4>
                        <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-list"
                                        data-ajax-url="{{ route('backend.bank-account.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="bank_accounts"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-center" data-priority="2">{{ __('backend.account_holder') }}</th>
                                                <th class="text-center" data-priority="3">{{ __('backend.account_number') }}</th>
                                                <th class="text-center" data-priority="4">{{ __('backend.bank') }}</th>
                                                <th class="text-center" data-priority="4">{{ __('backend.status') }}</th>
                                                <th class="text-center" data-priority="5">{{ __('backend.created_date') }}</th>
                                                <th class="text-center" data-priority="6">{{ __('backend.updated_date') }}</th>
                                                <th class="text-center" data-priority="7">{{ __('backend.action') }}</th>
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
    @include('backend.v1.bank-account.modal.add')
    @include('backend.v1.bank-account.modal.update')
@endsection
