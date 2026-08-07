<!--  Large modal example -->
<div class="modal fade bs-modal-authy-2factor modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Bảo mật đăng nhập 2 lớp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-authy-2factor frm-ajax-submit" novalidate
                    method="POST" data-ajax-url="{{ route('backend.account.ajax-validate-authy-2factor') }}"
                    data-close-modal=".bs-modal-authy-2factor">
                    <input value="" class="secret_key" name="secret_key" type="hidden" />
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <p class="text-center">Sử dụng ứng dụng Google Authenticator quét mã bên dưới</p>
                            <p class="image-qrcode text-center"></p>
                            <p class="text-center">Hoặc nhập thủ công mã <br /><span class="auth-code" style="    font-weight: bold;
    background: #ccc;
    color: #000;
    border: 1px solid #8f8f8f;
    padding: 2px 10px;"></span><br />vào ứng dụng Google Authenticator </p>
                            <p class="text-center">Sau khi tạo thành công vui lòng nhập mã trên ứng dụng Google
                                Authenticator vào ô bên dưới để xác thực<br />
                                <input style="width: 50%; text-align: center;margin: 0 auto;" type="text" value=""
                                    class="form-control" name="code" placeholder="Mã" required="">
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Xác nhận</button>
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