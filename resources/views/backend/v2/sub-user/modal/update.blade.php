<!--  Large modal example -->
<div class="modal fade bs-modal-update" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Cập nhật thông tin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body get-profile-info">
                <form class="form-horizontal needs-validation frm-ajax-submit user-form-submit-update" novalidate
                    autocomplete="off" method="POST" data-ajax-url="{{ route('backend.sub-user.ajax-update') }}"
                    data-reload-datatable="#data-table-list" data-trigger="user-form-submit-update-success"
                    data-close-modal=".bs-modal-update">
                    <input type="hidden" name="sub_user_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Họ<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="first_name" placeholder="Họ" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Tên<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="last_name" placeholder="Tên" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Email<span class="text-danger">(*)</span></label>
                                    <input type="text" readonly class="form-control" name="email" placeholder="Email"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Số điện thoại<span class="text-danger">(*)</span></label>
                                    <input type="text" readonly class="form-control" name="phone"
                                        placeholder="Số điện thoại" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Trạng thái</label>
                                    <select name="actived" style="width:100%" class="base-select2 form-control">
                                        <option value="1">Kích hoạt</option>
                                        <option value="0">Dừng kích hoạt</option>
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