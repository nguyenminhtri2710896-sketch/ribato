<table>
    <tr>
        <td>STT</td>
        <th>Mã</th>
        <th>Ngân hàng</th>
        <th>Số tài khoản</th>
        <th>Chủ khoản</th>
        <th>Nội dung CK</th>
        <th>Số tiền yêu cầu</th>
        <th>Phí</th>
        <th>Số tiền sau khi tính phí</th>
        <th>Trạng thái</th>
        <th>Mã Người dùng</th>
        <th>Người dùng</th>
        <th>Cổng</th>
        <th>Công thức phí cổng</th>
        <th>Phí cổng</th>
        <th>Công thức Phí khách</th>
        <th>Phí khách</th>
        <th>Công thức hoa hồng đại lý</th>
        <th>Hoa hồng đại lý</th>
        <th>Ngày giao dịch</th>
    </tr>
    @foreach ($objUserWithdraws as $key => $objUserWithdraw)
        <tr>
            <td>{{ ++$key }}</td>
            <td>'{{ $objUserWithdraw->trans_code }}</td>
            <td>{{ $objUserWithdraw->bank_short_name }}</td>
            <td>{{ $objUserWithdraw->bank_account_number }}</td>
            <td>{{ $objUserWithdraw->bank_account_name }}</td>
            <td>{{ $objUserWithdraw->remark }}</td>
            <td>{{ $objUserWithdraw->amount }}</td>
            <td>{{ $objUserWithdraw->fee }}</td>
            <td>{{ $objUserWithdraw->amount_after_fee }}</td>
            <td>{{ $status[$objUserWithdraw->status_id]['name'] ?? 'Unknown' }}</td>
            <td>{{ $objUserWithdraw->user_id }}</td>
            <td>{{ $objUserWithdraw->user_email }}</td>
            <td>{{ $gateway[$objUserWithdraw->gateway_id]["name"] ?? "Unknown" }}</td>
            <td>{{ $objUserWithdraw->gateway_fee_json }}</td>
            <td>{{ $objUserWithdraw->gateway_fee }}</td>
            <td>{{ $objUserWithdraw->user_fee_json }}</td>
            <td>{{ $objUserWithdraw->fee }}</td>
            <td>{{ $objUserWithdraw->referal_fee_json }}</td>
            <td>{{ $objUserWithdraw->referal_fee }}</td>
            <td>{{ $objUserWithdraw->created_at }}</td>
        </tr>
    @endforeach
</table>