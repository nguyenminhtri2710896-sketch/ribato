<!--  Large modal example -->
<div class="modal fade bs-modal-deduct-money" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Trừ số dư ví</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-transaction.ajax-deduct-money') }}"
                    data-reload-datatable="#data-table-user-transaction-list" 
                     data-close-modal=".bs-modal-deduct-money">
                    <input type="hidden" name="user_id" value="{{ $intUserId }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-0">Số dư trừ<span class="text-danger">(*)</span></label>
                                    <input type="tel" class="form-control form-control-custom decimal-input"
                                        data-for="input[name='amount']" placeholder="Số dư" required>
                                    <input type="hidden" class="form-control form-control-custom" name="amount"
                                        placeholder="Số dư">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-2 mb-0">Nội dung</label>
                                    <textarea type="text" class="form-control form-control-custom" name="note"
                                        placeholder="Ghi chú"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-warning "><i class="fas fa-save"></i> Trừ</button>
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