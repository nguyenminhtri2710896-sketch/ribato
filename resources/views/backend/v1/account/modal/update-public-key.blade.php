<!--  Large modal example -->
<div class="modal fade bs-modal-update-public-key modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Cập nhật Public key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-account-update-public-key frm-ajax-submit" novalidate
                    method="POST" data-ajax-url="{{ route('backend.user-token.ajax-update-public-key') }}">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">Public Key</label>
                            <div class="col-sm-10 mb-1">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <textarea type="text" class="form-control ps-15 bg-transparent" value=""
                                            name="public_key" placeholder="public key" required=""></textarea>
                                        <div class="invalid-tooltip">
                                            Vui lòng nhập public key
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">Nội dung tạo chữ ký</label>
                            <div class="col-sm-10 mb-1">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <textarea type="text" class="form-control ps-15 bg-transparent" value=""
                                            name="plaintext" placeholder="nội dung" required=""></textarea>
                                        <div class="invalid-tooltip">
                                            Vui lòng nhập nội dung tạo chữ ký
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">Chữ ký</label>
                            <div class="col-sm-10 mb-1">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <textarea type="text" class="form-control ps-15 bg-transparent" value=""
                                            name="signiture" placeholder="Chữ ký" required=""></textarea>
                                        <div class="invalid-tooltip">
                                            Vui lòng nhập chữ ký
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
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
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
