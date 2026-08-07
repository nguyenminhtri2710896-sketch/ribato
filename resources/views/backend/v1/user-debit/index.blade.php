@extends('backend.v1.layouts.default')
@section('title', __('backend.debit_management'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <script src="{{ asset('backend/v1/customes/js/user-debit.js') }}?v={{ config('app.asset_version') . time() }}"></script>
    <script type="text/javascript">
        var urlAjaxUserDebitDelete = '{{ route('backend.user-debit.ajax-delete') }}';
        userDebit.index();

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('backend.debit_management') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('backend.debit_list') }}</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <div class="btn-group ajax-select2  d-md-inline-block d-block  d-grid">
                            @if(auth()->user()->full_access)
                                <button type="button" class="btn btn-success waves-effect  btn-custome waves-light mt-1"
                                    data-bs-toggle="modal" data-bs-target=".bs-modal-user-debit-add"><i class="fas fa-plus"></i>
                                    {{ __('backend.create_debit') }}
                                </button>
                                <button type="button" class="btn btn-info waves-effect  btn-custome waves-light mt-1"
                                    data-bs-toggle="modal" data-bs-target=".bs-modal-user-debit-return"><i
                                        class="fas fa-plus"></i>
                                    {{ __('backend.create_repayment') }}
                                </button>
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
                                            <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.note') }}</label>
                                            <input type="text" name="user_debits.note" class="form-control"
                                                placeholder="{{ __('backend.note') }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="exampleDropdownFormRefCode">{{ __('backend.status') }}</label>
                                            <select name="user_debits.status_id" style="width:100%" class="base-select2">
                                                <option value="">==== {{ __('backend.all') }} ===</option>
                                                @php
                                                    foreach (\App\Services\UserDebitService::$arrStatusId as $id => $arrStatusId):
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
                                        @endif
                                        <div class="mb-2">
                                            <label>{{ __('backend.transaction_date') }}</label>
                                            <div class="input-daterange input-group" id="created-at"
                                                data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                                data-provide="datepicker" data-date-container="#created-at">
                                                <input type="text" class="form-control" name="user_debits.created_at_from"
                                                    placeholder="{{ __('backend.from_date') }}">
                                                <input type="text" class="form-control" name="user_debits.created_at_to"
                                                    placeholder="{{ __('backend.to_date') }}">
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label>{{ __('backend.borrow_date') }}</label>
                                            <div class="input-daterange input-group" id="created-at"
                                                data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                                data-provide="datepicker" data-date-container="#created-at">
                                                <input type="text" class="form-control" name="user_debits.created_at_from"
                                                    placeholder="{{ __('backend.from_date') }}">
                                                <input type="text" class="form-control" name="user_debits.created_at_to"
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
                        <h4 class="card-title">{{ __('backend.debit_list') }}</h4>
                        <div class="row">
                            <div class="col-sm-12">
                                <strong>{{ __('backend.total_debit') }}:</strong> <span class="text-danger sum-amount-debit"></span>
                            </div>
                        </div>
                        <div id="datatable_wrapper"
                            class="reponsive-with-mobile dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table-list"
                                        data-ajax-url="{{ route('backend.user-debit.ajax-get-list') }}"
                                        data-id-filter="table-filer" data-key="user_debits"
                                        class="table  table-striped dt-responsive  wrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center" data-priority="1">#</th>
                                                <th class="text-left" data-priority="2">{{ __('backend.customer') }}</th>
                                                <th class="text-left" data-priority="3">{{ __('backend.amount') }}</th>
                                                <th class="text-left" data-priority="4">{{ __('backend.type') }}</th>
                                                <th class="text-left" data-priority="5">{{ __('backend.note') }}</th>
                                                <th class="text-left" data-priority="6">{{ __('backend.created_date') }}</th>
                                                <th class="text-left" data-priority="7">{{ __('backend.borrow_date') }}</th>
                                                <th class="text-left" data-priority="8">{{ __('backend.action') }}</th>
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
            </div>
        </div>
    </div>
    @include('backend.v1.user-debit.modal.add')
    @include('backend.v1.user-debit.modal.return')
@endsection