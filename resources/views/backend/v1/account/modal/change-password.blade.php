<!--  Large modal example -->
<div class="modal fade bs-modal-change-password modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.change_password') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-account-change-password frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.account.ajax-change-password') }}">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">{{ __('backend.old_password') }}</label>
                                    <input type="password" class="form-control" name="old_password"
                                        placeholder="{{ __('backend.old_password') }}" required="">
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_old_password') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">{{ __('backend.new_password') }}</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="{{ __('backend.new_password') }}" required="">
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_new_password') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">{{ __('backend.repeat_new_password') }}</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="{{ __('backend.repeat_new_password') }}" required="">
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_repeat_new_password') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> {{ __('backend.change_password') }}</button>
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
