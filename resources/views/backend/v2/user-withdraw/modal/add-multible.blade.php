<!--  Large modal example -->
<div class="modal fade bs-modal-user-withdraw-add-multible ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm-1">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Thêm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-withdraw.ajax-add-multible') }}"
                    data-reload-datatable="#data-table-user-withdraw-list">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">Nội dung<span class="text-danger">(*)</span></label>
                                    <textarea type="text" class="form-control" name="content" placeholder="Nội dung"
                                        rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->authy_2factor == 1)
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Mã xác thực</label>
                                    <input type="text" class="form-control" name="otp" placeholder="Mã xác thực" required  pattern="\d*" maxlength="6" minlength="6"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
                                    <div class="invalid-tooltip">
                                        Vui nhập mã xác thực
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <srong>Nội dung nhập</srong>
                            <br />
                            <span class="text-danger" style="font-weight:bold">[Mã BANK],[Số TK],[Tên TK],[Số tiền],[Nội
                                dung]</span><br />
                            VD: VCB,0441003986682,NGUYEN MINH TRI,1000000,SA 0405
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 txt-content" style="    max-height: 400px;
    overflow: hidden;
    overflow-y: scroll;">
                            <table class="table  table-striped dt-responsive  wrap w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center" data-priority="1">#</th>
                                        <th class="text-left" data-priority="3">Ngân hàng</th>
                                        <th class="text-left" data-priority="4">Số TK</th>
                                        <th class="text-left" data-priority="4">Tên TK</th>
                                        <th class="text-left" data-priority="5">Số tiền</th>
                                        <th class="text-left" data-priority="5">Nội dung CK</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="button" class="btn btn-success "
                                    onclick="userWithdraw.addMultibleCheck(this)"><i class="far fa-calendar-check"></i>
                                    Kiểm
                                    tra</button>
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Gửi yêu
                                    cầu</button>
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