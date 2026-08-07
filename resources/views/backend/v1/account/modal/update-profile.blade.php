<!--  Large modal example -->
<div class="modal fade bs-modal-update-profile modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.update_account') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation form-account-update-profile frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.account.ajax-update-info') }}">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">{{ __('backend.fullname') }}</label>
                            <div class="col-sm-5 mb-1">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <input type="text" class="form-control ps-15 bg-transparent" value=""
                                            name="first_name" placeholder="{{ __('backend.first_name') }}" required="">
                                        <div class="invalid-tooltip">
                                            {{ __('backend.enter_first_name') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 mb-1">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <input type="text" class="form-control ps-15 bg-transparent" value=""
                                            name="last_name" placeholder="{{ __('backend.last_name') }}" required="">
                                        <div class="invalid-tooltip">
                                            {{ __('backend.enter_last_name') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <div class="form-group position-relative">
                                    <div class="input-group">
                                        <input type="text" value="" name="email"
                                            class="form-control ps-15 bg-transparent" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">{{ __('backend.phone') }}</label>
                            <div class="col-sm-10">
                                <div class="form-group position-relative">
                                    <div class="input-group">
                                        <input type="text" name="phone" value=""
                                            class="form-control ps-15 bg-transparent" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-12">
                        <div class="form-group row mt-3 mb-1">
                            <label for="example-email-input" class="col-sm-2 col-form-label">{{ __('backend.address') }}</label>
                            <div class="col-sm-10">
                                <div class="form-group position-relative">
                                    <div class="input-group has-validation">
                                        <input type="text" value="" class="form-control ps-15 bg-transparent"
                                            name="address" placeholder="{{ __('backend.address') }}" required="">
                                        <div class="invalid-tooltip">
                                            {{ __('backend.enter_address') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-3 text-right">
                                    <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> {{ __('backend.save') }}</button>
                                    <button type="reset" class="btn btn-danger" data-bs-dismiss="modal"
                                        aria-label="Close"><i class="fas fa-times"></i> {{ __('backend.close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
