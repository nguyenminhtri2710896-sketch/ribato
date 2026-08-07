@extends('backend.v2.layouts.doc')
@section('title', __('Tài liệu tích hợp API V2'))
@section('style')
    <style>
        .doc-portal-container {
            max-width: 900px;
            margin: 60px auto;
        }
        .portal-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .portal-title {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
            margin-bottom: 15px;
        }
        .portal-sub {
            font-size: 16px;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }
        .portal-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            background: #ffffff;
        }
        .portal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .card-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 24px;
        }
        .icon-collection {
            background-color: #ecfdf5;
            color: #059669;
        }
        .icon-payout {
            background-color: #eff6ff;
            color: #2563eb;
        }
        .portal-card-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .portal-card-desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .portal-btn {
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-collection {
            background-color: #059669;
            color: #ffffff;
            border: none;
        }
        .btn-collection:hover {
            background-color: #047857;
            color: #ffffff;
        }
        .btn-payout {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
        }
        .btn-payout:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .doc-portal-container {
                margin: 30px auto;
                padding: 0 15px;
            }
            .portal-header {
                margin-bottom: 30px;
            }
            .portal-title {
                font-size: 26px;
            }
            .portal-sub {
                font-size: 14px;
            }
            .portal-card-title {
                font-size: 18px;
            }
            .card-icon-wrap {
                width: 48px;
                height: 48px;
                font-size: 20px;
                margin-bottom: 16px;
            }
            .portal-card-desc {
                margin-bottom: 20px;
            }
        }
    </style>
@endsection
@section('content')
    <div class="container doc-portal-container">
        <div class="portal-header">
            <div class="portal-title">Tài liệu Tích hợp API V2</div>
            <p class="portal-sub">
                Hệ thống hỗ trợ cổng thanh toán (Collection) và chi hộ tự động (Payout) nhanh chóng, bảo mật thông qua xác thực Checksum MD5.
            </p>
        </div>

        <div class="row">
            {{-- Card: Collection --}}
            <div class="col-md-6 mb-4">
                <div class="card portal-card">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-icon-wrap icon-collection">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="portal-card-title">API Collection (Tiền vào)</div>
                            <p class="portal-card-desc">
                                Cho phép Merchant truy vấn danh sách giao dịch nạp tiền, chi tiết giao dịch, 
                                và tích hợp Webhook IPN tự động nhận thông báo biến động số dư khi có giao dịch thành công.
                            </p>
                        </div>
                        <div>
                            <a href="{{ request()->getHost() === 'doc-paypay.com' || request()->getHost() === 'www.doc-paypay.com' ? '/collection' : route('backend.doc.collection') }}" class="btn portal-btn btn-collection">
                                Xem tài liệu <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Payout --}}
            <div class="col-md-6 mb-4">
                <div class="card portal-card">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-icon-wrap icon-payout">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="portal-card-title">API Payout (Rút tiền / Chi hộ)</div>
                            <p class="portal-card-desc">
                                Cho phép tạo yêu cầu chi tiền tự động về tài khoản ngân hàng thụ hưởng, 
                                truy vấn danh sách ngân hàng được hỗ trợ, và cập nhật kết quả giao dịch chi hộ qua IPN.
                            </p>
                        </div>
                        <div>
                            <a href="{{ request()->getHost() === 'doc-paypay.com' || request()->getHost() === 'www.doc-paypay.com' ? '/payout' : route('backend.doc.payout') }}" class="btn portal-btn btn-payout">
                                Xem tài liệu <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
