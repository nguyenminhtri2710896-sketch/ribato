@component('mail::message')
    Dear <strong>{{ $email }}</strong>,<br />
    Có vẻ như quý khách không còn nhớ mật khẩu.<br />
    Nếu quý khách tiến hành lấy lại mật khẩu, vui lòng sử dụng mã code bên dưới để hoàn tất việc khôi phục.<br />
    <h1 style="text-align: center"><strong>{{ $code }}</strong></h1><br />
    Trân trọng,<br />
    — Đội ngũ {{ config('app.name') }}
@endcomponent
