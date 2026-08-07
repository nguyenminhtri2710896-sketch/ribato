<!--  Large modal example -->
<div class="modal fade bs-modal-user-bank-account-add ajax-select2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Thêm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-bank-account.ajax-add') }}"
                    data-reload-datatable="#data-table-user-bank-account-list">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">Người dùng<span class="text-danger">(*)</span></label>
                                    <select name="user_id" style="width:100%" class="js-data-select2" required=""
                                        data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                        data-text-placeholder="Danh sách thành viên"></select>
                                    <div class="invalid-tooltip">
                                        Vui chọn người dùng
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Tài khoản ngân hàng<span
                                            class="text-danger">(*)</span></label>
                                    <select name="bank_account_id" style="width:100%" class="js-data-select2"
                                        required=""
                                        data-ajax-url="{{ route('backend.bank-account.ajax-select2-get-list') }}"
                                        data-text-placeholder="Danh sách thành viên"></select>
                                    <div class="invalid-tooltip">
                                        Vui chọn tài khoản ngân hàng
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Trạng thái</label>
                                    <select name="status_id" style="width:100%" class="base-select2 form-control">
                                        @foreach (\App\Services\UserBankAccountService::$arrStatusId as $intId => $arrStatusId)
                                            <option value="{{ $intId }}">{{ $arrStatusId['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Lưu
                                    lại</button>
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
