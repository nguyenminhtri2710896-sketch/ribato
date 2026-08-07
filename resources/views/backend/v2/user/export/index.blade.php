<table>
    <tr>
        <td>STT</td>
        <th>Họ Tên</th>
        <th>Số điện thoại</th>
        <th>Email</th>
        <th>Số dư</th>
        <th>Số dư chờ duyệt trong ngày</th>
        <th>Số dư chờ duyệt N+1</th>
        <th>Số dư chờ duyệt N+2</th>
        <th>Cấu hình chốt giao dịch</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
    </tr>
    @foreach ($objUsers as $key => $objUser)
        <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $objUser->fullname }}</td>
            <td>'{{ $objUser->phone }}</td>
            <td>{{ $objUser->email }}</td>
            <td>{{ $objUser->balance }}</td>
            <td>{{ $objUser->user_balance_n1 }}</td>
            <td>{{ $objUser->user_balance_n2 }}</td>
            <td>{{ $objUser->user_balance_n3 }}</td>
            <td>
                @php 

                     $textForControl = "NULL";
                        if ($objUser->for_control_type == 3) {
                            $textForControl = "N+0";
                        }

                        if ($objUser->for_control_type == 4) {
                            $textForControl = "N+" . $objUser->number_day_for_control . " Chốt 15h30";
                        }

                        if ($objUser->for_control_type == 1) {
                            $textForControl = "N+" . $objUser->number_day_for_control . " Chốt 2h";
                        }
                        echo $textForControl;

                @endphp
                </td>
                <td>{{ $objUser->actived == 1 ? 'Kích hoạt' : 'Ngừng kích hoạt' }}</td>
                    <td>{{ $objUser->created_at }}</td>
                </tr>
    @endforeach
</table>