<div class="modal fade bs-modal-user-withdraw-whitelist-add ajax-select2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm mới whitelist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-withdraw-whitelist.ajax-add') }}"
                    data-reload-datatable="#data-table-user-withdraw-whitelist-list">
                    
                    <div class="row"><div class="col-md-12"><div class="form-group"><div class="has-validation">
                        <label class="mb-1">Người dùng<span class="text-danger">(*)</span></label>
                        <select name="user_id" style="width:100%" class="js-data-select2" required=""
                            data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                            data-text-placeholder="Danh sách thành viên"></select>
                        <div class="invalid-tooltip">Vui lòng chọn người dùng</div>
                    </div></div></div></div>
                    <div class="row"><div class="col-md-12"><div class="form-group"><div class="has-validation">
                        <label class="mt-3 mb-1">Ngân hàng<span class="text-danger">(*)</span></label>
                        <select name="bank_id" style="width:100%" class="js-data-select2" required=""
                            data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}"
                            data-text-placeholder="Danh sách ngân hàng"></select>
                        <div class="invalid-tooltip">Vui lòng chọn ngân hàng</div>
                    </div></div></div></div>
                    <div class="row"><div class="col-md-12"><div class="form-group"><div class="has-validation">
                                <label class="mt-3 mb-1">Số tài khoản<span class="text-danger">(*)</span></label>
                                <input type="text" name="bank_account_number" class="form-control" required>
                                <div class="invalid-tooltip">Vui lòng nhập số tài khoản</div>
                            </div></div></div></div>
                    <div class="row"><div class="col-md-12"><div class="form-group"><div class="has-validation">
                                <label class="mt-3 mb-1">Tên chủ khoản</label>
                                <input type="text" name="bank_account_name" class="form-control" >
                                <div class="invalid-tooltip">Vui lòng nhập tên chủ khoản</div>
                            </div></div></div></div>
                    <div class="row"><div class="col-md-12"><div class="form-group"><div class="has-validation">
                                <label class="mt-3 mb-1">Ghi chú</label>
                                <input type="text" name="note" class="form-control" >
                                <div class="invalid-tooltip">Vui lòng nhập ghi chú</div>
                            </div></div></div></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Lưu lại</button>
                                <button type="reset" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i> Đóng</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
