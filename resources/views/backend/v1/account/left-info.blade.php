<div class="card overflow-hidden">
    <div class="bg-primary bg-soft">
        <div class="row">
            <div class="col-7">
                <div class="text-primary p-3">
                    <h5 class="text-primary">{{ __('backend.welcome_user') }}</h5>
                    <p>{{ __('backend.thanks_for_coming') }}</p>
                </div>
            </div>
            <div class="col-5 align-self-end">
                <img src="" alt="" class="img-fluid account-cover">
            </div>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="row">
            <div class="col-sm-8">
                <div class="avatar-md profile-user-wid mb-4">
                    <a href="{{ route('backend.account.index') }}"> <img src="" alt=""
                            class="img-thumbnail rounded-circle account-avatar"></a>
                </div>
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
                    class="btn btn-info waves-effect waves-light btn-block"><i class="fas fa-edit"></i> {{ __('backend.update_account') }}</a>
            </div>

            <div class="col-lg-6 d-grid mt-1">
                <a data-bs-toggle="modal" data-bs-target=".bs-modal-change-password"
                    class="btn btn-warning waves-effect waves-light  btn-block"><i class="fas fa-key"></i> {{ __('backend.change_password') }}</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">API TOKEN</h4>
        <p class="text-muted mb-4">{{ __('backend.api_config_info') }}</p>
        <div class="table-responsive">
            <table class="table table-nowrap mb-0">
                <tbody>
                    <tr>
                        <td scope="row">Secret key:</td>
                        <td class="api-token"></td>
                    </tr>
                    <tr>
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
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="">
            <div class="row">
                <div class="col-lg-6 d-grid mt-1">
                    <a href="#!" data-bs-toggle="modal" data-bs-target=".bs-modal-update-public-key"
                        class="btn btn-info waves-effect waves-light btn-block"><i class="fas fa-key"></i> Upload
                        public key</a>
                </div>

                <div class="col-lg-6 d-grid mt-1">
                    <a href="#!" data-bs-toggle="modal" data-bs-target=".bs-modal-update-webhook-url"
                        class="btn btn-info waves-effect waves-light btn-block"><i class="fas fa-edit"></i> Update WebHook
                        URL</a>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12 d-grid">
                    <a href="{{ route('backend.personal-token.index') }}"
                        class="btn btn-primary waves-effect waves-light btn-block"><i class="fas fa-cog"></i> Quản lý Token cá nhân</a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">{{ __('backend.information') }}</h4>
        <p class="text-muted mb-4">{{ __('backend.basic_info_recorded', ['name' => auth()->user()->fullname]) }}</p>
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
</div>
