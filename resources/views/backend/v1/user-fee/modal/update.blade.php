<!--  Large modal example -->
<div class="modal fade bs-modal-update-user-fee" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Cập nhật phí</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-fee.ajax-update') }}"
                    data-reload-datatable="#data-table-user-fee-list">
                    <input type="hidden" class="form-control form-control-custom" name="id"
                        value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-0">Loại phí<span class="text-danger">(*)</span></label>
                                    <select name="type_id" style="width:100%" class="base-select2  form-control form-control-custom">
                                        @foreach (\App\Services\UserFeeService::$arrTypeId as $intId => $arrTypeId)
                                        <option value="{{ $intId }}">{{ $arrTypeId['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-tooltip">
                                        Vui chọn chọn loại phí
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2">
                                <div class="has-validation">
                                    <label class="mb-0">Phí<span class="text-danger">(*)</span></label>
                                    <input type="tel" class="form-control form-control-custom decimal-input"
                                        data-for="input[name='fee']" placeholder="Phí" required>
                                    <input type="hidden" class="form-control form-control-custom" name="fee"
                                        placeholder="Phí">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập phí
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2">
                                <div class="has-validation">
                                    <label class="mb-0">Phí tối thiểu<span class="text-danger">(*)</span></label>
                                    <input type="tel" class="form-control form-control-custom decimal-input"
                                        data-for="input[name='min_fee']" placeholder="Phí tối thiểu" required>
                                    <input type="hidden" class="form-control form-control-custom" name="min_fee"
                                        placeholder="Phí tối thiểu">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập phí tối thiểu
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Cập nhật</button>
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