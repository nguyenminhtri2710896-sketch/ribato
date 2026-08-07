<!--  Large modal example -->
<div class="modal fade bs-modal-update ajax-select2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.update') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                    data-ajax-url="{{ route('backend.bank-account.ajax-update') }}"  data-reload-datatable="#data-table-list">
                    <input type="hidden" class="form-control" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mb-1">{{ __('backend.account_holder') }}<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="bank_account_name"
                                        placeholder="{{ __('backend.account_holder') }}" required="">
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_account_holder') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.account_number') }}<span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="bank_account_number"
                                        placeholder="{{ __('backend.account_number') }}" required="">
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_account_number') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.bank') }}<span class="text-danger">(*)</span></label>
                                    <select name="bank_id" style="width:100%" class="form-control" required=""
                                        data-ajax-url="{{ route('backend.bank.ajax-select2-get-list') }}"
                                        data-text-placeholder="{{ __('backend.bank_list') }}"></select>
                                    <div class="invalid-tooltip">
                                        {{ __('backend.select_bank') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.status') }}</label>
                                    <select name="status_id" style="width:100%" class="base-select2 form-control">
                                        @foreach (\App\Services\BankAccountService::$arrStatusId as $intId => $arrStatusId)
                                            <option value="{{ $intId }}">{{ $arrStatusId['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3 mb-1">{{ __('backend.sort') }}</label>
                                    <input type="number" class="form-control" name="sorting" placeholder="{{ __('backend.sort') }}">
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
