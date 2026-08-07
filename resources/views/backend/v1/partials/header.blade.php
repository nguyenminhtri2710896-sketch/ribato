<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('backend.index.index') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        {{substr(env('APP_NAME'), 0, 1)}}
                    </span>
                    <span class="logo-lg">
                        {{env('APP_NAME')}}
                    </span>
                    {{-- <span class="logo-sm">
                        <img src="{{ asset('backend/v1/images/logo.svg') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/v1/images/logo-dark.png') }}" alt="" height="17">
                    </span> --}}
                </a>

                <a href="{{ route('backend.index.index') }}" class="logo logo-light">

                    <span class="logo-sm">
                        {{substr(env('APP_NAME'), 0, 1)}}
                    </span>
                    <span class="logo-lg">
                        {{env('APP_NAME')}}
                    </span>
                    {{-- <span class="logo-sm">
                        <img src="{{ asset('backend/v1/images/logo-light.svg') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/v1/images/logo-light.png') }}" alt="" height="19">
                    </span> --}}
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
                    <img id="header-lang-img-php" src="{{ asset('backend/v1/images/flags/' . $flag) }}" alt="Header Language"
                        height="16">
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a href="{{ route('backend.index.lang', 'vi') }}" class="dropdown-item notify-item {{ $locale == 'vi' ? 'active' : '' }}">
                        <img src="{{ asset('backend/v1/images/flags/vn.png') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">Tiếng Việt</span>
                    </a>
                    <!-- item-->
                    <a href="{{ route('backend.index.lang', 'en') }}" class="dropdown-item notify-item {{ $locale == 'en' ? 'active' : '' }}">
                        <img src="{{ asset('backend/v1/images/flags/us.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">English</span>
                    </a>
                    <!-- item-->
                    <a href="{{ route('backend.index.lang', 'zh') }}" class="dropdown-item notify-item {{ $locale == 'zh' ? 'active' : '' }}">
                        <img src="{{ asset('backend/v1/images/flags/cn.png') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">中文</span>
                    </a>
                </div>
            </div>

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
                                <img src="{{ asset('backend/v1/images/users/avatar-3.jpg') }}"
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
                                <img src="{{ asset('backend/v1/images/users/avatar-4.jpg') }}"
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

            <div class="dropdown  block-header-debit" style="display: none;">
                <button type="button" class="btn header-item noti-icon waves-effect header-item-balance">
                    <a href="{{ route('backend.user-debit.index') }}"> <i
                            class="mdi mdi-currency-usd-circle-outline"></i> <span
                            class="text-danger txt-header-debit">0đ</span></a>
                </button>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect header-item-balance">
                    <i class="mdi mdi-currency-usd-circle-outline"></i> <span
                        class="text-success txt-header-balance">0đ</span>
                </button>
                @if(auth()->user()->allow_tranfer_balance)
                    <button class="btn btn-lg-custom btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target=".bs-modal-tranfer-balance">
                        <i class="fas fa-exchange-alt"></i> {{ __('backend.transfer_money') }}
                    </button>
                @endif
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user header-profile-user-avatar"
                        src="{{ asset('backend/v1/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1  header-profile-user-fullname">{{ auth()->user()->fullname }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('backend.account.index') }}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">{{ __('backend.profile') }}</span></a>
                    <!-- <a class="dropdown-item d-block" href="#"><i
                            class="bx bx-wrench font-size-16 align-middle me-1"></i> <span key="t-settings">Cài
                            đặt</span></a> -->
                    <a class="dropdown-item d-block" href="#"><i class="far fa-bell"></i> {{ __('backend.notifications') }}</span></a>
                    {{-- <a class="dropdown-item d-block" href="{{route('backend.account.update-profile')}}"><i
                            class="far fa-edit"></i> Cập nhật
                        tài khoản</span></a> --}}
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="base.signOut()"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span>{{ __('backend.logout') }}</span></a>
                </div>
            </div>
        </div>
    </div>
</header>

@if(auth()->user()->allow_tranfer_balance)
    <!--  Large modal example -->
    <div class="modal fade bs-modal-tranfer-balance" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm  modal-custom-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">{{ __('backend.transfer_money') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal needs-validation frm-ajax-submit" novalidate method="POST"
                        data-ajax-url="{{ route('backend.account.ajax-transfer-balance') }}"
                        data-close-modal=".bs-modal-tranfer-balance">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mt-0">{{ __('backend.email') }}<span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" name="email" placeholder="{{ __('backend.email') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mt-3 mb-0">{{ __('backend.transfer_amount') }}<span
                                                class="text-danger">(*)</span></label>
                                        <input type="tel" class="form-control form-control-custom decimal-input"
                                            data-for="input[name='amount']" placeholder="{{ __('backend.balance') }}" required>
                                        <input type="hidden" class="form-control form-control-custom" name="amount"
                                            placeholder="{{ __('backend.balance') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mt-3 mb-0">{{ __('backend.note') }}</label>
                                        <textarea type="text" class="form-control form-control-custom" name="note"
                                            placeholder="{{ __('backend.note') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->authy_2factor == 1)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="has-validation">
                                        <label class="mt-3  mb-1">{{ __('backend.otp_code') }}</label>
                                        <input type="text" class="form-control" name="otp" placeholder="{{ __('backend.otp_code') }}"
                                            pattern="\d*" maxlength="6" minlength="6"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        <div class="invalid-tooltip">
                                            {{ __('backend.enter_otp') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-3 text-right">
                                    <button type="submit" class="btn btn-success "><i class="fas fa-save"></i>
                                        {{ __('backend.transfer') }}</button>
                                    <button type="reset" class="btn btn-danger" data-bs-dismiss="modal"
                                        aria-label="Close"><i class="fas fa-times"></i> {{ __('backend.close') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endif