<!--  Large modal example -->
<div class="modal fade bs-modal-add ajax-select2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Thêm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user.ajax-add') }}" data-reload-datatable="#data-table-list"
                    data-close-modal=".bs-modal-add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Họ<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="first_name" placeholder="Họ" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Tên<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="last_name" placeholder="Tên" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Email<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Số điện thoại<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="phone" placeholder="Số điện thoại"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Mật khẩu<span class="text-danger">(*)</span></label>
                                    <input type="password" class="form-control" name="password" placeholder="Mật khẩu"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Nhắc lại mật khẩu<span
                                            class="text-danger">(*)</span></label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Nhắc lại mật khẩu" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">User cha</label>
                                    <select name="user_parent_id" class="js-data-select2  form-control"
                                        data-ajax-url="{{ route('backend.user.ajax-select2-get-list') }}"
                                        data-text-placeholder="User cha"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Nhóm<span class="text-danger">(*)</span></label>
                                    <select name="group_id" class="js-data-select2  form-control" required=""
                                        data-ajax-url="{{ route('backend.user-group.ajax-select2-get-list') }}"
                                        data-text-placeholder="Nhóm"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation ajax-select2">
                                    <label class="mt-3 mb-1">Tài khoản collection<span
                                            class="text-danger">(*)</span></label>
                                    <select name="gateway_collection_ids[]"
                                        class="js-data-select2  form-control form-control-custom" multiple
                                        data-ajax-url="{{ route('backend.gateway-account.ajax-select2-get-list') }}"
                                        data-placeholder="Tài khoản payout"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation ajax-select2">
                                    <label class="mt-3 mb-1">Tài khoản payout<span
                                            class="text-danger">(*)</span></label>
                                    <select name="gateway_withdraw_ids[]"
                                        class="js-data-select2  form-control form-control-custom" multiple
                                        data-ajax-url="{{ route('backend.gateway-account.ajax-select2-get-list') }}"
                                        data-placeholder="Tài khoản payout"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Khung giờ chốt giao dịch</label>
                                    <select name="for_control_type" style="width:100%"
                                        class="base-select2 form-control">
                                        <option value="1">2h</option>
                                        <option value="4">15h30</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Thời gian chốt giao dịch</label>
                                    <select name="number_day_for_control" style="width:100%"
                                        class="base-select2 form-control">
                                        <option value="0">N+0</option>
                                        <option value="1">N+1</option>
                                        <option value="2">N+2</option>
                                        <option value="3">N+3</option>
                                        <option value="4">N+4</option>
                                        <option value="5">N+5</option>
                                        <option value="6">N+6</option>
                                        <option value="7">N+7</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">Trạng thái</label>
                                    <select name="actived" class="base-select2 form-control">
                                        <option value="1">Kích hoạt</option>
                                        <option value="0">Dừng kích hoạt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
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
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->