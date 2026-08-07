<!--  Large modal example -->
<div class="modal fade bs-modal-user-withdraw-add ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Thêm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-withdraw.ajax-add') }}"
                    data-close-modal=".bs-modal-user-withdraw-add"
                    data-reload-datatable="#data-table-user-withdraw-list">
                    <div class="row">
                        @if (auth()->user()->is_admin)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mb-1">Hình thức chuyển khoản<span
                                                class="text-danger">(*)</span></label>
                                        <select name="type_id" style="width:100%" class="base-select2 form-control">
                                            @foreach (\App\Services\UserWithdrawService::$arrTypeId as $intId => $arrTypeId)
                                                <option value="{{ $intId }}">{{ $arrTypeId['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-tooltip">
                                            Chọn hình thức chuyển khoản
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Ngân hàng<span class="text-danger">(*)</span></label>
                                    <select name="bank_id" style="width:100%" class="js-data-select2" required=""
                                        data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}"
                                        data-text-placeholder="Ngân hàng" data-params=''></select>
                                    <div class="invalid-tooltip">
                                        Vui chọn ngân hàng
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Chủ khoản <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="bank_account_name"
                                        onblur="this.value = base.removeVietnameseTones(this.value)"
                                        placeholder="Chủ khoản" required>
                                    <div class="invalid-tooltip">
                                        Vui nhập tên chủ khoản
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Số tài khoản<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="bank_account_number"
                                        placeholder="Số tài khoản" required>
                                    <div class="invalid-tooltip">
                                        Vui nhập số tài khoản
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Số tiền<span class="text-danger">(*)</span></label>
                                    <input type="tel" class="form-control form-control-custom decimal-input"
                                        data-for="input[name='amount']" placeholder="Số tiền" required>
                                    <input type="hidden" class="form-control form-control-custom" name="amount"
                                        placeholder="Số dư">
                                    <div class="invalid-tooltip">
                                        Vui nhập số tiền
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Nội dung chuyển khoản</label>
                                    <input type="text" class="form-control" name="remark"
                                        placeholder="Nội dung chuyển khoản" required>
                                    <div class="invalid-tooltip">
                                        Vui nhập nội dung chuyển khoản
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(auth()->user()->authy_2factor == 1)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mt-3  mb-1">Mã xác thực</label>
                                        <input type="text" class="form-control" name="otp" placeholder="Mã xác thực"
                                            pattern="\d*" maxlength="6" minlength="6"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        <div class="invalid-tooltip">
                                            Vui nhập mã xác thực
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Gửi yêu
                                    cầu</button>
                                <button type="reset" class="btn btn-danger" data-bs-dismiss="modal"
                                    aria-label="Close"><i class="fas fa-times"></i> Đóng</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->