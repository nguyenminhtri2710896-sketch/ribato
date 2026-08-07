<div class="modal fade bs-modal-ipn-collection-detail" tabindex="-1" role="dialog"
    aria-labelledby="ipnCollectionDetailModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ipnCollectionDetailModal">Chi tiết IPN Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Mã giao dịch</label>
                            <input type="text" class="form-control" name="transaction_code" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Mã đơn</label>
                            <input type="text" class="form-control" name="transaction_ref_code" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Người dùng</label>
                            <input type="text" class="form-control" name="transaction_user_email" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <input type="text" class="form-control" name="callback_status_text" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Retry</label>
                            <input type="text" class="form-control" name="callback_total_retry" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thông báo</label>
                    <textarea class="form-control" rows="2" name="message" readonly></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thông tin yêu cầu</label>
                    <textarea class="form-control" rows="8" name="param_request" readonly></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thông tin phản hồi</label>
                    <textarea class="form-control" rows="8" name="param_response" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


