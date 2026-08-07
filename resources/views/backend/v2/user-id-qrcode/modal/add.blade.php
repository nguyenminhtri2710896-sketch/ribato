<!--  Large modal example -->
<div class="modal fade bs-modal-user-id-qrcode-add ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Thêm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-id-qrcode.ajax-add') }}"
                    data-reload-datatable="#data-table-user-id-qrcode-list">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class=" mb-1">Tên <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="Tên"
                                        required>
                                    <div class="invalid-tooltip">
                                        Vui nhập tên
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Tài khoản VA<span class="text-danger">(*)</span></label>
                                    <select name="user_bank_account_id" style="width:100%" class="js-data-select2"
                                        required="" data-ajax-url="{{ route('backend.user-bank-account.ajax-select2-get-list') }}"
                                        data-text-placeholder="Tài khoản VA" data-params=''></select>
                                    <div class="invalid-tooltip">
                                        Vui chọn tài khoản VA
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Mã<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="code" placeholder="Mã" required>
                                    <div class="invalid-tooltip">
                                        Vui nhập mã
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Ghi chú<span class="text-danger">(*)</span></label>
                                    <textarea class="form-control" name="note"></textarea>
                                    <div class="invalid-tooltip">
                                        Vui nhập ghi chú
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Tạo mới</button>
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