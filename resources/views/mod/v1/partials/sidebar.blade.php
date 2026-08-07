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
                    <a href="{{ route('mod.index.index') }}" class="waves-effect">
                        <i class="fas fa-home"></i>
                        <span>{{ __('backend.dashboards') }}</span>
                    </a>
                </li>
                <li class="s-menu user">
                    <a href="{{ route('mod.transaction.index') }}" class="waves-effect">
                        <i class="fas fa-inbox"></i>
                        <span>{{ __('backend.payment_transactions') }}</span>
                    </a>
                </li>
                <li class="s-menu user">
                    <a href="{{ route('mod.user-withdraw.index') }}" class="waves-effect">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ __('backend.withdraw_list') }}</span>
                    </a>
                </li>
                <li class="s-menu user">
                    <a href="{{ route('mod.virtual-account.index') }}" class="waves-effect">
                        <i class="far fa-credit-card"></i>
                        <span>{{ __('backend.va_list') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>