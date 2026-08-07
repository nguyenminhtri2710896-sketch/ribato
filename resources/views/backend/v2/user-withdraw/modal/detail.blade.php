<!--  Large modal example -->
<div class="modal fade bs-modal-user-withdraw-detail ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Thêm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-withdraw.ajax-change-status') }}"
                    data-reload-datatable="#data-table-user-withdraw-list">
                    <input type="hidden" name="id" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-2"><strong>Thông tin chuyển khoản</strong></label>
                                    <div class="info-tranfer">
                                        <strong>Ngân hàng:</strong> <span class="bank bg-info badge"></span><br />
                                        <strong>Chủ khoản:</strong> <span
                                            class="bank-account bg-success badge"></span><br />
                                        <strong>Số tài khoản:</strong> <span
                                            class="bank-number bg-success badge"></span><br />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Trạng thái</label>
                                    <select name="status_id" style="width:100%" class="base-select2 form-control">
                                        @foreach (\App\Services\UserWithdrawService::$arrStatusId as $intId => $arrStatusId)
                                            <option value="{{ $intId }}">{{ $arrStatusId['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Ghi chú</label>
                                    <textarea class="form-control" name="note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Cập
                                    nhật</button>
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
