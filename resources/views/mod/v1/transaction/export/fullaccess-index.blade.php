<table>
    <tr>
        <td>STT</td>
        <th>Mã giao dịch</th>
        <th>Mã đơn</th>
        <th>Số tiền</th>
        <th>Phí</th>
        <th>Số tiền thực nhận</th>
        <th>Nội dung</th>
        <th>Ngân hàng</th>
        <th>Số tài khoản</th>
        <th>Chủ khoản</th>
        <th>Trạng thái</th>
        <th>Mã Người dùng</th>
        <th>Người dùng</th>
        <th>Cổng</th>
        <th>Công thức phí cổng</th>
        <th>Phí cổng</th>
        <th>Công thức Phí khách</th>
        <th>Phí khách</th>
        <th>Ngày giao dịch</th>
    </tr>
    @foreach ($objTransactions as $key => $objTransaction)
        <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $objTransaction->code }}</td>
            <td>{{ $objTransaction->ref_code }}</td>
            <td>{{ $objTransaction->amount }}</td>
            <td>{{ $objTransaction->fee }}</td>
            <td>{{ $objTransaction->amount_after_fee }}</td>
            <td>{{ $objTransaction->content }}</td>
            <td>{{ $objTransaction->bank_short_name }}</td>
            <td>'{{ $objTransaction->bank_account_number }}</td>
            <td>{{ $objTransaction->bank_account_name }}</td>
            <td>{{ $status[$objTransaction->status_id]['name'] ?? 'Unknown' }}</td>
            <td>{{ $objTransaction->user_id }}</td>
            <td>{{ $objTransaction->user_email }}</td>
            <td>{{ $gateway[$objTransaction->gateway_id]["name"] ?? "Unknown" }}</td>
            <td>{{ $objTransaction->gateway_fee_json }}</td>
            <td>{{ $objTransaction->gateway_fee }}</td>
            <td>{{ $objTransaction->user_fee_json }}</td>
            <td>{{ $objTransaction->fee }}</td>
            <td>{{ $objTransaction->created_at }}</td>
        </tr>
    @endforeach
</table>