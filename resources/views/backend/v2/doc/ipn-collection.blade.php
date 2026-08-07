@extends('backend.v2.layouts.default')
@section('title', __('Tài liệu IPN — Tiền vào'))
@section('style')
    @include('backend.v2.doc._doc-style')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                <li class="breadcrumb-item">Tài liệu</li>
                <li class="breadcrumb-item active">IPN Collection (Tiền vào)</li>
            </ol>
        </div>

        <div class="card doc-wrap">
            <div class="card-body p-4">
                <div class="doc-page-title">IPN Collection — Giao dịch tiền vào</div>
                <p class="doc-page-sub">
                    Khi có một giao dịch tiền vào thành công, hệ thống sẽ gửi một request <strong>POST</strong>
                    chứa nội dung JSON đến <strong>Webhook URL</strong> mà bạn đã cấu hình. Merchant cần xác thực
                    <code>checksum</code> trước khi xử lý dữ liệu.
                </p>

                {{-- Endpoint --}}
                <div class="doc-section">
                    <div class="doc-section-title">1. Cấu hình Webhook URL</div>
                    <div class="doc-note">
                        Vào <strong>Tài khoản → API TOKEN → Cập nhật WebHook URL</strong> để khai báo địa chỉ
                        nhận IPN tiền vào (<code>webhook_url</code>). Mọi giao dịch tiền vào thành công sẽ được
                        push tới URL này.
                    </div>
                </div>

                {{-- Request --}}
                <div class="doc-section">
                    <div class="doc-section-title">2. Request gửi tới Merchant</div>
                    <div class="doc-endpoint-row mb-3">
                        <span class="doc-pill post">POST</span>
                        <code>{webhook_url}</code>
                        <span class="doc-pill json">application/json</span>
                    </div>

                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Cấu trúc body request (JSON):
                    </p>

                    <div class="doc-code-block mb-3">
                        <div class="doc-code-header">
                            <span>Body — JSON</span>
                            <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                            </button>
                        </div>
                        <pre class="doc-code">
    {
        "success": true,
        "message": "Thành công",
        "data": {
            "amount": 1000000,
            "received_date": "2026-05-12 10:30:25",
            "content": "NAP TIEN REF001",
            "reference": "REF001",
            "checksum": "f5a9b1c7e3d2..."
        }
    }
                                            </pre>
                    </div>
                </div>

                {{-- Schema --}}
                <div class="doc-section">
                    <div class="doc-section-title">3. Tham số (trong <code>result</code>)</div>
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th style="width:200px;">Trường</th>
                                <th style="width:120px;">Kiểu</th>
                                <th style="width:120px;">Bắt buộc</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-field">amount</td>
                                <td class="col-type">number</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Số tiền giao dịch (VND).</td>
                            </tr>
                            <tr>
                                <td class="col-field">received_date</td>
                                <td class="col-type">datetime</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Thời gian hệ thống ghi nhận tiền vào, định dạng <code>Y-m-d H:i:s</code>.</td>
                            </tr>
                            <tr>
                                <td class="col-field">content</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Nội dung chuyển khoản (đã loại bỏ ký tự xuống dòng).</td>
                            </tr>
                            <tr>
                                <td class="col-field">reference</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Mã tham chiếu của merchant đính kèm khi tạo giao dịch.</td>
                            </tr>
                            <tr>
                                <td class="col-field">checksum</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Chuỗi MD5 xác thực dữ liệu — xem mục <strong>Verify checksum</strong>.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Checksum --}}
                <div class="doc-section">
                    <div class="doc-section-title">4. Xác thực checksum</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Checksum được sinh từ MD5 của <em>query string</em> các tham số trong <code>result</code>
                        (loại bỏ <code>checksum</code>) cộng nối với <code>API Token</code> của bạn.
                    </p>

                    <div class="doc-note warning mb-3">
                        <strong>Công thức:</strong>
                        <code>checksum = md5( http_build_query(result_không_chứa_checksum) + API_TOKEN )</code>
                    </div>

                    <div class="doc-code-block mb-3">
                        <div class="doc-code-header">
                            <span>PHP — Verify checksum</span>
                            <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                            </button>
                        </div>
                        <pre class="doc-code">
    &lt;?php
    $apiToken = 'YOUR_API_TOKEN';

    $body   = json_decode(file_get_contents('php://input'), true);
    $result = $body['result'] ?? [];

    $received = $result['checksum'] ?? '';
    unset($result['checksum']);

    $expected = md5(http_build_query($result) . $apiToken);

    if (!hash_equals($expected, $received)) {
        http_response_code(400);
        echo json_encode(['error_code' =&gt; 1, 'msg' =&gt; 'Invalid checksum']);
        exit;
    }

    // TODO: cập nhật đơn hàng theo $result['ref_code']
    echo json_encode(['error_code' =&gt; 0, 'msg' =&gt; 'OK']);
                                            </pre>
                    </div>
                </div>

                {{-- Response --}}
                <div class="doc-section">
                    <div class="doc-section-title">5. Phản hồi từ Merchant</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Merchant phản hồi HTTP <strong>200 OK</strong> để hệ thống xác nhận đã nhận thành công.
                        Nếu phản hồi lỗi hoặc timeout, hệ thống sẽ retry tối đa <strong>3 lần</strong>.
                    </p>
                    <div class="doc-code-block">
                        <div class="doc-code-header">
                            <span>Response mẫu</span>
                            <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                            </button>
                        </div>
                        <pre class="doc-code">
    {
        "success": true,
        "message": "OK"
    }
                                            </pre>
                    </div>
                </div>

                {{-- Retry --}}
                <div class="doc-section">
                    <div class="doc-section-title">6. Lưu ý &amp; Retry</div>
                    <ul style="font-size:13.5px; color:#334155; line-height:1.7; padding-left:20px; margin:0;">
                        <li>Hệ thống chỉ retry IPN tối đa <strong>3 lần</strong>, sau đó callback sẽ bị đánh dấu thất bại.
                        </li>
                        <li>IPN có thể đến trùng (duplicate). Merchant nên kiểm tra <code>ref_code</code> đã xử lý chưa
                            trước khi cập nhật.</li>
                        <li>Luôn xác thực <code>checksum</code> trước khi xử lý dữ liệu — không tin tưởng IP nguồn.</li>
                        <li>Có thể xem lại lịch sử callback tại menu <strong>Quản lý webhook → Ipn Collection</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection