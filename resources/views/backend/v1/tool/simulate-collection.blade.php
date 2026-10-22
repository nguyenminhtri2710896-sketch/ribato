@extends('backend.v1.layouts.default')
@section('title', 'Test Collect / Payout (Sandbox)')
@section('javascript')
    <script type="text/javascript">
        $(function () {
            function send(btn, url, form, resultEl) {
                var icon = btn.find("i");
                var iconOriginal = icon.attr("class");
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: url,
                    data: form.serialize(),
                    beforeSend: function () {
                        btn.prop('disabled', true);
                        icon.attr("class", "fas fa-spinner fa-spin");
                    },
                    success: function (result) {
                        resultEl.text(JSON.stringify(result.data, null, 2));
                        if (result.error_code != 0) {
                            var errors = (result.errors || []).map(function (e) { return Array.isArray(e) ? e.join(' ') : e; }).join(' ');
                            toastr["error"](result.message || errors);
                            return;
                        }
                        toastr["success"](result.message);
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                        icon.attr("class", iconOriginal);
                    }
                });
            }

            // Danh sách VA lọc theo người dùng đã chọn (nếu có)
            $("#collect-va").select2({
                dropdownParent: $("#collect-va").parent(),
                allowClear: true,
                placeholder: "Chọn VA",
                ajax: {
                    url: '{{ route('backend.virtual-account.ajax-select2-get-list') }}',
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function (params) {
                        var query = { name: params.term };
                        if ($("#collect-user").val()) {
                            query.user_id = $("#collect-user").val();
                        }
                        return { query: query, page: params.page };
                    }
                }
            });
            $("#collect-user").on("change", function () {
                $("#collect-va").val(null).trigger("change");
            });

            $(".btn-simulate-collection").click(function () {
                send($(this), '{{ route('backend.tool.simulate-collection.ajax-send') }}',
                    $(".form-simulate-collection"), $("#simulate-collection-result"));
            });

            $(".btn-simulate-payout").click(function () {
                send($(this), '{{ route('backend.tool.simulate-collection.ajax-create-payout') }}',
                    $(".form-simulate-payout"), $("#simulate-payout-result"));
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
                            <li class="breadcrumb-item active">Test Collect / Payout (Sandbox)</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Collect: giả lập giao dịch nhận tiền từ cổng</h4>
                        <p class="card-title-desc">Gửi một IPN collection như cổng thật. Chọn người dùng và/hoặc VA: chỉ chọn người dùng thì dùng ngẫu nhiên một VA đang hoạt động của họ.</p>
                        <form class="form-simulate-collection" autocomplete="off" onsubmit="return false;">
                            <div class="mb-3 ajax-select2">
                                <label class="form-label">Người dùng <small class="text-muted">(tùy chọn)</small></label>
                                <select name="user_id" id="collect-user" class="js-data-select2" style="width:100%"
                                    data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                    data-text-placeholder="Chọn người dùng">
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tài khoản VA <small class="text-muted">(tùy chọn, bỏ trống sẽ lấy ngẫu nhiên VA của người dùng)</small></label>
                                <select name="bank_account_number" id="collect-va" style="width:100%"></select>
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
                                <i class="fas fa-paper-plane"></i> Gửi giao dịch collect
                            </button>
                        </form>
                        <h5 class="mt-4">Kết quả</h5>
                        <pre id="simulate-collection-result" style="max-height:300px;overflow:auto;"></pre>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Payout: tạo yêu cầu rút tiền thay user</h4>
                        <p class="card-title-desc">Đi qua đúng luồng tạo rút tiền (trừ số dư, kiểm tra hạn mức). Sandbox sẽ tự giả lập thành công / thất bại ngẫu nhiên rồi gửi IPN payout.</p>
                        <form class="form-simulate-payout" autocomplete="off" onsubmit="return false;">
                            <div class="mb-3 ajax-select2">
                                <label class="form-label">Người dùng</label>
                                <select name="user_id" class="js-data-select2" style="width:100%"
                                    data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                    data-text-placeholder="Chọn người dùng">
                                </select>
                            </div>
                            <div class="mb-3 ajax-select2">
                                <label class="form-label">Ngân hàng nhận</label>
                                <select name="bank_id" class="js-data-select2" style="width:100%"
                                    data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}"
                                    data-text-placeholder="Chọn ngân hàng">
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số tài khoản</label>
                                    <input type="text" name="bank_account_number" class="form-control" placeholder="Số tài khoản nhận">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên chủ tài khoản</label>
                                    <input type="text" name="bank_account_name" class="form-control" placeholder="NGUYEN VAN A">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số tiền</label>
                                    <input type="number" name="amount" min="10000" class="form-control" placeholder="Tối thiểu 10,000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã tham chiếu (ref_code)</label>
                                    <input type="text" name="ref_code" class="form-control" placeholder="Không bắt buộc">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nội dung chuyển khoản</label>
                                <input type="text" name="remark" class="form-control" placeholder="Nhập nội dung chuyển khoản">
                            </div>
                            <button class="btn btn-primary btn-simulate-payout" type="button">
                                <i class="fas fa-paper-plane"></i> Tạo yêu cầu rút tiền
                            </button>
                        </form>
                        <h5 class="mt-4">Kết quả</h5>
                        <pre id="simulate-payout-result" style="max-height:300px;overflow:auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
