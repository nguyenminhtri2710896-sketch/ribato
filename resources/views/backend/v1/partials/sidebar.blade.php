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
                        <span>{{ __('backend.dashboards') }}</span>
                    </a>
                </li>
                @if (auth()->user()->is_accountant != 1)

                    <li class="s-menu user">
                        <a href="{{ route('backend.user-transaction.index') }}" class="waves-effect">
                            <i class="fas fa-money-bill-alt"></i>
                            <span>{{ __('backend.history_deposit_withdraw') }}</span>
                        </a>
                    </li>
                @endif
                <li class="s-menu user">
                    <a href="{{ route('backend.transaction.index') }}" class="waves-effect">
                        <i class="fas fa-inbox"></i>
                        <span>{{ __('backend.payment_transactions') }}</span>
                    </a>
                </li>

                <li class="s-menu user">
                    <a href="{{ route('backend.user-withdraw.index') }}" class="waves-effect">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ __('backend.withdraw_requests') }}</span>
                    </a>
                </li>
                <!-- <li class="s-menu user">
                    <a href="{{ route('backend.user-id-qrcode.index') }}" class="waves-effect">
                        <i class=" fas fa-qrcode"></i>
                        <span key="t-starter-page">Tạo QR định danh</span>
                    </a>
                </li> -->
                @if (auth()->user()->is_accountant != 1)
                    <li class="s-menu user">
                        <a href="{{ route('backend.sub-user.index') }}" class="waves-effect">
                            <i class="fas fa-user"></i>
                            <span>{{ __('backend.create_sub_account') }}</span>
                        </a>
                    </li>
                    <li class="s-menu user">
                        <a href="{{ route('backend.virtual-account.index') }}" class="waves-effect">
                            <i class="far fa-credit-card"></i>
                            <span>{{ __('backend.va_list') }}</span>
                        </a>
                    </li>

                    <li class="">
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-transfer-alt"></i>
                            <span>{{ __('backend.ipn_management') }}</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            <li><a href="{{ route('backend.ipn.collection') }}">Ipn Collection</a></li>
                            <li><a href="{{ route('backend.ipn.payout') }}">Ipn Payout</a></li>
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->is_admin)


                    <li class="menu-title">{{ __('backend.admin') }}</li>

                    @if (auth()->user()->full_access)


                        <li class="s-menu user">
                            <a href="{{ route('backend.bank.index') }}" class="waves-effect">
                                <i class="fas fa-university"></i>
                                <span>{{ __('backend.bank_management') }}</span>
                            </a>
                        </li>
                        <li class="s-menu user">
                            <a href="{{ route('backend.bank-account.index') }}" class="waves-effect">
                                <i class="fas fa-credit-card"></i>
                                <span>{{ __('backend.bank_account') }}</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->is_accountant)
                        <!-- <li class="">
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="bx bxs-bar-chart-alt-2"></i>
                                <span>{{ __('backend.payment_gateway') }}</span>
                            </a>
                            <ul class="sub-menu mm-collapse" aria-expanded="false">
                                <li><a
                                        href="{{ route('backend.gateway-account.index') }}">{{ __('backend.gateway_account_list') }}</a>
                                </li>
                            </ul>
                        </li> -->
                    @endif
                        <li class="">
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="bx bxs-bar-chart-alt-2"></i>
                                <span>{{ __('backend.payment_gateway') }}</span>
                            </a>
                            <ul class="sub-menu mm-collapse" aria-expanded="false">
                                <li><a href="{{ route('backend.gateway.index') }}">{{ __('backend.gateway_list') }}</a></li>
                                <li><a href="{{ route('backend.gateway-account.index') }}">{{ __('backend.gateway_account_list') }}</a></li>
                            </ul>
                        </li>
                    @if (auth()->user()->is_accountant || auth()->user()->full_access)
                        <li class="s-menu user">
                            <a href="{{ route('backend.user.index') }}" class="waves-effect">
                                <i class="fas fa-user"></i>
                                <span>{{ __('backend.user_management') }}</span>
                            </a>
                        </li>
                        <li class="s-menu user">
                            <a href="{{ route('backend.user-debit.index') }}" class="waves-effect">
                                <i class="fas fa-money-check-alt"></i>
                                <span>{{ __('backend.debit_management') }}</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="bx bxs-bar-chart-alt-2"></i>
                                <span>{{ __('backend.statistics') }}</span>
                            </a>
                            <ul class="sub-menu mm-collapse" aria-expanded="false">
                                <li>
                                    <a href="{{ route('backend.report.index') }}">{{ __('backend.summary') }}</a>
                                </li>
                                <li><a href="{{ route('backend.user-revenue-report.index') }}">{{ __('backend.revenue') }}</a>
                                </li>
                                <li><a href="{{ route('backend.report.user') }}">{{ __('backend.users') }}</a></li>
                            </ul>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</div>