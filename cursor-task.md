# QUẢY LÝ IPN
Menu bên trái tạo mục 
    Quản lý IPN
        Ipn Collection
        Ipn payout
    
    với  Ipn Collection sẽ lấy trong bảng transaction_callbacks
    Thông tin hiển thị là danh sách ipn của mỗi user, thông tin hiển thị gồm (mã giao dịch, mã đơn, nội dung, trạng thái, ngày xử lý, chức năng)
    Ở cột chức năng sẽ có nút xem chi tiết , khi nhấn vào sẽ mở modal show thông tin param request và params response
    Cột chức năng sẽ có nút gửi lại, thao tác sẽ gửi lại ipn thông qua job TransactionCallbackResultJob , trước khi gửi sẽ update lại cột callback_total_retry= 0 trong bảng transaction


     với  Ipn payout sẽ lấy trong bảng user_withdraw_callbacks
    Thông tin hiển thị là danh sách ipn của mỗi user, thông tin hiển thị gồm (mã giao dịch, mã đơn, nội dung, trạng thái, ngày xử lý, chức năng)
    Ở cột chức năng sẽ có nút xem chi tiết , khi nhấn vào sẽ mở modal show thông tin param request và params response
    Cột chức năng sẽ có nút gửi lại, thao tác sẽ gửi lại ipn thông qua job PayoutCallbackResultJob , trước khi gửi sẽ update lại cột callback_total_retry= 0 trong bảng user_withdraws


# Tạo biểu đồ lợi nhuận trong biểu đồ tổng hợp
- Dữ liệu sẽ được lấy trong bảng user_revenue_reports 
- Biểu đồ thể hiện mỗi ngày là cột đôi (cột tổng giao dịch (giao dịch collect thôi không lấy giao dịch payout) và lợi nhuận (lấy luôn collect và payout)) và hiện 30 ngày gần nhất, bên trên biểu đồ sẽ có chọn xem biểu đồ từ ngày đến ngày, giữa biểu dồ sẽ thấy tổng lợi nhuận toàn hệ thống



Tạo mục thêm yêu cầu rút tiền thủ công
 - Chỉ được sử dụng trong quản trị
 - Thông tin đầu vào gồm: người dùng số tiền yêu cầu rút, phí rút, ngân hàng m số tài khoản, tên chủ khoản, ghi chú