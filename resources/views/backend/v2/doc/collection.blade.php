@extends('backend.v2.layouts.doc')
@section('title', __('Tài liệu API Collection V2 — Tiền vào'))
@section('style')
    @include('backend.v2.doc._doc-style')
@endsection
@section('content')
    <div class="container my-4">
        <div class="page-title-box">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ request()->getHost() === 'doc-paypay.com' || request()->getHost() === 'www.doc-paypay.com' ? '/' : route('backend.doc.index') }}"><i class="fas fa-arrow-left"></i> Quay lại cổng tài liệu</a></li>
            </ol>
        </div>

        <div class="card doc-wrap">
            <div class="card-body p-4">
                <div class="doc-page-title">Tài liệu API Collection V2 — Nhận tiền &amp; Giao dịch Tiền vào</div>
                <p class="doc-page-sub">
                    Tài liệu này hướng dẫn tích hợp bộ API Collection phiên bản V2 (lấy danh sách giao dịch tiền vào, 
                    lấy chi tiết giao dịch) sử dụng mã xác thực <code>checksum</code> (MD5) và cấu hình Webhook (IPN) 
                    nhận kết quả giao dịch tiền vào tự động.
                </p>

                {{-- Hướng dẫn chung --}}
                <div class="doc-section font-sans">
                    <div class="doc-section-title">1. Thông tin chung &amp; Xác thực Checksum</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Mọi API yêu cầu truyền Header xác thực <code>api-token</code> và tham số xác thực <code>checksum</code> 
                        trong JSON body.
                    </p>
                    <table class="doc-table mb-3">
                        <thead>
                            <tr>
                                <th style="width:200px;">Header / Content-Type</th>
                                <th style="width:150px;">Giá trị</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-field">api-token</td>
                                <td class="col-type">string</td>
                                <td>API Token được cung cấp trong cấu hình tài khoản của bạn.</td>
                            </tr>
                            <tr>
                                <td class="col-field">Content-Type</td>
                                <td class="col-type">application/json</td>
                                <td>Định dạng dữ liệu gửi lên hệ thống (JSON).</td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 class="mt-4" style="font-size: 14.5px; font-weight: 700;">Quy trình tạo mã xác thực (<code>checksum</code>)</h5>
                    <p class="text-muted" style="font-size:13.5px;">
                        Mã <code>checksum</code> được tính bằng cách băm MD5 của chuỗi Query String các tham số yêu cầu nối với API Token của bạn:
                    </p>
                    <ol style="font-size:13.5px; color:#334155; line-height:1.7; padding-left:20px;">
                        <li>Loại bỏ trường <code>checksum</code> ra khỏi mảng dữ liệu gửi đi (nếu có).</li>
                        <li>Sắp xếp mảng tham số tăng dần theo bảng chữ cái của key (sử dụng <code>ksort</code>).</li>
                        <li>Chuyển tất cả giá trị <code>null</code> trong mảng thành chuỗi rỗng <code>""</code>.</li>
                        <li>Tạo query string bằng hàm <code>http_build_query</code>, sau đó giải mã URL bằng <code>urldecode</code>.</li>
                             <div class="code-tab-container mt-4">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="javascript:void(0);" onclick="switchTab(this, 'col-php-code')">PHP</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);" onclick="switchTab(this, 'col-node-code')">Node.js</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);" onclick="switchTab(this, 'col-python-code')">Python</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-2">
                            <div id="col-php-code" class="tab-pane-custom">
                                <div class="doc-code-block mb-3">
                                    <div class="doc-code-header">
                                        <span>PHP — Tạo Checksum V2 mẫu</span>
                                        <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                            <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                                        </button>
                                    </div>
                                    <pre class="doc-code">&lt;?php
function generateChecksum(array $params, $apiToken) {
    if (isset($params['checksum'])) {
        unset($params['checksum']);
    }
    
    // 1. Sắp xếp key
    ksort($params);
    
    // 2. Định dạng null -> chuỗi rỗng
    array_walk_recursive($params, function (&$val) {
        if ($val === null) {
            $val = '';
        }
    });
    
    // 3. Build query string & urldecode
    $queryString = urldecode(http_build_query($params));
    
    // 4. Nối với API Token và băm MD5
    return md5($queryString . $apiToken);
}</pre>
                                </div>
                            </div>
                            <div id="col-node-code" class="tab-pane-custom d-none">
                                <div class="doc-code-block mb-3">
                                    <div class="doc-code-header">
                                        <span>Node.js — Tạo Checksum V2 mẫu</span>
                                        <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                            <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                                        </button>
                                    </div>
                                    <pre class="doc-code">const crypto = require('crypto');
const querystring = require('querystring');

function generateChecksum(params, apiToken) {
    const data = { ...params };
    delete data.checksum;

    const sortedData = {};
    Object.keys(data).sort().forEach(key => {
        let value = data[key];
        if (value === null) {
            value = '';
        }
        sortedData[key] = value;
    });

    const queryString = decodeURIComponent(querystring.stringify(sortedData));
    return crypto.createHash('md5').update(queryString + apiToken).digest('hex');
}</pre>
                                </div>
                            </div>
                            <div id="col-python-code" class="tab-pane-custom d-none">
                                <div class="doc-code-block mb-3">
                                    <div class="doc-code-header">
                                        <span>Python — Tạo Checksum V2 mẫu</span>
                                        <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                            <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                                        </button>
                                    </div>
                                    <pre class="doc-code">import urllib.parse
import hashlib

def generate_checksum(params, api_token):
    data = params.copy()
    if 'checksum' in data:
        del data['checksum']
        
    for k, v in data.items():
        if v is None:
            data[k] = ''
            
    sorted_data = sorted(data.items())
    query_string = urllib.parse.unquote(urllib.parse.urlencode(sorted_data))
    input_str = query_string + api_token
    return hashlib.md5(input_str.encode('utf-8')).hexdigest()</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- API: Lấy danh sách giao dịch tiền vào --}}
                <div class="doc-section">
                    <div class="doc-section-title">2. API Lấy danh sách giao dịch tiền vào</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Lấy lịch sử các giao dịch nạp tiền thành công và đang xử lý trên cổng thanh toán.
                    </p>
                    <div class="doc-endpoint-row mb-3">
                        <span class="doc-pill post">POST</span>
                        <code>/api/v2/transaction/get-list</code>
                        <span class="doc-pill json">application/json</span>
                    </div>

                    <h5 style="font-size: 14.5px; font-weight: 700; margin-top: 15px;">Tham số Request Body</h5>
                    <table class="doc-table mb-3">
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
                                <td class="col-field">page</td>
                                <td class="col-type">integer</td>
                                <td><span class="col-required is-optional">optional</span></td>
                                <td>Số trang hiện tại (Mặc định: 1).</td>
                            </tr>
                            <tr>
                                <td class="col-field">limit</td>
                                <td class="col-type">integer</td>
                                <td><span class="col-required is-optional">optional</span></td>
                                <td>Số dòng trên 1 trang (Mặc định: 10).</td>
                            </tr>
                            <tr>
                                <td class="col-field">query</td>
                                <td class="col-type">object</td>
                                <td><span class="col-required is-optional">optional</span></td>
                                <td>
                                    Các bộ lọc chi tiết:<br />
                                    - <code>content</code>: Lọc nội dung chuyển khoản.<br />
                                    - <code>ref_code</code>: Mã tham chiếu của đơn hàng Merchant.<br />
                                    - <code>code</code>: Mã giao dịch trên hệ thống cổng thanh toán.
                                </td>
                            </tr>
                            <tr>
                                <td class="col-field">checksum</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Mã xác thực MD5 checksum của request.</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="doc-code-block mb-3">
                        <div class="doc-code-header">
                            <span>Request Body (JSON)</span>
                            <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                            </button>
                        </div>
                        <pre class="doc-code">{
    "page": 1,
    "limit": 10,
    "query": {
        "ref_code": "REF001"
    },
    "checksum": "MD5_CHECKSUM_HERE"
}</pre>
                    </div>

                    <div class="doc-code-block mb-3">
                        <div class="doc-code-header">
                            <span>Response mẫu (JSON)</span>
                            <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                            </button>
                        </div>
                        <pre class="doc-code">{
    "error_code": 0,
    "message": "Thành công.",
    "data": {
        "transactions": [
            {
                "id": 556677,
                "code": "TX12345678",
                "ref_code": "REF001",
                "user_id": 100,
                "amount": 100000,
                "fee": 1000,
                "amount_after_fee": 99000,
                "content": "NAP TIEN REF001",
                "status_id": 2,
                "bank_short_name": "VCB",
                "bank_account_number": "99999999999",
                "bank_account_name": "CONG TY XYZ",
                "created_at": "2026-08-02 11:22:33",
                "updated_at": "2026-08-02 11:22:33"
            }
        ],
        "records_total": 1,
        "status": {
            "1": {"name": "Đang xử lý"},
            "2": {"name": "Thành công"},
            "3": {"name": "Thất bại"},
            "5": {"name": "Chờ kiểm tra giao dịch"},
            "6": {"name": "Chờ đối soát"},
            "7": {"name": "Chuyển tiếp"}
        },
        "page": 1,
        "limit": 10
    }
}</pre>
                    </div>
                </div>

                {{-- API: Lấy chi tiết giao dịch --}}
                <div class="doc-section">
                    <div class="doc-section-title">3. API Lấy chi tiết giao dịch tiền vào</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Lấy thông tin chi tiết của một giao dịch cụ thể dựa vào bộ lọc query.
                    </p>
                    <div class="doc-endpoint-row mb-3">
                        <span class="doc-pill post">POST</span>
                        <code>/api/v2/transaction/get-detail</code>
                        <span class="doc-pill json">application/json</span>
                    </div>

                    <div class="doc-code-block mb-3">
                        <div class="doc-code-header">
                            <span>Request Body (JSON)</span>
                            <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                            </button>
                        </div>
                        <pre class="doc-code">{
    "query": {
        "ref_code": "REF001"
    },
    "checksum": "MD5_CHECKSUM_HERE"
}</pre>
                    </div>
                </div>

                <hr style="border-top: 2px dashed #cbd5e1; margin: 40px 0;" />

                {{-- IPN SECTION - MERGED --}}
                <div class="doc-section">
                    <div class="doc-page-title" style="font-size: 20px;">Tài liệu Webhook IPN — Tiếp nhận giao dịch tiền vào</div>
                    <p class="doc-page-sub" style="margin-bottom: 20px;">
                        Khi có một giao dịch tiền vào thành công, hệ thống sẽ gửi một request <strong>POST</strong>
                        chứa nội dung JSON đến <strong>Webhook URL</strong> mà bạn cấu hình. Merchant cần xác thực 
                        <code>checksum</code> MD5 trước khi ghi nhận tiền.
                    </p>

                    <div class="doc-section font-sans">
                        <div class="doc-section-title">1. Cấu hình Webhook URL</div>
                        <div class="doc-note">
                            Vào <strong>Tài khoản → API TOKEN → Cập nhật WebHook URL</strong> để khai báo địa chỉ
                            nhận IPN tiền vào (<code>webhook_url</code>). Mọi giao dịch tiền vào thành công sẽ được
                            push tới URL này.
                        </div>
                    </div>

                    <div class="doc-section">
                        <div class="doc-section-title">2. Request gửi tới Merchant</div>
                        <div class="doc-endpoint-row mb-3">
                            <span class="doc-pill post">POST</span>
                            <code>{webhook_url}</code>
                            <span class="doc-pill json">application/json</span>
                        </div>

                        <p class="text-muted mb-2" style="font-size:13.5px;">
                            Cấu trúc body request gửi đến hệ thống của bạn (JSON):
                        </p>

                        <div class="doc-code-block mb-3">
                            <div class="doc-code-header">
                                <span>Body — JSON</span>
                                <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                    <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                                </button>
                            </div>
                            <pre class="doc-code">{
    "success": true,
    "message": "Thành công",
    "data": {
        "amount": 1000000,
        "received_date": "2026-05-12 10:30:25",
        "content": "NAP TIEN REF001",
        "reference": "REF001",
        "checksum": "f5a9b1c7e3d2..."
    }
}</pre>
                        </div>
                    </div>

                    <div class="doc-section">
                        <div class="doc-section-title">3. Tham số (trong <code>data</code>)</div>
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
                                    <td>Số tiền giao dịch nhận được (VND).</td>
                                </tr>
                                <tr>
                                    <td class="col-field">received_date</td>
                                    <td class="col-type">datetime</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>Thời gian ghi nhận tiền vào (<code>Y-m-d H:i:s</code>).</td>
                                </tr>
                                <tr>
                                    <td class="col-field">content</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>Nội dung chuyển khoản.</td>
                                </tr>
                                <tr>
                                    <td class="col-field">reference</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>Mã tham chiếu (ref_code) của đơn hàng từ phía Merchant.</td>
                                </tr>
                                <tr>
                                    <td class="col-field">checksum</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>Chuỗi MD5 xác thực dữ liệu.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="doc-section font-sans">
                        <div class="doc-section-title">4. Xác thực checksum IPN</div>
                        <p class="text-muted mb-2" style="font-size:13.5px;">
                            Mã checksum được sinh bằng cách băm MD5 chuỗi query string các thuộc tính trong <code>data</code> (loại trừ <code>checksum</code>) nối với API Token của bạn.
                        </p>
                        <div class="doc-note warning mb-3">
                            <strong>Công thức:</strong>
                            <code>checksum = md5( http_build_query(data_không_chứa_checksum) + API_TOKEN )</code>
                        </div>

                        <div class="doc-code-block mb-3">
                            <div class="doc-code-header">
                                <span>PHP — Verify checksum IPN</span>
                                <button type="button" class="doc-copy-btn" onclick="docCopy(this)">
                                    <i class="mdi mdi-content-copy"></i> <span>Copy</span>
                                </button>
                            </div>
                            <pre class="doc-code">&lt;?php
$apiToken = 'YOUR_API_TOKEN';

$body   = json_decode(file_get_contents('php://input'), true);
$data = $body['data'] ?? [];

$received = $data['checksum'] ?? '';
unset($data['checksum']);

// Định dạng null -> chuỗi rỗng và build query
array_walk_recursive($data, function (&$val) {
    if ($val === null) {
        $val = '';
    }
});
$expected = md5(urldecode(http_build_query($data)) . $apiToken);

if (!hash_equals($expected, $received)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid checksum']);
    exit;
}

// TODO: Xử lý cộng tiền cho đơn hàng của Merchant tương ứng với $data['reference']
echo json_encode(['success' => true, 'message' => 'OK']);</pre>
                        </div>
                    </div>

                    <div class="doc-section">
                        <div class="doc-section-title">5. Phản hồi từ Merchant</div>
                        <p class="text-muted mb-2" style="font-size:13.5px;">
                            Phản hồi HTTP <strong>200 OK</strong> định dạng JSON để hệ thống ngừng gửi lại:
                        </p>
                        <div class="doc-code-block">
                            <pre class="doc-code">{
    "success": true,
    "message": "OK"
}</pre>
                        </div>
                    </div>

                    <div class="doc-section font-sans">
                        <div class="doc-section-title">6. Lưu ý &amp; Retry</div>
                        <ul style="font-size:13.5px; color:#334155; line-height:1.7; padding-left:20px; margin:0;">
                            <li>Hệ thống chỉ retry gửi IPN tối đa <strong>3 lần</strong> nếu nhận được mã lỗi hoặc hết thời gian chờ phản hồi.</li>
                            <li>Webhook IPN có thể bị gửi trùng lặp. Vui lòng kiểm tra mã đơn hàng <code>reference</code> đã được cập nhật chưa trước khi xử lý.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
