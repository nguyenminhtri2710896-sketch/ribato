<!--  Large modal example -->
<div class="modal fade bs-modal-update-webhook-url modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Cập nhật WEBHOOK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-account-update-webhook-url frm-ajax-submit"
                    novalidate method="POST" data-ajax-url="{{ route('backend.user-token.ajax-update-webhook-url') }}">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">Collection IPN</label>
                            <div class="col-sm-10 mb-1">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <input type="text" class="form-control ps-15 bg-transparent" value=""
                                            name="webhook_url" placeholder="Collection IPN"  />
                                        <div class="invalid-tooltip">
                                            Vui lòng nhập Collection IPN
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">Payout IPN</label>
                            <div class="col-sm-10 mb-1">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <input type="text" class="form-control ps-15 bg-transparent" value=""
                                            name="webhook_payout_url" placeholder="Payout IPN"  />
                                        <div class="invalid-tooltip">
                                            Vui lòng nhập Payout IPN
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