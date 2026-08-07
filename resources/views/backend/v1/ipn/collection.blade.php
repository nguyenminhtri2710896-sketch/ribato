@extends('backend.v1.layouts.default')
@section('title', __('backend.ipn_management') . ' - Collection')
@section('javascript')
    <script src="{{ asset('backend/v1/customes/js/ipn.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxIpnCollectionDetail = '{{ route('backend.ipn.collection.ajax-detail') }}';
        var urlAjaxIpnCollectionResend = '{{ route('backend.ipn.collection.ajax-resend') }}';
        ipnCollection.index();
    </script>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('backend.ipn_management') }}</a></li>
                            <li class="breadcrumb-item active">Ipn Collection</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <div class="btn-group ajax-select2 d-md-inline-block d-block d-grid">
                            <div class="btn-group ajax-select2 mt-1">
                                <button type="button"
                                    class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                        class="fas fa-search"></i> {{ __('backend.search') }} <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                    <form id="table-filter-ipn-collection" autocomplete="off">
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('backend.transaction_code') }}</label>
                                            <input type="text" name="transaction_code" class="form-control"
                                                placeholder="{{ __('backend.enter_transaction_code') }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('backend.order_code') }}</label>
                                            <input type="text" name="ref_code" class="form-control"
                                                placeholder="{{ __('backend.enter_order_code') }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('backend.ipn_status') }}</label>
                                            <select name="status_id" class="base-select2" style="width:100%"
                                                data-text-placeholder="{{ __('backend.select_status') }}">
                                                <option value="">==== {{ __('backend.all') }} ====</option>
                                                @foreach ($callbackStatuses as $id => $status)
                                                    <option value="{{ $id }}">{{ $status['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('backend.user') }}</label>
                                            <select name="user_id" class="js-data-select2" style="width:100%"
                                                data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                                data-text-placeholder="{{ __('backend.select_user') }}">
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label>{{ __('backend.processing_date') }}</label>
                                            <div class="input-daterange input-group" id="created-at-collection"
                                                data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                                data-provide="datepicker" data-date-container="#created-at-collection">
                                                <input type="text" class="form-control" name="created_at_from"
                                                    placeholder="{{ __('backend.from_date') }}">
                                                <input type="text" class="form-control" name="created_at_to"
                                                    placeholder="{{ __('backend.to_date') }}">
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 mb-2">
                                            <button class="btn btn-info btn-secondary btn-block"><i class="fa fa-search"></i>
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">{{ __('backend.ipn_collection_list') }}</h4>
                        <div >
                            <table id="data-table-ipn-collection"
                                data-ajax-url="{{ route('backend.ipn.collection.ajax-get-list') }}"
                                data-id-filter="table-filter-ipn-collection" data-key="callbacks"
                                class="table table-striped dt-responsive wrap w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center" data-priority="1">#</th>
                                        <th class="text-left" data-priority="2">{{ __('backend.transaction_code') }}</th>
                                        <th class="text-left" data-priority="3">{{ __('backend.order_code') }}</th>
                                        <th class="text-left" data-priority="4">{{ __('backend.content') }}</th>
                                        <th class="text-center" data-priority="5">{{ __('backend.status') }}</th>
                                        <th class="text-center" data-priority="6">{{ __('backend.processing_date') }}</th>
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
    </div>
    @include('backend.v1.ipn.modal.collection-detail')
@endsection


