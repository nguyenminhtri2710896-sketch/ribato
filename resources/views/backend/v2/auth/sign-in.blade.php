@extends('backend.v2.layouts.auth')
@section('title', __('Đăng nhập'))
@section('style')
    <style>
        :root {
            --auth-dark-1: #0f172a;
            --auth-dark-2: #1e293b;
            --auth-dark-3: #334155;
            --auth-bg: #f4f6fa;
            --auth-text: #0f172a;
            --auth-muted: #94a3b8;
            --auth-border: #e2e8f0;
            --auth-input-bg: #ffffff;
        }

        body.auth-v2 {
            margin: 0;
            min-height: 100vh;
            background: var(--auth-bg);
            font-family: 'Inter', 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
            color: var(--auth-text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 48px -22px rgba(15, 23, 42, 0.22), 0 6px 16px -6px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .auth-card__header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            padding: 36px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-card__header::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08), transparent 40%),
                              radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05), transparent 40%);
            pointer-events: none;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .auth-brand__logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .auth-brand__logo svg {
            width: 24px;
            height: 24px;
            fill: #fff;
        }

        .auth-brand__name {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1.2px;
            line-height: 1;
        }

        .auth-card__body {
            padding: 26px 26px 24px;
        }

        .auth-field {
            margin-bottom: 16px;
        }

        .auth-field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--auth-text);
        }

        .auth-input-wrap {
            position: relative;
        }

        .auth-input {
            width: 100%;
            height: 42px;
            padding: 0 14px;
            border: 1px solid var(--auth-border);
            border-radius: 10px;
            background: var(--auth-input-bg);
            font-size: 14px;
            color: var(--auth-text);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
        }

        .auth-input:focus {
            border-color: var(--auth-dark-2);
            box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.08);
        }

        .auth-input::placeholder {
            color: var(--auth-muted);
            font-size: 14px;
        }

        .auth-input--password {
            padding-right: 50px;
        }

        .auth-password-toggle {
            position: absolute;
            right: 6px;
            top: 5px;
            width: 32px;
            height: 32px;
            border: none;
            background: #f1f5f9;
            color: var(--auth-text);
            cursor: pointer;
            border-radius: 8px;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s;
        }

        .auth-password-toggle:hover {
            background: #e2e8f0;
        }

        .auth-toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 18px;
            flex-wrap: wrap;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--auth-text);
        }

        .auth-check input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .auth-check__box {
            width: 16px;
            height: 16px;
            border-radius: 5px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, border-color 0.2s;
            flex-shrink: 0;
        }

        .auth-check__box::after {
            content: '';
            width: 4px;
            height: 8px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) scale(0);
            transition: transform 0.15s;
            margin-bottom: 2px;
        }

        .auth-check input:checked + .auth-check__box {
            background: var(--auth-dark-2);
            border-color: var(--auth-dark-2);
        }

        .auth-check input:checked + .auth-check__box::after {
            transform: rotate(45deg) scale(1);
        }

        .auth-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--auth-muted);
            font-size: 15px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-link:hover {
            color: var(--auth-dark-2);
        }

        .auth-submit {
            width: 100%;
            height: 44px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
            box-shadow: 0 14px 28px -14px rgba(15, 23, 42, 0.5);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }

        .auth-submit i {
            font-size: 16px;
        }

        .auth-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 40px -15px rgba(15, 23, 42, 0.65);
        }

        .auth-submit:active {
            transform: translateY(0);
        }

        .input-auth {
            animation: fade-slide 0.25s ease-out;
        }

        @keyframes fade-slide {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-input.is-invalid,
        .was-validated .auth-input:invalid {
            border-color: #ef4444;
            background-color: #fff5f5;
            background-image: none;
            padding-right: 14px;
        }

        .auth-input--password.is-invalid,
        .was-validated .auth-input--password:invalid {
            padding-right: 50px;
        }

        .auth-input.is-invalid:focus,
        .was-validated .auth-input:invalid:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }

        .invalid-tooltip {
            position: static;
            display: none;
            margin-top: 6px;
            padding: 0 0 0 2px;
            background: transparent;
            color: #dc2626;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.3;
            border-radius: 0;
            max-width: 100%;
        }

        .invalid-tooltip::before {
            content: '⚠';
            margin-right: 5px;
            font-size: 12px;
        }

        .auth-input.is-invalid ~ .invalid-tooltip,
        .was-validated .auth-input:invalid ~ .invalid-tooltip {
            display: block;
        }

        .auth-input.is-valid,
        .was-validated .auth-input:valid {
            background-image: none;
        }

        @media (max-width: 420px) {
            .auth-card { border-radius: 16px; }
            .auth-card__header { padding: 28px 18px; }
            .auth-brand__name { font-size: 20px; }
            .auth-brand__logo { width: 36px; height: 36px; }
            .auth-card__body { padding: 22px 18px 20px; }
            .auth-input { height: 40px; }
            .auth-password-toggle { top: 4px; }
            .auth-submit { height: 42px; }
        }
    </style>
@endsection
@section('javascript')
    <!-- INSET JAVA SCRIPT -->
    <script src="{{ asset('backend/v2/customes/js/auth.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        auth.signIn();
        document.body.classList.add('auth-v2');
        $('#password-addon').on('click', function () {
            var icon = $(this).find('i');
            if (!icon.length) return;
            icon.toggleClass('mdi-eye-outline mdi-eye-off-outline');
        });
    </script>
@endsection
@section('content')
    <div class="auth-card">
        <div class="auth-card__header">
            <div class="auth-brand">
                <span class="auth-brand__logo">
                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="2"  y="6"  width="4" height="20" rx="1"/>
                        <rect x="9"  y="2"  width="4" height="28" rx="1"/>
                        <rect x="16" y="6"  width="4" height="20" rx="1"/>
                        <rect x="23" y="10" width="4" height="12" rx="1"/>
                    </svg>
                </span>
                <span class="auth-brand__name">{{ strtoupper(env('APP_NAME')) }}</span>
            </div>
        </div>

        <div class="auth-card__body">
            <form class="needs-validation frm-ajax-submit" novalidate method="POST"
                data-ajax-url="{{ route('backend.auth.ajax-sign-in') }}"
                data-redirect-url="{{ route('backend.index.index') }}">

                <div class="auth-field">
                    <label for="email">Email</label>
                    <div class="auth-input-wrap">
                        <input name="email" type="text" id="email" class="auth-input"
                            placeholder="Nhập email" required autocomplete="username">
                        <div class="invalid-tooltip">Vui lòng nhập email</div>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password">Mật khẩu</label>
                    <div class="auth-input-wrap">
                        <input name="password" type="password" id="password"
                            class="auth-input auth-input--password"
                            placeholder="Nhập mật khẩu" required autocomplete="current-password"
                            aria-describedby="password-addon">
                        <button class="auth-password-toggle" type="button" id="password-addon" tabindex="-1"
                            aria-label="Hiện/ẩn mật khẩu">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                        <div class="invalid-tooltip">Vui lòng nhập mật khẩu</div>
                    </div>
                </div>

                <div class="auth-toggle-row">
                    <label class="auth-check" for="authenticator-check">
                        <input type="checkbox" id="authenticator-check">
                        <span class="auth-check__box"></span>
                        <span>Đăng nhập bằng Authenticator</span>
                    </label>
                </div>

                <div class="auth-field input-auth" style="display: none;">
                    <label for="auth_2factor_code">Mã xác thực</label>
                    <div class="auth-input-wrap">
                        <input name="auth_2factor_code" type="text" id="auth_2factor_code" class="auth-input"
                            placeholder="Nhập 6 chữ số" autocomplete="one-time-code" inputmode="numeric">
                    </div>
                </div>

                <button class="auth-submit" type="submit">
                    <i class="mdi mdi-login-variant"></i>
                    <span>Đăng nhập</span>
                </button>
            </form>
        </div>
    </div>
@endsection
