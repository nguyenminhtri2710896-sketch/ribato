@extends('backend.v1.layouts.auth')
@section('title', __('Trang Chủ'))
@section('style')
    <!-- INSET STYLE SCRIPT -->

@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v1/customes/js/auth.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        auth.signIn();
    </script>
@endsection
@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-12 align-self-center" style="    padding: 30px 0;text-align: center;    font-size: 28px;
            font-weight: bold;
            color: #3b56d9; ">
                                    {{ env('APP_NAME') }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="p-2">
                                <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                                    data-ajax-url="{{ route('backend.auth.ajax-sign-in') }}"
                                    data-redirect-url="{{ route('backend.index.index') }}">
                                    <div class="mb-3 position-relative ">
                                        <label for="username" class="form-label">Email</label>
                                        <input name="email" type="text" class="form-control" id="email"
                                            placeholder="Vui lòng nhập email" required>
                                        <div class="invalid-tooltip">
                                            Vui lòng nhập email
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative">
                                        <label class="form-label">Mật khẩu</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input name="password" type="password" class="form-control"
                                                placeholder="Vui lòng nhập mật khẩu" aria-label="Password"
                                                aria-describedby="password-addon" required>
                                            <button class="btn btn-light " type="button" id="password-addon"><i
                                                    class="mdi mdi-eye-outline"></i></button>
                                            <div class="invalid-tooltip">
                                                Vui lòng nhập mật khẩu
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                            <input class="form-check-input" type="checkbox" id="authenticator-check">
                                            <label class="form-check-label" for="authenticator-check">Đăng nhập sử dụng
                                                Authenticator</label>
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative input-auth" style="display: none;">
                                        <label for="username" class="form-label">Mã xác thực</label>
                                        <input name="auth_2factor_code" type="text" class="form-control" id="auth_2factor_code" placeholder="Vui lòng nhập mã">
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit"><i
                                                class="fas"></i> Đăng nhập</button>
                                    </div>

                                    <!-- <div class="mt-4 text-center">
                                                <h5 class="font-size-14 mb-3">Sign in with</h5>

                                                <ul class="list-inline">
                                                    <li class="list-inline-item">
                                                        <a href="javascript::void()"
                                                            class="social-list-item bg-primary text-white border-primary">
                                                            <i class="mdi mdi-facebook"></i>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <a href="javascript::void()"
                                                            class="social-list-item bg-info text-white border-info">
                                                            <i class="mdi mdi-twitter"></i>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <a href="javascript::void()"
                                                            class="social-list-item bg-danger text-white border-danger">
                                                            <i class="mdi mdi-google"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div> -->

                                    {{-- <div class="mt-4 text-center">
                                        <a href="auth-recoverpw.html" class="text-muted"><i class="mdi mdi-lock me-1"></i>
                                            Forgot your password?</a>
                                    </div> --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection