@extends('backend.v1.layouts.default')
@section('title', 'Test Collect (Sandbox)')
@section('javascript')
    <script type="text/javascript">
        $(function () {
            $(".btn-simulate-collection").click(function () {
                var btn = $(this);
                var icon = btn.find("i");
                var iconOriginal = icon.attr("class");
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: '{{ route('backend.tool.simulate-collection.ajax-send') }}',
                    data: $(".form-simulate-collection").serialize(),
                    beforeSend: function () {
                        btn.prop('disabled', true);
                        icon.attr("class", "fas fa-spinner fa-spin");
                    },
                    success: function (result) {
                        if (result.error_code != 0) {
                            toastr["error"](result.message);
                            return;
                        }
                        toastr["success"](result.message);
                        $("#simulate-result").text(JSON.stringify(result.data, null, 2));
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                        icon.attr("class", iconOriginal);
                    }
                });
            });
        });
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('backend.admin') }}</a></li>
                            <li class="breadcrumb-item active">Test Collect (Sandbox)</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Giả lập giao dịch collect từ cổng</h4>
                        <p class="card-title-desc">Hệ thống sẽ gửi một IPN collection như cổng thật tới VA đã chọn.</p>
                        <form class="form-simulate-collection" autocomplete="off" onsubmit="return false;">
                            <div class="mb-3 ajax-select2">
                                <label class="form-label">Tài khoản VA</label>
                                <select name="bank_account_number" class="js-data-select2" style="width:100%"
                                    data-ajax-url="{{ route('backend.virtual-account.ajax-select2-get-list') }}"
                                    data-text-placeholder="Chọn VA">
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số tiền</label>
                                <input type="number" name="amount" min="1" class="form-control" placeholder="Nhập số tiền">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nội dung chuyển khoản</label>
                                <textarea name="content" rows="3" class="form-control" placeholder="Nhập nội dung chuyển khoản"></textarea>
                            </div>
                            <button class="btn btn-primary btn-simulate-collection" type="button">
                                <i class="fas fa-paper-plane"></i> Gửi giao dịch
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Kết quả</h4>
                        <pre id="simulate-result" style="max-height:500px;overflow:auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
