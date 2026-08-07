@extends('payment.v1.layouts.default')
@section('title', __('Payment'))
@section('style')

@endsection
@section('javascript')
    <script type="text/javascript">
        var errorUrl = "{{ route('payment.transaction.error') }}";
        var urlTransactionCancel = "{{ $objTransaction->payment_cancel_url ?? '' }}";
        var urlTransactionSuccess = "{{ $objTransaction->payment_success_url ?? '' }}";
        @php
            $intTime = strtotime($objTransaction->expired_at) - time();
        @endphp
        var timer = {{ $intTime < 0 ? 0 : $intTime }};

        function checkTransaction() {
            $.ajax({
                type: "GET",
                url: '{{ route('payment.transaction.check-complete') }}',
                data: {
                    hash: '{{ $objTransaction->code_hashed }}'
                },
                dataType: 'JSON',
                success: function(result) {
                    if (result.error_code != 0) {
                        return;
                    }

                    if (result.data.status_id == 2 || result.data.status_id == 6) {
                        location.href = urlTransactionSuccess;
                    }

                },
                complete: function() {
                    setTimeout(() => {
                        checkTransaction();
                    }, 10000);
                }
            });
        }

        checkTransaction();
    </script>
@endsection

@section('content')
    <div class="box box-main">
        <!-- _custom.header -->
        <div class="box__header header-box header-box-simple">
            <div class="box__header-inner">
                <div class="section">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-md-auto header-box-top">
                            {{-- <div class="row align-items-center justify-content-md-center">
                        <div class="col-auto show-mobile">
                            <!-- button.button -->
                            <a href="/Transaction/GoBack.html?token=001797a571f64097a1055072f59aa460"
                                class="ubg-transparent ubox-size-button-default ubox-square ubg-hover ubg-active ubtn">
                                <div class="ubtn-inner">
                                    <span class="ubtn-ic ubtn-ic-left">
                                        <img src="/images/icons-color/default/default/24x24-chevron-left-circle.svg"
                                            alt="" class="ic-default">
                                    </span>
                                </div>
                            </a>
                            <!-- end button.button -->
                        </div>
                        <div class="col-md-auto col logo-group-wrap w-100">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-auto col">
                                    <div class="logo d-block">
                                        <img src="/Images/brands/logo.svg" alt="VNPAY">
                                    </div>
                                </div>
                                <div class="col-md-auto col">
                                    <div class="logo d-block text-right">
                                        <img src="/images/merchant/viettelIDC_logo2.png" alt="Merchant Logo">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto show-mobile box-ic-holder-col">
                            <div class="box-ic-holder"></div>
                        </div>
                    </div> --}}
                        </div>
                        <div class="col-md timer">
                            <div class="timmer-inner">
                                <div
                                    class="row row-12 align-items-center justify-content-md-end justify-content-between list-mb12 list-crop">
                                    <div class="col-md-auto col color-sub-default">
                                        Giao dịch hết hạn sau
                                    </div>
                                    <div class="col-auto color-sub-default">
                                        <div class="timer-clock fz-h3 weight5">
                                            <div class="row row-4 align-items-center">
                                                <div class="col-auto">
                                                    <div class="ubg-default ubox-size-ic-xs ubox-square ubox ubox-ic"
                                                        id="minute"></div>
                                                </div>
                                                <div class="col-auto">:</div>
                                                <div class="col-auto">
                                                    <div class="ubg-default ubox-size-ic-xs ubox-square ubox ubox-ic"
                                                        id="second"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- end _custom.header -->
        <div class="box__body ubg-white">
            <div class="list-mb24">
                <div class="layout-bills-inner box-section">
                    <div class="row list-mb24 list-crop">
                        <div class="col-12 main-title-mobile h2 text-center">
                            Thanh toán qua Ngân hàng {{ $objTransaction->bank_short_name }}
                        </div>
                        <div class="col-md-6 bills-col">
                            <div class="bills">
                                <div class="bills-header-mobile show-mobile list-mb12 list-last-mb"
                                    data-bs-toggle="collapse" data-bs-target="#accordionBill" aria-expanded="true">
                                    <div class="title weight5">

                                    </div>
                                    <div class="row color-primary align-items-center">
                                        <div class="col h2">
                                            <span
                                                id="totalAmountMb">{{ number_format($objTransaction->amount) }}</span><sup>{{ strtoupper($objTransaction->currency) }}</sup>
                                        </div>
                                    </div>
                                </div>
                                <div class="bills-body">
                                    <div>
                                        <div class="bills-list list-mb24 list-last-mb">
                                            <div class="bills-list-item">
                                                <div class="row">
                                                    <div class="col-md-12 col-5 mb4">
                                                        <div class="sub-title color-sub-default">
                                                            Số tiền thanh toán
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col">
                                                        <div class="title text-left-md-right color-primary h2">
                                                            <span
                                                                id="totalAmountDt">{{ number_format($objTransaction->amount) }}</span><sup>{{ strtoupper($objTransaction->currency) }}</sup>
                                                            <span class="txt-copytext"
                                                                data-text="{{ $objTransaction->amount }}"><img
                                                                    src="{{ asset('payment/v1/image/icon/icon-copy.svg') }}"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bills-list-item">
                                                <div class="row">
                                                    <div class="col-md-12 col-5 mb4">
                                                        <div class="sub-title color-sub-default">
                                                            Chủ khoản
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col">
                                                        <div class="title text-left-md-right h3">
                                                            {{ $objTransaction->bank_account_name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bills-list-item">
                                                <div class="row">
                                                    <div class="col-md-12 col-5 mb4">
                                                        <div class="sub-title color-sub-default">
                                                            Số tài khoản
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col">
                                                        <div class="title text-left-md-right h3">
                                                            {{ $objTransaction->bank_account_number }} <span
                                                                class="txt-copytext"
                                                                data-text="{{ $objTransaction->bank_account_number }}"><img
                                                                    src="{{ asset('payment/v1/image/icon/icon-copy.svg') }}"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bills-list-item">
                                                <div class="row">
                                                    <div class="col-md-12 col-5 mb4">
                                                        <div class="sub-title color-sub-default">
                                                            Nội dung chuyển khoản
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col">
                                                        <div class="title text-left-md-right h3">
                                                            {{ $objTransaction->code }} <span class="txt-copytext"
                                                                data-text="{{ $objTransaction->code }}"><img
                                                                    src="{{ asset('payment/v1/image/icon/icon-copy.svg') }}"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md right-bill-col">
                            <div class="right-bill-col-inner">
                                <div class="bank-payement-section">
                                    <div class="list-mb24 list-last-mb qr-selection">
                                        <div class="text">MÃ QRPAY</div>
                                        <div class="bank-payement-section-inner list-mb24 list-last-mb">
                                            <img
                                                src="https://img.vietqr.io/image/{{ $objTransaction->bank_napas_code }}-{{ $objTransaction->bank_account_number }}-qr_only.png?addInfo={{ $objTransaction->code }}&amount={{ $objTransaction->amount }}" />
                                        </div>
                                        <div class="note">khách hàng sử dụng QRPay để thanh toán nhanh hơn<br /></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row sub-recommend">
                    <p>Mã QR chỉ hiệu lực cho <span class="hilight">1 lần thanh toán DUY NHẤT</span>. Việc sử dụng lại mã QR
                        này hoặc thay đổi
                        nội dung sẽ dẫn đến giao dịch KHÔNG được xử lý.</p>

                    <p>Vui lòng kiểm tra chính xác <span class="hilight">&lt;Số tài khoản&gt;&lt;Nội dung giao
                            dịch&gt;&lt;Số tiền&gt;</span>
                        trước khi thanh toán để đảm bảo đơn hàng được xử lý NGAY lập tức.</p>

                    <p>Sử dụng chuyển khoản nhanh Napas 24/7 để chuyển tiền đi ngay lập tức cho đơn hàng. Ngay khi
                        nhận xác thực thành công từ phía Ngân hàng, chúng tôi sẽ thông báo đến bạn!</p>
                </div>
            </div>
        </div>
        <!-- _custom.footerBox -->
        @include('payment.v1.transaction.box-footer')

    </div>
@endsection
