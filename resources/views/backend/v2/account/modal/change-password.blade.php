<!--  Large modal example -->
<div class="modal fade bs-modal-change-password modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Đổi mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-account-change-password frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.account.ajax-change-password') }}">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">Mật khẩu cũ</label>
                                    <input type="password" class="form-control" name="old_password"
                                        placeholder="Mật khẩu cũ" required="">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập mật khẩu cũ
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">Mật khẩu mới</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Mật khẩu mới" required="">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập mật khẩu mới
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">Nhập lại mật khẩu mới</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Nhắc lại mật khẩu mới" required="">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập lại mật khẩu mới
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Đổi mật
                                    khẩu</button>
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
