<!--  Large modal example -->
<div class="modal fade bs-modal-authy-2factor modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.two_factor_login_security') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-authy-2factor frm-ajax-submit" novalidate
                    method="POST" data-ajax-url="{{ route('backend.account.ajax-validate-authy-2factor') }}"
                    data-close-modal=".bs-modal-authy-2factor">
                    <input value="" class="secret_key" name="secret_key" type="hidden" />
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <p class="text-center">{{ __('backend.scan_qr_auth') }}</p>
                            <p class="image-qrcode text-center"></p>
                            <p class="text-center">{{ __('backend.manual_auth_code_prefix') }} <br /><span class="auth-code" style="    font-weight: bold;
    background: #ccc;
    color: #000;
    border: 1px solid #8f8f8f;
    padding: 2px 10px;"></span><br />{{ __('backend.manual_auth_code_suffix') }} </p>
                            <p class="text-center">{{ __('backend.enter_auth_code_confirm') }}<br />
                                <input style="width: 50%; text-align: center;margin: 0 auto;" type="text" value=""
                                    class="form-control" name="code" placeholder="{{ __('backend.code') }}" required="">
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> {{ __('backend.confirm') }}</button>
                                <button type="reset" class="btn btn-danger" data-bs-dismiss="modal"
                                    aria-label="Close"><i class="fas fa-times"></i> {{ __('backend.close') }}</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->