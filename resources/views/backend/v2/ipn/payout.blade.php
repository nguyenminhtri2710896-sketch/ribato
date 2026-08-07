@extends('backend.v2.layouts.default')
@section('title', __('Quản lý IPN - Payout'))
@section('javascript')
    <script src="{{ asset('backend/v2/customes/js/ipn.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxIpnPayoutDetail = '{{ route('backend.ipn.payout.ajax-detail') }}';
        var urlAjaxIpnPayoutResend = '{{ route('backend.ipn.payout.ajax-resend') }}';
        ipnPayout.index();
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Quản lý IPN</a></li>
                            <li class="breadcrumb-item active">Ipn Payout</li>
                        </ol>
                    </div>
                    <div class="page-title-right">
                        <div class="btn-group ajax-select2 d-md-inline-block d-block d-grid">
                            <div class="btn-group ajax-select2 mt-1">
                                <button type="button"
                                    class="waves-effect waves-light btn btn-info btn-custome btn-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                        class="fas fa-search"></i> Tìm kiếm <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-md p-3 dropdown-menu-search">
                                    <form id="table-filter-ipn-payout" autocomplete="off">
                                        <div class="mb-2">
                                            <label class="form-label">Mã giao dịch</label>
                                            <input type="text" name="trans_code" class="form-control"
                                                placeholder="Nhập mã giao dịch">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Mã đơn</label>
                                            <input type="text" name="ref_code" class="form-control"
                                                placeholder="Nhập mã đơn">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Trạng thái IPN</label>
                                            <select name="status_id" class="base-select2" style="width:100%"
                                                data-text-placeholder="Chọn trạng thái">
                                                <option value="">==== Tất cả ====</option>
                                                @foreach ($callbackStatuses as $id => $status)
                                                    <option value="{{ $id }}">{{ $status['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Người dùng</label>
                                            <select name="user_id" class="js-data-select2" style="width:100%"
                                                data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                                data-text-placeholder="Chọn người dùng">
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label>Ngày xử lý</label>
                                            <div class="input-daterange input-group" id="created-at-payout"
                                                data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                                data-provide="datepicker" data-date-container="#created-at-payout">
                                                <input type="text" class="form-control" name="created_at_from"
                                                    placeholder="Từ ngày">
                                                <input type="text" class="form-control" name="created_at_to"
                                                    placeholder="Đến ngày">
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 mb-2">
                                            <button class="btn btn-info btn-secondary btn-block"><i class="fa fa-search"></i>
                                                Tìm</button>
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
                        <h4 class="card-title mb-3">Danh sách IPN Payout</h4>
                        <div>
                            <table id="data-table-ipn-payout"
                                data-ajax-url="{{ route('backend.ipn.payout.ajax-get-list') }}"
                                data-id-filter="table-filter-ipn-payout" data-key="callbacks"
                                class="table table-striped dt-responsive wrap w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center" data-priority="1">#</th>
                                        <th class="text-left" data-priority="2">Mã giao dịch</th>
                                        <th class="text-left" data-priority="3">Mã đơn</th>
                                        <th class="text-left" data-priority="4">Nội dung</th>
                                        <th class="text-center" data-priority="5">Trạng thái</th>
                                        <th class="text-center" data-priority="6">Ngày xử lý</th>
                                        <th class="text-center" data-priority="7">Chức năng</th>
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
    @include('backend.v2.ipn.modal.payout-detail')
@endsection


