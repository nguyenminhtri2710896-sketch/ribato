@extends('backend.v2.layouts.confirm-withdraw')
@section('title', __('Xác nhận yêu cầu rút tiền'))
@section('style')
@endsection
@section('javascript')
    <script src="{{ asset('backend/v2/customes/js/confirm-withdraw.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxConfirmWithDraw = '{{ route('backend.confirm-withdraw.ajax-confirm') }}';
        var urlAjaxCancelWithDraw = '{{ route('backend.confirm-withdraw.ajax-cancel') }}';
        confirmWithDraw.index();
    </script>

@endsection
@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        @if($objUserWithdraw)
                            @if(in_array($objUserWithdraw->status_id, [2, 3]))
                                <div class="card-body">
                                    <div class="p-2" style="text-align: center;">
                                        <h5 class="text-primary">
                                            Giao dịch đã được xử lý
                                        </h5>
                                    </div>
                                </div>
                            @elseif(in_array($objUserWithdraw->partner_transaction_status_id, [2, 3]))
                                <div class="card-body">
                                    <div class="p-2" style="text-align: center;">
                                        <h5 class="text-primary">
                                            @if($objUserWithdraw->partner_transaction_status_id == 2)
                                                Giao dịch đã xác nhận
                                            @elseif($objUserWithdraw->partner_transaction_status_id == 3)
                                                Giao dịch đã huỷ
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            @else
                                <div class="bg-primary bg-soft">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="text-primary p-4" style="text-align: center;">
                                                <h5 class="text-primary">Thông tin giao dịch</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="p-2">
                                        <form class="form-horizontal needs-validation frm-ajax-submit frm-confirm-withdraw"
                                            novalidate method="POST"
                                            data-ajax-url="{{ route('backend.confirm-withdraw.index', ["hash" => $hash]) }}">
                                            <input type="hidden" name="hash" value="{{$hash }}" />
                                            <input type="hidden" name="code" value="{{$code }}" />
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="col">Ngân hàng</th>
                                                                    <input type="hidden" class="bank_short_name"
                                                                        value="{{ $objUserWithdraw->bank_short_name }}" />
                                                                    <td scope="col">{{ $objUserWithdraw->bank_short_name }} <a
                                                                            href="javascript:base.copyClipboard('bank_short_name')"><i
                                                                                class="fas fa-copy"></i></a></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Chủ khoản:</th>
                                                                    <input type="hidden" class="bank_account_name"
                                                                        value="{{ $objUserWithdraw->bank_account_name }}" />
                                                                    <td>{{ $objUserWithdraw->bank_account_name }} <a
                                                                            href="javascript:base.copyClipboard('bank_account_name')"><i
                                                                                class="fas fa-copy"></i></a></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Số tài khoản</th>
                                                                    <input type="hidden" class="bank_account_number"
                                                                        value="{{ $objUserWithdraw->bank_account_number }}" />
                                                                    <td>{{ $objUserWithdraw->bank_account_number }} <a
                                                                            href="javascript:base.copyClipboard('bank_account_number')"><i
                                                                                class="fas fa-copy"></i></a></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Số tiền yêu cầu</th>
                                                                    <input type="hidden" class="amount"
                                                                        value="{{ $objUserWithdraw->amount }}" />
                                                                    <td><span
                                                                            class="text-success">{{ number_format($objUserWithdraw->amount) }}<sup>đ</sup>
                                                                            <a href="javascript:base.copyClipboard('amount')"><i
                                                                                    class="fas fa-copy"></i></a></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Nội dung chuyển khoản</th>
                                                                    <input type="hidden" class="remark"
                                                                        value="{{ $objUserWithdraw->remark }}" />
                                                                    <td><span>{{ $objUserWithdraw->remark }} <a
                                                                                href="javascript:base.copyClipboard('remark')"><i
                                                                                    class="fas fa-copy"></i></a></span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="upload-box-cmnd basic-image-upload"
                                                        data-url="{{ route('backend.upload.image') }}">
                                                        <div class="content-success">
                                                            <div class="img"><img /></div>
                                                        </div>
                                                        <div class="error-message"></div>
                                                        <div class="loading"><i class="fas fa-spinner fa-spin"></i></div>
                                                        <input class="image" type="hidden" name="partner_transaction_image" />
                                                        <div class="title">
                                                            Hình ảnh chứng từ
                                                            <div class="small-customize">(.jpg , .jpeg , .png)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="partner_transaction_cancel_reason" />
                                            </div>
                                            <div class="text-end">
                                                <button class="btn btn-danger w-md waves-effect waves-light btn-cancel"
                                                    type="button">Huỷ</button>
                                                <button class="btn btn-success w-md waves-effect waves-light btn-confirm"
                                                    type="button">Xác nhận</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="card-body">
                                <div class="p-2" style="text-align: center;">
                                    <h5 class="text-primary">Giao dịch xử lý không tồn tại</h5>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection