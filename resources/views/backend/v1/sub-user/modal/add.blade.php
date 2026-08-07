<!--  Large modal example -->
<div class="modal fade bs-modal-add ajax-select2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.add_new') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.sub-user.ajax-add') }}" data-reload-datatable="#data-table-list"
                    data-close-modal=".bs-modal-add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.first_name') }}<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="first_name" placeholder="{{ __('backend.first_name') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.last_name') }}<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="last_name" placeholder="{{ __('backend.last_name') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.email') }}<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="email" placeholder="{{ __('backend.email') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.phone') }}<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="phone" placeholder="{{ __('backend.phone') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.password') }}<span class="text-danger">(*)</span></label>
                                    <input type="password" class="form-control" name="password" placeholder="{{ __('backend.password') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.repeat_password') }}<span
                                            class="text-danger">(*)</span></label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="{{ __('backend.repeat_password') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.status') }}</label>
                                    <select name="actived" class="base-select2 form-control">
                                        <option value="1">{{ __('backend.active') }}</option>
                                        <option value="0">{{ __('backend.deactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> {{ __('backend.save') }}</button>
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