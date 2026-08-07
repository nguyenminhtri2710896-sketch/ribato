<!--  Large modal example -->
<div class="modal fade bs-modal-user-withdraw-add-multible ajax-select2" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-custom-sm-1">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.add_new') }}</h5>
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
                                    <label class="mb-1">{{ __('backend.content') }}<span class="text-danger">(*)</span></label>
                                    <textarea type="text" class="form-control" name="content" placeholder="{{ __('backend.content') }}"
                                        rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->authy_2factor == 1)
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="has-validation">
                                    <label class="mt-3  mb-1">{{ __('backend.otp_code') }}</label>
                                    <input type="text" class="form-control" name="otp" placeholder="{{ __('backend.otp_code') }}" required  pattern="\d*" maxlength="6" minlength="6"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
                                    <div class="invalid-tooltip">
                                        {{ __('backend.enter_otp') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <srong>{{ __('backend.input_content') }}</srong>
                            <br />
                            <span class="text-danger" style="font-weight:bold">{{ __('backend.format_description') }}</span><br />
                            {{ __('backend.example') }}: VCB,0441003986682,NGUYEN MINH TRI,1000000,SA 0405
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
                                        <th class="text-left" data-priority="3">{{ __('backend.bank') }}</th>
                                        <th class="text-left" data-priority="4">{{ __('backend.account_number') }}</th>
                                        <th class="text-left" data-priority="4">{{ __('backend.account_name') }}</th>
                                        <th class="text-left" data-priority="5">{{ __('backend.amount') }}</th>
                                        <th class="text-left" data-priority="5">{{ __('backend.transfer_remark') }}</th>
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
                                    {{ __('backend.check') }}</button>
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