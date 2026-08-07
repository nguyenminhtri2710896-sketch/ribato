<div class="modal fade bs-modal-history" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lịch sử biến động tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <form id="form-filter-gateway-account-history" autocomplete="off">
                            <input type="hidden" name="gateway_account_histories.gateway_account_id" id="history-gateway-account-id">
                            <div class="row">
                                <div class="col-md-8">
                                    <label>Ngày tạo</label>
                                    <div class="input-daterange input-group" id="history-created-at"
                                        data-date-format="dd/mm/yyyy" data-date-autoclose="true"
                                        data-provide="datepicker" data-date-container="#history-created-at">
                                        <input type="text" class="form-control"
                                            name="created_at_from" placeholder="Từ ngày">
                                        <input type="text" class="form-control"
                                            name="created_at_to" placeholder="Đến ngày">
                                    </div>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <button type="button" class="btn btn-info btn-block" onclick="gatewayAccount.getHistory()"><i class="fa fa-search"></i> Tìm</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="data-table-gateway-account-history"
                            data-ajax-url="{{ route('backend.gateway-account.ajax-get-history-list') }}"
                            data-id-filter="form-filter-gateway-account-history"
                            data-key="gateway_account_histories"
                            class="table table-striped dt-responsive wrap w-100">
                            <thead>
                                <tr>
                                    <th class="text-center" data-priority="1">#</th>
                                    <th class="text-left" data-priority="2">Tên tài khoản</th>
                                    <th class="text-right" data-priority="3">Số dư</th>
                                    <th class="text-center" data-priority="4">Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
