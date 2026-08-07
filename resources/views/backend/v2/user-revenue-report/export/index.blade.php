<table>
    <tr>
        <th>STT</th>
        <th>Khách hàng</th>
        <th>Phí người giới thiệu</th>
        <th>Phí cổng</th>
        <th>Lợi nhuận</th>
        <th>Tổng tiền giao dịch</th>
        <th>Tổng phí giao dịch</th>
        <th>Loại giao dịch</th>
        <th>Ngày chốt</th>
    </tr>
    @foreach ($reports as $idx => $report)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $report->user_fullname }} ({{ $report->user_email }})</td>
            <td>{{ $report->total_referal_fee }}</td>
            <td>{{ $report->total_gateway_fee }}</td>
            <td>{{ $report->total_profit }}</td>
            <td>{{ $report->total_transaction_amount }}</td>
            <td>{{ $report->total_transaction_fee }}</td>
            <td>{{ $type[$report->type_id]['name'] ?? 'Unknown' }}</td>
            <td>{{ $report->report_at }}</td>
        </tr>
    @endforeach
    @if ($summary)
        <tr>
            <td colspan="2"><strong>Tổng</strong></td>
            <td><strong>{{ $summary->total_referal_fee }}</strong></td>
            <td><strong>{{ $summary->total_gateway_fee }}</strong></td>
            <td><strong>{{ $summary->total_profit }}</strong></td>
            <td><strong>{{ $summary->total_transaction_amount }}</strong></td>
            <td><strong>{{ $summary->total_transaction_fee }}</strong></td>
            <td colspan="2"></td>
        </tr>
    @endif
</table>