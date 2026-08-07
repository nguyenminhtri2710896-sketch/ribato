<!--  Large modal example -->
<div class="modal fade bs-modal-user-withdraw-add ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.add_new') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.user-withdraw.ajax-add') }}"
                    data-close-modal=".bs-modal-user-withdraw-add"
                    data-reload-datatable="#data-table-user-withdraw-list">
                    <div class="row">
                        @if (auth()->user()->is_admin)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mb-1">{{ __('backend.transfer_type') }}<span
                                                class="text-danger">(*)</span></label>
                                        <select name="type_id" style="width:100%" class="base-select2 form-control">
                                            @foreach (\App\Services\UserWithdrawService::$arrTypeId as $intId => $arrTypeId)
                                                <option value="{{ $intId }}">{{ $arrTypeId['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-tooltip">
                                            {{ __('backend.select_transfer_type') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">Ngân hàng<span class="text-danger">(*)</span></label>
                                    <select name="bank_id" style="width:100%" class="js-data-select2" required=""
                                        data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}"
                                        data-text-placeholder="{{ __('backend.bank') }}" data-params=''></select>
                                    <div class="invalid-tooltip">
                                        {{ __('backend.select_bank') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">{{ __('backend.account_holder') }} <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="bank_account_name"
                                        onblur="this.value = base.removeVietnameseTones(this.value)"
                                        placeholder="{{ __('backend.account_holder') }}" required>
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_account_holder') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">{{ __('backend.account_number') }}<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="bank_account_number"
                                        placeholder="{{ __('backend.account_number') }}" required>
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_account_number') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">{{ __('backend.amount') }}<span class="text-danger">(*)</span></label>
                                    <input type="tel" class="form-control form-control-custom decimal-input"
                                        data-for="input[name='amount']" placeholder="{{ __('backend.amount') }}" required>
                                    <input type="hidden" class="form-control form-control-custom" name="amount"
                                        placeholder="Số dư">
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_amount') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">{{ __('backend.transfer_remark') }}</label>
                                    <input type="text" class="form-control" name="remark"
                                        placeholder="{{ __('backend.transfer_remark') }}" required>
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_transfer_remark') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(auth()->user()->authy_2factor == 1)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mt-3  mb-1">{{ __('backend.otp_code') }}</label>
                                        <input type="text" class="form-control" name="otp" placeholder="{{ __('backend.otp_code') }}"
                                            pattern="\d*" maxlength="6" minlength="6"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        <div class="invalid-tooltip">
                                            {{ __('backend.enter_otp') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> {{ __('backend.send_request') }}</button>
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