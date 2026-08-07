<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('mod.index.index') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        {{substr(env('APP_NAME'), 0, 1)}}
                    </span>
                    <span class="logo-lg">
                        MOD {{env('APP_NAME')}}
                    </span>
                </a>

                <a href="{{ route('mod.index.index') }}" class="logo logo-light">

                    <span class="logo-sm">
                        {{substr(env('APP_NAME'), 0, 1)}}
                    </span>
                    <span class="logo-lg">
                        MOD {{env('APP_NAME')}}
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block">
                @php
                    $locale = App::getLocale();
                    $flag = 'us.jpg';
                    if ($locale == 'vi') {
                        $flag = 'vn.png';
                    } elseif ($locale == 'zh') {
                        $flag = 'cn.png';
                    }
                @endphp
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <img id="header-lang-img" src="{{ asset('mod/v1/images/flags/' . $flag) }}" alt="Header Language"
                        height="16">
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a href="{{ route('mod.index.lang', 'vi') }}" class="dropdown-item notify-item">
                        <img src="{{ asset('mod/v1/images/flags/vn.png') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">Tiếng Việt</span>
                    </a>
                    <!-- item-->
                    <a href="{{ route('mod.index.lang', 'en') }}" class="dropdown-item notify-item">
                        <img src="{{ asset('mod/v1/images/flags/us.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">English</span>
                    </a>
                    <!-- item-->
                    <a href="{{ route('mod.index.lang', 'zh') }}" class="dropdown-item notify-item">
                        <img src="{{ asset('mod/v1/images/flags/cn.png') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">中文</span>
                    </a>
                </div>
            </div>

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect header-item-balance">
                    <i class="mdi mdi-currency-usd-circle-outline"></i> <span
                        class="text-success txt-header-balance">0đ</span>
                </button>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user header-profile-user-avatar"
                        src="{{ asset('mod/v1/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1  header-profile-user-fullname" key="t-henry"></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('mod.account.index') }}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">{{ __('backend.profile') }}</span></a>
                    <a class="dropdown-item d-block" href="#"><i class="far fa-bell"></i> {{ __('backend.notifications') }}</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="base.signOut()"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">{{ __('backend.logout') }}</span></a>
                </div>
            </div>
        </div>
    </div>
</header>