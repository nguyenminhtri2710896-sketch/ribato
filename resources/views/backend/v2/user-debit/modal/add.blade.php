<!--  Large modal example -->
<div class="modal fade bs-modal-user-debit-add  ajax-select2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Thêm công nợ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-debit.ajax-add') }}"
                    data-reload-datatable="#data-table-list">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-0">Người dùng<span class="text-danger">(*)</span></label>
                                    <select name="user_id" style="width:100%" class="form-control form-control-custom js-data-select2"
                                        data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}">
                                    </select>
                                    <div class="invalid-tooltip">
                                        Vui chọn chọn người dùng
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2">
                                <div class="has-validation">
                                    <label class="mb-0">Loại phí<span class="text-danger">(*)</span></label>
                                    <select name="type_id" style="width:100%"
                                        class="base-select2  form-control form-control-custom">
                                        @foreach (\App\Services\UserDebitService::$arrTypeId as $intId => $arrTypeId)
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
                                    <label class="mb-0">Số tiền<span class="text-danger">(*)</span></label>
                                    <input type="tel" class="form-control form-control-custom decimal-input"
                                        data-for="input[name='amount']" placeholder="Số tiền" required>
                                    <input type="hidden" class="form-control form-control-custom" name="amount"
                                        placeholder="Phí">
                                    <div class="invalid-tooltip">
                                        Vui lòng nhập số tiền
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2">
                                <div class="has-validation">
                                    <label class="mb-0">Chi chú </label>
                                    <textarea name="note" class="form-control form-control-custom "></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2">
                                <div class="has-validation">
                                    <label class="mb-0">Ngày mượn<span class="text-danger">(*)</span></label>
                                    <div class="input-group" id="datepicker1">
                                        <input type="text" class="form-control" placeholder="dd/mm/yyyy" name="debit_at"
                                            data-date-format="dd/mm/yyyy" data-date-container='#datepicker1' value="{{ date('d/m/Y') }}"
                                            data-provide="datepicker">

                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                    </div><!-- input-group -->
                                    <div class="invalid-tooltip">
                                        Vui lòng chọn ngày mượn
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Thêm
                                    mới</button>
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