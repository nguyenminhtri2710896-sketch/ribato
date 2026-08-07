<!--  Large modal example -->
<div class="modal fade bs-modal-change-password modal-sm" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Đổi mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate
                    method="POST" data-ajax-url="{{ route('backend.user.ajax-change-password') }}"
                    data-success-model-hide=".bs-modal-change-password"
                    
                     data-close-modal=".bs-modal-change-password">
                    <input type="hidden" class="form-control form-control-custom" name="user_id"
                        value="{{ $intUserId }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-0 mb-0">Mật khẩu</label>
                                    <input type="password" class="form-control form-control-custom" name="password"
                                        placeholder="Mật khẩu" required="">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập mật khẩu
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-2 mb-0">Nhập lại mật khẩu </label>
                                    <input type="password" class="form-control form-control-custom"
                                        name="password_confirmation" placeholder="Nhắc lại mật khẩu" required="">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập lại mật khẩu
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success btn-lg-custom"><i class="fas fa-save"></i>
                                    Đổi mật
                                    khẩu</button>
                                <button type="reset" class="btn btn-danger btn-lg-custom" data-bs-dismiss="modal"
                                    aria-label="Close"><i class="fas fa-times"></i> Đóng</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->