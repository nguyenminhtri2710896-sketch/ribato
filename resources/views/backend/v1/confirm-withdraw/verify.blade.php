@extends('backend.v1.layouts.confirm-withdraw')
@section('title', __('Xác nhận yêu cầu rút tiền'))
@section('style')
@endsection
@section('javascript')

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
                                        <div class="col-7">
                                            <div class="text-primary p-4">
                                                <h5 class="text-primary">Xác nhận giao dịch</h5>
                                                <p>Nhập mã truy cập được cung cấp vào ô bên dưới!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="p-2">
                                        <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                                            data-ajax-url="{{ route('backend.confirm-withdraw.verify', ["hash" => $hash]) }}">
                                            <input type="hidden" name="hash" value="{{$hash }}" />
                                            <div class="mb-3">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="has-validation">
                                                                <label class="mt-2 mb-0">Mã truy cập</label>
                                                                <input type="password" class="form-control form-control-custom"
                                                                    name="code" placeholder="Mã" required="">
                                                                <div class="invalid-tooltip">
                                                                    Vui lòng nhập mã truy cập
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Truy
                                                    cập</button>
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