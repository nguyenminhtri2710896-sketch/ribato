<!--  Large modal example -->
<div class="modal fade bs-modal-transaction-qrcode ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Tạo QR giao dịch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="has-validation">
                                <label class="mt-0 mb-1">Số tiền<span class="text-danger">(*)</span></label>
                                <input type="tel" class="form-control form-control-custom decimal-input"
                                    data-for="input[name='amount']" placeholder="Số tiền" required>
                                <input type="hidden" class="form-control form-control-custom" name="amount"
                                    placeholder="Số tiền">
                                <div class="invalid-tooltip">
                                    Vui lòng nhập số tiền giao dịch
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12  text-center bank-text-info">

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 image-qrcode  text-center">

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-success  btn-transaction-qrcode-create"><i
                                    class="fas fa-save"></i> Tạo mã QR</button>
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="fas fa-times"></i> Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->