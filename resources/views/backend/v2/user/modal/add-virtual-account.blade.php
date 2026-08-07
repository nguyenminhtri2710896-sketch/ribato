<!--  Large modal example -->
<div class="modal fade bs-modal-add-user-virtual-account" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Tạo tài khoản VA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body get-profile-info">
                <form class="form-horizontal needs-validation frm-ajax-submit user-form-submit-update" novalidate
                    method="POST" data-ajax-url="{{ route('backend.user-virtual-account.ajax-add') }}"
                    data-reload-datatable="#data-table-user-virtual-account-list"
                    data-close-modal=".bs-modal-add-user-virtual-account">
                    <input type="hidden" name="user_id" value="{{ request()->all()["user_id"] ?? 0 }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation ajax-select2">
                                    <label class="mt-1 mb-1">Ngân hàng<span class="text-danger">(*)</span></label>
                                    <select name="bank_id" class="js-data-select2  form-control form-control-custom"
                                        data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}?query_in_list[short_code]=MSB,BIDV,TCB"
                                        data-placeholder="Ngân hàng"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Chủ khoản<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="bank_account_name"
                                        placeholder="Tên chủ khoản" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation ajax-select2">
                                    <label class="mt-3 mb-1">Tài khoản cổng<span class="text-danger">(*)</span></label>
                                    <select name="gateway_account_id"
                                        class="js-data-select2  form-control form-control-custom"
                                        data-ajax-url="{{ route('backend.gateway-account.ajax-select2-get-list') }}"
                                        data-placeholder="Tài khoản cổng"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Tạo
                                    VA</button>
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