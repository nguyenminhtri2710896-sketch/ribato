<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <title> Chọn phương thức thanh toán</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    <link href="{{ asset('payment/v1/css/bootstrap.bundles.css') }}?v={{ config('app.asset_version') }}" rel=stylesheet>
    <link href="{{ asset('payment/v1/css/select2.bundles.css') }}?v={{ config('app.asset_version') }}" rel=stylesheet>
    <link href="{{ asset('payment/v1/css/style.css') }}?v={{ config('app.asset_version') }}" rel=stylesheet>
    <link href="{{ asset('payment/v1/css/custom.bundles.css') }}?v={{ config('app.asset_version') }}" rel=stylesheet>
    <link href="{{ asset('payment/v1/image/favicon.png') }}?v={{ config('app.asset_version') }}" rel=stylesheet>
    @yield('style')
</head>

<body>
    {{-- <div class="loading">
        <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div> --}}
    <div class="main main-layout-sm">
        <!-- _custom.header -->
        @include('payment.v1.partials.header')
        <div class="main-wrap">
            <div class="main-inner main-inner-page">
                @yield('content')
            </div>
        </div>
        @include('payment.v1.partials.footer')
    </div>
    @include('payment.v1.partials.modal')
</body>
<script src="{{ asset('payment/v1/js/jquery.bundles.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/jquery.init.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/bootstrap.bundles.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/bootstrap.init.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/select2.bundles.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/select2.init.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/parsley.bundles.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/parsley.init.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/cleave.bundles.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/cleave.init.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/autosize.bundles.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/autosize.init.js') }}?v={{ config('app.asset_version') }}"></script>
<script src="{{ asset('payment/v1/js/custom.min.js') }}?v={{ config('app.asset_version') }}"></script>
<script>
    $(document).ready(function() {
        $('#btnCancelModal')
            .click(function() {
                var x = this;
                var postData = {
                    "btnCancel": "confirm"
                };
                var submitUrl = $(x).closest('form').attr("action");
                $(".loading").show();
                if (urlTransactionCancel != "") {
                    location.href = urlTransactionCancel;
                } else {
                    history.back()
                }
                // $.ajax({
                //     type: "POST",
                //     url: submitUrl,
                //     data: postData,
                //     dataType: 'JSON',
                //     success: function(data) {
                //         if (data.code === '00') {
                //             //Check ifram container
                //             if (self === top) {
                //                 //  location.href = data.url;
                //                 setTimeout(function() {
                //                     location.href = data.url;
                //                 }, 200);
                //             } else {
                //                 //  window.top.location.href = data.url;
                //                 setTimeout(function() {
                //                     window.top.location.href = data.url;
                //                 }, 200);
                //             }
                //             return false;
                //         } else {
                //             if (data.code === '100') {
                //                 alert(
                //                     'Quý khách không thể thực hiện hủy do giao dịch đã được thanh toán thành công. Quý khách sẽ được chuyển hướng về trang mua hàng ngay sau đây.'
                //                 )
                //                 //Check ifram container
                //                 if (self === top) {
                //                     //  location.href = data.url;
                //                     setTimeout(function() {
                //                         location.href = data.url;
                //                     }, 2000);
                //                 } else {
                //                     //  window.top.location.href = data.url;
                //                     setTimeout(function() {
                //                         window.top.location.href = data.url;
                //                     }, 2000);
                //                 }
                //             } else {
                //                 alert(data.message);
                //             }
                //             return false;
                //         }
                //     }
                // });
                $(".loading").hide();
                return false;
            });
    });
</script>
@yield('javascript')


</html>
