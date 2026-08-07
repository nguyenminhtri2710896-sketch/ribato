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
                        {{env('APP_NAME')}}
                    </span>
                    {{-- <span class="logo-sm">
                        <img src="{{ asset('mod/v2/images/logo.svg') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('mod/v2/images/logo-dark.png') }}" alt="" height="17">
                    </span> --}}
                </a>

                <a href="{{ route('mod.index.index') }}" class="logo logo-light">

                    <span class="logo-sm">
                        {{substr(env('APP_NAME'), 0, 1)}}
                    </span>
                    <span class="logo-lg">
                        {{env('APP_NAME')}}
                    </span>
                    {{-- <span class="logo-sm">
                        <img src="{{ asset('mod/v2/images/logo-light.svg') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('mod/v2/images/logo-light.png') }}" alt="" height="19">
                    </span> --}}
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            {{-- <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <img id="header-lang-img" src="{{ asset('mod/v2/images/flags/us.jpg') }}" alt="Header Language"
                        height="16">
                </button>
                <div class="dropdown-menu dropdown-menu-end">

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="en">
                        <img src="{{ asset('mod/v2/images/flags/us.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">English</span>
                    </a>
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="sp">
                        <img src="{{ asset('mod/v2/images/flags/spain.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">Spanish</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="gr">
                        <img src="{{ asset('mod/v2/images/flags/germany.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">German</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="it">
                        <img src="{{ asset('mod/v2/images/flags/italy.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">Italian</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="ru">
                        <img src="{{ asset('mod/v2/images/flags/russia.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">Russian</span>
                    </a>
                </div>
            </div> --}}

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            {{-- <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-bell bx-tada"></i>
                    <span class="badge bg-danger rounded-pill">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                                <a href="#!" class="small" key="t-view-all"> View All</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="d-flex">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                                        <i class="bx bx-cart"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" key="t-your-order">Your order is placed</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1" key="t-grammer">If several languages coalesce the
                                            grammar</p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-min-ago">3
                                                min ago</span></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="d-flex">
                                <img src="{{ asset('mod/v2/images/users/avatar-3.jpg') }}"
                                    class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">James Lemire</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1" key="t-simplified">It will seem like simplified
                                            English.</p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-hours-ago">1
                                                hours ago</span></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="d-flex">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title bg-success rounded-circle font-size-16">
                                        <i class="bx bx-badge-check"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" key="t-shipped">Your item is shipped</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1" key="t-grammer">If several languages coalesce the
                                            grammar</p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-min-ago">3
                                                min ago</span></p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="d-flex">
                                <img src="{{ asset('mod/v2/images/users/avatar-4.jpg') }}"
                                    class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Salena Layfield</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1" key="t-occidental">As a skeptical Cambridge friend
                                            of mine occidental.</p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-hours-ago">1
                                                hours ago</span></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                            <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">View
                                More..</span>
                        </a>
                    </div>
                </div>
            </div> --}}

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
                        src="{{ asset('mod/v2/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1  header-profile-user-fullname" key="t-henry"></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('mod.account.index') }}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">Thông tin tài khoản</span></a>
                    <!-- <a class="dropdown-item d-block" href="#"><i
                            class="bx bx-wrench font-size-16 align-middle me-1"></i> <span key="t-settings">Cài
                            đặt</span></a> -->
                    <a class="dropdown-item d-block" href="#"><i class="far fa-bell"></i> Thông
                        báo</span></a>
                    {{-- <a class="dropdown-item d-block" href="{{route('mod.account.update-profile')}}"><i
                            class="far fa-edit"></i> Cập nhật
                        tài khoản</span></a> --}}
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="base.signOut()"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">Logout</span></a>
                </div>
            </div>
        </div>
    </div>
</header>

