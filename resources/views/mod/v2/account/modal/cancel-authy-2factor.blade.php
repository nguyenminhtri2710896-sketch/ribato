<!--  Large modal example -->
<div class="modal fade bs-modal-cancel-authy-2factor modal-sm" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Huỷ xác minh 2 lớp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-authy-2factor frm-ajax-submit" novalidate
                    method="POST" data-ajax-url="{{ route('mod.account.ajax-cancel-authy-2factor') }}"
                    data-close-modal=".bs-modal-cancel-authy-2factor">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-0 mb-1">Mã OTP từ ứng dụng Google Authenticator<span
                                            class="text-danger">(*)</span></label>
                                    <input type="text" inputmode="numeric" pattern="\d*" maxlength="6" minlength="6"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control"
                                        name="code" placeholder="Otp" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Mật khẩu đăng nhập<span
                                            class="text-danger">(*)</span></label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Mật khẩu đăng nhập" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Xác
                                    nhận</button>
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