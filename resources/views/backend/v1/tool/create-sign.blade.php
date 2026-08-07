@extends('backend.v1.layouts.tool')
@section('title', __('Xác nhận yêu cầu rút tiền'))
@section('style')
@endsection
@section('javascript')
    <script src="{{ asset('backend/v1/customes/js/tool.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        var urlAjaxCreateSign = '{{ route('backend.tool.ajax-create-sign') }}';
        tool.index();
    </script>
@endsection
@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tạo sign</h4>
                        <p class="card-title-desc"></p>
                        <form class="needs-validation  form-create-sign" novalidate="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Private Key</label>
                                        <textarea type="text" class="form-control" id="private_key" style="width: 100%;"
                                            name="private_key" rows="10" placeholder="Private Key" value=""
                                            required=""></textarea>
                                        <div class="valid-feedback">
                                            Vui lòng nhập private Key
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">secret key</label>
                                        <textarea type="text" class="form-control" id="plain_text" placeholder="secret key"
                                            name="secret_code" style="width: 100%;" value="" required=""></textarea>
                                        <div class="valid-feedback">
                                            Vui lòng nhập secret key
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Plaintext</label>
                                        <textarea type="text" class="form-control" id="plain_text" placeholder="Plaintext"
                                            name="plain_text" style="width: 100%;" value="" required=""></textarea>
                                        <div class="valid-feedback">
                                            Vui lòng nhập Plaintext
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Sign</label>
                                        <textarea type="text" class="form-control" id="sign" placeholder="Sign"
                                            style="width: 100%;" value=""></textarea>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-create-sign" type="button">Tạo</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>
    </div>
@endsection