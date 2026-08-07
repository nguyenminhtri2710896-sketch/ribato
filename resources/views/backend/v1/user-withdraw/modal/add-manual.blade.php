<!--  Large modal example -->
<div class="modal fade bs-modal-user-withdraw-add-manual ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="modalUserWithdrawAddManual" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserWithdrawAddManual">Thêm yêu cầu rút thủ công</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-withdraw.ajax-add-manual') }}"
                    data-close-modal=".bs-modal-user-withdraw-add-manual"
                    data-reload-datatable="#data-table-user-withdraw-list">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">Người dùng<span class="text-danger">(*)</span></label>
                                    <select name="user_id" style="width:100%" class="js-data-select2"
                                        data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                        data-text-placeholder="Người dùng" required></select>
                                    <div class="invalid-tooltip">
                                        Vui lòng chọn người dùng
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Cổng<span class="text-danger">(*)</span></label>
                                    <select name="gateway_id" style="width:100%" class="js-data-select2" required
                                        data-ajax-url="{{ route('backend.gateway.ajax-select2-get-list') }}"
                                        data-text-placeholder="Cổng"></select>
                                    <div class="invalid-tooltip">
                                        Vui lòng chọn cổng
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Ngân hàng<span class="text-danger">(*)</span></label>
                                    <select name="bank_id" style="width:100%" class="js-data-select2" required
                                        data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}"
                                        data-text-placeholder="Ngân hàng" data-params=''></select>
                                    <div class="invalid-tooltip">
                                        Vui lòng chọn ngân hàng
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
                                        Vui lòng nhập tên chủ khoản
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
                                        Vui lòng nhập số tài khoản
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Số tiền yêu cầu<span class="text-danger">(*)</span></label>
                                    <input type="tel" class="form-control form-control-custom decimal-input"
                                        data-for="input[name='amount']" placeholder="Số tiền yêu cầu" required>
                                    <input type="hidden" class="form-control form-control-custom" name="amount"
                                        placeholder="Số tiền yêu cầu">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập số tiền yêu cầu
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Ngày yêu cầu</label>
                                    <input type="datetime-local" class="form-control" name="created_at"
                                        placeholder="Ngày yêu cầu (tuỳ chọn)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Mã giao dịch</label>
                                    <input type="text" class="form-control" name="trans_code"
                                        placeholder="Mã giao dịch (tuỳ chọn)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Trạng thái<span class="text-danger">(*)</span></label>
                                    <select name="status_id" style="width:100%" class="base-select2 form-control" required>
                                        @foreach (\App\Services\UserWithdrawService::$arrStatusId as $intId => $arrStatusId)
                                            <option {{ $intId == 2 ? 'selected' : '' }} value="{{ $intId }}">{{ $arrStatusId['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-tooltip">
                                        Vui lòng chọn trạng thái
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Ghi chú</label>
                                    <textarea class="form-control" name="remark" placeholder="Ghi chú" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
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

