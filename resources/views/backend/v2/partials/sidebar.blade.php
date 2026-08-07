@php
$routerName = request()
    ->route()
    ->getName();
@endphp
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                {{-- <li class="menu-title" key="t-menu">Quản trị</li> --}}

                <li class="s-menu user">
                    <a href="{{ route('backend.index.index') }}" class="waves-effect">
                        <i class="fas fa-home"></i>
                        <span key="t-starter-page">Dashboard</span>
                    </a>
                </li>
                   @if (auth()->user()->is_accountant!=1)

                <li class="s-menu user">
                    <a href="{{ route('backend.user-transaction.index') }}" class="waves-effect">
                        <i class="fas fa-money-bill-alt"></i>
                        <span key="t-starter-page">Giao dịch nạp rút</span>
                    </a>
                </li>    
                 @endif
                <li class="s-menu user">
                    <a href="{{ route('backend.transaction.index') }}" class="waves-effect">
                        <i class="fas fa-inbox"></i>
                        <span key="t-starter-page">Giao dịch tiền vào</span>
                    </a>
                </li>
           
                <li class="s-menu user">
                    <a href="{{ route('backend.user-withdraw.index') }}" class="waves-effect">
                        <i class="fas fa-sign-out-alt"></i>
                        <span key="t-starter-page">Rút tiền</span>
                    </a>
                </li>
                <!-- <li class="s-menu user">
                    <a href="{{ route('backend.user-id-qrcode.index') }}" class="waves-effect">
                        <i class=" fas fa-qrcode"></i>
                        <span key="t-starter-page">Tạo QR định danh</span>
                    </a>
                </li> -->
                   @if (auth()->user()->is_accountant!=1)
                <li class="s-menu user">
                    <a href="{{ route('backend.sub-user.index') }}" class="waves-effect">
                        <i class="fas fa-user"></i>
                        <span key="t-starter-page">Tài khoản phụ</span>
                    </a>
                </li>
                <li class="s-menu user">
                    <a href="{{ route('backend.virtual-account.index') }}" class="waves-effect">
                        <i class="far fa-credit-card"></i>
                        <span key="t-starter-page">Tài khoản ngân hàng</span>
                    </a>
                </li>
              
                <li class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-transfer-alt"></i>
                        <span key="t-charts">Quản lý webhook</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        <li><a href="{{ route('backend.ipn.collection') }}">Ipn Collection</a></li>
                        <li><a href="{{ route('backend.ipn.payout') }}">Ipn Payout</a></li>
                    </ul>
                </li>

                <li class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-book"></i>
                        <span key="t-doc">Tài liệu</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        <li><a href="{{ route('backend.doc.collection') }}">API Collection (Tiền vào)</a></li>
                        <li><a href="{{ route('backend.doc.payout') }}">API Payout (Rút tiền)</a></li>
                    </ul>
                </li>
                @endif
                @if (auth()->user()->is_admin)
                   

                    <li class="menu-title" key="t-pages">Quản trị</li>
                   
                    <li class="">
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bxs-bar-chart-alt-2"></i>
                            <span key="t-charts">Cổng thanh toán</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            <li><a href="{{ route('backend.gateway.index') }}" key="t-apex-charts">Danh sách cổng</a></li>
                            <li><a href="{{ route('backend.gateway-account.index') }}" key="t-e-charts">Danh sách tài khoản
                                    cổng</a></li>
                        </ul>
                    </li>
               
                    @if (auth()->user()->full_access)
                      <li class="s-menu user">
                        <a href="{{ route('backend.bank.index') }}" class="waves-effect">
                            <i class="fas fa-university"></i>
                            <span key="t-starter-page">Quản lý ngân hàng</span>
                        </a>
                    </li>
                    <li class="s-menu user">
                        <a href="{{ route('backend.bank-account.index') }}" class="waves-effect">
                            <i class="fas fa-credit-card"></i>
                            <span key="t-starter-page">Tài khoản ngân hàng</span>
                        </a>
                    </li>
                    @endif

                    @if (auth()->user()->is_accountant||auth()->user()->full_access)
                    <li class="s-menu user">
                        <a href="{{ route('backend.user.index') }}" class="waves-effect">
                            <i class="fas fa-user"></i>
                            <span key="t-starter-page">Quản lý người dùng</span>
                        </a>
                    </li>
                    <li class="s-menu user">
                        <a href="{{ route('backend.user-debit.index') }}" class="waves-effect">
                            <i class="fas fa-money-check-alt"></i>
                            <span key="t-starter-page">Quản lý công nợ</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bxs-bar-chart-alt-2"></i>
                            <span key="t-charts">Thống kê</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            <li>
                                <a href="{{ route('backend.report.index') }}" key="t-apex-charts">Tổng hợp</a>
                            </li>
                            <li><a href="{{ route('backend.user-revenue-report.index') }}" key="t-apex-charts">Doanh số</a>
                            </li>
                            <li><a href="{{ route('backend.report.user') }}" key="t-e-charts">Người dùng</a></li>
                        </ul>
                    </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</div>