@extends('payment.v1.layouts.default')
@section('title', __('Payment'))
@section('style')

@endsection
@section('javascript')

    <script type="text/javascript">
        var errorUrl = "{{ route('payment.transaction.error') }}";
        var urlTransactionCancel = "{{ $objTransaction->payment_cancel_url ?? '' }}";
        var urlTransactionSuccess = "{{ $objTransaction->payment_success_url ?? '' }}";
    </script>

    <script type="text/javascript">
        $("#searchPayMethod1").keypress(function(e) {
            if (e.shiftKey && (e.which === 37 || e.which === 39)) {
                return true;
            }
            //Arrow key
            if (e.keyCode === 37 || e.keyCode === 39) {
                return true;
            }
            if (e.ctrlKey && (e.which === 67 || e.which === 86)) {
                return true;
            }
            if (e.keyCode === 8 || e.keyCode === 46) {
                return true;
            }
            var inputVal = String.fromCharCode(e.which);
            var characterReg = /^\s*[a-zA-Z,\s]+\s*$/;
            if (!characterReg.test(inputVal)) {
                return false;
            }
            return true;
        });

        $("#searchPayMethod1").keyup(function(e) {
            let valueSearch = $('#searchPayMethod1').val().toLowerCase();
            $(".domestic-bank").each(function(index) {
                if ($(this).attr('search-value').indexOf(valueSearch) === -1) {
                    $(this).addClass('hidden');
                } else {
                    $(this).removeClass('hidden');
                }
            });
        });
        $("#searchPayMethod2").keypress(function(e) {
            if (e.shiftKey && (e.which === 37 || e.which === 39)) {
                return true;
            }
            //Arrow key
            if (e.keyCode === 37 || e.keyCode === 39) {
                return true;
            }
            if (e.ctrlKey && (e.which === 67 || e.which === 86)) {
                return true;
            }
            if (e.keyCode === 8 || e.keyCode === 46) {
                return true;
            }
            var inputVal = String.fromCharCode(e.which);
            var characterReg = /^\s*[a-zA-Z,\s]+\s*$/;
            if (!characterReg.test(inputVal)) {
                return false;
            }
            return true;
        });

        $("#searchPayMethod2").keyup(function(e) {
            let valueSearch = $('#searchPayMethod2').val().toLowerCase();
            $(".domestic-bank").each(function(index) {
                if ($(this).attr('search-value').indexOf(valueSearch) === -1) {
                    $(this).addClass('hidden');
                } else {
                    $(this).removeClass('hidden');
                }
            });
        });
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
                            <div class="row align-items-center justify-content-md-center">
                                <div class="col-auto show-mobile">
                                    <!-- button.button -->
                                    <a data-bs-toggle="modal" data-bs-target="#modalCancelPayment"
                                        class="ubg-transparent ubox-size-button-default ubox-square ubg-hover ubg-active ubtn">
                                        <div class="ubtn-inner">
                                            <span class="ubtn-ic ubtn-ic-left">
                                                <img src="{{ asset('payment/v1/image/icon/24x24-chevron-left-circle.svg') }}"
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
                                                {{-- <img src="{{ asset('payment/v1/image/icon/logo.svg') }}" alt="{{env('APP_NAME')}}"> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-auto col">
                                            <div class="logo d-block text-right">
                                                {{-- <img src="{{ asset('payment/v1/image/icon/viettelIDC_logo2.png') }}"
                                                    alt="Merchant Logo" /> --}}
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
        <div class="box__body ubg-porcelain-light">
            <form action="{{ route('payment.transaction.payment-method', ['hash' => $strHash]) }}" method="post">
                <div class="pv32 box-section">
                    <div class="list-mb24 list-crop">
                        <div class="h2 text-center main-title-mobile mb24">
                            Chọn phương thức thanh to&#225;n
                        </div>
                        <div class="list-method list-mb8 list-last-mb accordion" id="accordionList">
                            <div class="list-method-item accordion-item">
                                <div class="list-method-button" data-bs-toggle="collapse" data-bs-target="#accordionList1"
                                    aria-expanded="true" aria-controls="accordionList1">
                                    <div class="row row-16 align-items-center">
                                        <div class="col">
                                            <div class="title h3 color-default">
                                                Thẻ nội địa v&#224; t&#224;i khoản ng&#226;n h&#224;ng
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon">
                                                <img src="{{ asset('payment/v1/image/icon/64x64-bank.svg') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-method-item-content accordion-collapse collapse show" id="accordionList1"
                                    data-bs-parent="#accordionList1">
                                    <div>
                                        <div class="list-bank list-bank-grid-4">
                                            <div class="list-mb24 list-last-mb">
                                                <div class="list-bank-search">
                                                    <!-- form.input -->
                                                    <div class="form-group">
                                                        <div
                                                            class="input-group-wrap input-default input-size-default input-group-vertical">
                                                            <label class="input-inner-wrap">
                                                                <input type="text"
                                                                    class="input input-label-change input-has-clear"
                                                                    placeholder="T&#236;m kiếm..." autocorrect="off"
                                                                    id="searchPayMethod1">
                                                                <div class="input-extend input-extend-left">
                                                                    <div class="input-box input-ic">
                                                                        <img src="{{ asset('payment/v1/image/icon/24x24-search.svg') }}"
                                                                            alt="" class="ic-default">
                                                                    </div>
                                                                </div>
                                                                <div class="input-extend input-extend-right">
                                                                    <div class="input-box input-ic-clear">
                                                                    </div>
                                                                </div>
                                                                <div class="input-frame"></div>
                                                            </label>
                                                        </div>
                                                        <div class="errorBlock"></div>
                                                    </div>
                                                    <!-- end form.input -->
                                                </div>
                                                <div class="list-bank-main">
                                                    <div class="row row-8 list-mb8 list-crop">
                                                        @foreach ($objUserBankAccounts as $objUserBankAccount)
                                                            <div class="col-item col-sm-3 col-4 domestic-bank"
                                                                search-value="{{ $objUserBankAccount->bank_name }}">
                                                                <button type="submit"
                                                                    value="{{ $objUserBankAccount->bank_account_id }}"
                                                                    id="{{ $objUserBankAccount->bank_short_code }}"
                                                                    name="bank_account_id" class="list-bank-item">
                                                                    <div class="list-bank-item-inner"
                                                                        style="background-image: url({{ asset('payment/v1/image/bank/' . $objUserBankAccount->bank_logo) }})">
                                                                    </div>
                                                                </button>
                                                            </div>
                                                        @endforeach
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
            </form>
        </div>
        <!-- _custom.footerBox -->
        @include('payment.v1.transaction.box-footer')

    </div>
@endsection
