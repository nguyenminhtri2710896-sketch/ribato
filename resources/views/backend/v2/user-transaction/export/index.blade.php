<table>
    <tr>
        <td>STT</td>
        <th>Mã giao dịch</th>
        <th>Loại giao dịch</th>
        <th>Số tiền</th>
        <th>Số dư cuối</th>
        <th>Ghi chú</th>
        <th>Ngày giao dịch</th>
    </tr>
    @foreach ($objUserTransactions as $key => $objUserTransaction)
        <tr>
            <td>{{ ++$key }}</td>
            <td>'{{ $objUserTransaction->trans_code }}</td>
            <td>{{ $types[$objUserTransaction->type_id]['name'] ?? 'Unknown' }}</td>
            <td>{{ $objUserTransaction->amount }}</td>
            <td>{{ $objUserTransaction->user_balance }}</td>
            <td>{{ $objUserTransaction->note }}</td>
            <td>{{ $objUserTransaction->created_at }}</td>
        </tr>
    @endforeach
</table>