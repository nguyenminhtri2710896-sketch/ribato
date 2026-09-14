<!DOCTYPE html>
<html class="scroll-smooth" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Razorepay - Best Payment Solution for Online Payments')</title>
    <meta name="description" content="Online Payments: Start Accepting Payments Instantly with Razorepay's Payment Suite, supporting Cards, UPI, QR, Payouts, and Business Banking.">
    <link rel="icon" href="https://framerusercontent.com/images/CU1m0xFonUl76ZeaW0IdkQ0M.png" type="image/png">

    <!-- Google Fonts Matching Official Razorpay Design -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fragment+Mono&family=Inter+Tight:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=TASA+Orbiter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        rzp: {
                            blue: '#305eff',
                            'blue-hover': '#254ce0',
                            'blue-dark': '#2950da',
                            'blue-light': '#f0f5ff',
                            'blue-muted': '#305eff17',
                            navy: '#192839',
                            'navy-light': '#40566d',
                            'navy-muted': '#768ea7',
                            'navy-dark': '#0c1927',
                            'navy-bg': '#050b14',
                            'navy-card': '#243547',
                            green: '#009e5c',
                            'green-light': '#48d08c',
                            'green-dark': '#006c3f',
                            'green-bg': '#edf7f7',
                            bg: '#f8fafc',
                            'bg-gray': '#f1f5fa',
                            'border-subtle': '#cbd5e22e',
                            border: '#e2e8f0',
                        },
                    },
                    fontFamily: {
                        sans: ['"Inter Tight"', '"Inter"', 'sans-serif'],
                        display: ['"TASA Orbiter"', '"Inter Tight"', '"Space Grotesk"', 'sans-serif'],
                        mono: ['"Fragment Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'rzp-card': '0px 2px 16px 0px rgba(25, 40, 57, 0.09)',
                        'rzp-card-hover': '0px 8px 30px 0px rgba(48, 94, 255, 0.12)',
                        'rzp-dropdown': '0px 10px 40px -10px rgba(25, 40, 57, 0.15)',
                        'rzp-btn': '0px 4px 14px 0px rgba(48, 94, 255, 0.35)',
                    }
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Inter Tight', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #192839;
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Razorpay Brand Button */
        .btn-rzp-primary {
            background-color: #305eff;
            color: #ffffff;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-rzp-primary:hover {
            background-color: #254ce0;
            box-shadow: 0 4px 14px rgba(48, 94, 255, 0.35);
            transform: translateY(-1px);
        }

        .btn-rzp-secondary {
            background-color: transparent;
            color: #2950da;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-rzp-secondary:hover {
            color: #1d3db0;
            text-decoration: underline;
        }

        /* Razorpay Product Card */
        .rzp-product-card {
            background: #ffffff;
            border: 1px solid rgba(121, 135, 156, 0.18);
            border-radius: 8px;
            box-shadow: 0px 2px 16px 0px rgba(49, 49, 51, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .rzp-product-card:hover {
            box-shadow: 0px 8px 30px 0px rgba(48, 94, 255, 0.14);
            border-color: rgba(48, 94, 255, 0.35);
            transform: translateY(-2px);
        }

        /* Custom Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Category Nav Tab Pill */
        .category-tab {
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.2s ease;
            color: #40566d;
            border: 1px solid transparent;
        }
        .category-tab:hover {
            background-color: #f1f5fa;
            color: #192839;
        }
        .category-tab.active {
            background-color: #edf7f7;
            color: #006c3f;
            border-color: #b6ecd1;
        }
    </style>
    @yield('style')
</head>

<body class="bg-white text-rzp-navy min-h-screen flex flex-col antialiased selection:bg-rzp-blue selection:text-white">

    <!-- Top Announcement Strip -->
    <aside aria-label="Announcement" class="bg-[#edf7f7] border-b border-[#b6ecd1]/60 text-xs py-2 px-4 text-center flex items-center justify-center gap-2 text-rzp-green-dark">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#009e5c] text-white tracking-wide" data-i18n="announcement_badge">MỚI</span>
        <span class="font-medium" data-i18n="announcement_text">Cổng thanh toán thế hệ mới & AI Payments đã sẵn sàng! Trải nghiệm thanh toán nhanh gấp 5 lần.</span>
        <a href="#build-ai-native" class="font-bold text-[#006c3f] hover:underline inline-flex items-center gap-0.5 ml-1" data-i18n="announcement_link">
            Khám phá ngay →
        </a>
    </aside>

    <!-- Header / Navbar (Clean White Razorpay Header with ONLY Language Selector on the Right) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-rzp-border transition-all duration-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[72px]">
                
                <!-- Brand Logo & Navigation -->
                <div class="flex items-center gap-8 xl:gap-10">
                    <!-- Razorepay SVG Logo Replica -->
                    <a href="/" class="flex items-center gap-2.5 group">
                        <svg class="w-7 h-7 text-rzp-blue" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13.5 2L3 14h8.5l-1.5 8 11-12h-8.5l1-8z" />
                        </svg>
                        <span class="text-2xl font-black tracking-tight text-[#0c2340] font-sans">
                            Razorepay
                        </span>
                    </a>

                    <!-- Desktop Menu -->
                    <nav class="hidden lg:flex items-center gap-6 text-[14.5px] font-medium text-rzp-navy">
                        <!-- Payments Dropdown -->
                        <div class="relative group py-6">
                            <button class="flex items-center gap-1 hover:text-rzp-blue transition py-1">
                                <span data-i18n="nav_payments">Thanh toán</span>
                                <svg class="w-4 h-4 text-rzp-navy-muted group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute top-full left-0 w-80 p-4 bg-white border border-rzp-border rounded-xl shadow-rzp-dropdown opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-2 group-hover:translate-y-0">
                                <a href="#accept-payments" class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-rzp-bg transition">
                                    <div class="p-2 rounded bg-rzp-blue-muted text-rzp-blue">
                                        <span class="material-symbols-outlined text-lg">credit_card</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-rzp-navy text-sm" data-i18n="nav_pg">Cổng thanh toán</div>
                                        <div class="text-xs text-rzp-navy-muted" data-i18n="nav_pg_desc">100+ phương thức thanh toán, thẻ & mã QR</div>
                                    </div>
                                </a>
                                <a href="#accept-payments" class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-rzp-bg transition">
                                    <div class="p-2 rounded bg-rzp-green-bg text-rzp-green">
                                        <span class="material-symbols-outlined text-lg">qr_code_2</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-rzp-navy text-sm" data-i18n="nav_qr">Mã QR & Thu tiền thông minh</div>
                                        <div class="text-xs text-rzp-navy-muted" data-i18n="nav_qr_desc">Mã QR tức thì với webhook thời gian thực</div>
                                    </div>
                                </a>
                                <a href="#accept-payments" class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-rzp-bg transition">
                                    <div class="p-2 rounded bg-purple-50 text-purple-600">
                                        <span class="material-symbols-outlined text-lg">link</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-rzp-navy text-sm" data-i18n="nav_links">Link & Trang thanh toán</div>
                                        <div class="text-xs text-rzp-navy-muted" data-i18n="nav_links_desc">Link thanh toán không cần lập trình</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Banking+ Dropdown -->
                        <div class="relative group py-6">
                            <button class="flex items-center gap-1 hover:text-rzp-blue transition py-1">
                                <span data-i18n="nav_banking">Ngân hàng+</span>
                                <svg class="w-4 h-4 text-rzp-navy-muted group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute top-full left-0 w-80 p-4 bg-white border border-rzp-border rounded-xl shadow-rzp-dropdown opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-2 group-hover:translate-y-0">
                                <a href="#business-banking" class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-rzp-bg transition">
                                    <div class="p-2 rounded bg-amber-50 text-amber-600">
                                        <span class="material-symbols-outlined text-lg">account_balance</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-rzp-navy text-sm" data-i18n="nav_ca">Tài khoản Doanh nghiệp</div>
                                        <div class="text-xs text-rzp-navy-muted" data-i18n="nav_ca_desc">Ngân hàng số tự động cho doanh nghiệp</div>
                                    </div>
                                </a>
                                <a href="#make-payouts" class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-rzp-bg transition">
                                    <div class="p-2 rounded bg-cyan-50 text-cyan-600">
                                        <span class="material-symbols-outlined text-lg">send_money</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-rzp-navy text-sm" data-i18n="nav_payouts">Chi trả đối tác tức thì</div>
                                        <div class="text-xs text-rzp-navy-muted" data-i18n="nav_payouts_desc">Chi trả ngân hàng 24/7 tự động</div>
                                    </div>
                                </a>
                                <a href="#automate-payroll" class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-rzp-bg transition">
                                    <div class="p-2 rounded bg-emerald-50 text-emerald-600">
                                        <span class="material-symbols-outlined text-lg">payments</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-rzp-navy text-sm" data-i18n="nav_payroll">Tự động hóa Bảng lương</div>
                                        <div class="text-xs text-rzp-navy-muted" data-i18n="nav_payroll_desc">Chuyển lương & quyết toán thuế tự động</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- AI Native -->
                        <a href="#build-ai-native" class="hover:text-rzp-blue transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            <span data-i18n="nav_ai">Giải pháp AI Native</span>
                        </a>

                        <a href="#no-code-section" class="hover:text-rzp-blue transition" data-i18n="nav_nocode">Bộ công cụ No-Code</a>
                        <a href="#developer-section" class="hover:text-rzp-blue transition" data-i18n="nav_devs">Lập trình viên</a>
                        <a href="#pricing-calculator" class="hover:text-rzp-blue transition" data-i18n="nav_pricing">Bảng giá</a>
                    </nav>
                </div>

                <!-- Right Actions: ONLY Language & Country Selector Dropdown -->
                <div class="hidden lg:flex items-center gap-3">
                    
                    <!-- Interactive Multi-Country & Language Selector Dropdown -->
                    <div class="relative" id="lang-dropdown-container">
                        <button id="lang-menu-btn" type="button" class="flex items-center gap-2 px-3 py-2 rounded-lg border border-rzp-border text-xs text-rzp-navy font-bold bg-rzp-bg hover:bg-white hover:border-rzp-blue hover:shadow-sm transition-all duration-150">
                            <span id="current-lang-flag" class="text-base">🇻🇳</span>
                            <span id="current-lang-label" class="font-semibold">Việt Nam</span>
                            <svg class="w-3.5 h-3.5 text-rzp-navy-muted transition-transform duration-200" id="lang-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <!-- Dropdown Options Box -->
                        <div id="lang-dropdown-menu" class="absolute right-0 top-full mt-2 w-64 p-2 bg-white border border-rzp-border rounded-xl shadow-rzp-dropdown opacity-0 invisible transition-all duration-200 z-50">
                            <div class="px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 mb-1">
                                Chọn Quốc gia / Vùng lãnh thổ
                            </div>
                            
                            <!-- 1. Việt Nam -->
                            <button type="button" onclick="setLanguage('vi')" class="lang-option-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-medium text-rzp-navy hover:bg-blue-50 hover:text-rzp-blue transition text-left" data-lang="vi">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-lg">🇻🇳</span>
                                    <span><strong>Việt Nam</strong></span>
                                </span>
                                <span class="lang-check text-rzp-blue material-symbols-outlined text-sm">check</span>
                            </button>

                            <!-- 2. Malaysia -->
                            <button type="button" onclick="setLanguage('ms')" class="lang-option-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-medium text-rzp-navy hover:bg-blue-50 hover:text-rzp-blue transition text-left" data-lang="ms">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-lg">🇲🇾</span>
                                    <span><strong>Malaysia</strong></span>
                                </span>
                                <span class="lang-check text-rzp-blue hidden material-symbols-outlined text-sm">check</span>
                            </button>

                            <!-- 3. Singapore -->
                            <button type="button" onclick="setLanguage('sg')" class="lang-option-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-medium text-rzp-navy hover:bg-blue-50 hover:text-rzp-blue transition text-left" data-lang="sg">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-lg">🇸🇬</span>
                                    <span><strong>Singapore</strong></span>
                                </span>
                                <span class="lang-check text-rzp-blue hidden material-symbols-outlined text-sm">check</span>
                            </button>

                            <!-- 4. United States -->
                            <button type="button" onclick="setLanguage('us')" class="lang-option-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-medium text-rzp-navy hover:bg-blue-50 hover:text-rzp-blue transition text-left" data-lang="us">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-lg">🇺🇸</span>
                                    <span><strong>United States</strong></span>
                                </span>
                                <span class="lang-check text-rzp-blue hidden material-symbols-outlined text-sm">check</span>
                            </button>

                            <!-- 5. India -->
                            <button type="button" onclick="setLanguage('in')" class="lang-option-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-medium text-rzp-navy hover:bg-blue-50 hover:text-rzp-blue transition text-left" data-lang="in">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-lg">🇮🇳</span>
                                    <span><strong>India</strong></span>
                                </span>
                                <span class="lang-check text-rzp-blue hidden material-symbols-outlined text-sm">check</span>
                            </button>
                        </div>
                    </div>

                    <!-- Contact Us button -->
                    <button onclick="openContactModal()" class="px-3.5 py-2 rounded border border-rzp-blue/30 text-xs font-bold text-rzp-blue hover:bg-blue-50 transition" data-i18n="nav_contact">
                        Liên hệ tư vấn
                    </button>
                </div>

                <!-- Mobile Menu Hamburger -->
                <div class="flex lg:hidden items-center gap-2">
                    <button onclick="openContactModal()" class="text-xs font-semibold text-rzp-blue px-3 py-1.5 border border-rzp-blue/30 rounded" data-i18n="nav_contact">
                        Liên hệ
                    </button>
                    <button id="mobile-menu-btn" class="p-2 rounded-lg bg-rzp-bg text-rzp-navy border border-rzp-border hover:bg-slate-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-drawer" class="lg:hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="fixed right-0 top-0 bottom-0 w-4/5 max-w-sm bg-white border-l border-rzp-border p-6 flex flex-col justify-between transform translate-x-full transition-transform duration-300 overflow-y-auto" id="mobile-drawer-content">
                <div>
                    <div class="flex items-center justify-between pb-5 border-b border-rzp-border">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-rzp-blue" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 2L3 14h8.5l-1.5 8 11-12h-8.5l1-8z" /></svg>
                            <span class="font-extrabold text-[#0c2340] text-xl">Razorepay</span>
                        </div>
                        <button id="close-mobile-drawer" class="p-2 text-rzp-navy-muted hover:text-rzp-navy">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="mt-6 flex flex-col gap-2">
                        <a href="#build-ai-native" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="nav_ai">Giải pháp AI Native</a>
                        <a href="#accept-payments" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="nav_payments">Nhận thanh toán</a>
                        <a href="#make-payouts" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="tab_payouts">Chi trả tiền</a>
                        <a href="#business-banking" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="tab_banking">Ngân hàng Doanh nghiệp</a>
                        <a href="#automate-payroll" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="tab_payroll">Bảng lương</a>
                        <a href="#no-code-section" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="nav_nocode">Bộ công cụ No-Code</a>
                        <a href="#developer-section" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="nav_devs">Lập trình viên & APIs</a>
                        <a href="#pricing-calculator" class="mobile-nav-link text-sm font-semibold text-rzp-navy hover:text-rzp-blue py-2 border-b border-slate-100" data-i18n="nav_pricing">Bảng giá</a>
                    </div>
                </div>

                <!-- Mobile Language Selector (Replaces Sign Up & Login) -->
                <div class="pt-6 border-t border-rzp-border">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Quốc gia & Vùng lãnh thổ</div>
                    <div class="grid grid-cols-1 gap-1">
                        <button type="button" onclick="setLanguage('vi')" class="mobile-lang-btn flex items-center justify-between p-2 rounded-lg text-xs font-semibold text-rzp-navy hover:bg-slate-100 transition" data-lang="vi">
                            <span class="flex items-center gap-2"><span>🇻🇳</span> Việt Nam</span>
                            <span class="mobile-lang-check text-rzp-blue font-bold">✓</span>
                        </button>
                        <button type="button" onclick="setLanguage('ms')" class="mobile-lang-btn flex items-center justify-between p-2 rounded-lg text-xs font-semibold text-rzp-navy hover:bg-slate-100 transition" data-lang="ms">
                            <span class="flex items-center gap-2"><span>🇲🇾</span> Malaysia</span>
                            <span class="mobile-lang-check hidden text-rzp-blue font-bold">✓</span>
                        </button>
                        <button type="button" onclick="setLanguage('sg')" class="mobile-lang-btn flex items-center justify-between p-2 rounded-lg text-xs font-semibold text-rzp-navy hover:bg-slate-100 transition" data-lang="sg">
                            <span class="flex items-center gap-2"><span>🇸🇬</span> Singapore</span>
                            <span class="mobile-lang-check hidden text-rzp-blue font-bold">✓</span>
                        </button>
                        <button type="button" onclick="setLanguage('us')" class="mobile-lang-btn flex items-center justify-between p-2 rounded-lg text-xs font-semibold text-rzp-navy hover:bg-slate-100 transition" data-lang="us">
                            <span class="flex items-center gap-2"><span>🇺🇸</span> United States</span>
                            <span class="mobile-lang-check hidden text-rzp-blue font-bold">✓</span>
                        </button>
                        <button type="button" onclick="setLanguage('in')" class="mobile-lang-btn flex items-center justify-between p-2 rounded-lg text-xs font-semibold text-rzp-navy hover:bg-slate-100 transition" data-lang="in">
                            <span class="flex items-center gap-2"><span>🇮🇳</span> India</span>
                            <span class="mobile-lang-check hidden text-rzp-blue font-bold">✓</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Razorepay Rich Navy Footer -->
    <footer class="bg-[#0c1927] text-[#768ea7] pt-16 pb-12 text-sm border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 pb-12 border-b border-white/10">
                <!-- Brand Column -->
                <div class="col-span-2 md:col-span-2 lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-7 h-7 text-rzp-blue" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 2L3 14h8.5l-1.5 8 11-12h-8.5l1-8z" /></svg>
                        <span class="text-2xl font-black text-white">Razorepay</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed mb-6 max-w-sm" data-i18n="footer_brand_desc">
                        Razorepay là nền tảng dịch vụ tài chính toàn diện hàng đầu giúp doanh nghiệp nhận thanh toán, tự động hóa ngân hàng, quản lý bảng lương và tiếp cận tín dụng doanh nghiệp.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="https://twitter.com/razorpay" target="_blank" class="w-8 h-8 rounded bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 hover:text-white transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://linkedin.com/company/razorpay" target="_blank" class="w-8 h-8 rounded bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 hover:text-white transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        <a href="https://github.com/razorpay" target="_blank" class="w-8 h-8 rounded bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 hover:text-white transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Accept Payments Column -->
                <div>
                    <h5 class="text-white font-semibold text-xs uppercase tracking-wider mb-4" data-i18n="footer_col_accept">Nhận thanh toán</h5>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#accept-payments" class="hover:text-white transition" data-i18n="card_pg_title">Cổng thanh toán</a></li>
                        <li><a href="#accept-payments" class="hover:text-white transition" data-i18n="card_pl_title">Link thanh toán</a></li>
                        <li><a href="#accept-payments" class="hover:text-white transition" data-i18n="card_pp_title">Trang thanh toán</a></li>
                        <li><a href="#accept-payments" class="hover:text-white transition" data-i18n="card_sub_title">Thanh toán định kỳ</a></li>
                        <li><a href="#accept-payments" class="hover:text-white transition" data-i18n="nav_qr">Mã QR & Thu tiền thông minh</a></li>
                    </ul>
                </div>

                <!-- Banking & Payouts Column -->
                <div>
                    <h5 class="text-white font-semibold text-xs uppercase tracking-wider mb-4" data-i18n="footer_col_banking">Ngân hàng & Chi trả</h5>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#business-banking" class="hover:text-white transition" data-i18n="card_ca_title">Tài khoản Doanh nghiệp</a></li>
                        <li><a href="#make-payouts" class="hover:text-white transition" data-i18n="card_bulk_title">Chi trả tự động</a></li>
                        <li><a href="#automate-payroll" class="hover:text-white transition" data-i18n="card_pay_title">Bảng lương tự động</a></li>
                        <li><a href="#business-banking" class="hover:text-white transition" data-i18n="card_cards_title">Thẻ Doanh nghiệp</a></li>
                        <li><a href="#credit-and-loans" class="hover:text-white transition" data-i18n="card_wc_title">Vốn lưu động</a></li>
                    </ul>
                </div>

                <!-- Developers Column -->
                <div>
                    <h5 class="text-white font-semibold text-xs uppercase tracking-wider mb-4" data-i18n="footer_col_devs">Lập trình viên</h5>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#developer-section" class="hover:text-white transition" data-i18n="dev_btn_docs">Tài liệu API</a></li>
                        <li><a href="#developer-section" class="hover:text-white transition">Bộ SDK Libraries</a></li>
                        <li><a href="#developer-section" class="hover:text-white transition">Tài liệu Webhooks</a></li>
                        <li><a href="#developer-section" class="hover:text-white transition">Postman Collection</a></li>
                        <li><a href="#developer-section" class="hover:text-white transition">Uptime SLA (99.99%)</a></li>
                    </ul>
                </div>

                <!-- Company Column -->
                <div>
                    <h5 class="text-white font-semibold text-xs uppercase tracking-wider mb-4" data-i18n="footer_col_company">Doanh nghiệp & Pháp lý</h5>
                    <ul class="space-y-2.5 text-xs">
                        <li><button onclick="openContactModal()" class="hover:text-white transition text-left" data-i18n="nav_contact">Liên hệ tư vấn</button></li>
                        <li><a href="#privacy" class="hover:text-white transition">Chính sách bảo mật</a></li>
                        <li><a href="#terms" class="hover:text-white transition">Điều khoản sử dụng</a></li>
                        <li><a href="#compliance" class="hover:text-white transition">Bảo mật & Tuân thủ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Compliance Strip -->
            <div class="mt-8 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex flex-wrap items-center gap-6">
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <span class="material-symbols-outlined text-base text-rzp-green">verified_user</span>
                        <span>PCI-DSS Level 1 Compliant</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <span class="material-symbols-outlined text-base text-rzp-blue">lock</span>
                        <span>ISO/IEC 27001 Certified</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <span class="material-symbols-outlined text-base text-amber-400">shield</span>
                        <span>256-Bit SSL Encryption</span>
                    </div>
                </div>
                <div>
                    © {{ date('Y') }} Razorepay Software. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Contact Modal -->
    <div id="contact-modal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
        <div class="bg-white border border-rzp-border rounded-xl w-full max-w-lg p-6 sm:p-8 shadow-2xl relative transform scale-95 transition-all duration-200" id="contact-modal-box">
            <button onclick="closeContactModal()" class="absolute top-4 right-4 p-2 text-rzp-navy-muted hover:text-rzp-navy transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="mb-6">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-rzp-blue-muted text-rzp-blue text-xs font-semibold mb-2">
                    <span data-i18n="nav_contact">Liên hệ tư vấn</span>
                </div>
                <h3 class="text-xl font-bold text-rzp-navy" data-i18n="contact_modal_title">Trao đổi cùng Đội ngũ Giải pháp</h3>
                <p class="text-xs text-rzp-navy-muted mt-1" data-i18n="contact_modal_desc">Khám phá bảng giá riêng, kiến trúc doanh nghiệp và giải pháp tích hợp tùy chỉnh.</p>
            </div>

            <form id="contact-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-rzp-navy mb-1" data-i18n="form_name">Họ và tên *</label>
                    <input type="text" name="full_name" required class="w-full bg-slate-50 border border-rzp-border rounded px-3.5 py-2.5 text-sm text-rzp-navy focus:outline-none focus:border-rzp-blue focus:bg-white" placeholder="Nguyễn Văn A">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-rzp-navy mb-1" data-i18n="form_email">Email Doanh nghiệp *</label>
                        <input type="email" name="email" required class="w-full bg-slate-50 border border-rzp-border rounded px-3.5 py-2.5 text-sm text-rzp-navy focus:outline-none focus:border-rzp-blue focus:bg-white" placeholder="name@company.com">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-rzp-navy mb-1" data-i18n="form_company">Công ty</label>
                        <input type="text" name="company" class="w-full bg-slate-50 border border-rzp-border rounded px-3.5 py-2.5 text-sm text-rzp-navy focus:outline-none focus:border-rzp-blue focus:bg-white" placeholder="Công ty TNHH ABC">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-rzp-navy mb-1" data-i18n="form_msg">Yêu cầu & Sản lượng dự kiến *</label>
                    <textarea name="message" rows="3" required class="w-full bg-slate-50 border border-rzp-border rounded px-3.5 py-2.5 text-sm text-rzp-navy focus:outline-none focus:border-rzp-blue focus:bg-white" placeholder="Chia sẻ nhu cầu thanh toán hoặc sản lượng xử lý dự kiến..."></textarea>
                </div>
                <div id="contact-form-message" class="hidden text-xs py-2 px-3 rounded"></div>
                <button type="submit" id="contact-submit-btn" class="w-full py-3 btn-rzp-primary shadow-rzp-btn" data-i18n="form_submit">
                    Gửi yêu cầu
                </button>
            </form>
        </div>
    </div>

    <!-- Multi-Language Translator System -->
    <script src="/frontend/v2/js/translations.js?v={{ time() }}"></script>

    <!-- General Scripts -->
    <script>
        function openContactModal() {
            const modal = document.getElementById('contact-modal');
            const box = document.getElementById('contact-modal-box');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
            box.classList.add('scale-100');
        }

        function closeContactModal() {
            const modal = document.getElementById('contact-modal');
            const box = document.getElementById('contact-modal-box');
            modal.classList.add('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
        }

        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeMobileBtn = document.getElementById('close-mobile-drawer');
        const mobileDrawer = document.getElementById('mobile-drawer');
        const mobileDrawerContent = document.getElementById('mobile-drawer-content');

        if (mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                mobileDrawer.classList.remove('opacity-0', 'pointer-events-none');
                mobileDrawerContent.classList.remove('translate-x-full');
            });
        }

        if (closeMobileBtn) {
            closeMobileBtn.addEventListener('click', () => {
                mobileDrawer.classList.add('opacity-0', 'pointer-events-none');
                mobileDrawerContent.classList.add('translate-x-full');
            });
        }

        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                mobileDrawer.classList.add('opacity-0', 'pointer-events-none');
                mobileDrawerContent.classList.add('translate-x-full');
            });
        });

        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = document.getElementById('contact-submit-btn');
                const msgBox = document.getElementById('contact-form-message');
                submitBtn.disabled = true;
                submitBtn.innerText = 'Đang gửi...';

                try {
                    const formData = new FormData(contactForm);
                    const response = await fetch('{{ route("frontend.contact.submit") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        msgBox.className = 'text-xs py-2 px-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200';
                        msgBox.innerText = data.message || 'Cảm ơn bạn! Đội ngũ tư vấn sẽ liên hệ lại sớm.';
                        msgBox.classList.remove('hidden');
                        contactForm.reset();
                        setTimeout(() => {
                            closeContactModal();
                            msgBox.classList.add('hidden');
                        }, 2500);
                    } else {
                        throw new Error(data.message || 'Gửi thất bại.');
                    }
                } catch (err) {
                    msgBox.className = 'text-xs py-2 px-3 rounded bg-red-50 text-red-700 border border-red-200';
                    msgBox.innerText = err.message || 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
                    msgBox.classList.remove('hidden');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Gửi yêu cầu';
                }
            });
        }
    </script>
    @yield('javascript')
</body>
</html>
