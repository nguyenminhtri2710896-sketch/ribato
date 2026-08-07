<div class="card overflow-hidden">
    <div class="bg-primary bg-soft">
        <div class="row">
            <div class="col-5 align-self-end">
                <img src="" alt="" class="img-fluid account-cover">
            </div>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="row">
            <div class="col-sm-8 mt-5">
               
                <h5 class="font-size-15 text-truncate account-fullname">null</h5>
                <p class="text-muted mb-0 text-truncate account-email">null</p>
            </div>

            <div class="col-sm-4">

            </div>
        </div>
    </div>
    <div class="px-4 py-3 border-top">
        <div class="row">
            <div class="col-lg-6 d-grid mt-1">
                <a href="#!" data-bs-toggle="modal" data-bs-target=".bs-modal-update-profile"
                    class="btn btn-info waves-effect waves-light btn-block"><i class="fas fa-edit"></i> Cập nhật tài
                    khoản</a>
            </div>

            <div class="col-lg-6 d-grid mt-1">
                <a data-bs-toggle="modal" data-bs-target=".bs-modal-change-password"
                    class="btn btn-warning waves-effect waves-light  btn-block"><i class="fas fa-key"></i> Đổi mật
                    khẩu</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
                    <div class="card-body border-bottom card-custom-header">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 card-title flex-grow-1">Cài đặt bảo mật</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox" id="SwitchAuthy2factor" {{ auth()->user()->authy_2factor ? 'checked' : '' }}>
                                    <label class="form-check-label" for="SwitchAuthy2factor">Bảo mật 2 lớp (Sử dụng cho đăng nhập và yêu cầu rút tiền)</label>
                                </div>
                            </div>
                            <!-- <div class="col-sm-6">
                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox" id="SwitchOtpWithdraw" {{ auth()->user()->otp_widthdraw ? 'checked' : '' }}>
                                    <label class="form-check-label" for="SwitchOtpWithdraw">Tạo OTP chuyển khoản</label>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">API TOKEN</h4>
        <p class="text-muted mb-4">Cấu hình api và thông tin kết nối</p>
        <div>
            
            <table class="table table-nowrap mb-0">
                <tbody>
                    <tr>
                        <td scope="row">Secret key:</td>
                        <td class="api-token-cell">
                            <div class="api-token-box" data-token="" data-revealed="0">
                                <span class="api-token-display text-muted">--</span>
                                <button type="button" class="api-token-btn api-token-toggle" title="Hiện/ẩn token" aria-label="Hiện/ẩn token">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                                <button type="button" class="api-token-btn api-token-copy" title="Sao chép" aria-label="Sao chép">
                                    <i class="mdi mdi-content-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td scope="row">Public key:</td>
                        <td class="api-public-key">
                            <textarea class="form-control" readonly></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">Collection URL:</td>
                        <td class="api-webhook-url"></td>
                    </tr>
                    <tr>
                        <td scope="row">Payout URL:</td>
                        <td class="api-webhook-payout-url"></td>
                    </tr> -->
                </tbody>
            </table>
        </div>
        <div class="">
            <div class="row">
                <!-- <div class="col-lg-6 d-grid mt-1">
                    <a href="#!" data-bs-toggle="modal" data-bs-target=".bs-modal-update-public-key"
                        class="btn btn-info waves-effect waves-light btn-block"><i class="fas fa-key"></i> Upload
                        public key</a>
                </div> -->

                <div class="col-lg-12 d-grid mt-1">
                    <a href="#!" data-bs-toggle="modal" data-bs-target=".bs-modal-update-webhook-url"
                        class="btn btn-info waves-effect waves-light btn-block"><i class="fas fa-edit"></i> Cập nhật WebHook
                        URL</a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Thông tin</h4>
        <p class="text-muted mb-4">Xin chào <strong class="account-fullname"></strong> thông tin cơ bản của
            bạn được chúng tôi ghi nhận</p>
        <div class="table-responsive">
            <table class="table table-nowrap mb-0">
                <tbody>
                    <tr>
                        <th scope="row">Full Name :</th>
                        <td class="account-fullname"></td>
                    </tr>
                    <tr>
                        <th scope="row">Mobile :</th>
                        <td class="account-phone"></td>
                    </tr>
                    <tr>
                        <th scope="row">E-mail :</th>
                        <td class="account-email"></td>
                    </tr>
                    <tr>
                        <th scope="row">Address :</th>
                        <td class="account-address"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div> -->
