@extends('backend.v2.layouts.doc')
@section('title', __('Tài liệu API Payout V2 — Rút tiền'))
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
                <div class="doc-page-title">Tài liệu API Payout V2 — Chi hộ &amp; Rút tiền</div>
                <p class="doc-page-sub">
                    Tài liệu này hướng dẫn tích hợp bộ API Payout phiên bản V2 (lấy danh sách ngân hàng thụ hưởng, 
                    tạo giao dịch rút tiền, lấy danh sách lịch sử rút tiền) sử dụng mã xác thực <code>checksum</code> (MD5) 
                    và cấu hình Webhook (IPN) tiếp nhận kết quả trạng thái chi tiền tự động.
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
                        <li>Nối chuỗi kết quả trên với <code>API Token</code> của bạn.</li>
                        <li>Băm chuỗi dữ liệu cuối cùng bằng thuật toán <strong>MD5</strong> để làm giá trị cho trường <code>checksum</code>.</li>
                    </ol>

                    <div class="code-tab-container mt-4">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="javascript:void(0);" onclick="switchTab(this, 'pay-php-code')">PHP</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);" onclick="switchTab(this, 'pay-node-code')">Node.js</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);" onclick="switchTab(this, 'pay-python-code')">Python</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-2">
                            <div id="pay-php-code" class="tab-pane-custom">
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
                            <div id="pay-node-code" class="tab-pane-custom d-none">
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
                            <div id="pay-python-code" class="tab-pane-custom d-none">
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

                {{-- API: Lấy danh sách ngân hàng --}}
                <div class="doc-section">
                    <div class="doc-section-title">2. API Lấy danh sách ngân hàng hỗ trợ</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Lấy danh sách các ngân hàng được hệ thống hỗ trợ để lấy mã <code>bank_id</code> chuẩn bị cho giao dịch rút tiền.
                    </p>
                    <div class="doc-endpoint-row mb-3">
                        <span class="doc-pill post">POST</span>
                        <code>/api/v2/bank/get-list</code>
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
    "page": 1,
    "limit": 100,
    "query": {
        "name": ""
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
        "banks": [
            {
                "id": 1,
                "napas_code": "970436",
                "name": "Ngân hàng TMCP Ngoại Thương Việt Nam",
                "short_name": "Vietcombank",
                "short_code": "VCB",
                "logo": "vcb.png",
                "status_id": 1,
                "created_at": "2026-08-01 12:00:00",
                "updated_at": "2026-08-01 12:00:00"
            }
        ],
        "records_total": 1,
        "page": 1,
        "limit": 100
    }
}</pre>
                    </div>
                </div>

                {{-- API: Tạo yêu cầu rút tiền --}}
                <div class="doc-section">
                    <div class="doc-section-title">3. API Tạo yêu cầu rút tiền (Payout)</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Tạo một giao dịch chi hộ / rút tiền về số tài khoản ngân hàng đích.
                    </p>
                    <div class="doc-endpoint-row mb-3">
                        <span class="doc-pill post">POST</span>
                        <code>/api/v2/user-withdraw/create</code>
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
                                <td class="col-field">bank_id</td>
                                <td class="col-type">integer</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>ID ngân hàng nhận tiền (lấy từ API danh sách ngân hàng).</td>
                            </tr>
                            <tr>
                                <td class="col-field">bank_account_number</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Số tài khoản ngân hàng thụ hưởng.</td>
                            </tr>
                            <tr>
                                <td class="col-field">bank_account_name</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Tên chủ tài khoản thụ hưởng (Không dấu, in hoa).</td>
                            </tr>
                            <tr>
                                <td class="col-field">amount</td>
                                <td class="col-type">integer</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Số tiền rút (Tối thiểu 10,000đ, tối đa 300,000,000đ, phải là bội số của 10).</td>
                            </tr>
                            <tr>
                                <td class="col-field">remark</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-required">required</span></td>
                                <td>Nội dung chuyển tiền.</td>
                            </tr>
                            <tr>
                                <td class="col-field">ref_code</td>
                                <td class="col-type">string</td>
                                <td><span class="col-required is-optional">optional</span></td>
                                <td>Mã tham chiếu duy nhất của Merchant để đối chiếu trạng thái.</td>
                            </tr>
                            <tr>
                                <td class="col-field">type_id</td>
                                <td class="col-type">integer</td>
                                <td><span class="col-required is-optional">optional</span></td>
                                <td>Loại tài khoản nguồn: <code>1</code> (Tài khoản Công ty - Mặc định), <code>2</code> (Tài khoản cá nhân).</td>
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
    "bank_id": 1,
    "bank_account_number": "1021312323",
    "bank_account_name": "NGUYEN VAN A",
    "amount": 100000,
    "remark": "THANH TOAN DON HANG A001",
    "ref_code": "WD-REF-001",
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
    "message": "Tạo yêu cầu rút tiền thành công.",
    "data": {
        "user_withdraw": {
            "id": 12345,
            "user_id": 100,
            "bank_id": 1,
            "ref_code": "WD-REF-001",
            "trans_code": "20260802123456889",
            "bank_short_name": "VCB",
            "bank_account_number": "1021312323",
            "bank_account_name": "NGUYEN VAN A",
            "amount": 100000,
            "fee": 3300,
            "amount_after_fee": 103300,
            "status_id": 1,
            "created_at": "2026-08-02 12:34:56",
            "updated_at": "2026-08-02 12:34:56"
        }
    }
}</pre>
                    </div>
                </div>

                {{-- API: Lấy danh sách giao dịch rút tiền --}}
                <div class="doc-section">
                    <div class="doc-section-title">4. API Lấy danh sách giao dịch rút tiền</div>
                    <p class="text-muted mb-2" style="font-size:13.5px;">
                        Lấy lịch sử các yêu cầu rút tiền/chi hộ cùng trạng thái xử lý tương ứng trên hệ thống.
                    </p>
                    <div class="doc-endpoint-row mb-3">
                        <span class="doc-pill post">POST</span>
                        <code>/api/v2/user-withdraw/get-list</code>
                        <span class="doc-pill json">application/json</span>
                    </div>

                    <h5 style="font-size: 14.5px; font-weight: 700; margin-top: 15px;">Tham số Request Body (Bộ lọc query)</h5>
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
                                    - <code>created_at_from</code>: Lọc từ ngày (Y-m-d).<br />
                                    - <code>created_at_to</code>: Lọc đến ngày (Y-m-d).<br />
                                    - <code>bank_account_name</code>: Tên tài khoản thụ hưởng.<br />
                                    - <code>remark</code>: Nội dung rút.<br />
                                    - <code>ref_code</code>: Mã tham chiếu Merchant.<br />
                                    - <code>trans_code</code>: Mã giao dịch hệ thống.
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
        "ref_code": "WD-REF-001"
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
        "user_withdraws": [
            {
                "id": 12345,
                "user_id": 100,
                "trans_code": "20260802123456889",
                "ref_code": "WD-REF-001",
                "bank_short_name": "VCB",
                "bank_account_number": "1021312323",
                "bank_account_name": "NGUYEN VAN A",
                "amount": 100000,
                "fee": 3300,
                "amount_after_fee": 103300,
                "status_id": 2,
                "created_at": "2026-08-02 12:34:56",
                "updated_at": "2026-08-02 12:50:00"
            }
        ],
        "records_total": 1,
        "status": {
            "1": {"name": "Yêu cầu rút"},
            "4": {"name": "Đang xử lý"},
            "2": {"name": "Đã xử lý"},
            "3": {"name": "Huỷ"},
            "5": {"name": "Chờ xác minh giao dịch"}
        },
        "page": 1,
        "limit": 10
    }
}</pre>
                    </div>
                </div>

                <hr style="border-top: 2px dashed #cbd5e1; margin: 40px 0;" />

                {{-- IPN SECTION - MERGED --}}
                <div class="doc-section">
                    <div class="doc-page-title" style="font-size: 20px;">Tài liệu Webhook IPN — Tiếp nhận trạng thái rút tiền</div>
                    <p class="doc-page-sub" style="margin-bottom: 20px;">
                        Sau khi một yêu cầu rút tiền có cập nhật trạng thái (thành công / thất bại / đang xử lý),
                        hệ thống sẽ gửi một request <strong>POST</strong> JSON đến <strong>Webhook Payout URL</strong>
                        bạn đã cấu hình. Merchant cần xác thực <code>checksum</code> trước khi xử lý.
                    </p>

                    <div class="doc-section font-sans">
                        <div class="doc-section-title">1. Cấu hình Webhook Payout URL</div>
                        <div class="doc-note">
                            Vào <strong>Tài khoản → API TOKEN → Cập nhật WebHook URL</strong> để khai báo địa chỉ
                            nhận IPN rút tiền (<code>webhook_payout_url</code>). Khi trạng thái lệnh rút tiền thay đổi,
                            hệ thống sẽ push tới URL này.
                        </div>
                    </div>

                    <div class="doc-section">
                        <div class="doc-section-title">2. Request gửi tới Merchant</div>
                        <div class="doc-endpoint-row mb-3">
                            <span class="doc-pill post">POST</span>
                            <code>{webhook_payout_url}</code>
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
        "amount": 500000,
        "trans_code": "PO20260512001",
        "reference": "WD-REF-001",
        "content": "Rut tien ve TK 0123456789",
        "status_id": 2,
        "code": "SUCCESS",
        "message": "Giao dịch thành công",
        "checksum": "a8f3c5e9d1b4..."
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
                                    <td>Số tiền rút (VND).</td>
                                </tr>
                                <tr>
                                    <td class="col-field">trans_code</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>Mã giao dịch trên hệ thống (duy nhất).</td>
                                </tr>
                                <tr>
                                    <td class="col-field">reference</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>Mã tham chiếu của merchant khi tạo lệnh rút (ref_code).</td>
                                </tr>
                                <tr>
                                    <td class="col-field">content</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-optional">optional</span></td>
                                    <td>Nội dung / ghi chú của lệnh rút.</td>
                                </tr>
                                <tr>
                                    <td class="col-field">status_id</td>
                                    <td class="col-type">integer</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>
                                        Mã trạng thái dạng số:<br />
                                        <span class="doc-status-chip pending">1</span> Đang xử lý —
                                        <span class="doc-status-chip success">2</span> Thành công —
                                        <span class="doc-status-chip failed">3</span> Thất bại
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-field">code</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-required">required</span></td>
                                    <td>
                                        Mã trạng thái dạng text:
                                        <span class="doc-status-chip pending">PENDING</span>
                                        <span class="doc-status-chip success">SUCCESS</span>
                                        <span class="doc-status-chip failed">FAILED</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-field">message</td>
                                    <td class="col-type">string</td>
                                    <td><span class="col-required is-optional">optional</span></td>
                                    <td>Thông điệp ghi chú trạng thái (lý do thất bại).</td>
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
                        <div class="doc-section-title">4. Xác thực checksum IPN Payout</div>
                        <p class="text-muted mb-2" style="font-size:13.5px;">
                            Mã checksum được sinh bằng cách băm MD5 chuỗi query string các thuộc tính trong <code>data</code> (loại trừ <code>checksum</code>) nối với API Token của bạn.
                        </p>
                        <div class="doc-note warning mb-3">
                            <strong>Công thức:</strong>
                            <code>checksum = md5( http_build_query(data_không_chứa_checksum) + API_TOKEN )</code>
                        </div>

                        <div class="doc-code-block mb-3">
                            <div class="doc-code-header">
                                <span>PHP — Verify checksum IPN Payout</span>
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
    echo json_encode(['error_code' => 1, 'msg' => 'Invalid checksum']);
    exit;
}

// Cập nhật trạng thái lệnh rút theo $data['reference'] / $data['code'] trong Merchant DB
switch ($data['code']) {
    case 'SUCCESS': /* đã chi thành công */ break;
    case 'FAILED':  /* hoàn quỹ / cập nhật thất bại */ break;
    case 'PENDING': /* tiếp tục chờ */ break;
}

echo json_encode(['error_code' => 0, 'msg' => 'OK']);</pre>
                        </div>
                    </div>

                    <div class="doc-section font-sans">
                        <div class="doc-section-title">5. Phản hồi từ Merchant</div>
                        <p class="text-muted mb-2" style="font-size:13.5px;">
                            Phản hồi HTTP <strong>200 OK</strong> JSON để hệ thống xác nhận:
                        </p>
                        <div class="doc-code-block">
                            <pre class="doc-code">{
    "error_code": 0,
    "msg": "OK"
}</pre>
                        </div>
                    </div>

                    <div class="doc-section font-sans">
                        <div class="doc-section-title">6. Lưu ý &amp; Retry</div>
                        <ul style="font-size:13.5px; color:#334155; line-height:1.7; padding-left:20px; margin:0;">
                            <li>Hệ thống retry gửi IPN tối đa <strong>3 lần</strong>; sau đó callback sẽ bị đánh dấu thất bại.</li>
                            <li>Trạng thái lệnh rút có thể chuyển từ PENDING → SUCCESS/FAILED. Merchant cần xử lý idempotent theo <code>trans_code</code>.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
