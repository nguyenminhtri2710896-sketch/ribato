@extends('payment.v1.layouts.default')
@section('title', __('Error'))
@section('style')

@endsection
@section('javascript')
    <script type="text/javascript">
        var errorUrl = "{{ route('payment.transaction.error') }}";
        var urlTransactionCancel = "{{ $objTransaction->payment_cancel_url ?? '' }}";
        var urlTransactionSuccess = "{{ $objTransaction->payment_success_url ?? '' }}";
    </script>
@endsection

@section('content')
    <div class="box box-main">
        <div class="box__header header-box header-box-simple">
            <div class="box__header-inner">
                <div class="section">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-md-auto header-box-top">

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="box__body ubg-white">
            <div class="box-error">
                <div class="box-error-inner">
                    <div class="list-mb8 list-crop">
                        <form action="/Payment/Error.html" autocomplete="off" class="form form-vertical" id="formRenew"
                            method="post" novalidate="">
                            <div class="icon">
                                <img src="{{ asset('payment/v1/image/icon/error.svg') }}" alt="">
                            </div>
                            <div class="title h2 color-danger">
                                Thông báo
                            </div>
                            <div class="fz-h3">
                                @if (session('message'))
                                    {{ session('message') }}
                                @else
                                    Có lỗi xảy ra trong quá trình xử lý! Quý khách vui lòng thực hiện lại giao dịch
                                @endif
                            </div>
                            <input hidden="" name="code">
                            <div class="section mt24">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('payment.v1.transaction.box-footer')
    </div>
@endsection
